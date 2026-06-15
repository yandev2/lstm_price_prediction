<?php

namespace App\Filament\Admin\Resources\Pasars\Tables;

use App\Models\Pasar;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PasarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No. ')
                    ->width('sm')
                    ->rowIndex()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('nama_pasar')
                    ->searchable(),
                TextColumn::make('tipe')
                    ->searchable(),
                TextColumn::make('location')
                    ->label('Lokasi')
                    ->getStateUsing('Lihat Lokasi')
                    ->badge()
                    ->color('success')
                    ->tooltip(fn($record) => ($record->latitude && $record->longitude) != null ? 'Lihat lokasi pasar di Google Maps' : 'Lokasi Tidak Tersedia')
                    ->url(fn($record) => ($record->latitude && $record->longitude) != null ? "https://www.google.com/maps/search/?api=1&query={$record->latitude},{$record->longitude}" : null, shouldOpenInNewTab: true)
                    ->openUrlInNewTab(),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make()->columnSpan(2),
                SelectFilter::make('tipe')
                    ->options(fn() => (new Pasar())->tipe_options)
                    ->searchable()->columnSpan(1),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
