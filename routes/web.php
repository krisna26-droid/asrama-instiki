<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Controller Admin Asrama
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KamarController;
use App\Http\Controllers\Admin\ReservasiController;
use App\Http\Controllers\Admin\PenghuniController;
use App\Http\Controllers\Admin\LaporanController;

// Controller Admin Keuangan
use App\Http\Controllers\Keuangan\DashboardController as KeuanganDashboardController;
use App\Http\Controllers\Keuangan\PembayaranController;
use App\Http\Controllers\Keuangan\LaporanKeuanganController;

// Controller Penghuni
use App\Http\Controllers\Penghuni\DashboardController as PenghuniDashboardController;
use App\Http\Controllers\Penghuni\KamarController as PenghuniKamarController;
use App\Http\Controllers\Penghuni\ReservasiController as PenghuniReservasiController;
use App\Http\Controllers\Penghuni\PembayaranController as PenghuniPembayaranController;

Route::get('/', function () {
    return view('welcome');
});

// 1. Rute Khusus Penghuni
Route::middleware(['auth', 'role:penghuni'])->group(function () {
    Route::get('/dashboard', [PenghuniDashboardController::class, 'index'])->name('dashboard');

    // Rute Kamar Tersedia untuk Mahasiswa
    Route::get('/penghuni/kamar', [PenghuniKamarController::class, 'index'])->name('penghuni.kamar.index');

    // Reservasi Saya (Pengajuan & Riwayat)
    Route::get('/penghuni/reservasi', [PenghuniReservasiController::class, 'index'])->name('penghuni.reservasi.index');
    Route::post('/penghuni/reservasi', [PenghuniReservasiController::class, 'store'])->name('penghuni.reservasi.store');

    // Pembayaran
    Route::get('/penghuni/pembayaran', [PenghuniPembayaranController::class, 'index'])->name('penghuni.pembayaran.index');
    Route::post('/penghuni/pembayaran', [PenghuniPembayaranController::class, 'store'])->name('penghuni.pembayaran.store');
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
    Route::get('/keuangan/dashboard', [KeuanganDashboardController::class, 'index'])->name('keuangan.dashboard');

    Route::get('/keuangan/pembayaran', [PembayaranController::class, 'index'])->name('keuangan.pembayaran.index');
    Route::patch('/keuangan/pembayaran/{pembayaran}/verify', [PembayaranController::class, 'verify'])->name('keuangan.pembayaran.verify');
    Route::patch('/keuangan/pembayaran/{pembayaran}/reject', [PembayaranController::class, 'reject'])->name('keuangan.pembayaran.reject');

    Route::get('/keuangan/riwayat', [PembayaranController::class, 'riwayat'])->name('keuangan.riwayat.index');

    Route::get('/keuangan/laporan', [LaporanKeuanganController::class, 'index'])->name('keuangan.laporan.index');
    Route::get('/keuangan/laporan/export/{type}', [LaporanKeuanganController::class, 'export'])->name('keuangan.laporan.export');
});

// Rute Profil bawaan Breeze
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';