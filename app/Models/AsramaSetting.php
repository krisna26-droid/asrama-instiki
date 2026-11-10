<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsramaSetting extends Model
{
    protected $table = 'asrama_settings';

    protected $fillable = [
        'whatsapp',
        'telepon',
        'email',
        'alamat',
    ];
}
