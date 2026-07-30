<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KamarController;
use App\Http\Controllers\Admin\ReservasiController;
use App\Http\Controllers\Admin\PenghuniController;
use App\Http\Controllers\Admin\LaporanController;

Route::get('/', function () {
    return view('welcome');
});

// 1. Rute Khusus Penghuni
Route::middleware(['auth', 'role:penghuni'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); 
    })->name('dashboard');
});

// 2. Rute Khusus Admin Asrama
Route::middleware(['auth', 'role:admin_asrama'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('/admin/kamar', KamarController::class)->except(['create', 'show', 'edit']);

    Route::get('/admin/reservasi', [ReservasiController::class, 'index'])->name('admin.reservasi.index');
    Route::patch('/admin/reservasi/{reservasi}/approve', [ReservasiController::class, 'approve'])->name('admin.reservasi.approve');
    Route::patch('/admin/reservasi/{reservasi}/reject', [ReservasiController::class, 'reject'])->name('admin.reservasi.reject');

    Route::get('/admin/penghuni', [PenghuniController::class, 'index'])->name('admin.penghuni.index');
    Route::get('/admin/penghuni/{reservasi}', [PenghuniController::class, 'show'])->name('admin.penghuni.show');

    Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporan.index');
    Route::get('/admin/laporan/export/{type}', [LaporanController::class, 'export'])->name('admin.laporan.export');
});

// 3. Rute Khusus Admin Keuangan
Route::middleware(['auth', 'role:admin_keuangan'])->group(function () {
    Route::get('/keuangan/dashboard', function () {
        return view('keuangan.dashboard');
    })->name('keuangan.dashboard');
});

// Rute Profil bawaan Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';