<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateStaffAccount extends Command
{
    protected $signature = 'asrama:create-staff-account {role : Role akun (admin_asrama atau admin_keuangan)}';

    protected $description = 'Buat akun staf asrama secara interaktif tanpa menyimpan kredensial di source code';

    public function handle(): int
    {
        $role = (string) $this->argument('role');
        if (! in_array($role, ['admin_asrama', 'admin_keuangan'], true)) {
            $this->error('Role harus admin_asrama atau admin_keuangan.');

            return self::INVALID;
        }

        $attributes = [
            'nama' => trim((string) $this->ask('Nama lengkap')),
            'nim_nik' => trim((string) $this->ask('Username / ID login')),
            'email' => trim((string) $this->ask('Email')),
            'no_telepon' => trim((string) $this->ask('Nomor telepon (opsional)')) ?: null,
        ];

        $validator = Validator::make($attributes, [
            'nama' => ['required', 'string', 'max:255'],
            'nim_nik' => ['required', 'string', 'max:255', Rule::unique('users', 'nim_nik')],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $password = (string) $this->secret('Password baru (minimal 8 karakter)');
        $passwordConfirmation = (string) $this->secret('Ulangi password');

        if (mb_strlen($password) < 8 || ! hash_equals($password, $passwordConfirmation)) {
            $this->error('Password harus minimal 8 karakter dan kedua input harus sama. Tidak ada akun yang dibuat.');

            return self::FAILURE;
        }

        if (! $this->confirm("Buat akun {$role} dengan username {$attributes['nim_nik']}?", false)) {
            $this->warn('Pembuatan akun dibatalkan.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($attributes, $password, $role): void {
            $user = new User();
            $user->nama = $attributes['nama'];
            $user->nim_nik = $attributes['nim_nik'];
            $user->email = $attributes['email'];
            $user->no_telepon = $attributes['no_telepon'];
            $user->role = $role;
            $user->password = Hash::make($password);
            $user->is_active = true;
            $user->must_change_password = false;
            $user->save();
        });

        $this->info("Akun {$role} berhasil dibuat untuk username {$attributes['nim_nik']}.");
        $this->line('Password tidak ditampilkan atau disimpan dalam bentuk plaintext.');

        return self::SUCCESS;
    }
}
