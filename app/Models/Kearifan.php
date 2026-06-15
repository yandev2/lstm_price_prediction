<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Str;

class Kearifan extends Model
{
    use SoftDeletes, LogsActivity;
    protected $fillable = [
        'nama_kearifan',
        'deskripsi',
        'jenis',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nama_kearifan',
                'deskripsi',
                'jenis',
            ])
            ->logOnlyDirty()
            ->useLogName('kearifan-lokal')
            ->dontLogEmptyChanges();
    }

    public function getJenisOptionsAttribute()
    {
        $lookup = \App\Models\Lookup::where('key', 'jenis_kearifan_lokal')->first();

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
}
