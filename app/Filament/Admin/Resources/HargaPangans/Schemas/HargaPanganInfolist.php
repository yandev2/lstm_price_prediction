<?php

namespace App\Filament\Admin\Resources\HargaPangans\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;

class HargaPanganInfolist
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
                                Section::make('Informasi Utama')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextEntry::make('komoditas.nama_komoditas')
                                                ->label('Komoditas Pangan')
                                                ->icon('heroicon-o-shopping-bag')
                                                ->weight('bold')
                                                ->placeholder('-'),
                                                
                                            TextEntry::make('pasar.nama_pasar')
                                                ->label('Lokasi Pasar')
                                                ->icon('heroicon-o-building-storefront')
                                                ->weight('bold')
                                                ->placeholder('-'),
                                                
                                            TextEntry::make('tanggal')
                                                ->label('Tanggal Harga')
                                                ->date('d F Y')
                                                ->icon('heroicon-o-calendar')
                                                ->placeholder('-'),
                                                
                                            TextEntry::make('harga')
                                                ->label('Harga Pangan')
                                                ->numeric()
                                                ->prefix('Rp ')
                                                ->color('success')
                                                ->weight('bold')
                                                ->placeholder('-'),
                                        ]),
                                    ]),

                                Section::make('Detail Tambahan')
                                    ->schema([
                                        TextEntry::make('sumber_data')
                                            ->label('Sumber Data')
                                            ->placeholder('-'),
                                            
                                        TextEntry::make('faktor_eksternal')
                                            ->label('Faktor Eksternal')
                                            ->badge()
                                            ->color('warning')
                                            ->placeholder('Tidak ada faktor eksternal'),
                                    ]),
                            ]),

                        Group::make()
                            ->columnSpan(['default' => 3, 'md' => 1])
                            ->schema([
                                Section::make('Meta Data')
                                    ->schema([
                                        TextEntry::make('createdBy.name')
                                            ->label('Dicatat Oleh')
                                            ->icon('heroicon-o-user')
                                            ->placeholder('-'),
                                            
                                        TextEntry::make('created_at')
                                            ->label('Waktu Pencatatan')
                                            ->dateTime('d M Y, H:i')
                                            ->placeholder('-'),
                                            
                                        TextEntry::make('updated_at')
                                            ->label('Terakhir Diubah')
                                            ->dateTime('d M Y, H:i')
                                            ->placeholder('-'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
