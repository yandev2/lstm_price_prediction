<?php

namespace App\Filament\Admin\Resources\PrediksiHargas;

use App\Filament\Admin\Resources\PrediksiHargas\Pages\CreatePrediksiHarga;
use App\Filament\Admin\Resources\PrediksiHargas\Pages\EditPrediksiHarga;
use App\Filament\Admin\Resources\PrediksiHargas\Pages\ListPrediksiHargas;
use App\Filament\Admin\Resources\PrediksiHargas\Pages\ViewPrediksiHarga;
use App\Filament\Admin\Resources\PrediksiHargas\Schemas\PrediksiHargaForm;
use App\Filament\Admin\Resources\PrediksiHargas\Schemas\PrediksiHargaInfolist;
use App\Filament\Admin\Resources\PrediksiHargas\Tables\PrediksiHargasTable;
use App\Models\PrediksiHarga;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PrediksiHargaResource extends Resource
{
    protected static ?string $model = PrediksiHarga::class;

    protected static string|BackedEnum|null $navigationIcon = null;
    protected static string|UnitEnum|null $navigationGroup = 'Monitoring';
    protected static ?string $navigationLabel = "Prediksi Harga";
    protected static ?string $pluralModelLabel = "Prediksi Harga";
    protected static ?string $slug = "prediksi-harga";
    public static function form(Schema $schema): Schema
    {
        return PrediksiHargaForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PrediksiHargaInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrediksiHargasTable::configure($table);
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
            'index' => ListPrediksiHargas::route('/'),
            // 'create' => CreatePrediksiHarga::route('/create'),
            'view' => ViewPrediksiHarga::route('/{record}'),
            // 'edit' => EditPrediksiHarga::route('/{record}/edit'),
        ];
    }
}
