<?php

namespace App\Observers;

use App\Models\ModelAi;
use Illuminate\Support\Facades\Storage;

class ModelAiObserver
{
    /**
     * Handle the ModelAi "created" event.
     */
    public function created(ModelAi $modelAi): void
    {
        //
    }

    /**
     * Handle the ModelAi "updated" event.
     */
    public function updated(ModelAi $modelAi): void
    {
        // 1. Cek apakah admin mengganti file model_file
        if ($modelAi->wasChanged('model_file')) {
            $oldModelFile = $modelAi->getOriginal('model_file');

            // Jika ada file lama dan file tersebut ada di storage, hapus!
            if ($oldModelFile && Storage::disk('local')->exists($oldModelFile)) {
                Storage::disk('local')->delete($oldModelFile);
            }
        }

        // 2. Cek apakah admin mengganti file scaler_file
        if ($modelAi->wasChanged('scaler_file')) {
            $oldScalerFile = $modelAi->getOriginal('scaler_file');

            // Jika ada file lama dan file tersebut ada di storage, hapus!
            if ($oldScalerFile && Storage::disk('local')->exists($oldScalerFile)) {
                Storage::disk('local')->delete($oldScalerFile);
            }
        }
    }

    /**
     * Handle the ModelAi "deleted" event.
     */
    public function deleted(ModelAi $modelAi): void
    {
        if ($modelAi->model_file && Storage::disk('local')->exists($modelAi->model_file)) {
            Storage::disk('local')->delete($modelAi->model_file);
        }

        if ($modelAi->scaler_file && Storage::disk('local')->exists($modelAi->scaler_file)) {
            Storage::disk('local')->delete($modelAi->scaler_file);
        }
    }

    /**
     * Handle the ModelAi "restored" event.
     */
    public function restored(ModelAi $modelAi): void
    {
        //
    }

    /**
     * Handle the ModelAi "force deleted" event.
     */
    public function forceDeleted(ModelAi $modelAi): void
    {
        //
    }
}
