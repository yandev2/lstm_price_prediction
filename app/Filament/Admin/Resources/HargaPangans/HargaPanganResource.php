<?php

namespace App\Filament\Admin\Resources\HargaPangans;

use App\Filament\Admin\Resources\HargaPangans\Pages\CreateHargaPangan;
use App\Filament\Admin\Resources\HargaPangans\Pages\EditHargaPangan;
use App\Filament\Admin\Resources\HargaPangans\Pages\ListHargaPangans;
use App\Filament\Admin\Resources\HargaPangans\Pages\ViewHargaPangan;
use App\Filament\Admin\Resources\HargaPangans\Schemas\HargaPanganForm;
use App\Filament\Admin\Resources\HargaPangans\Schemas\HargaPanganInfolist;
use App\Filament\Admin\Resources\HargaPangans\Tables\HargaPangansTable;
use App\Models\HargaPangan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HargaPanganResource extends Resource
{
    protected static ?string $model = HargaPangan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Banknotes;
    protected static string | UnitEnum | null $navigationGroup = 'Monitoring';

    protected static ?string $navigationLabel = "Harga Pangan ";
    protected static ?string $pluralModelLabel = "Harga Pangan ";
    protected static ?string $slug = "harga-pangan";
    public static function form(Schema $schema): Schema
    {
        return HargaPanganForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return HargaPanganInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HargaPangansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListHargaPangans::route('/'),
            'create' => CreateHargaPangan::route('/create'),
            'view' => ViewHargaPangan::route('/{record}'),
            'edit' => EditHargaPangan::route('/{record}/edit'),
        ];
    }
}
