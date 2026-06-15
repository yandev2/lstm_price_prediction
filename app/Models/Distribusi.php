<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Str;


class Distribusi extends Model
{
    use LogsActivity;
    protected $fillable = [
        'komoditas_id',
        'pasar_asal_id',
        'pasar_tujuan_id',
        'volume',
        'tanggal',
        'transportasi',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'komoditas.nama_komoditas',
                'pasarAsal.nama_pasar',
                'pasarTujuan.nama_pasar',
                'volume',
                'tanggal',
                'transportasi',
            ])
            ->logOnlyDirty()
            ->useLogName('distribusi')
            ->dontLogEmptyChanges();
    }

    public function getTransportasiOptionsAttribute()
    {
        $lookup = \App\Models\Lookup::where('key', 'transportasi_distribusi')->first();

        if ($lookup && $lookup->value) {
            $arr = is_array($lookup->value)
                ? $lookup->value
                : json_decode($lookup->value, true);

            return collect($arr)->mapWithKeys(function ($item) {
                return [$item => Str::title($item)];
            })->toArray();
        }

        return [];
    }

    public function komoditas()
    {
        return $this->belongsTo(Komoditas::class, 'komoditas_id');
    }
    public function pasarAsal()
    {
        return $this->belongsTo(Pasar::class, 'pasar_asal_id');
    }
    public function pasarTujuan()
    {
        return $this->belongsTo(Pasar::class, 'pasar_tujuan_id');
    }
}
