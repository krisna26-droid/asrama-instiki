<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerpanjanganPenempatan extends Model
{
    protected $table = 'perpanjangan_penempatans';

    protected $fillable = [
        'penempatan_id', 'user_id', 'tanggal_akhir_sebelum', 'tanggal_akhir_diajukan',
        'durasi_bulan', 'tarif_bulanan', 'jumlah_tagihan', 'status', 'catatan_penghuni',
        'catatan_admin', 'diajukan_at', 'diputuskan_oleh', 'diputuskan_at', 'dibayar_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_akhir_sebelum' => 'date',
            'tanggal_akhir_diajukan' => 'date',
            'durasi_bulan' => 'integer',
            'tarif_bulanan' => 'integer',
            'jumlah_tagihan' => 'integer',
            'diajukan_at' => 'datetime',
            'diputuskan_at' => 'datetime',
            'dibayar_at' => 'datetime',
        ];
    }

    public function penempatan(): BelongsTo { return $this->belongsTo(PenempatanPenghuni::class, 'penempatan_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function diputuskanOleh(): BelongsTo { return $this->belongsTo(User::class, 'diputuskan_oleh'); }
    public function pembayarans(): HasMany { return $this->hasMany(Pembayaran::class, 'perpanjangan_id'); }
}
