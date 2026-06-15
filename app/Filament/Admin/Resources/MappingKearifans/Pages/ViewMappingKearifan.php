<?php

namespace App\Filament\Admin\Resources\MappingKearifans\Pages;

use App\Filament\Admin\Resources\MappingKearifans\MappingKearifanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMappingKearifan extends ViewRecord
{
    protected static string $resource = MappingKearifanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
