<?php

namespace App\Filament\Admin\Resources\EksportHistories\Pages;

use App\Filament\Admin\Resources\EksportHistories\EksportHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageEksportHistories extends ManageRecords
{
    protected static string $resource = EksportHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
