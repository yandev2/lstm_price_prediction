<?php

namespace App\Filament\Admin\Resources\ModelAis\Pages;

use App\Filament\Admin\Resources\ModelAis\ModelAiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageModelAis extends ManageRecords
{
    protected static string $resource = ModelAiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
