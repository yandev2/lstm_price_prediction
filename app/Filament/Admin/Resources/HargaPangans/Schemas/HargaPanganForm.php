<?php

namespace App\Filament\Admin\Resources\HargaPangans\Schemas;

use App\Models\HargaPangan;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class HargaPanganForm
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
                    ->footer(function (Get $get, $record) {
                        if($record) return ;

                        if ($get('komoditas_id') && $get('tanggal') && $get('pasar_id')) {
                            $lastHarga =  HargaPangan::where('komoditas_id', $get('komoditas_id'))
                                ->where('pasar_id', $get('pasar_id'))
                                ->when($record?->id, function ($query, $currentId) {
                                    return $query->where('id', '!=', $currentId);
                                })
                                ->latest('tanggal')
                                ->first();
                            if ($lastHarga) {
                                return 'Harga Periode Lalu (' . Carbon::parse($lastHarga->tanggal)->translatedFormat('d F Y') . ') Dari Komoditas ' . $lastHarga->komoditas->nama_komoditas . ' ' . number_format($lastHarga->harga, 0, ',', '.')  . ' per ' . $lastHarga->komoditas->satuan . ' di pasar ' . $lastHarga?->pasar?->nama_pasar;
                            }
                            return null;
                        }
                        return null;
                    })
                    ->schema([
                        Hidden::make('created_by')
                            ->required()
                            ->dehydrated()
                            ->default(fn() => auth()->user()->id),

                        DatePicker::make('tanggal')
                            ->required(),

                        Select::make('komoditas_id')
                            ->required()
                            ->live()
                            ->relationship('komoditas', 'nama_komoditas'),

                        Select::make('pasar_id')
                            ->required()
                            ->relationship('pasar', 'nama_pasar'),

                        TextInput::make('harga')
                            ->live()
                            ->required(),

                        Select::make('faktor_eksternal')
                            ->live()
                            ->label('Faktor perubahaan harga')
                            ->multiple()
                            ->options(fn() => (new HargaPangan())->jenis_faktor_options),

                        TextInput::make('sumber_data'),
                    ])
            ]);
    }
}
