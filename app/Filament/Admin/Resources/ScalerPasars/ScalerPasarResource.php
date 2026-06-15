<?php

namespace App\Filament\Admin\Resources\ScalerPasars;

use App\Filament\Admin\Resources\ScalerPasars\Pages\ManageScalerPasars;
use App\Models\Pasar;
use App\Models\ScalerPasar;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

class ScalerPasarResource extends Resource
{
    protected static ?string $model = ScalerPasar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::AdjustmentsHorizontal;
    protected static string|UnitEnum|null $navigationGroup = 'Ai Management';
    protected static ?string $navigationLabel = "Encode Pasar";
    protected static ?string $pluralModelLabel = "Encode Pasar";
    protected static ?string $slug = "encode-pasar";
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->live()
                    ->required(),
                TextInput::make('versi')
                    ->required()
                    ->live()
                    ->numeric(),
                Select::make('daftar_pasar')
                    ->required()
                    ->columnSpanFull()
                    ->multiple()
                    ->label('Daftar Pasar')
                    ->options(Pasar::pluck('nama_pasar', 'nama_pasar')),
                FileUpload::make('file_url')
                    ->disk('local')
                    ->label('Upload File Encode')
                    ->directory('ai_models')
                    ->required()
                    ->columnSpanFull()
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string {
                        $ekstensi = $file->getClientOriginalExtension();
                        $versi = $get('versi');
                        $nama = $get('nama');
                        return "scaler_{$nama}_v{$versi}.{$ekstensi}";
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No. ')
                    ->width('sm')
                    ->rowIndex()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('daftar_pasar')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('versi')
                    ->numeric()
                    ->badge()
                    ->color('success'),
                TextColumn::make('file_url')
                    ->searchable()
                    ->badge()
                    ->color('info')
            ])
            ->filters([
                //
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
            'index' => ManageScalerPasars::route('/'),
        ];
    }
}
