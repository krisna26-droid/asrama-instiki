<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        //seeder dummy
        $this->call([
            AsramaSeeder::class,    
        ]);
        // Akun Penghuni / Mahasiswa
        User::create([
            'nama' => 'Mahasiswa Penghuni',
            'nim_nik' => '220010001',
            'no_telepon' => '081234567890',
            'role' => 'penghuni',
            'email' => 'penghuni@instiki.ac.id',
            'password' => Hash::make('password'),
        ]);

        // Akun Admin Asrama
        User::create([
            'nama' => 'Admin Asrama',
            'nim_nik' => '199001001',
            'no_telepon' => '081234567891',
            'role' => 'admin_asrama',
            'email' => 'admin@instiki.ac.id',
            'password' => Hash::make('password'),
        ]);

        // Akun Admin Keuangan
        User::create([
            'nama' => 'Admin Keuangan',
            'nim_nik' => '199001002',
            'no_telepon' => '081234567892',
            'role' => 'admin_keuangan',
            'email' => 'keuangan@instiki.ac.id',
            'password' => Hash::make('password'),
        ]);
    }
}