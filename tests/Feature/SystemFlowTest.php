<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Kamar;
use App\Models\Reservasi;
use App\Models\Pembayaran;

class SystemFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $penghuni;
    protected $adminAsrama;
    protected $adminKeuangan;
    protected $kamar;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Buat User dummy untuk 3 Role
        $this->penghuni = User::factory()->create([
            'nama' => 'Mahasiswa Test',
            'nim_nik' => '2201010001',
            'role' => 'penghuni'
        ]);

        $this->adminAsrama = User::factory()->create([
            'nama' => 'Admin Asrama Test',
            'role' => 'admin_asrama'
        ]);

        $this->adminKeuangan = User::factory()->create([
            'nama' => 'Admin Keuangan Test',
            'role' => 'admin_keuangan'
        ]);

        // 2. Buat Kamar dummy
        $this->kamar = Kamar::create([
            'nomor_kamar' => '101',
            'blok' => 'A',
            'lantai' => 1,
            'kategori' => 'putra',
            'kapasitas' => 3,
            'terisi' => 0,
            'harga_bulanan' => 850000,
            'status' => 'tersedia',
            'fasilitas' => 'AC, WiFi'
        ]);
    }

    /** Test Akses & Alur Penghuni */
    public function test_penghuni_flow()
    {
        // Akses Dashboard & Kamar
        $this->actingAs($this->penghuni)->get('/dashboard')->assertStatus(200);
        $this->actingAs($this->penghuni)->get('/penghuni/kamar')->assertStatus(200);

        // Pengajuan Reservasi
        $responseReservasi = $this->actingAs($this->penghuni)->post('/penghuni/reservasi', [
            'kamar_id' => $this->kamar->id,
            'durasi_sewa' => '1 Tahun',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);
        $responseReservasi->assertRedirect();
        $this->assertDatabaseHas('reservasis', ['user_id' => $this->penghuni->id]);

        // Akses Pembayaran
        $this->actingAs($this->penghuni)->get('/penghuni/pembayaran')->assertStatus(200);
    }

    /** Test Akses & Alur Admin Asrama */
    public function test_admin_asrama_flow()
    {
        // Akses Dashboard & Kelola Kamar
        $this->actingAs($this->adminAsrama)->get('/admin/dashboard')->assertStatus(200);
        $this->actingAs($this->adminAsrama)->get('/admin/kamar')->assertStatus(200);

        // Buat Reservasi Dummy untuk Diverifikasi
        $reservasi = Reservasi::create([
            'kode_reservasi' => 'RSV-TEST-001',
            'user_id' => $this->penghuni->id,
            'kamar_id' => $this->kamar->id,
            'durasi_sewa' => '1 Tahun',
            'status' => 'pending',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        // Approve Reservasi
        $this->actingAs($this->adminAsrama)
             ->patch("/admin/reservasi/{$reservasi->id}/approve", ['kamar_id' => $this->kamar->id])
             ->assertRedirect();

        $this->assertDatabaseHas('reservasis', ['id' => $reservasi->id, 'status' => 'approved']);

        // Akses Penghuni Aktif & Laporan
        $this->actingAs($this->adminAsrama)->get('/admin/penghuni')->assertStatus(200);
        $this->actingAs($this->adminAsrama)->get('/admin/laporan')->assertStatus(200);

        // Ekspor Laporan Admin
        $this->actingAs($this->adminAsrama)->get('/admin/laporan/export/okupansi')->assertStatus(200);
        $this->actingAs($this->adminAsrama)->get('/admin/laporan/export/penghuni')->assertStatus(200);
    }

    /** Test Akses & Alur Admin Keuangan */
    public function test_admin_keuangan_flow()
    {
        // Akses Dashboard Keuangan
        $this->actingAs($this->adminKeuangan)->get('/keuangan/dashboard')->assertStatus(200);
        $this->actingAs($this->adminKeuangan)->get('/keuangan/pembayaran')->assertStatus(200);

        // Buat Pembayaran Dummy
        $reservasi = Reservasi::create([
            'kode_reservasi' => 'RSV-TEST-002',
            'user_id' => $this->penghuni->id,
            'kamar_id' => $this->kamar->id,
            'durasi_sewa' => '1 Tahun',
            'status' => 'approved',
            'tanggal_pengajuan' => now()->toDateString(),
        ]);

        $pembayaran = Pembayaran::create([
            'kode_pembayaran' => 'PAY-TEST-001',
            'reservasi_id' => $reservasi->id,
            'user_id' => $this->penghuni->id,
            'jumlah_bayar' => 850000,
            'metode_pembayaran' => 'Bank Transfer (BNI)',
            'bukti_pembayaran' => 'bukti_pembayaran/sample.jpg',
            'status' => 'pending',
        ]);

        // Verifikasi Pembayaran
        $this->actingAs($this->adminKeuangan)
             ->patch("/keuangan/pembayaran/{$pembayaran->id}/verify")
             ->assertRedirect();

        $this->assertDatabaseHas('pembayarans', ['id' => $pembayaran->id, 'status' => 'paid']);

        // Cetak Kuitansi PDF
        $this->actingAs($this->adminKeuangan)
             ->get("/keuangan/pembayaran/{$pembayaran->id}/kuitansi")
             ->assertStatus(200);

        // Akses Riwayat & Ekspor Laporan Keuangan
        $this->actingAs($this->adminKeuangan)->get('/keuangan/riwayat')->assertStatus(200);
        $this->actingAs($this->adminKeuangan)->get('/keuangan/laporan')->assertStatus(200);

        // Ekspor 6 Laporan Keuangan (Pemisah CSV Titik Koma)
        $this->actingAs($this->adminKeuangan)->get('/keuangan/laporan/export/pendapatan')->assertStatus(200);
        $this->actingAs($this->adminKeuangan)->get('/keuangan/laporan/export/log-pembayaran')->assertStatus(200);
        $this->actingAs($this->adminKeuangan)->get('/keuangan/laporan/export/tunggakan')->assertStatus(200);
        $this->actingAs($this->adminKeuangan)->get('/keuangan/laporan/export/penolakan')->assertStatus(200);
        $this->actingAs($this->adminKeuangan)->get('/keuangan/laporan/export/metode-pembayaran')->assertStatus(200);
        $this->actingAs($this->adminKeuangan)->get('/keuangan/laporan/export/ringkasan-tahunan')->assertStatus(200);
    }
}