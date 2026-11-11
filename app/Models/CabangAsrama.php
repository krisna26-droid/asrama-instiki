<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CabangAsrama extends Model
{
    protected $table = 'cabang_asramas';

    protected $fillable = ['kode', 'nama', 'alamat', 'maps_url', 'telepon', 'whatsapp', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function kamars(): HasMany
    {
        return $this->hasMany(Kamar::class, 'cabang_asrama_id');
    }

    public function reservasis(): HasMany
    {
        return $this->hasMany(Reservasi::class, 'cabang_asrama_id');
    }

    public function penempatans(): HasMany
    {
        return $this->hasMany(PenempatanPenghuni::class, 'cabang_asrama_id');
    }
}
