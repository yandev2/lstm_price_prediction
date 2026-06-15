<?php

namespace App\Filament\Admin\Resources\MappingKearifans\Pages;

use App\Filament\Admin\Resources\MappingKearifans\MappingKearifanResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMappingKearifan extends CreateRecord
{
    protected static string $resource = MappingKearifanResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
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
