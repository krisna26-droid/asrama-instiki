<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Controller Admin Asrama
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KamarController;
use App\Http\Controllers\Admin\ReservasiController;
use App\Http\Controllers\Admin\PenghuniController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\AkunPenghuniController;
use App\Http\Controllers\Admin\KontenAsramaController;
use App\Http\Controllers\Admin\CabangAsramaController;
use App\Http\Controllers\Admin\PerpanjanganController as AdminPerpanjanganController;

// Controller Admin Keuangan
use App\Http\Controllers\Keuangan\DashboardController as KeuanganDashboardController;
use App\Http\Controllers\Keuangan\PembayaranController;
use App\Http\Controllers\Keuangan\LaporanKeuanganController;

// Controller Penghuni
use App\Http\Controllers\Penghuni\DashboardController as PenghuniDashboardController;
use App\Http\Controllers\Penghuni\KamarController as PenghuniKamarController;
use App\Http\Controllers\Penghuni\ReservasiController as PenghuniReservasiController;
use App\Http\Controllers\Penghuni\PembayaranController as PenghuniPembayaranController;
use App\Http\Controllers\Penghuni\PerpanjanganController as PenghuniPerpanjanganController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PublicAsramaController;

Route::get('/', [PublicAsramaController::class, 'index'])->name('home');
Route::get('/asrama/detail', [PublicAsramaController::class, 'showDetails'])->name('asrama.detail');

Route::middleware('auth')->get('/dokumen/ktp/{reservasi}', [DocumentController::class, 'ktp'])->name('dokumen.ktp');
Route::middleware('auth')->get('/dokumen/pembayaran/{pembayaran}', [DocumentController::class, 'paymentProof'])->name('dokumen.pembayaran');

// 1. Rute Khusus Penghuni
Route::middleware(['auth', 'role:penghuni'])->group(function () {
    Route::get('/dashboard', [PenghuniDashboardController::class, 'index'])->name('dashboard');

    // Rute Kamar Tersedia untuk Mahasiswa
    Route::get('/penghuni/kamar', [PenghuniKamarController::class, 'index'])->name('penghuni.kamar.index');

    // Reservasi Saya (Pengajuan & Riwayat)
    Route::get('/penghuni/reservasi', [PenghuniReservasiController::class, 'index'])->name('penghuni.reservasi.index');
    Route::post('/penghuni/reservasi', [PenghuniReservasiController::class, 'store'])->name('penghuni.reservasi.store');

    // Pembayaran & Cetak Kuitansi
    Route::get('/penghuni/pembayaran', [PenghuniPembayaranController::class, 'index'])->name('penghuni.pembayaran.index');
    Route::post('/penghuni/pembayaran', [PenghuniPembayaranController::class, 'store'])->name('penghuni.pembayaran.store');
    Route::post('/penghuni/perpanjangan/{placement}', [PenghuniPerpanjanganController::class, 'store'])->name('penghuni.perpanjangan.store');
    Route::post('/penghuni/perpanjangan/{extension}/pembayaran', [PenghuniPembayaranController::class, 'storeExtension'])->name('penghuni.perpanjangan.payment.store');
    Route::get('/penghuni/pembayaran/{id}/kuitansi', [PenghuniPembayaranController::class, 'cetakKuitansi'])->name('penghuni.pembayaran.kuitansi');
    Route::get('/password/first-login', [\App\Http\Controllers\Auth\FirstLoginPasswordController::class, 'edit'])->withoutMiddleware('password.changed')->name('password.first-login');
    Route::put('/password/first-login', [\App\Http\Controllers\Auth\FirstLoginPasswordController::class, 'update'])->withoutMiddleware('password.changed')->name('password.first-login.update');
});

