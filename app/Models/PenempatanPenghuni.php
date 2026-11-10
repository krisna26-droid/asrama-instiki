<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenempatanPenghuni extends Model
{
    protected $table = 'penempatan_penghunis';

    protected $fillable = [
        'reservasi_id', 'user_id', 'kamar_id', 'transfer_dari_id',
        'tanggal_mulai', 'tanggal_akhir', 'tanggal_akhir_asal_sebelum_transfer', 'status', 'check_in_at',
        'check_in_oleh', 'check_out_at', 'check_out_oleh', 'catatan_lifecycle',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_akhir' => 'date',
            'tanggal_akhir_asal_sebelum_transfer' => 'date',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
        ];
    }

    public function reservasi(): BelongsTo { return $this->belongsTo(Reservasi::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function kamar(): BelongsTo { return $this->belongsTo(Kamar::class); }
    public function transferDari(): BelongsTo { return $this->belongsTo(self::class, 'transfer_dari_id'); }
    public function transferKe(): HasMany { return $this->hasMany(self::class, 'transfer_dari_id'); }
    public function checkInOleh(): BelongsTo { return $this->belongsTo(User::class, 'check_in_oleh'); }
    public function checkOutOleh(): BelongsTo { return $this->belongsTo(User::class, 'check_out_oleh'); }
    public function perpanjangans(): HasMany { return $this->hasMany(PerpanjanganPenempatan::class, 'penempatan_id'); }
}
