<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kamar extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_kamar',
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

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'kapasitas' => 'integer', 'terisi' => 'integer'];
    }
}
