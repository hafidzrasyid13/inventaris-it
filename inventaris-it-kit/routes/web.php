<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\TicketController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

// Hanya untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
});

// Wajib login
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Khusus admin: kelola aset, kategori, lokasi (didaftarkan sebelum {asset} agar /assets/create tidak bentrok)
    Route::middleware(RoleMiddleware::class . ':admin')->group(function () {
        Route::get('/assets/export', [AssetController::class, 'export'])->name('assets.export');
        Route::resource('assets', AssetController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'destroy']);
        Route::resource('locations', LocationController::class)->only(['index', 'store', 'destroy']);
    });

    // Semua user login boleh melihat aset
    Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
    Route::get('/assets/{asset}', [AssetController::class, 'show'])->whereNumber('asset')->name('assets.show');

    // Tiket perbaikan
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->whereNumber('ticket')->name('tickets.show');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])
        ->whereNumber('ticket')
        ->middleware(RoleMiddleware::class . ':admin,teknisi')
        ->name('tickets.update');
});
