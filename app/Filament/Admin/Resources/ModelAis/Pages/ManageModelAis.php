<?php

namespace App\Filament\Admin\Resources\ModelAis\Pages;

use App\Filament\Admin\Resources\ModelAis\ModelAiResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;

class ManageModelAis extends ManageRecords
{
    protected static string $resource = ModelAiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('test_run')
                ->label('Uji Coba Model')
                ->modalHeading('Run Prediction')
                ->modalWidth('md')
                ->modalIcon(Heroicon::InformationCircle)
                ->modalSubmitActionLabel('Mulai Prediksi')
                ->modalFooterActionsAlignment('center')
                ->modalAlignment('center')
                ->modalDescription("Prediksi harga dijalankan otomatis setiap jam 01:00,gunakan ini hanya untuk pengujian")
                ->action(function () {
                    try {
                      
                        Artisan::call('ai:predict-daily');
                        
                        Notification::make()
                            ->title('Prediksi Sedang Dijalankan')
                            ->body('Seluruh proses prediksi telah dimasukkan ke dalam antrean (Queue) di latar belakang.')
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal menjalankan prediksi')
                            ->danger()
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
            CreateAction::make(),
        ];
    }
}
