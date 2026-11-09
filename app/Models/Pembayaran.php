<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_pembayaran',
        'reservasi_id',
        'user_id',
        'jumlah_bayar',
        'metode_pembayaran',
        'bukti_pembayaran',
        'catatan_keuangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class);
    }

    public function diputuskanOleh()
    {
        return $this->belongsTo(User::class, 'diputuskan_oleh');
    }

    protected function casts(): array
    {
        return ['diputuskan_at' => 'datetime'];
    }
}
