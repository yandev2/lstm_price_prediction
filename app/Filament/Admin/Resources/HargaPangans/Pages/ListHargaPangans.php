<?php

namespace App\Filament\Admin\Resources\HargaPangans\Pages;

use App\Filament\Admin\Resources\HargaPangans\HargaPanganResource;
use App\Filament\Admin\Resources\HargaPangans\Widgets\SummaryHargaPangan;
use App\Filament\Imports\HargaPanganImporter;
use Filament\Actions\CreateAction;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\ImportAction;
use Filament\Support\Icons\Heroicon;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Width;

class ListHargaPangans extends ListRecords
{
    protected static string $resource = HargaPanganResource::class;
    use ExposesTableToWidgets;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make('import_harga_pangan')
                ->color('success')
                ->label('Import Data')
                ->modalWidth(Width::Medium)
                ->modalAlignment(Alignment::Center)
                ->modalIcon('heroicon-o-arrow-down-on-square-stack')
                ->modalHeading(\Str::title("Import Data Harga"))
                ->icon(Heroicon::ArrowDownTray)
                ->options([
                    'creator' => auth()->user()->id,
                ])
                ->importer(HargaPanganImporter::class),
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
