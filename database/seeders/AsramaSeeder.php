<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kamar;
use App\Models\Reservasi;
use App\Models\Pembayaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AsramaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ==========================================
        // 1. SEED DATA AKUN ADMIN
        // ==========================================
        
        User::create([
            'nama'        => 'Admin Asrama INSTIKI',
            'nim_nik'     => 'ADM-ASRAMA-01',
            'email'       => 'admin.asrama@instiki.ac.id',
            'password'    => Hash::make('password'),
            'no_telepon'  => '081234567890',
            'role'        => 'admin_asrama',
        ]);

        User::create([
            'nama'        => 'Admin Keuangan INSTIKI',
            'nim_nik'     => 'ADM-KEU-01',
            'email'       => 'admin.keuangan@instiki.ac.id',
            'password'    => Hash::make('password'),
            'no_telepon'  => '081234567891',
            'role'        => 'admin_keuangan',
        ]);

        // ==========================================
        // 2. SEED DATA KAMAR (GAMBAR INTERIOR NYATA)
        // ==========================================

        $kamar101 = Kamar::create([
            'nomor_kamar'   => '101',
            'blok'          => 'A',
            'lantai'        => 1,
            'kategori'      => 'putra',
            'kapasitas'     => 2,
            'terisi'        => 2,
            'harga_bulanan' => 850000,
            'status'        => 'tersewa_penuh',
            'fasilitas'     => 'AC, Wi-Fi, Kasur, Lemari, Meja Belajar',
            'foto'          => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?auto=format&fit=crop&w=800&q=80',
        ]);

        $kamar102 = Kamar::create([
            'nomor_kamar'   => '102',
            'blok'          => 'A',
            'lantai'        => 1,
            'kategori'      => 'putra',
            'kapasitas'     => 4,
            'terisi'        => 3,
            'harga_bulanan' => 850000,
            'status'        => 'tersedia',
            'fasilitas'     => 'AC, Wi-Fi, Kamar Mandi Dalam, Meja Belajar',
            'foto'          => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?auto=format&fit=crop&w=800&q=80',
        ]);

        $kamar201 = Kamar::create([
            'nomor_kamar'   => '201',
            'blok'          => 'A',
            'lantai'        => 2,
            'kategori'      => 'putra',
            'kapasitas'     => 2,
            'terisi'        => 1,
            'harga_bulanan' => 1200000,
            'status'        => 'tersedia',
            'fasilitas'     => 'AC, Wi-Fi, Balkon, Kamar Mandi Dalam',
            'foto'          => 'https://images.unsplash.com/photo-1616594039964-ae9021a400a0?auto=format&fit=crop&w=800&q=80',
        ]);

        $kamar202 = Kamar::create([
            'nomor_kamar'   => '202',
            'blok'          => 'A',
            'lantai'        => 2,
            'kategori'      => 'putra',
            'kapasitas'     => 2,
            'terisi'        => 0,
            'harga_bulanan' => 1200000,
            'status'        => 'tersedia',
            'fasilitas'     => 'AC, Wi-Fi, Meja Belajar, Lemari',
            'foto'          => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=800&q=80',
        ]);

        $kamar105 = Kamar::create([
            'nomor_kamar'   => '105',
            'blok'          => 'B',
            'lantai'        => 1,
            'kategori'      => 'putri',
            'kapasitas'     => 4,
            'terisi'        => 2,
            'harga_bulanan' => 950000,
            'status'        => 'tersedia',
            'fasilitas'     => 'AC, Wi-Fi, Meja Belajar, Dapur Bersama',
            'foto'          => 'https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?auto=format&fit=crop&w=800&q=80',
        ]);

        $kamar106 = Kamar::create([
            'nomor_kamar'   => '106',
            'blok'          => 'B',
            'lantai'        => 1,
            'kategori'      => 'putri',
            'kapasitas'     => 2,
            'terisi'        => 2,
            'harga_bulanan' => 950000,
            'status'        => 'tersewa_penuh',
            'fasilitas'     => 'AC, Wi-Fi, Kamar Mandi Dalam',
            'foto'          => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=800&q=80',
        ]);

        $kamar301 = Kamar::create([
            'nomor_kamar'   => '301',
            'blok'          => 'B',
            'lantai'        => 3,
            'kategori'      => 'putri',
            'kapasitas'     => 2,
            'terisi'        => 0,
            'harga_bulanan' => 1500000,
            'status'        => 'perbaikan',
            'fasilitas'     => 'AC, Wi-Fi, Kamar Mandi Dalam, Water Heater',
            'foto'          => 'https://images.unsplash.com/photo-1598928506311-c55ded91a20c?auto=format&fit=crop&w=800&q=80',
        ]);

        $kamar302 = Kamar::create([
            'nomor_kamar'   => '302',
            'blok'          => 'B',
            'lantai'        => 3,
            'kategori'      => 'putri',
            'kapasitas'     => 2,
            'terisi'        => 1,
            'harga_bulanan' => 1500000,
            'status'        => 'tersedia',
            'fasilitas'     => 'AC, Wi-Fi, Balkon, Water Heater',
            'foto'          => 'https://images.unsplash.com/photo-1513694203232-719a280e022f?auto=format&fit=crop&w=800&q=80',
        ]);

        // ==========================================
        // 3. SEED AKUN PENGHUNI & TRANSAKSI
        // ==========================================

        // --- A. KELOMPOK PENGHUNI LUNAS ---
        $penghunisLunas = [
            ['nama' => 'Andi Pratama', 'nim' => '2201010001', 'email' => 'andi@asrama.ac.id', 'kamar' => $kamar101, 'kode_rsv' => 'RSV-2026-A001', 'kode_pay' => 'PAY-2026-0001'],
            ['nama' => 'Bagus Setiawan', 'nim' => '2201010002', 'email' => 'bagus@asrama.ac.id', 'kamar' => $kamar101, 'kode_rsv' => 'RSV-2026-A002', 'kode_pay' => 'PAY-2026-0002'],
            ['nama' => 'Dewi Anjani', 'nim' => '2201010003', 'email' => 'dewi@asrama.ac.id', 'kamar' => $kamar106, 'kode_rsv' => 'RSV-2026-B001', 'kode_pay' => 'PAY-2026-0003'],
            ['nama' => 'Eka Putri', 'nim' => '2201010004', 'email' => 'ekaputri@asrama.ac.id', 'kamar' => $kamar106, 'kode_rsv' => 'RSV-2026-B002', 'kode_pay' => 'PAY-2026-0004'],
            ['nama' => 'Faisal Reza', 'nim' => '2201010005', 'email' => 'faisal@asrama.ac.id', 'kamar' => $kamar102, 'kode_rsv' => 'RSV-2026-A003', 'kode_pay' => 'PAY-2026-0005'],
        ];

        foreach ($penghunisLunas as $p) {
            $user = User::create([
                'nama'       => $p['nama'],
                'nim_nik'    => $p['nim'],
                'email'      => $p['email'],
                'password'   => Hash::make('password'),
                'no_telepon' => '0821' . rand(10000000, 99999999),
                'role'       => 'penghuni',
            ]);

            $rsv = Reservasi::create([
                'kode_reservasi'    => $p['kode_rsv'],
                'user_id'           => $user->id,
                'kamar_id'          => $p['kamar']->id,
                'tanggal_pengajuan' => now()->subMonths(1),
                'durasi_sewa'       => '1 Semester',
                'status'            => 'approved',
                'catatan_admin'     => 'Persyaratan pendaftaran disetujui.',
            ]);

            Pembayaran::create([
                'kode_pembayaran'   => $p['kode_pay'],
                'reservasi_id'      => $rsv->id,
                'user_id'           => $user->id,
                'jumlah_bayar'      => $p['kamar']->harga_bulanan,
                'metode_pembayaran' => 'Bank Transfer (BNI)',
                'bukti_pembayaran'  => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80',
                'status'            => 'paid',
                'catatan_keuangan'  => 'Pembayaran terkonfirmasi lunas.',
            ]);
        }

        // --- B. KELOMPOK PENGHUNI MENUNGGU VERIFIKASI PEMBAYARAN ---
        $penghunisPendingPay = [
            ['nama' => 'Gita Gutawa', 'nim' => '2201010006', 'email' => 'gita@asrama.ac.id', 'kamar' => $kamar105, 'kode_rsv' => 'RSV-2026-B003', 'kode_pay' => 'PAY-2026-0006'],
            ['nama' => 'Hadi Sucipto', 'nim' => '2201010007', 'email' => 'hadi@asrama.ac.id', 'kamar' => $kamar102, 'kode_rsv' => 'RSV-2026-A004', 'kode_pay' => 'PAY-2026-0007'],
        ];

        foreach ($penghunisPendingPay as $p) {
            $user = User::create([
                'nama'       => $p['nama'],
                'nim_nik'    => $p['nim'],
                'email'      => $p['email'],
                'password'   => Hash::make('password'),
                'no_telepon' => '0821' . rand(10000000, 99999999),
                'role'       => 'penghuni',
            ]);

            $rsv = Reservasi::create([
                'kode_reservasi'    => $p['kode_rsv'],
                'user_id'           => $user->id,
                'kamar_id'          => $p['kamar']->id,
                'tanggal_pengajuan' => now()->subDays(2),
                'durasi_sewa'       => '1 Semester',
                'status'            => 'approved',
                'catatan_admin'     => 'Silakan lakukan pembayaran.',
            ]);

            Pembayaran::create([
                'kode_pembayaran'   => $p['kode_pay'],
                'reservasi_id'      => $rsv->id,
                'user_id'           => $user->id,
                'jumlah_bayar'      => $p['kamar']->harga_bulanan,
                'metode_pembayaran' => 'Bank Transfer (BNI)',
                'bukti_pembayaran'  => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80',
                'status'            => 'pending',
                'catatan_keuangan'  => null,
            ]);
        }

        // --- C. KELOMPOK PENGHUNI PEMBAYARAN DITOLAK ---
        $penghunisRejectedPay = [
            ['nama' => 'Indah Permata', 'nim' => '2201010008', 'email' => 'indah@asrama.ac.id', 'kamar' => $kamar105, 'kode_rsv' => 'RSV-2026-B004', 'kode_pay' => 'PAY-2026-0008', 'alasan' => 'Nominal pembayaran tidak sesuai tarif sewa.'],
            ['nama' => 'Joko Susilo', 'nim' => '2201010009', 'email' => 'joko@asrama.ac.id', 'kamar' => $kamar102, 'kode_rsv' => 'RSV-2026-A005', 'kode_pay' => 'PAY-2026-0009', 'alasan' => 'Bukti transfer tidak terbaca. Silakan unggah kembali.'],
        ];

        foreach ($penghunisRejectedPay as $p) {
            $user = User::create([
                'nama'       => $p['nama'],
                'nim_nik'    => $p['nim'],
                'email'      => $p['email'],
                'password'   => Hash::make('password'),
                'no_telepon' => '0821' . rand(10000000, 99999999),
                'role'       => 'penghuni',
            ]);

            $rsv = Reservasi::create([
                'kode_reservasi'    => $p['kode_rsv'],
                'user_id'           => $user->id,
                'kamar_id'          => $p['kamar']->id,
                'tanggal_pengajuan' => now()->subDays(4),
                'durasi_sewa'       => '1 Semester',
                'status'            => 'approved',
                'catatan_admin'     => 'Disetujui.',
            ]);

            Pembayaran::create([
                'kode_pembayaran'   => $p['kode_pay'],
                'reservasi_id'      => $rsv->id,
                'user_id'           => $user->id,
                'jumlah_bayar'      => $p['kamar']->harga_bulanan,
                'metode_pembayaran' => 'Bank Transfer (BNI)',
                'bukti_pembayaran'  => 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80',
                'status'            => 'rejected',
                'catatan_keuangan'  => $p['alasan'],
            ]);
        }

        // --- D. KELOMPOK PENGHUNI MENUNGGU RESERVASI ---
        $penghunisPendingRsv = [
            ['nama' => 'Kharisma Putri', 'nim' => '2201010010', 'email' => 'kharisma@asrama.ac.id', 'kamar' => $kamar302, 'kode_rsv' => 'RSV-2026-B005'],
            ['nama' => 'Lukman Hakim', 'nim' => '2201010011', 'email' => 'lukman@asrama.ac.id', 'kamar' => $kamar201, 'kode_rsv' => 'RSV-2026-A006'],
        ];

        foreach ($penghunisPendingRsv as $p) {
            $user = User::create([
                'nama'       => $p['nama'],
                'nim_nik'    => $p['nim'],
                'email'      => $p['email'],
                'password'   => Hash::make('password'),
                'no_telepon' => '0821' . rand(10000000, 99999999),
                'role'       => 'penghuni',
            ]);

            Reservasi::create([
                'kode_reservasi'    => $p['kode_rsv'],
                'user_id'           => $user->id,
                'kamar_id'          => $p['kamar']->id,
                'tanggal_pengajuan' => now()->subHours(12),
                'durasi_sewa'       => '1 Semester',
                'status'            => 'pending',
                'catatan_admin'     => null,
            ]);
        }

        // --- E. KELOMPOK PENGHUNI RESERVASI DITOLAK ---
        $userRejectedRsv = User::create([
            'nama'       => 'Miftah Hidayat',
            'nim_nik'    => '2201010012',
            'email'      => 'miftah@asrama.ac.id',
            'password'   => Hash::make('password'),
            'no_telepon' => '082177778888',
            'role'       => 'penghuni',
        ]);

        Reservasi::create([
            'kode_reservasi'    => 'RSV-2026-A007',
            'user_id'           => $userRejectedRsv->id,
            'kamar_id'          => $kamar202->id,
            'tanggal_pengajuan' => now()->subDays(7),
            'durasi_sewa'       => '1 Semester',
            'status'            => 'rejected',
            'catatan_admin'     => 'KTM yang diunggah tidak valid atau kedaluwarsa.',
        ]);

        // --- F. KELOMPOK MAHASISWA BARU ---
        $mahasiswaBaru = [
            ['nama' => 'Nadia Salsabila', 'nim' => '2201010013', 'email' => 'nadia@asrama.ac.id'],
            ['nama' => 'Oki Setiawan', 'nim' => '2201010014', 'email' => 'oki@asrama.ac.id'],
            ['nama' => 'Pratama Yuda', 'nim' => '2201010015', 'email' => 'yuda@asrama.ac.id'],
        ];

        foreach ($mahasiswaBaru as $m) {
            User::create([
                'nama'       => $m['nama'],
                'nim_nik'    => $m['nim'],
                'email'      => $m['email'],
                'password'   => Hash::make('password'),
                'no_telepon' => '0821' . rand(10000000, 99999999),
                'role'       => 'penghuni',
            ]);
        }
    }
}