<?php

namespace App\Filament\Admin\Resources\Pasars\Schemas;

use App\Models\Pasar;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PasarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_pasar')
                    ->unique(ignoreRecord: true)
                    ->required(),
                Select::make('tipe')
                    ->required()
                    ->options(fn() => (new Pasar())->tipe_options),
                Textarea::make('alamat')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('latitude'),
                TextInput::make('longitude'),
            ]);
    }
}
