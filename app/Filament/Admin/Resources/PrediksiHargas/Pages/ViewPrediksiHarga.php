<?php

namespace App\Filament\Admin\Resources\PrediksiHargas\Pages;

use App\Filament\Admin\Resources\PrediksiHargas\PrediksiHargaResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPrediksiHarga extends ViewRecord
{
    protected static string $resource = PrediksiHargaResource::class;

    protected function getHeaderActions(): array
    {
        return [
          
        ];
    }
}
