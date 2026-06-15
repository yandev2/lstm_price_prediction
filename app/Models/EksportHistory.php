<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class EksportHistory extends Model
{
    use LogsActivity;
    protected $fillable = [
        'user_id',
        'filename',
        'file_path',
        'disk',
        'mime_type',
        'file_size',
        'module',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id',
                'filename',
                'file_path',
                'disk',
                'mime_type',
                'file_size',
                'module',
            ])
            ->logOnlyDirty()
            ->useLogName('export')
            ->dontLogEmptyChanges();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getReadableSizeAttribute(): string
    {
        if (!$this->file_size) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $this->file_size > 0 ? floor(log($this->file_size, 1024)) : 0;

        return number_format($this->file_size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
}
