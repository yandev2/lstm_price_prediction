<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Str;

class HargaPangan extends Model
{
    use LogsActivity;

    protected $fillable = [
        'created_by',
        'komoditas_id',
        'pasar_id',
        'faktor_eksternal',
        'tanggal',
        'harga',
        'sumber_data',
    ];

    protected $casts = [
        'faktor_eksternal' => 'array'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'createdBy.name',
                'komoditas.nama_komoditas',
                'pasar.nama_pasar',
                'faktor_eksternal',
                'tanggal',
                'harga',
                'sumber_data',
            ])
            ->logOnlyDirty()
            ->useLogName('harga-pangan')
            ->dontLogEmptyChanges();
    }

    public function getJenisFaktorOptionsAttribute()
    {
        $lookup = \App\Models\Lookup::where('key', 'jenis_faktor')->first();

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

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function komoditas()
    {
        return $this->belongsTo(Komoditas::class, 'komoditas_id');
    }

    public function pasar()
    {
        return $this->belongsTo(Pasar::class, 'pasar_id');
    }

    public function faktorEksternal()
    {
        return $this->hasMany(FaktorEksternal::class, 'harga_pangan_id');
    }
}
