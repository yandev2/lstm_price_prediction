<?php

namespace App\Filament\Admin\Resources\ModelAis;

use App\Filament\Admin\Resources\ModelAis\Pages\ManageModelAis;
use App\Models\Komoditas;
use App\Models\ModelAi;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use UnitEnum;

class ModelAiResource extends Resource
{
    protected static ?string $model = ModelAi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CpuChip;
    protected static string|UnitEnum|null $navigationGroup = 'Ai Management';
    protected static ?string $navigationLabel = "Model";
    protected static ?string $pluralModelLabel = "Model";
    protected static ?string $slug = "model";
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('komoditas_id')
                    ->required()
                    ->label('Komoditas')
                    ->live()
                    ->relationship('komoditas', 'nama_komoditas'),
                TextInput::make('versi')
                    ->required()
                    ->live()
                    ->numeric(),
                DatePicker::make('tanggal_training')
                    ->required(),
                TextInput::make('mape')
                    ->required()
                    ->numeric(),
                FileUpload::make('scaler_file')
                    ->disk('local')
                    ->label('Upload File Scaler')
                    ->directory('ai_models')
                    ->required()
                    ->disabled(fn(Get $get): bool => blank($get('komoditas_id')))
                    ->columnSpanFull()
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string {
                        $komoditas = Komoditas::find($get('komoditas_id'));
                        $namaKomoditas = $komoditas ? strtolower(str_replace(' ', '_', trim($komoditas->nama_komoditas))) : 'unknown';
                        $ekstensi = $file->getClientOriginalExtension();
                        $versi = $get('versi');
                        return "scaler_{$namaKomoditas}_v{$versi}.{$ekstensi}";
                    }),

                FileUpload::make('model_file')
                    ->disk('local')
                    ->directory('ai_models')
                    ->label('Upload File Model')
                    ->required()
                    ->maxSize(10000)
                    ->disabled(fn(Get $get): bool => blank($get('komoditas_id')))
                    ->columnSpanFull()
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string {
                        $komoditas = Komoditas::find($get('komoditas_id'));
                        $namaKomoditas = $komoditas ? strtolower(str_replace(' ', '_', trim($komoditas->nama_komoditas))) : 'unknown';
                        $ekstensi = $file->getClientOriginalExtension();
                        $versi = $get('versi');
                        return "model_{$namaKomoditas}_v{$versi}.{$ekstensi}";
                    }),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('komoditas_id')
                    ->numeric()
                    ->placeholder('-'),
                TextEntry::make('versi')
                    ->numeric(),
                TextEntry::make('tanggal_training')
                    ->date(),
                TextEntry::make('scaler_file'),
                TextEntry::make('model_file'),
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
                TextColumn::make('komoditas.nama_komoditas')
                    ->searchable(),
                TextColumn::make('versi')
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('mape')
                    ->badge()
                    ->color('success'),
                TextColumn::make('tanggal_training')
                    ->date('d-m-Y')
                    ->sortable(),
                TextColumn::make('scaler_file')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn(?string $state): string => $state ? basename($state) : '-'),
                TextColumn::make('model_file')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn(?string $state): string => $state ? basename($state) : '-'),
            ])
            ->filters([
                //ai_models\model_cabai_merah_v1.h5
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
            'index' => ManageModelAis::route('/'),
        ];
    }
}
