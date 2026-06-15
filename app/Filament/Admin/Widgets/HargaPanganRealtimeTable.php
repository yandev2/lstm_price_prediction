<?php

namespace App\Filament\Admin\Widgets;

use App\Models\HargaPangan;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class HargaPanganRealtimeTable extends TableWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;

    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        $pasarId = $this->pageFilters['pasar'] ?? null;
        $komoditasId = $this->pageFilters['komoditas'] ?? null;

        return $table
            ->selectable(false)
            ->query(
                fn() =>  HargaPangan::query()
                    ->select('harga_pangans.*')
                    ->addSelect(['harga_sebelumnya' => HargaPangan::select('harga')
                        ->whereColumn('pasar_id', 'harga_pangans.pasar_id')
                        ->whereColumn('komoditas_id', 'harga_pangans.komoditas_id')
                        ->whereColumn('id', '<', 'harga_pangans.id')
                        ->orderBy('id', 'desc')
                        ->limit(1)
                    ])
                    ->with(['komoditas', 'pasar'])
                    ->when($pasarId, function ($query, $pasarId) {
                        return $query->where('pasar_id', $pasarId);
                    })
                    ->when($komoditasId, function ($query, $komoditasId) {
                        return $query->where('komoditas_id', $komoditasId);
                    })
                    // Menggunakan subquery untuk memastikan hanya mengambil inputan paling terakhir dari setiap komoditas di setiap pasar
                    ->whereIn('id', function ($query) {
                        $query->select(DB::raw('MAX(id)'))
                            ->from('harga_pangans')
                            ->groupBy(['pasar_id', 'komoditas_id']);
                    })
                    ->orderBy('created_at', 'desc')
            )
            ->columns([
                TextColumn::make('index')
                    ->label('No. ')
                    ->width('sm')
                    ->rowIndex()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('created_at')
                    ->label('Waktu Update')
                    ->dateTime('d M Y | H:i')
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('komoditas.nama_komoditas')
                    ->label('Komoditas')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('pasar.nama_pasar')
                    ->label('Lokasi Pasar')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('harga')
                    ->label('Harga Riil')
                    ->money('IDR', locale: 'id')
                    ->weight('semibold')
                    ->alignEnd()
                    ->sortable(),

                // Kolom Status Perubahan Harga (Day-over-Day internal per pasar)
                TextColumn::make('status_tren')
                    ->label('Tren Perubahan')
                    ->alignCenter()
                    ->state(function (HargaPangan $record): string {
                        $hargaSebelumnya = $record->harga_sebelumnya;

                        if (! $hargaSebelumnya || $hargaSebelumnya == $record->harga) {
                            return '⚪ Stabil (0%)';
                        }

                        $selisih = (($record->harga - $hargaSebelumnya) / $hargaSebelumnya) * 100;
                        $formatPersen = round($selisih, 1) . '%';

                        return $selisih > 0 ? "▲ +{$formatPersen}" : "▼ {$formatPersen}";
                    })
                    ->badge()
                    ->color(fn(string $state): string => match (true) {
                        str_contains($state, '▲') => 'danger',
                        str_contains($state, '▼') => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
