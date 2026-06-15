<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Lookup extends Model
{
    use LogsActivity;
    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'key',
                'value',
            ])
            ->logOnlyDirty()
            ->useLogName('lookup')
            ->dontLogEmptyChanges();
    }
}
