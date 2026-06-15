<?php

namespace App\Filament\Admin\Resources\MappingKearifans\Pages;

use App\Filament\Admin\Resources\MappingKearifans\MappingKearifanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMappingKearifans extends ListRecords
{
    protected static string $resource = MappingKearifanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
