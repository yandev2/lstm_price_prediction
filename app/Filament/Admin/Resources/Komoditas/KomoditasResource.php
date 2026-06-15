<?php

namespace App\Filament\Admin\Resources\Komoditas;

use App\Filament\Admin\Resources\Komoditas\Pages\CreateKomoditas;
use App\Filament\Admin\Resources\Komoditas\Pages\EditKomoditas;
use App\Filament\Admin\Resources\Komoditas\Pages\ListKomoditas;
use App\Filament\Admin\Resources\Komoditas\Pages\ViewKomoditas;
use App\Filament\Admin\Resources\Komoditas\RelationManagers\HargaPanganRelationManager;
use App\Filament\Admin\Resources\Komoditas\Schemas\KomoditasForm;
use App\Filament\Admin\Resources\Komoditas\Schemas\KomoditasInfolist;
use App\Filament\Admin\Resources\Komoditas\Tables\KomoditasTable;
use App\Models\Komoditas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class KomoditasResource extends Resource
{
    protected static ?string $model = Komoditas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string | UnitEnum | null $navigationGroup = 'Data Master';
    protected static ?string $pluralModelLabel = "Komoditas";
    protected static ?string $navigationLabel = "Komoditas";
    protected static ?string $slug = "komoditas";

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_komoditas', 'kategori'];
    }

    public static function form(Schema $schema): Schema
    {
        return KomoditasForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return KomoditasInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KomoditasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            HargaPanganRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKomoditas::route('/'),
            'create' => CreateKomoditas::route('/create'),
            'view' => ViewKomoditas::route('/{record}'),
            'edit' => EditKomoditas::route('/{record}/edit'),
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
