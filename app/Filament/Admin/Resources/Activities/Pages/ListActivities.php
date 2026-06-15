<?php

namespace App\Filament\Admin\Resources\Activities\Pages;

use App\Filament\Admin\Resources\Activities\ActivityResource;
use App\Filament\Resources\Activities\Widgets\ActivityAnalytic;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

 use ExposesTableToWidgets;
    public  function getHeaderWidgets(): array
    {
        return [
            ActivityAnalytic::class,
        ];
    }
}
