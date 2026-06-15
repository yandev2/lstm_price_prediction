<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Str;


class MappingKearifan extends Model
{
    use LogsActivity;
    protected $fillable = [
        'kearifan_id',
        'pasar_id',
        'komoditas_id',
        'skor_harga',
        'skor_distribusi',
        'skor_frekuensi',
        'kearifan_score',
        'pengaruh',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'kearifan.nama_kearifan',
                'pasar.nama_pasar',
                'komoditas.nama_komoditas',
                'pengaruh',
            ])
            ->logOnlyDirty()
            ->useLogName('mapping-kearifan')
            ->dontLogEmptyChanges();
    }

    public function getTingkatPengaruhOptionsAttribute()
    {
        $lookup = \App\Models\Lookup::where('key', 'mapping_tingkat_pengaruh')->first();

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

    public function kearifan()
    {
        return $this->belongsTo(Kearifan::class, 'kearifan_id');
    }

    public function pasar()
    {
        return $this->belongsTo(Pasar::class, 'pasar_id');
    }

    public function komoditas()
    {
        return $this->belongsTo(Komoditas::class, 'komoditas_id');
    }
}
