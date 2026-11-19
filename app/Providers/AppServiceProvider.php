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

        // Otomatis migrate & seed jika tabel users belum ada (untuk runtime Wasmer Edge / Serverless)
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
}
