<?php

namespace App\Filament\Admin\Resources\Activities;

use App\Filament\Admin\Resources\Activities\Pages\CreateActivity;
use App\Filament\Admin\Resources\Activities\Pages\EditActivity;
use App\Filament\Admin\Resources\Activities\Pages\ListActivities;
use App\Filament\Admin\Resources\Activities\Pages\ViewActivity;
use App\Filament\Admin\Resources\Activities\Schemas\ActivityForm;
use App\Filament\Admin\Resources\Activities\Schemas\ActivityInfolist;
use App\Filament\Admin\Resources\Activities\Tables\ActivitiesTable;
use Spatie\Activitylog\Models\Activity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ComputerDesktop;
    protected static ?string $navigationLabel = "Activity";
    protected static ?string $pluralModelLabel = "Activity";
    protected static string | UnitEnum | null $navigationGroup = 'Management User';
    public static function form(Schema $schema): Schema
    {
        return ActivityForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ActivityInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table);
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
            'index' => ListActivities::route('/'),
            'create' => CreateActivity::route('/create'),
            'view' => ViewActivity::route('/{record}'),
            'edit' => EditActivity::route('/{record}/edit'),
        ];
    }
}
