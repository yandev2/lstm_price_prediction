<?php

namespace App\Filament\Admin\Resources\PrediksiHargas\Pages;

use App\Filament\Admin\Resources\PrediksiHargas\PrediksiHargaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPrediksiHargas extends ListRecords
{
    protected static string $resource = PrediksiHargaResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