// 2. Rute Khusus Admin Asrama
Route::middleware(['auth', 'role:admin_asrama'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/konten-asrama', [KontenAsramaController::class, 'index'])->name('admin.konten-asrama.index');
    Route::put('/admin/konten-asrama/kontak', [KontenAsramaController::class, 'updateContact'])->name('admin.konten-asrama.contact.update');
    Route::post('/admin/konten-asrama/fasilitas', [KontenAsramaController::class, 'storeFacility'])->name('admin.konten-asrama.fasilitas.store');
    Route::put('/admin/konten-asrama/fasilitas/{facility}', [KontenAsramaController::class, 'updateFacility'])->name('admin.konten-asrama.fasilitas.update');
    Route::patch('/admin/konten-asrama/fasilitas/{facility}/toggle', [KontenAsramaController::class, 'toggleFacility'])->name('admin.konten-asrama.fasilitas.toggle');
    Route::get('/admin/akun-penghuni', [AkunPenghuniController::class, 'index'])->name('admin.akun-penghuni.index');
    Route::post('/admin/akun-penghuni', [AkunPenghuniController::class, 'store'])->name('admin.akun-penghuni.store');
    Route::put('/admin/akun-penghuni/{user}', [AkunPenghuniController::class, 'update'])->name('admin.akun-penghuni.update');
    Route::patch('/admin/akun-penghuni/{user}/toggle', [AkunPenghuniController::class, 'toggle'])->name('admin.akun-penghuni.toggle');
    Route::patch('/admin/akun-penghuni/{user}/reset-password', [AkunPenghuniController::class, 'resetPassword'])->name('admin.akun-penghuni.reset-password');
    Route::resource('/admin/kamar', KamarController::class)
        ->except(['create', 'show', 'edit'])
        ->names('admin.kamar');
    Route::patch('/admin/kamar/{kamar}/toggle-active', [KamarController::class, 'toggleActive'])->name('admin.kamar.toggle-active');
    Route::get('/admin/cabang-asrama', [CabangAsramaController::class, 'index'])->name('admin.cabang.index');
    Route::post('/admin/cabang-asrama', [CabangAsramaController::class, 'store'])->name('admin.cabang.store');
    Route::put('/admin/cabang-asrama/{cabang}', [CabangAsramaController::class, 'update'])->name('admin.cabang.update');
    Route::patch('/admin/cabang-asrama/{cabang}/toggle', [CabangAsramaController::class, 'toggle'])->name('admin.cabang.toggle');

    Route::get('/admin/reservasi', [ReservasiController::class, 'index'])->name('admin.reservasi.index');
    Route::get('/admin/perpanjangan', [AdminPerpanjanganController::class, 'index'])->name('admin.perpanjangan.index');
    Route::patch('/admin/perpanjangan/{extension}/approve', [AdminPerpanjanganController::class, 'approve'])->name('admin.perpanjangan.approve');
    Route::patch('/admin/perpanjangan/{extension}/reject', [AdminPerpanjanganController::class, 'reject'])->name('admin.perpanjangan.reject');
    Route::patch('/admin/perpanjangan/{extension}/cancel', [AdminPerpanjanganController::class, 'cancel'])->name('admin.perpanjangan.cancel');
    Route::patch('/admin/reservasi/{reservasi}/approve', [ReservasiController::class, 'approve'])->name('admin.reservasi.approve');
    Route::patch('/admin/reservasi/{reservasi}/verify-legacy', [ReservasiController::class, 'verifyLegacy'])->name('admin.reservasi.verify-legacy');
    Route::patch('/admin/reservasi/{reservasi}/reject', [ReservasiController::class, 'reject'])->name('admin.reservasi.reject');

    Route::get('/admin/penghuni', [PenghuniController::class, 'index'])->name('admin.penghuni.index');
    Route::get('/admin/penghuni/{placement}', [PenghuniController::class, 'show'])->name('admin.penghuni.show');
    Route::patch('/admin/penempatan/{placement}/jadwal-check-in', [PenghuniController::class, 'rescheduleCheckIn'])->name('admin.penempatan.check-in.schedule.update');
    Route::post('/admin/penempatan/{placement}/check-in', [PenghuniController::class, 'checkIn'])->name('admin.penempatan.check-in');
    Route::post('/admin/penempatan/{placement}/check-out', [PenghuniController::class, 'checkOut'])->name('admin.penempatan.check-out');
    Route::post('/admin/penempatan/{placement}/transfer', [PenghuniController::class, 'transfer'])->name('admin.penempatan.transfer');

    Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('admin.laporan.index');
    Route::get('/admin/laporan/export/{type}', [LaporanController::class, 'export'])->name('admin.laporan.export');
});

// 3. Rute Khusus Admin Keuangan
Route::middleware(['auth', 'role:admin_keuangan'])->group(function () {
    Route::get('/keuangan/dashboard', [KeuanganDashboardController::class, 'index'])->name('keuangan.dashboard');

    Route::get('/keuangan/pembayaran', [PembayaranController::class, 'index'])->name('keuangan.pembayaran.index');
    Route::patch('/keuangan/pembayaran/{pembayaran}/verify', [PembayaranController::class, 'verify'])->name('keuangan.pembayaran.verify');
    Route::patch('/keuangan/pembayaran/{pembayaran}/reject', [PembayaranController::class, 'reject'])->name('keuangan.pembayaran.reject');

    // Rute cetak kuitansi untuk Admin Keuangan
    Route::get('/keuangan/pembayaran/{id}/kuitansi', [PembayaranController::class, 'cetakKuitansi'])->name('keuangan.pembayaran.kuitansi');

    Route::get('/keuangan/riwayat', [PembayaranController::class, 'riwayat'])->name('keuangan.riwayat.index');

    Route::get('/keuangan/laporan', [LaporanKeuanganController::class, 'index'])->name('keuangan.laporan.index');
    Route::get('/keuangan/laporan/export/{type}', [LaporanKeuanganController::class, 'export'])->name('keuangan.laporan.export');
});

// Profil identity tidak self-edit; hanya password dapat diperbarui oleh pemilik akun.
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});

require __DIR__.'/auth.php';
