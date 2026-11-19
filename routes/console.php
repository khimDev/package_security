<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Package;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Command untuk membersihkan paket yang sudah diambil > 30 hari beserta file tanda tangannya
Artisan::command('packages:prune-picked-up', function () {
    $count = Package::pruneExpiredPickups();
    $this->info("Pembersihan selesai: {$count} paket yang sudah diambil lebih dari 30 hari berhasil dihapus.");
})->purpose('Hapus otomatis paket yang sudah diambil lebih dari 30 hari beserta tanda tangannya');

// Jadwalkan otomatis jalan setiap hari
Schedule::command('packages:prune-picked-up')->daily();
