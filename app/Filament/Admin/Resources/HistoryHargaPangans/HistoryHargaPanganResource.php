<?php

namespace App\Filament\Admin\Resources\HistoryHargaPangans;

use App\Filament\Admin\Resources\HistoryHargaPangans\Pages\ManageHistoryHargaPangans;
use App\Models\HistoryHargaPangan;
use BackedEnum;
use Devletes\FilamentTimelineView\Tables\Columns\TimelineEntry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use UnitEnum;

class HistoryHargaPanganResource extends Resource
{
    protected static ?string $model = HistoryHargaPangan::class;
    protected static string|BackedEnum|null $navigationIcon = null;
    protected static string | UnitEnum | null $navigationGroup = 'Monitoring';
    protected static ?string $navigationLabel = "History Harga ";
    protected static ?string $pluralModelLabel = "History Harga ";
    protected static ?string $slug = "history-harga";

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal_perubahan', 'desc')
            ->defaultGroup(Group::make('tanggal_perubahan')->date()->collapsible())
            ->asTimeline()
            ->asDoubleSidedTimeline()
            ->columns([
                Stack::make([
                    TextColumn::make('hargaPangan.komoditas.nama_komoditas')
                        ->weight(FontWeight::Bold)
                        ->grow(true)
                        ->size(TextSize::Medium)
                        ->searchable(),

                    TextColumn::make('hargaPangan.pasar.nama_pasar')
                        ->searchable()
                        ->icon(Heroicon::MapPin)
                        ->iconColor('primary'),

                    Split::make([
                        TextColumn::make('harga_lama')
                            ->money('idr', true)
                            ->color('danger')
                            ->badge()
                            ->grow(true)
                            ->tooltip('Harga lama'),
                        TextColumn::make('created_at')
                            ->icon(Heroicon::ArrowRight)
                            ->formatStateUsing(fn($state) => '')
                            ->iconColor('primary'),
                        TextColumn::make('harga_baru')
                            ->money('idr', true)
                            ->color('success')
                            ->badge()
                            ->grow(false)
                            ->tooltip('Harga baru'),
                    ]),
                    Panel::make([
                        TextColumn::make('deskripsi')
                            ->size(TextSize::Small)
                    ]),
                    TimelineEntry::make()
                        ->author(
                            'hargaPangan.createdBy.name',
                            fn($record) => $record->hargaPangan->createdBy?->getFilamentAvatarUrl(),
                        )
                        ->grow(true)
                        ->time('tanggal_perubahan', format: 'd M Y H:i'),
                ])
                    ->space(2)
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
            ])
        ;
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageHistoryHargaPangans::route('/'),
        ];
    }
}
