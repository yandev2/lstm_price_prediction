<?php

namespace App\Filament\Admin\Resources\PrediksiHargas\Schemas;

use Dom\Text;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class PrediksiHargaInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 3
                    ])
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('komoditas.nama_komoditas')
                            ->label('Komoditas'),
                        TextEntry::make('pasar.nama_pasar')
                            ->label('Pasar'),
                        TextEntry::make('tanggal_prediksi')
                            ->date('d F Y'),
                        TextEntry::make('prediksi_harga_untuk_tanggal')
                            ->date('d F Y'),
                        TextEntry::make('harga_prediksi')
                            ->money('IDR')
                            ->label('Harga prediksi'),
                        TextEntry::make('status_anomali')
                            ->placeholder('-')
                            ->badge()
                            ->color(fn($record) => match ($record->status_anomali) {
                                'lonjakan' => 'danger',
                                'penurunan ekstrem' => 'warning',
                                'normal' => 'success',
                                default => 'gray'
                            })
                    ]),

                Section::make()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 3
                    ])
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('summary')
                            ->columnSpanFull()
                            ->getStateUsing(function ($record) {
                                $hargaKomoditas = $record->pasar->hargaPangan
                                    ->where('komoditas_id', $record->komoditas_id);

                                $batasTanggal = now()->subDays(60);
                                $rataRata = $hargaKomoditas
                                    ->where('tanggal', '>=', $batasTanggal)
                                    ->avg('harga');

                                $hargaTerbaru = $hargaKomoditas
                                    ->sortByDesc('tanggal')
                                    ->first()?->harga ?? 0;

                                $tanggal = Carbon::parse($record->prediksi_harga_untuk_tanggal)->translatedFormat('l, d F Y');

                                $fmtPrediksi = 'Rp ' . number_format($record->harga_prediksi, 0, ',', '.');
                                $fmtRataRata = 'Rp ' . number_format($rataRata, 0, ',', '.');
                                $fmtTerbaru = 'Rp ' . number_format($hargaTerbaru, 0, ',', '.');

                                return "Prediksi harga untuk komoditas {$record->komoditas->nama_komoditas} di {$record->pasar->nama_pasar} untuk {$tanggal} adalah {$fmtPrediksi}. " .
                                    "Sebagai perbandingan, rata-rata harga dalam 60 hari terakhir adalah {$fmtRataRata}. " .
                                    "Terdapat selisih sebesar {$record->selisih_persen}% jika dibandingkan dengan harga terakhir ({$fmtTerbaru}).";
                            })
                    ]),
            ]);
    }
}
