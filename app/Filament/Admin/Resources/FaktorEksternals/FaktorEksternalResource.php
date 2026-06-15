<?php

namespace App\Filament\Admin\Resources\FaktorEksternals;

use App\Filament\Admin\Resources\FaktorEksternals\Pages\ManageFaktorEksternals;
use App\Models\FaktorEksternal;
use App\Models\Komoditas;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class FaktorEksternalResource extends Resource
{
    protected static ?string $model = FaktorEksternal::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::GlobeAlt;
    protected static string | UnitEnum | null $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = "Faktor Eksternal";
    protected static ?string $pluralModelLabel = "Faktor Eksternal";
    protected static ?string $slug = "faktor-eksternal";
    public static function getGloballySearchableAttributes(): array
    {
        return ['jenis', 'nilai'];
    }
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('tanggal')
                    ->required(),
                Select::make('jenis')
                    ->required()
                    ->options(fn() => (new FaktorEksternal())->jenis_options),
                TextInput::make('nilai')
                    ->columnSpanFull()
                    ->numeric()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('index')
                    ->label('No. ')
                    ->width('sm')
                    ->rowIndex()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('hargaPangan.komoditas.nama_komoditas')
                    ->searchable(),
                TextColumn::make('tanggal')
                    ->date('d F Y')
                    ->searchable(),
                TextColumn::make('jenis')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                TextColumn::make('nilai')
                    ->searchable(),
            ])
            ->filtersFormWidth('md')
            ->filtersFormColumns(2)
            ->filters([
                SelectFilter::make('jenis')
                    ->options(fn() => (new FaktorEksternal())->jenis_options)
                    ->searchable(),
                SelectFilter::make('komoditas')
                    ->options(Komoditas::pluck('nama_komoditas', 'id'))
                    ->query(function (Builder $query, array $state): Builder {
                        return $query->when(
                            $state['value'],
                            fn(Builder $query, $value): Builder => $query->whereHas('hargaPangan', function ($q) use ($value) {
                                $q->where('komoditas_id', $value);
                            })
                        );
                    })
                    ->searchable()
                    ->label('Pilih Komoditas'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageFaktorEksternals::route('/'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
