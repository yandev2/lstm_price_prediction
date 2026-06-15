<?php

namespace App\Filament\Admin\Resources\PrediksiHargas\Pages;

use App\Filament\Admin\Resources\PrediksiHargas\PrediksiHargaResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPrediksiHarga extends EditRecord
{
    protected static string $resource = PrediksiHargaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
