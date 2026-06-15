<?php

namespace App\Filament\Admin\Resources\Distribusis;

use App\Filament\Admin\Resources\Distribusis\Pages\ManageDistribusis;
use App\Http\Controllers\ExportController;
use App\Models\Distribusi;
use App\Models\Komoditas;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class DistribusiResource extends Resource
{
    protected static ?string $model = Distribusi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Truck;
    protected static string | UnitEnum | null $navigationGroup = 'Monitoring';
    protected static ?string $navigationLabel = "Distribusi";
    protected static ?string $pluralModelLabel = "Distribusi";
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('komoditas_id')
                    ->required()
                    ->relationship('komoditas', 'nama_komoditas'),
                Select::make('pasar_asal_id')
                    ->required()
                    ->relationship('pasarAsal', 'nama_pasar'),
                Select::make('pasar_tujuan_id')
                    ->required()
                    ->relationship('pasarTujuan', 'nama_pasar'),
                TextInput::make('volume')
                    ->integer()
                    ->required(),
                DatePicker::make('tanggal')
                    ->required(),
                Select::make('transportasi')
                    ->required()
                    ->options(fn() => (new Distribusi())->transportasi_options),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('index')
                    ->label('No. ')
                    ->width('sm')
                    ->rowIndex()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('tanggal')
                    ->date('d F Y'),
                TextColumn::make('komoditas.nama_komoditas')
                    ->searchable(),
                TextColumn::make('pasarAsal.nama_pasar')
                    ->searchable(),
                TextColumn::make('pasarTujuan.nama_pasar')
                    ->searchable(),
                TextColumn::make('volume')
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->searchable(),
                TextColumn::make('transportasi')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('pasar_asal_id')
                    ->label('Pasar Asal')
                    ->relationship('pasarAsal', 'nama_pasar')
                    ->searchable(),

                SelectFilter::make('pasar_tujuan_id')
                    ->label('Pasar Tujuan')
                    ->relationship('pasarTujuan', 'nama_pasar')
                    ->searchable(),

                SelectFilter::make('komoditas_id')
                    ->label('Komoditas')
                    ->relationship('komoditas', 'nama_komoditas')
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                Action::make('distribusi')
                    ->button()
                    ->label('Cetak Laporan Distribusi')
                    ->modalHeading(\Str::title("Cetak Laporan Distribusi"))
                    ->accessSelectedRecords()
                    ->modalIcon(Heroicon::Printer)
                    ->modalAlignment('center')
                    ->color('primary')
                    ->modalWidth('md')
                    ->icon(Heroicon::Printer)
                    ->modalSubmitActionLabel('Mulai Cetak')
                    ->modalFooterActionsAlignment('center')
                    ->action(fn($data) => static::exportData($data))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('type')
                                    ->columnSpanFull()
                                    ->label('Jenis File')
                                    ->options([
                                        'pdf' => 'PDF',
                                        'exel' => 'Excel',
                                    ])
                                    ->required(),

                                Select::make('transportasi')
                                    ->multiple()
                                    ->columnSpan(1)
                                    ->placeholder('')
                                    ->options((new Distribusi())->transportasi_options),

                                Select::make('komoditas')
                                    ->multiple()
                                    ->columnSpan(1)
                                    ->placeholder('')
                                    ->options(Komoditas::pluck('nama_komoditas', 'id')),

                                Fieldset::make('Periode')
                                    ->columnSpanFull()
                                    ->schema([
                                        DatePicker::make('start')
                                            ->required()
                                            ->columnSpanFull()
                                            ->displayFormat('d F Y')
                                            ->format('Y-m-d')
                                            ->live()
                                            ->closeOnDateSelection()
                                            ->label('Dari'),
                                        DatePicker::make('end')
                                            ->required()
                                            ->columnSpanFull()
                                            ->minDate(fn($get) => \Carbon\Carbon::parse($get('start'))->addDay())
                                            ->disabled(fn($get) => $get('start') == null ? true : false)
                                            ->displayFormat('d F Y')
                                            ->format('Y-m-d')
                                            ->closeOnDateSelection()
                                            ->label('Sampai'),
                                    ])
                            ])
                    ]),
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDistribusis::route('/'),
        ];
    }

    private static function exportData(array $data)
    {
        $type =  $data['type'];
        $transportasi = $data['transportasi'];
        $komoditas = $data['komoditas'];
        $start =  \Carbon\Carbon::parse($data['start']);
        $end =  \Carbon\Carbon::parse($data['end']);

        $query = Distribusi::query()
            ->select(['id', 'tanggal', 'komoditas_id', 'transportasi'])
            ->whereBetween('tanggal', [$start, $end])
            ->when($transportasi, function ($q) use ($transportasi) {
                return $q->whereIn('transportasi', $transportasi);
            })
            ->when($komoditas, function ($q) use ($komoditas) {
                return $q->whereIn('komoditas_id', $komoditas);
            });

        $id = $query->pluck('id')->toArray();

        if (empty($id)) {
            Notification::make()
                ->title('Gagal mencetak laporan')
                ->body('Data harga yang dipilih tidak tersedia (Kosong)')
                ->warning()
                ->send();
            return;
        }

        $controller = app()->make(ExportController::class);
        $controller->export_distribusi($id, $type);

        Notification::make()
            ->title('Laporan Sedang Diproses')
            ->body('Kami sedang menyiapkan data laporan Anda. Anda akan menerima notifikasi segera setelah laporan siap diunduh.')
            ->success()
            ->send();
    }
}
