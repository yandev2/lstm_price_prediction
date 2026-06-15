<?php

namespace App\Filament\Admin\Resources\Pasars\Pages;

use App\Filament\Admin\Resources\Pasars\PasarResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPasar extends ViewRecord
{
    protected static string $resource = PasarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
