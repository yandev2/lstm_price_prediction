<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScalerPasar extends Model
{
    protected $casts = [
        'daftar_pasar' => 'array',
    ];
    protected $fillable = [
        'nama',
        'daftar_pasar',
        'versi',
        'file_url',
    ];
}
