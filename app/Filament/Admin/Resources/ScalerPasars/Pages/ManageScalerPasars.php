<?php

namespace App\Filament\Admin\Resources\ScalerPasars\Pages;

use App\Filament\Admin\Resources\ScalerPasars\ScalerPasarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageScalerPasars extends ManageRecords
{
    protected static string $resource = ScalerPasarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
