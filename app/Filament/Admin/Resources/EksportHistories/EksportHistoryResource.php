<?php

namespace App\Filament\Admin\Resources\EksportHistories;

use App\Filament\Admin\Resources\EksportHistories\Pages\ManageEksportHistories;
use App\Models\EksportHistory;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class EksportHistoryResource extends Resource
{
    protected static ?string $model = EksportHistory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Printer;
    protected static string|UnitEnum|null $navigationGroup = 'Management User';
    protected static ?string $navigationLabel = "Export History";
    protected static ?string $pluralModelLabel = "Export History";
    protected static ?string $slug = "export-history";

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('filename')
                    ->required(),
                TextInput::make('file_path')
                    ->required(),
                TextInput::make('disk')
                    ->required()
                    ->default('public'),
                TextInput::make('mime_type'),
                TextInput::make('file_size')
                    ->numeric(),
                TextInput::make('module'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                if (!auth()->user()->hasRole('super_admin')) {
                    $query->where('user_id', auth()->id());
                }
            })
            ->columns([
                TextColumn::make('index')
                    ->label('No. ')
                    ->width('sm')
                    ->rowIndex(),
                TextColumn::make('user.name')
                    ->label('Author')
                    ->badge()
                    ->sortable(),
                TextColumn::make('module')
                    ->badge()
                    ->color('success')
                    ->searchable(),
                TextColumn::make('readable_size')
                    ->label('Size')
                    ->badge()
                    ->color('danger')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->badge()
                    ->color('warning')
                    ->date('d F Y')
                    ->searchable(),
                TextColumn::make('filename')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('download')
                    ->size('sm')
                    ->color('info')
                    ->label('Download File')
                    ->button()
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn($record) => asset('storage/' . $record->file_path), shouldOpenInNewTab: true),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageEksportHistories::route('/'),
        ];
    }
}
