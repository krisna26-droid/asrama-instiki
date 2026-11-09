<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reservasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_reservasi',
        'user_id',
        'kamar_id',
        'tanggal_pengajuan',
        'durasi_sewa',
        'durasi_bulan',
        'status',
        'catatan_admin',
        'foto_ktp_path',
        'kamar_legacy_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kamar()
    {
        return $this->belongsTo(Kamar::class);
    }

    public function kamarLegacy()
    {
        return $this->belongsTo(Kamar::class, 'kamar_legacy_id');
    }

    public function penempatans(): HasMany
    {
        return $this->hasMany(PenempatanPenghuni::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    protected function casts(): array
    {
        return [
            'durasi_bulan' => 'integer',
            'tarif_bulanan_disetujui' => 'integer',
            'jumlah_tagihan' => 'integer',
            'tanggal_mulai_disetujui' => 'date',
            'tanggal_akhir_disetujui' => 'date',
        ];
    }
}
