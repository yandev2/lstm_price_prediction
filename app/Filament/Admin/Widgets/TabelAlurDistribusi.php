<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Distribusi;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Devletes\FilamentTimelineView\Tables\Columns\TimelineEntry;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TabelAlurDistribusi extends TableWidget
{

    use InteractsWithPageFilters;
    use HasWidgetShield;


    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $komoditas = $this->pageFilters['komoditas'] ?? null;

        // Filter Rentang Tanggal dari Page Filter
        $currentDate = isset($this->pageFilters['start_date']) ? Carbon::parse($this->pageFilters['start_date'])->startOfDay() : now()->subMonthsNoOverflow(1)->startOfDay();
        $lastDate = isset($this->pageFilters['end_date']) ? Carbon::parse($this->pageFilters['end_date'])->endOfDay() : now()->endOfDay();
        $komoditas = $this->pageFilters['komoditas'] ?? null;

        return $table
            ->selectable(false)
            ->query(fn() => Distribusi::query()
                ->with(['komoditas', 'pasarAsal', 'pasarTujuan'])
                ->when($komoditas, fn($q) => $q->where('komoditas_id', $komoditas))
                ->whereBetween('tanggal', [$currentDate, $lastDate])
                ->orderBy('tanggal', 'desc'))
            ->columns([
                TextColumn::make('index')
                    ->label('No. ')
                    ->width('sm')
                    ->rowIndex()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),


                TextColumn::make('komoditas.nama_komoditas')
                    ->label('Komoditas')
                    ->weight('bold')
                    ->searchable(),

                // ASAL DISTRIBUSI
                TextColumn::make('pasarAsal.nama_pasar')
                    ->label('📍 Asal Pengiriman')
                    ->searchable(),

                // TUJUAN DISTRIBUSI
                TextColumn::make('pasarTujuan.nama_pasar')
                    ->label('🎯 Tujuan Pasar')
                    ->searchable(),

                TextColumn::make('volume')
                    ->label('Volume')
                    ->numeric(decimalPlaces: 0, locale: 'id')
                    ->suffix(fn($record) => $record->komoditas->satuan)
                    ->weight('semibold')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('transportasi')
                    ->label('Moda Transportasi')
                    ->badge()
                    ->color('info'),
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
