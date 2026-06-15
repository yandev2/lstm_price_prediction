<?php

namespace App\Filament\Admin\Resources\MappingKearifans;

use App\Filament\Admin\Resources\MappingKearifans\Pages\CreateMappingKearifan;
use App\Filament\Admin\Resources\MappingKearifans\Pages\EditMappingKearifan;
use App\Filament\Admin\Resources\MappingKearifans\Pages\ListMappingKearifans;
use App\Filament\Admin\Resources\MappingKearifans\Pages\ViewMappingKearifan;
use App\Filament\Admin\Resources\MappingKearifans\Schemas\MappingKearifanForm;
use App\Filament\Admin\Resources\MappingKearifans\Schemas\MappingKearifanInfolist;
use App\Filament\Admin\Resources\MappingKearifans\Tables\MappingKearifansTable;
use App\Models\MappingKearifan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MappingKearifanResource extends Resource
{
    protected static ?string $model = MappingKearifan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static string|UnitEnum|null $navigationGroup = 'Ai Management';

    protected static ?string $navigationLabel = "Mapping Kearifan";
    protected static ?string $pluralModelLabel = "Mapping Kearifan";
    protected static ?string $slug = "mapping-kearifan";
    public static function form(Schema $schema): Schema
    {
        return MappingKearifanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MappingKearifanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MappingKearifansTable::configure($table);
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
            'index' => ListMappingKearifans::route('/'),
            'create' => CreateMappingKearifan::route('/create'),
            //'view' => ViewMappingKearifan::route('/{record}'),
            'edit' => EditMappingKearifan::route('/{record}/edit'),
        ];
    }
}
