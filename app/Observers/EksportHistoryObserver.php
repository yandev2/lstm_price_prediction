<?php

namespace App\Observers;

use App\Models\EksportHistory;
use Illuminate\Support\Facades\Storage;

class EksportHistoryObserver
{
    /**
     * Handle the EksportHistory "created" event.
     */
    public function created(EksportHistory $eksportHistory): void
    {
        //
    }

    /**
     * Handle the EksportHistory "updated" event.
     */
    public function updated(EksportHistory $eksportHistory): void
    {
        //
    }

    /**
     * Handle the EksportHistory "deleted" event.
     */
    public function deleted(EksportHistory $eksportHistory): void
    {
        if ($eksportHistory->file_path && Storage::disk('public')->exists($eksportHistory->file_path)) {
            Storage::disk('public')->delete($eksportHistory->file_path);
        }
    }

    /**
     * Handle the EksportHistory "restored" event.
     */
    public function restored(EksportHistory $eksportHistory): void
    {
        //
    }

    /**
     * Handle the EksportHistory "force deleted" event.
     */
    public function forceDeleted(EksportHistory $eksportHistory): void
    {
        //
    }
}
