<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_kamar',
        'cabang_asrama_id',
        'blok',
        'lantai',
        'kategori',
        'kapasitas',
        'terisi',
        'harga_bulanan',
        'status',
        'is_active',
        'fasilitas',
        'foto',
    ];

    public function penempatans(): HasMany
    {
        return $this->hasMany(PenempatanPenghuni::class);
    }

    public function cabangAsrama(): BelongsTo
    {
        return $this->belongsTo(CabangAsrama::class, 'cabang_asrama_id');
    }

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'kapasitas' => 'integer', 'terisi' => 'integer'];
    }
}
