<?php

namespace App\Filament\Admin\Resources\Activities\Schemas;

use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('')
                    ->columnSpanFull()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                        'lg' => 3,
                        'xl' => 3
                    ])
                    ->schema([
                        TextEntry::make('causer.name')
                            ->icon('heroicon-o-user')
                            ->label('Author'),
                        TextEntry::make('event')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'info',
                                'deleted' => 'danger',
                                'export'  => 'warning',
                                default => 'gray',
                            })
                            ->label('Event'),

                        TextEntry::make('event_time')
                            ->getStateUsing(fn($record) => $record->created_at)
                            ->dateTime('l, d F Y H:i'),

                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull()
                            ->formatStateUsing(fn($record) => ($record->causer?->name ?? 'System') . " melakukan  {$record->description} pada data " . \Str::replace('App\Models\\', '', $record->subject_type)),
                    ]),


                KeyValueEntry::make('properties.attributes')
                    ->columnSpanFull()
                    ->visible(fn($record) => $record->event == 'export' && isset($record->properties['attributes'])),

                KeyValueEntry::make('attribute_changes.old')
                    ->label(fn($record) => ($record->event == 'deleted' || $record->event == 'created') ? 'Data' : 'Old Data')
                    ->visible(fn($record) => isset($record->attribute_changes['old']))
                    ->getStateUsing(function ($record) {
                        $data = $record->attribute_changes['old'] ?? [];
                        return is_array($data) ? array_filter($data, fn($item) => !is_array($item)) : [];
                    }),

                KeyValueEntry::make('attribute_changes.attributes')
                    ->label('New Data')
                    ->visible(fn($record) => isset($record->attribute_changes['attributes']))
                    ->getStateUsing(function ($record) {
                        $data = $record->attribute_changes['attributes'] ?? [];
                        return is_array($data) ? array_filter($data, fn($item) => !is_array($item)) : [];
                    }),
                    
                Fieldset::make('note')
                    ->dense()
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('note')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->color('warning')
                            ->state("Integritas Data: Demi menjaga keamanan, transparansi, dan akuntabilitas sistem, seluruh log aktivitas bersifat read-only (tidak dapat diubah atau dihapus secara manual oleh pengguna maupun administrator). Sistem secara otomatis akan melakukan pembersihan data (auto-pruning) terhadap catatan aktivitas yang telah melampaui masa simpan 365 hari (1 tahun) sejak tanggal pencatatan.")
                    ])
            ]);
    }
}
