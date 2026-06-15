<?php

namespace App\Filament\Admin\Resources\PrediksiHargas\Tables;

use App\Http\Controllers\ExportController;
use App\Models\Komoditas;
use App\Models\Pasar;
use App\Models\PrediksiHarga;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class PrediksiHargasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultGroup(Group::make('tanggal_prediksi')
                ->label('')

                ->getTitleFromRecordUsing(function ($record): string {
                    return 'Tanggal Prediksi ' . Carbon::parse($record->tanggal_prediksi)
                        ->translatedFormat('d F Y');
                })
                ->collapsible())

            ->columns([
                TextColumn::make('komoditas.nama_komoditas')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('pasar.nama_pasar')
                    ->badge()
                    ->color('info')
                    ->searchable(),
                TextColumn::make('prediksi_harga_untuk_tanggal')
                    ->label('Untuk tanggal')
                    ->date('d F Y')
                    ->sortable(),
                TextColumn::make('harga_prediksi')
                    ->label('Prediksi harga')
                    ->money('idr', true)
                    ->sortable(),
                TextColumn::make('selisih_persen')
                    ->label('Selisih')
                    ->suffix('%')
                    ->badge()
                    ->color(fn($record) => match ($record->status_anomali) {
                        'lonjakan' => 'danger',
                        'penurunan ekstrem' => 'warning',
                        'normal' => 'success',
                        default => 'gray'
                    })
                    ->sortable(),
                TextColumn::make('status_anomali')
                    ->color(fn($record) => match ($record->status_anomali) {
                        'lonjakan' => 'danger',
                        'penurunan ekstrem' => 'warning',
                        'normal' => 'success',
                        default => 'gray'
                    })
                    ->iconColor(fn($record) => match ($record->status_anomali) {
                        'lonjakan' => 'danger',
                        'penurunan ekstrem' => 'warning',
                        'normal' => 'success',
                        default => 'gray'
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'lonjakan' => 'heroicon-m-arrow-trending-up',
                        'penurunan ekstrem' => 'heroicon-m-arrow-trending-down',
                        'normal' => 'heroicon-m-check-circle',
                        default => 'heroicon-m-minus-circle',
                    })
                    ->searchable(),
                IconColumn::make('alert_harga')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('komoditas_id')
                    ->label('Komoditas')
                    ->relationship('komoditas', 'nama_komoditas')
                    ->searchable(),
                SelectFilter::make('pasar_id')
                    ->label('Pasar')
                    ->relationship('pasar', 'nama_pasar')
                    ->searchable(),
                SelectFilter::make('status_anomali')
                    ->label('Anomali')
                    ->options([
                        'lonjakan' => "Lonjakan",
                        'penurunan ekstrem' => "Penurunan Ekstrem",
                        'normal' => "Normal"
                    ])
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),

            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
                Action::make('prediksi')
                    ->button()
                    ->label('Cetak Laporan Prediksi Harga')
                    ->modalHeading(\Str::title("Cetak Laporan Prediksi Harga"))
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

                                Select::make('pasar')
                                    ->columnSpan(1)
                                    ->placeholder('')
                                    ->multiple()
                                    ->options(Pasar::pluck('nama_pasar', 'id')),

                                Select::make('komoditas')
                                    ->columnSpan(1)
                                    ->placeholder('')
                                    ->multiple()
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
                                            ->label('Smapai'),
                                    ])
                            ])
                    ]),
            ]);
    }

    private static function exportData(array $data)
    {
        $type = $data['type'];
        $pasar = $data['pasar'];
        $komoditas = $data['komoditas'];
        $start = \Carbon\Carbon::parse($data['start']);
        $end = \Carbon\Carbon::parse($data['end']);

        $query = PrediksiHarga::query()
            ->select(['id', 'tanggal_prediksi', 'komoditas_id', 'pasar_id'])
            ->whereBetween('tanggal_prediksi', [$start, $end])
            ->when($pasar, function ($q) use ($pasar) {
                return $q->whereIn('pasar_id', $pasar);
            })
            ->when($komoditas, function ($q) use ($komoditas) {
                return $q->whereIn('komoditas_id', $komoditas);
            });

        $id = $query->pluck('id')->toArray();

        if (empty($id)) {
            Notification::make()
                ->title('Gagal mencetak laporan')
                ->body('Data 0prediksi harga yang dipilih tidak tersedia (Kosong)')
                ->warning()
                ->send();
            return;
        }

        $controller = app()->make(ExportController::class);
        $controller->export_prediksi_harga_pangan($id, $type);

        Notification::make()
            ->title('Laporan Sedang Diproses')
            ->body('Kami sedang menyiapkan data laporan Anda. Anda akan menerima notifikasi segera setelah laporan siap diunduh.')
            ->success()
            ->send();
    }
}
