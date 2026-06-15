<?php

namespace App\Filament\Admin\Resources\MappingKearifans\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MappingKearifanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
               Section::make()
                    ->columnSpanFull()
                    ->columns([
                        'sm' => 2,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 3,
                        '2xl' => 3,
                    ])
                    ->schema([
                        Select::make('kearifan_id')
                            ->required()
                            ->relationship('kearifan', 'nama_kearifan'),
                        Select::make('pasar_id')
                            ->required()
                            ->relationship('pasar', 'nama_pasar'),
                        Select::make('komoditas_id')
                            ->required()
                            ->relationship('komoditas', 'nama_komoditas'),
                        Select::make('skor_harga')
                            ->options([
                                1 => '1 - Sangat Rendah',
                                2 => '2 - Rendah',
                                3 => '3 - Sedang',
                                4 => '4 - Tinggi',
                                5 => '5 - Sangat Tinggi',
                            ])
                            ->required(),
                        Select::make('skor_distribusi')
                            ->options([
                                1 => '1 - Tidak Berpengaruh',
                                2 => '2 - Rendah',
                                3 => '3 - Sedang',
                                4 => '4 - Tinggi',
                                5 => '5 - Sangat Mempengaruhi Supply',
                            ])
                            ->required(),
                        Select::make('skor_frekuensi')
                            ->options([
                                1 => '1 - Jarang',
                                2 => '2 - Kadang',
                                3 => '3 - Periodik',
                                4 => '4 - Sering',
                                5 => '5 - Hampir Selalu',
                            ])
                            ->required(),
                    ])
            ]);
    }
}
