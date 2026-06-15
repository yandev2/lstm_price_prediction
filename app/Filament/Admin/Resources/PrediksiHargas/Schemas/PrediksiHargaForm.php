<?php

namespace App\Filament\Admin\Resources\PrediksiHargas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PrediksiHargaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('komoditas_id')
                    ->required()
                    ->numeric(),
                TextInput::make('pasar_id')
                    ->required()
                    ->numeric(),
                DatePicker::make('tanggal_prediksi')
                    ->required(),
                DatePicker::make('prediksi_harga_untuk_tanggal')
                    ->required(),
                TextInput::make('harga_prediksi')
                    ->required()
                    ->numeric(),
                TextInput::make('selisih_persen')
                    ->numeric(),
                TextInput::make('status_anomali'),
                Toggle::make('alert_harga'),
            ]);
    }
}
