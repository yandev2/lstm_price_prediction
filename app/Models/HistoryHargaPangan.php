<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class HistoryHargaPangan extends Model
{
    use LogsActivity;
    protected $fillable = [
        'pasar_id',
        'harga_pangan_id',
        'komoditas_id',
        'harga_lama',
        'harga_baru',
        'tanggal_perubahan',
        'deskripsi',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'hargaPangan.tanggal',
                'harga_lama',
                'harga_baru',
                'tanggal_perubahan',
            ])
            ->logOnlyDirty()
            ->useLogName('history-harga-pangan')
            ->dontLogEmptyChanges();
    }

    public function komoditas()
    {
        return $this->belongsTo(Komoditas::class);
    }

    public function pasar()
    {
        return $this->belongsTo(Pasar::class);
    }

    public function hargaPangan()
    {
        return $this->belongsTo(HargaPangan::class);
    }

  
}
