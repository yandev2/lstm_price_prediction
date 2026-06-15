<?php

namespace App\Filament\Admin\Resources\HargaPangans\Pages;

use App\Filament\Admin\Resources\HargaPangans\HargaPanganResource;
use App\Filament\Admin\Resources\HargaPangans\Widgets\SummaryHargaPangan;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;

class ListHargaPangans extends ListRecords
{
    protected static string $resource = HargaPanganResource::class;
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
            SummaryHargaPangan::class,
        ];
    }
}
