<?php

namespace App\Filament\Admin\Resources\FaktorEksternals\Pages;

use App\Filament\Admin\Resources\FaktorEksternals\FaktorEksternalResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageFaktorEksternals extends ManageRecords
{
    protected static string $resource = FaktorEksternalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
