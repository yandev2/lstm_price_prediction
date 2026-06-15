<?php

namespace App\Filament\Admin\Resources\Distribusis\Pages;

use App\Filament\Admin\Resources\Distribusis\DistribusiResource;
use App\Filament\Admin\Resources\Distribusis\Widgets\VolumeDistribusi;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ManageRecords;

class ManageDistribusis extends ManageRecords
{
    protected static string $resource = DistribusiResource::class;
    use ExposesTableToWidgets;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            VolumeDistribusi::class,
        ];
    }
}
