<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Str;

class Komoditas extends Model
{
    use SoftDeletes, LogsActivity;
    protected $fillable = [
        'nama_komoditas',
        'kategori',
        'satuan',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nama_komoditas',
                'kategori',
                'satuan',
            ])
            ->logOnlyDirty()
            ->useLogName('komoditas')
            ->dontLogEmptyChanges();
    }

    public function getKategoriOptionsAttribute()
    {
        $lookup = \App\Models\Lookup::where('key', 'kategori_komoditas')->first();

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

    public function getSatuanOptionsAttribute()
    {
        $lookup = \App\Models\Lookup::where('key', 'satuan_komoditas')->first();

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

    public function hargaPangan()
    {
        return $this->hasMany(HargaPangan::class);
    }
}
