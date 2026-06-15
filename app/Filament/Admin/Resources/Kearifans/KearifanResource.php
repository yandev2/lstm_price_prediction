<?php

namespace App\Filament\Admin\Resources\Kearifans;

use App\Filament\Admin\Resources\Kearifans\Pages\ManageKearifans;
use App\Models\Kearifan;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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

class KearifanResource extends Resource
{
    protected static ?string $model = Kearifan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::UserGroup;
    protected static string | UnitEnum | null $navigationGroup = 'Data Master';
    protected static ?string $navigationLabel = "Kearifan";
    protected static ?string $pluralModelLabel = "Kearifan";
    protected static ?string $slug = "kearifan";

    public static function getGloballySearchableAttributes(): array
    {
        return ['nama_kearifan', 'jenis'];
    }
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_kearifan')
                    ->unique(ignoreRecord: true)
                    ->required(),
                Select::make('jenis')
                    ->required()
                    ->options(fn() => (new Kearifan())->jenis_options),
                Textarea::make('deskripsi')
                    ->required()
                    ->columnSpanFull(),
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
                TextColumn::make('nama_kearifan')
                    ->searchable(),
                TextColumn::make('jenis')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('deskripsi'),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
          
            ->filters([
                TrashedFilter::make()->columnSpan(2),
                SelectFilter::make('jenis')
                    ->options(fn() => (new Kearifan())->jenis_options)
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageKearifans::route('/'),
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
