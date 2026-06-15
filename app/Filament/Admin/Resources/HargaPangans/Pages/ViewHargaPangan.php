<?php

namespace App\Filament\Admin\Resources\HargaPangans\Pages;

use App\Filament\Admin\Resources\HargaPangans\HargaPanganResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewHargaPangan extends ViewRecord
{
    protected static string $resource = HargaPanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
