<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\PrediksiHargas\PrediksiHargaResource;
use App\Models\PrediksiHarga;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TabelPrediksiHarga extends TableWidget
{

    use HasWidgetShield;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => PrediksiHarga::query()->whereDate('tanggal_prediksi', now()))
            ->columns([
                TextColumn::make('komoditas.nama_komoditas')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('pasar.nama_pasar')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('harga_prediksi')
                    ->label('Prediksi harga')
                    ->money('idr', true)
                    ->sortable(),
                TextColumn::make('selisih_persen')
                    ->label('Selisih')
                    ->suffix('%')
                    ->badge()
                    ->color(fn($record) => match ($record->status_anomali) {
                        'lonjakan' => 'danger',
                        'penurunan ekstrem' => 'warning',
                        'normal' => 'success',
                        default => 'gray'
                    })
                    ->sortable(),
                TextColumn::make('status_anomali')
                    ->color(fn($record) => match ($record->status_anomali) {
                        'lonjakan' => 'danger',
                        'penurunan ekstrem' => 'warning',
                        'normal' => 'success',
                        default => 'gray'
                    })
                    ->iconColor(fn($record) => match ($record->status_anomali) {
                        'lonjakan' => 'danger',
                        'penurunan ekstrem' => 'warning',
                        'normal' => 'success',
                        default => 'gray'
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'lonjakan' => 'heroicon-m-arrow-trending-up',
                        'penurunan ekstrem' => 'heroicon-m-arrow-trending-down',
                        'normal' => 'heroicon-m-check-circle',
                        default => 'heroicon-m-minus-circle',
                    })
                    ->searchable()
            ])
            ->filters([
                SelectFilter::make('komoditas_id')
                    ->label('Komoditas')
                    ->relationship('komoditas', 'nama_komoditas')
                    ->searchable(),
                SelectFilter::make('pasar_id')
                    ->label('Pasar')
                    ->relationship('pasar', 'nama_pasar')
                    ->searchable(),
                SelectFilter::make('status_anomali')
                    ->label('Anomali')
                    ->options([
                        'lonjakan' => "Lonjakan",
                        'penurunan ekstrem' => "Penurunan Ekstrem",
                        'normal' => "Normal"
                    ])
                    ->searchable(),
            ])
            ->headerActions([
            ])
            ->recordActions([
                ViewAction::make('Lihat')
                    ->url(fn($record) => PrediksiHargaResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
