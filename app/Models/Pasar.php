<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Str;

class Pasar extends Model
{
    use SoftDeletes, LogsActivity;
    protected $fillable = [
        'nama_pasar',
        'alamat',
        'tipe',
        'latitude',
        'longitude',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nama_pasar',
                'alamat',
                'tipe',
                'latitude',
                'longitude',
            ])
            ->logOnlyDirty()
            ->useLogName('pasar')
            ->dontLogEmptyChanges();
    }

    public function getTipeOptionsAttribute()
    {
        $lookup = \App\Models\Lookup::where('key', 'tipe_pasar')->first();

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

