<?php

namespace App\Filament\Admin\Resources\HargaPangans\Tables;

use App\Http\Controllers\ExportController;
use Filament\Notifications\Notification;

use App\Models\HargaPangan;
use App\Models\Komoditas;
use App\Models\Pasar;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class HargaPangansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('tanggal', 'desc')
            ->columns([
                TextColumn::make('no')
                    ->label('No. ')
                    ->width('sm')
                    ->rowIndex()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('createdBy.name')
                    ->label('Ditambahkan oleh')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('komoditas.nama_komoditas')
                    ->searchable(),
                TextColumn::make('pasar.nama_pasar')
                    ->searchable(),
                TextColumn::make('tanggal')
                    ->date('d F Y')
                    ->searchable(),
                TextColumn::make('harga')
                    ->money('idr', true)
                    ->searchable(),
                TextColumn::make('sumber_data')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('faktor_eksternal')
                    ->searchable()
                    ->badge()
                    ->color('info')
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
             ->filtersFormWidth('lg')
            ->filtersFormColumns(2)
            ->filters([
                SelectFilter::make('komoditas_id')
                    ->label('Komoditas')
                    ->relationship('komoditas', 'nama_komoditas')
                    ->searchable(),
                SelectFilter::make('pasar_id')
                    ->label('Pasar')
                    ->relationship('pasar', 'nama_pasar')
                    ->searchable(),
                Filter::make('rentang')
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Rentang Tanggal')
                            ->columnSpanFull()
                            ->columns(2)
                            ->live()
                            ->schema([
                                DatePicker::make('dari')
                                    ->live()
                                    ->label('Dari tanggal'),
                                DatePicker::make('sampai')
                                    ->disabled(fn(Get $get) => !$get('dari'))
                                    ->minDate(fn($get) => $get('dari'))
                                    ->label('Sampai tanggal'),
                            ])
                    ])
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['dari'] ?? null) {
                            $indicators[] = 'Dari ' . \Carbon\Carbon::parse($data['dari'])->format('d-m-Y');
                        }

                        if ($data['sampai'] ?? null) {
                            $indicators[] = 'Sampai ' . \Carbon\Carbon::parse($data['sampai'])->format('d-m-Y');
                        }

                        return $indicators;
                    })
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '>=', $date),
                            )
                            ->when(
                                $data['sampai'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal', '<=', $date),
                            );
                    })
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
            ])
            ->toolbarActions([
                Action::make('harga')
                    ->button()
                    ->label('Cetak Laporan Harga')
                    ->modalHeading(\Str::title("Cetak Laporan Harga"))
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
                                            ->label('Sampai'),
                                    ])
                            ])
                    ]),
                DeleteBulkAction::make(),
            ]);
    }

    private static function exportData(array $data)
    {
        $type =  $data['type'];
        $pasar = $data['pasar'];
        $komoditas = $data['komoditas'];
        $start =  \Carbon\Carbon::parse($data['start']);
        $end =  \Carbon\Carbon::parse($data['end']);

        $query = HargaPangan::query()
            ->select(['id', 'tanggal', 'komoditas_id', 'pasar_id'])
            ->whereBetween('tanggal', [$start, $end])
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
                ->body('Data harga yang dipilih tidak tersedia (Kosong)')
                ->warning()
                ->send();
            return;
        }

        $controller = app()->make(ExportController::class);
        $controller->export_harga_pangan($id, $type);

        Notification::make()
            ->title('Laporan Sedang Diproses')
            ->body('Kami sedang menyiapkan data laporan Anda. Anda akan menerima notifikasi segera setelah laporan siap diunduh.')
            ->success()
            ->send();
    }
}
