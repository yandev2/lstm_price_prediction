<?php

namespace App\Filament\Admin\Resources\MappingKearifans\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MappingKearifansTable
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
                TextColumn::make('kearifan.nama_kearifan')
                    ->searchable(),
                TextColumn::make('pasar.nama_pasar')
                    ->searchable(),
                TextColumn::make('komoditas.nama_komoditas')
                    ->searchable(),
                TextColumn::make('kearifan_score')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                TextColumn::make('pengaruh')
                    ->badge()
                    ->color(fn($record) => match ($record->pengaruh) {
                        'Tinggi' => 'danger',
                        'Sedang' => 'warning',
                        'Rendah' => 'success',
                        default => 'gray'
                    })
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
            ])
            ->filters([
                SelectFilter::make('kearifan_id')
                    ->label('Kearifan Lokal')
                    ->relationship('kearifan', 'nama_kearifan'),
                SelectFilter::make('pasar_id')
                    ->label('Pasar')
                    ->relationship('pasar', 'nama_pasar'),
                SelectFilter::make('komoditas_id')
                    ->label('Komoditas')
                    ->relationship('komoditas', 'nama_komoditas'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
