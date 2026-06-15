<?php

namespace App\Filament\Admin\Resources\HargaPangans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class HargaPanganInfolist
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
                TextEntry::make('created_by')
                    ->numeric(),
                TextEntry::make('komoditas_id')
                    ->numeric(),
                TextEntry::make('pasar_id')
                    ->numeric(),
                TextEntry::make('faktor_eksternal_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('tanggal')
                    ->dateTime(),
                TextEntry::make('harga')
                    ->numeric(),
                TextEntry::make('sumber_data')
                    ->placeholder('-'),
            ]);
    }
}
