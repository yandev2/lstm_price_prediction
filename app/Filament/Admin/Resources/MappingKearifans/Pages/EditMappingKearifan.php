<?php

namespace App\Filament\Admin\Resources\MappingKearifans\Pages;

use App\Filament\Admin\Resources\MappingKearifans\MappingKearifanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditMappingKearifan extends EditRecord
{
    protected static string $resource = MappingKearifanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $score = (
            (0.40 * $data['skor_harga']) +
            (0.35 * $data['skor_distribusi']) +
            (0.25 * $data['skor_frekuensi'])
        ) / 5;

        $finalScore = round($score, 2);
        $data['kearifan_score'] = $finalScore;
        
        $data['pengaruh'] = match (true) {
            $finalScore <= 0.33 => 'Rendah',
            $finalScore <= 0.66 => 'Sedang',
            default => 'Tinggi',
        };

        return $data;
    }
}
