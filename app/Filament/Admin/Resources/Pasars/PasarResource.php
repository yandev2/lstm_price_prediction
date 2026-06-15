<?php

namespace App\Filament\Admin\Resources\Pasars;

use App\Filament\Admin\Resources\Pasars\Pages\CreatePasar;
use App\Filament\Admin\Resources\Pasars\Pages\EditPasar;
use App\Filament\Admin\Resources\Pasars\Pages\ListPasars;
use App\Filament\Admin\Resources\Pasars\Pages\ViewPasar;
use App\Filament\Admin\Resources\Pasars\Schemas\PasarForm;
use App\Filament\Admin\Resources\Pasars\Schemas\PasarInfolist;
use App\Filament\Admin\Resources\Pasars\Tables\PasarsTable;
use App\Models\HargaPangan;
use App\Models\Pasar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class PasarResource extends Resource
{
    protected static ?string $model = Pasar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingStorefront;
    protected static string | UnitEnum | null $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = "Pasar";
    protected static ?string $pluralModelLabel = "Pasar";
    protected static ?string $slug = "pasar";

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_pasar', 'tipe'];
    }
    public static function form(Schema $schema): Schema
    {
        return PasarForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PasarInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PasarsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPasars::route('/'),
            'create' => CreatePasar::route('/create'),
            'view' => ViewPasar::route('/{record}'),
            'edit' => EditPasar::route('/{record}/edit'),
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
