<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Str;

class FaktorEksternal extends Model
{
    use SoftDeletes, LogsActivity;
    protected $fillable = [
        'harga_pangan_id',
        'tanggal',
        'jenis',
        'nilai',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'tanggal',
                'jenis',
                'nilai',
            ])
            ->logOnlyDirty()
            ->useLogName('faktor-eksternal')
            ->dontLogEmptyChanges();
    }

    public function getJenisOptionsAttribute()
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

    public function hargaPangan()
    {
        return $this->belongsTo(HargaPangan::class, foreignKey: 'harga_pangan_id');
    }
}
