<?php

namespace App\Filament\Admin\Resources\Komoditas\Schemas;

use App\Models\Komoditas;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class KomoditasForm
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
                        TextInput::make('nama_komoditas')
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Select::make('kategori')
                            ->required()
                            ->options(fn() => (new Komoditas())->kategori_options),
                        Select::make('satuan')
                            ->required()
                            ->options(fn() => (new Komoditas())->satuan_options),
                    ])
            ]);
    }
}
