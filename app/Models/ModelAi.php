<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelAi extends Model
{
    protected $fillable = [
        'komoditas_id',
        'versi',
        'tanggal_training',
        'scaler_file',
        'model_file',
        'mape'
    ];

    public function komoditas()
    {
        return $this->belongsTo(Komoditas::class, 'komoditas_id');
    }
}
