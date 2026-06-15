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

                TextEntry::make('summary')
                    ->hiddenLabel()
                    ->html()
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

                        $tanggal = \Carbon\Carbon::parse($record->prediksi_harga_untuk_tanggal)->translatedFormat('l, d F Y');

                        $fmtPrediksi = 'Rp ' . number_format($record->harga_prediksi, 0, ',', '.');
                        $fmtRataRata = 'Rp ' . number_format($rataRata, 0, ',', '.');
                        $fmtTerbaru = 'Rp ' . number_format($hargaTerbaru, 0, ',', '.');

                        $selisihWarna = $record->selisih_persen > 0 ? 'text-danger-500' : 'text-success-500';
                        $selisihIcon = $record->selisih_persen > 0 ? '↑' : '↓';

                        return new \Illuminate\Support\HtmlString('
                            <div class="p-5 rounded-xl bg-primary-50 dark:bg-primary-500/10">
                                <h4 class="flex items-center gap-2 font-bold text-primary-700 dark:text-primary-400 mb-4">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Kesimpulan Prediksi
                                </h4>
                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed mb-4">
                                    Prediksi harga untuk komoditas <strong>' . $record->komoditas->nama_komoditas . '</strong> 
                                    di <strong>' . $record->pasar->nama_pasar . '</strong> pada <strong>' . $tanggal . '</strong> 
                                    diperkirakan berada di angka <strong class="text-primary-600 dark:text-primary-400 text-lg">' . $fmtPrediksi . '</strong>.
                                </p>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="p-4 rounded-lg bg-white dark:bg-white/5 shadow-sm flex flex-col justify-center">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Rata-rata (60 Hari Terakhir)</span>
                                        <span class="text-lg font-bold text-gray-900 dark:text-white mt-1">' . $fmtRataRata . '</span>
                                    </div>
                                    <div class="p-4 rounded-lg bg-white dark:bg-white/5 shadow-sm flex flex-col justify-center">
                                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Harga Terakhir Tercatat</span>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-lg font-bold text-gray-900 dark:text-white">' . $fmtTerbaru . '</span>
                                            <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-700 ' . $selisihWarna . '">
                                                ' . $selisihIcon . ' ' . abs($record->selisih_persen) . '%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ');
                    })
            ]);
    }
}
