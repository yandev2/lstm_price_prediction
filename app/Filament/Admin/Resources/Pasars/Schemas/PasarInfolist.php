<?php

namespace App\Filament\Admin\Resources\Pasars\Schemas;

use App\Models\Pasar;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PasarInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                        'lg' => 3
                    ])
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('nama_pasar'),
                        TextEntry::make('tipe'),
                        TextEntry::make('lokasi')
                            ->badge()
                            ->color('success')
                            ->getStateUsing('Lihat Lokasi')
                            ->tooltip(fn($record) => ($record->latitude && $record->longitude) != null ? 'Lihat lokasi pasar di Google Maps' : 'Lokasi Tidak Tersedia')
                            ->url(fn(Pasar $record) => ($record->latitude && $record->longitude) != null ? "https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longitude}" : null, shouldOpenInNewTab: true),
                     
                            Fieldset::make('Alamat')
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('alamat')
                                    ->hiddenLabel()
                                    ->columnSpanFull(),
                            ]),

                        TextEntry::make('deleted_at')
                            ->dateTime()
                            ->visible(fn(Pasar $record): bool => $record->trashed()),
                    ])
            ]);
    }
}
