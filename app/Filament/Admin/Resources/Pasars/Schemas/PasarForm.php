<?php

namespace App\Filament\Admin\Resources\Pasars\Schemas;

use App\Models\Pasar;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PasarForm
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
                                            TextInput::make('nama_pasar')
                                                ->label('Nama Pasar')
                                                ->placeholder('Contoh: Pasar Induk Kramat Jati')
                                                ->unique(ignoreRecord: true)
                                                ->required()
                                                ->maxLength(255),
                                                
                                            Select::make('tipe')
                                                ->label('Tipe Pasar')
                                                ->placeholder('Pilih tipe pasar')
                                                ->required()
                                                ->options(fn() => (new Pasar())->tipe_options)
                                                ->native(false),
                                        ]),
                                    ]),

                                Section::make('Data Lokasi & Alamat')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('latitude')
                                                ->label('Latitude (Garis Lintang)')
                                                ->placeholder('Contoh: -6.200000')
                                                ->numeric(),
                                                
                                            TextInput::make('longitude')
                                                ->label('Longitude (Garis Bujur)')
                                                ->placeholder('Contoh: 106.816666')
                                                ->numeric(),
                                        ]),
                                        
                                        Textarea::make('alamat')
                                            ->label('Alamat Lengkap')
                                            ->placeholder('Masukkan alamat lengkap pasar beserta patokan (opsional)')
                                            ->required()
                                            ->rows(4)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Group::make()
                            ->columnSpan(['default' => 3, 'md' => 1])
                            ->schema([
                                Section::make('Panduan Lokasi')
                                    ->description('Untuk mempermudah validasi data dan monitoring, pastikan Anda mengisi Koordinat Latitude & Longitude dengan benar.')
                                    ->schema([
                                        \Filament\Forms\Components\Placeholder::make('bantuan')
                                            ->hiddenLabel()
                                            ->content(new \Illuminate\Support\HtmlString('
                                                <div class="text-sm text-gray-500">
                                                    <b>Cara mendapatkan koordinat:</b><br/>
                                                    1. Buka <a href="https://maps.google.com" target="_blank" class="text-primary-600 underline">Google Maps</a>.<br/>
                                                    2. Cari lokasi pasar.<br/>
                                                    3. Klik kanan pada titik lokasi.<br/>
                                                    4. Klik angka koordinat yang muncul untuk menyalinnya (contoh: -6.123, 106.456).<br/>
                                                    5. Angka pertama adalah Latitude, kedua adalah Longitude.
                                                </div>
                                            ')),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
