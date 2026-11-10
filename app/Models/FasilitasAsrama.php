<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FasilitasAsrama extends Model
{
    protected $table = 'fasilitas_asramas';

    protected $fillable = [
        'judul',
        'slug',
        'caption',
        'deskripsi',
        'gambar_path',
        'urutan',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
            'aktif' => 'boolean',
        ];
    }
}
