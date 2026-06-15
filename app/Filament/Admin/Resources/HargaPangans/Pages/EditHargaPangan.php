<?php

namespace App\Filament\Admin\Resources\HargaPangans\Pages;

use App\Filament\Admin\Resources\HargaPangans\HargaPanganResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditHargaPangan extends EditRecord
{
    protected static string $resource = HargaPanganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
