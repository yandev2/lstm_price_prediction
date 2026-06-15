<?php

namespace App\Filament\Admin\Resources\Lookups;

use App\Filament\Admin\Resources\Lookups\Pages\ManageLookups;
use App\Models\Lookup;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class LookupResource extends Resource
{
    protected static ?string $model = Lookup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ListBullet;
    protected static string | UnitEnum | null $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = "Lookup";
    protected static ?string $pluralModelLabel = "Lookup";
    protected static ?string $slug = "lookup";

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('key')
                    ->required()
                    ->disabledOn('edit')
                    ->columnSpanFull()
                    ->maxLength(255),

                TagsInput::make('value')
                    ->columnSpanFull()
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No. ')
                    ->width('sm')
                    ->rowIndex()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('key')
                    ->searchable(),
                TextColumn::make('value')
                    ->badge()
                    ->color('info')
                    ->wrap()
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLookups::route('/'),
        ];
    }
}
