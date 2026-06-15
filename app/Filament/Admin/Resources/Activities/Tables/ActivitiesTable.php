<?php

namespace App\Filament\Admin\Resources\Activities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No. ')
                    ->width('sm')
                    ->rowIndex(),
                TextColumn::make('description')
                    ->label('Author')
                    ->formatStateUsing(fn($record, $state) => $record->causer == null ? 'sistem' : $record->causer->name)
                    ->icon('heroicon-o-user')
                    ->badge()
                    ->color('info'),
                TextColumn::make('event')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'export'  => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('log_name')
                    ->label('Log')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filtersFormWidth('md')
            ->filtersFormColumns(2)
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Log')
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->options(Activity::query()
                        ->distinct()
                        ->pluck('log_name', 'log_name')
                        ->filter()
                        ->toArray()),
                SelectFilter::make('event')
                    ->label('Event')
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->options([
                        'created' => 'created',
                        'updated' => 'updated',
                        'deleted' => 'deleted',
                        'export' => 'export',
                    ])
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
