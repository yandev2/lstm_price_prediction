<?php

namespace App\Filament\Admin\Resources\Komoditas\Pages;

use App\Filament\Admin\Resources\Komoditas\KomoditasResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditKomoditas extends EditRecord
{
    protected static string $resource = KomoditasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
