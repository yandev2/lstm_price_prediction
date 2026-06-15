<?php

namespace App\Filament\Admin\Resources\MappingKearifans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MappingKearifanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('kearifan_id')
                    ->numeric(),
                TextEntry::make('pasar_id')
                    ->numeric(),
                TextEntry::make('komoditas_id')
                    ->numeric(),
                TextEntry::make('skor_harga')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('skor_distribusi')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('skor_frekuensi')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('kearifan_score')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('pengaruh')
                    ->placeholder('-')
                    ->columnSpanFull(),
            ]);
    }
}
