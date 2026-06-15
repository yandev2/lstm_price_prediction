<?php

namespace App\Filament\Admin\Resources\Komoditas\Schemas;

use App\Models\Komoditas;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KomoditasInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
              Section::make()
                    ->columnSpanFull()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 3
                    ])
                    ->schema([
                        TextEntry::make('nama_komoditas'),
                        TextEntry::make('kategori'),
                        TextEntry::make('satuan'),
                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn(Komoditas $record): bool => $record->trashed()),
                    ])
            ]);
    }
}
