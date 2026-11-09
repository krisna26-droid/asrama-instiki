<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'nim_nik',
        'nik',
        'jenis_kelamin',
        'email',
        'password',
        'no_telepon',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    /**
     * Relasi ke pengajuan terbuka yang belum selesai ditindaklanjuti.
     */
    public function reservasiAktif()
    {
        return $this->hasOne(Reservasi::class)
            ->whereIn('status', ['pending', 'approved', 'legacy_unverified', 'no_show'])
            ->latestOfMany();
    }

    /**
     * Cek apakah penghuni sudah memiliki kamar / reservasi aktif
     */
    public function punyaKamarAktif(): bool
    {
        return $this->reservasiAktif()->exists();
    }

    public function penempatans(): HasMany
    {
        return $this->hasMany(PenempatanPenghuni::class);
    }
}
