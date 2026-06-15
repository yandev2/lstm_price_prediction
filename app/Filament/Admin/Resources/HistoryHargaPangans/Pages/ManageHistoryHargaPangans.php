<?php

namespace App\Filament\Admin\Resources\HistoryHargaPangans\Pages;

use App\Filament\Admin\Resources\HistoryHargaPangans\HistoryHargaPanganResource;
use App\Filament\Admin\Widgets\TimeLineInfo;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Override;

class ManageHistoryHargaPangans extends ManageRecords
{
    protected static string $resource = HistoryHargaPanganResource::class;

    #[Override]
    public function getHeaderWidgets(): array
    {
        return [
            TimeLineInfo::class
        ];
    }
}
