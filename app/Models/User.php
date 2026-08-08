<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'nim_nik',
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
        ];
    }

    /**
     * Relasi ke reservasi aktif (pending atau approved)
     */
    public function reservasiAktif()
    {
        return $this->hasOne(Reservasi::class)
            ->whereIn('status', ['pending', 'approved'])
            ->latestOfMany();
    }

    /**
     * Cek apakah penghuni sudah memiliki kamar / reservasi aktif
     */
    public function punyaKamarAktif(): bool
    {
        return $this->reservasiAktif()->exists();
    }
}