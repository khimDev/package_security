<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('pagination.custom');

        // Pastikan asset & URL menggunakan HTTPS saat diakses melalui tunnel Cloudflare
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }

        // Otomatis migrate & seed jika tabel users belum ada (khusus saat menerima request HTTP di runtime Wasmer Edge)
        if (!app()->runningInConsole()) {
            try {
                if (!Schema::hasTable('users')) {
                    Artisan::call('migrate', [
                        '--seed' => true,
                        '--force' => true,
                    ]);
                }
            } catch (Throwable $e) {
                // Diamkan agar tidak crash jika database belum siap
            }
        }

        // Hapus symlink/folder public/storage jika terdeteksi agar tidak diblokir oleh php -S di Wasmer Edge
        try {
            $storageLink = public_path('storage');
            if (is_link($storageLink)) {
                @unlink($storageLink);
            } elseif (is_dir($storageLink)) {
                @rmdir($storageLink);
            }
        } catch (Throwable $e) {
            // Diamkan agar tidak mengganggu booting aplikasi
        }
    }
}
