<?php

namespace App\Filament\Admin\Resources\Pasars\Schemas;

use App\Models\Pasar;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PasarInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Group::make()
                            ->columnSpan(['default' => 3, 'md' => 2])
                            ->schema([
                                Section::make('Informasi Pasar')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextEntry::make('nama_pasar')
                                                ->label('Nama Pasar')
                                                ->icon('heroicon-o-building-storefront')
                                                ->weight('bold')
                                               
                                                ->color('primary'),
                                                
                                            TextEntry::make('tipe')
                                                ->label('Tipe Pasar')
                                                ->badge()
                                                ->color('info'),
                                                
                                            TextEntry::make('lokasi')
                                                ->label('Koordinat Peta')
                                                ->icon('heroicon-o-map-pin')
                                                ->badge()
                                                ->color('success')
                                                ->getStateUsing(fn() => 'Lihat Lokasi')
                                                ->tooltip(fn($record) => ($record->latitude && $record->longitude) != null ? 'Buka lokasi pasar di Google Maps' : 'Lokasi Tidak Tersedia')
                                                ->url(fn(Pasar $record) => ($record->latitude && $record->longitude) != null ? "https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longitude}" : null, shouldOpenInNewTab: true),
                                        ]),
                                    ]),

                                Section::make('Alamat Lengkap')
                                    ->schema([
                                        TextEntry::make('alamat')
                                            ->hiddenLabel()
                                            ->columnSpanFull()
                                            ->placeholder('Alamat tidak tersedia'),
                                    ]),
                            ]),

                        Group::make()
                            ->columnSpan(['default' => 3, 'md' => 1])
                            ->schema([
                                Section::make('Status & Meta Data')
                                    ->schema([
                                        TextEntry::make('created_at')
                                            ->label('Didaftarkan Pada')
                                            ->dateTime('d M Y, H:i')
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('-'),
                                            
                                        TextEntry::make('updated_at')
                                            ->label('Terakhir Diubah')
                                            ->dateTime('d M Y, H:i')
                                            ->placeholder('-'),
                                            
                                        TextEntry::make('deleted_at')
                                            ->label('Waktu Dihapus')
                                            ->dateTime('d M Y, H:i')
                                            ->color('danger')
                                            ->icon('heroicon-o-trash')
                                            ->visible(fn(Pasar $record): bool => $record->trashed()),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
