<?php

namespace App\Jobs;

use App\Models\ExportFile;
use App\Models\User;
use Filament\Actions\Action;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class NotifyUserOfCompletedExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public string $pathFile;
    public string $model;
    public bool $isFail;


    public function __construct(User $user, string $pathFile, string $model, bool $isFail)
    {
        $this->user = $user;
        $this->pathFile = $pathFile;
        $this->model = $model;
        $this->isFail = $isFail;
    }

    public function handle()
    {

        if ($this->isFail == true) {
            Notification::make()
                ->title("Export Gagal")
                ->danger()
                ->body("Terjadi kesalahan teknis saat memproses laporan data {$this->model}.")
                ->actions([
                    Action::make('Tandai Dibaca')
                        ->color('warning')
                        ->button()
                        ->markAsRead()
                ])
                ->sendToDatabase($this->user);
        } else {

            Notification::make()
                ->title("Export Selesai")
                ->success()
                ->body("Selesai! Laporan data {$this->model} yang Anda minta sudah siap. Silakan klik tombol di bawah untuk mengunduh.")
                ->actions([
                    Action::make('download')
                        ->label('Download')
                        ->button()
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('success')
                        ->markAsRead()
                        ->url(Storage::url($this->pathFile), shouldOpenInNewTab: true),
                ])
                ->sendToDatabase($this->user);
        }
    }
}
