<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PackagePickupController;
use App\Http\Controllers\UserController;

// Rute untuk Pengguna yang Belum Login (Guest)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Register / Buat Akun Petugas
    Route::get('/register', [RegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Halaman yang wajib login
Route::middleware('auth')->group(function () {

    Route::get('/', [DashboardController::class, 'index']);

    // Halaman Data Paket (Khusus yang belum diambil)
    Route::get('/packages', [PackageController::class, 'index'])
        ->name('packages.index');

    // Halaman Riwayat Pengambilan Paket (Khusus yang sudah diambil)
    Route::get('/history', [PackageController::class, 'history'])
        ->name('packages.history');

    // Proses Simpan Paket
    Route::post('/packages', [PackageController::class, 'store'])
        ->name('packages.store');

    // Detail Paket
    Route::get('/packages/{package}', [PackageController::class, 'show'])->name('packages.show');

    // Manajemen Petugas / User Management (Khusus Administrator)
    Route::middleware('admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Proses Pengambilan Paket & Tanda Tangan Digital
    Route::get('/packages/{package}/pickup', [PackagePickupController::class, 'create'])
        ->name('packages.pickup.create');
    Route::post('/packages/{package}/pickup', [PackagePickupController::class, 'store'])
        ->name('packages.pickup.store');

    // Proses Logout
    Route::post('/logout', [LoginController::class, 'logout']);

});

// Serving file public storage secara langsung (fallback jika symlink tidak didukung oleh runtime Wasmer Edge)
Route::get('/storage/{path}', function ($path) {
    $filePath = storage_path('app/public/' . $path);

    if (!file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath);
})->where('path', '.*');
