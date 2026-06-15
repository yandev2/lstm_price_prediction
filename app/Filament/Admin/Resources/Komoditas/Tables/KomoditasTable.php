<?php

namespace App\Filament\Admin\Resources\Komoditas\Tables;

use App\Models\Komoditas;
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

class KomoditasTable
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

                TextColumn::make('nama_komoditas')
                    ->searchable(),
                TextColumn::make('kategori')
                    ->searchable(),
                TextColumn::make('satuan')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('perubahan_harga')
                    ->badge()
                    ->color('info')
                    ->tooltip('Jumlah perubahan harga')
                    ->getStateUsing(fn($record) => $record->hargaPangan->count()),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filtersFormWidth('md')
            ->filtersFormColumns(2)
            ->filters([
                TrashedFilter::make()->columnSpan(2),
                SelectFilter::make('kategori')
                    ->options(fn() => (new Komoditas())->kategori_options)
                    ->searchable(),
                SelectFilter::make('satuan')
                    ->options(fn() => (new Komoditas())->satuan_options)
                    ->searchable(),
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
