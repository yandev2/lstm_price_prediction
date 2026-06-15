<?php

namespace App\Filament\Admin\Resources\Kearifans\Pages;

use App\Filament\Admin\Resources\Kearifans\KearifanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageKearifans extends ManageRecords
{
    protected static string $resource = KearifanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
