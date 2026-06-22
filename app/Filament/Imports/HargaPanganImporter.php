<?php

namespace App\Filament\Imports;

use App\Models\HargaPangan;
use App\Models\Lookup;
use Carbon\Carbon;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class HargaPanganImporter extends Importer
{
    protected static ?string $model = HargaPangan::class;

    public static function getColumns(): array
    {
        // Pre-load data to prevent N+1 query issue inside the loop during import
        $komoditasList = \App\Models\Komoditas::pluck('nama_komoditas')->mapWithKeys(fn($item) => [strtolower(trim($item)) => $item])->toArray();
        $pasarList = \App\Models\Pasar::pluck('nama_pasar')->mapWithKeys(fn($item) => [strtolower(trim($item)) => $item])->toArray();
        
        $lookup = \App\Models\Lookup::where('key', 'jenis_faktor')->first();
        $validOptions = [];
        if ($lookup && $lookup->value) {
            $validOptions = is_array($lookup->value) ? $lookup->value : json_decode($lookup->value, true);
        }
        $validOptionsLower = array_map(fn($v) => strtolower(trim($v)), $validOptions);

        return [
            ImportColumn::make('komoditas')
                ->requiredMapping()
                ->relationship(resolveUsing: 'nama_komoditas')
                ->exampleHeader('komoditas')
                ->examples(['Bawang Merah'])
                ->helperText('Pastikan nama komoditas sudah terdaftar di data master.')
                ->rules(['required'])
                ->castStateUsing(function (?string $state) use ($komoditasList): ?string {
                    if (blank($state)) return null;
                    return $komoditasList[strtolower(trim($state))] ?? null;
                }),
            ImportColumn::make('pasar')
                ->requiredMapping()
                ->relationship(resolveUsing: 'nama_pasar')
                ->exampleHeader('pasar')
                ->examples(['Pasar Bukit Sulap'])
                ->helperText('Pastikan nama pasar sudah terdaftar di data master.')
                ->rules(['required'])
                ->castStateUsing(function (?string $state) use ($pasarList): ?string {
                    if (blank($state)) return null;
                    return $pasarList[strtolower(trim($state))] ?? null;
                }),
            ImportColumn::make('faktor_eksternal')
                ->exampleHeader('faktor_eksternal')
                ->examples(['Cuaca Buruk, Gagal Panen'])
                ->helperText('Pastikan faktor eksternal sudah sesuai dengan data master sistem.')
                ->castStateUsing(function (?string $state) use ($validOptions, $validOptionsLower): ?array {
                    if (blank($state)) return null;
                    
                    $inputs = array_map('trim', explode(',', $state));
                    $filtered = [];
                    
                    foreach ($inputs as $input) {
                        $idx = array_search(strtolower($input), $validOptionsLower);
                        if ($idx !== false) {
                            $filtered[] = $validOptions[$idx];
                        }
                    }

                    return empty($filtered) ? null : array_values($filtered);
                }),
            ImportColumn::make('tanggal')
                ->requiredMapping()
                ->exampleHeader('tanggal')
                ->examples(['22-06-2026'])
                ->rules(['required'])
                ->castStateUsing(function (?string $state): ?string {
                    if (blank($state)) return null;
                    try {
                        return Carbon::parse($state)->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        return null;
                    }
                }),
            ImportColumn::make('harga')
                ->requiredMapping()
                ->exampleHeader('harga')
                ->examples(['50000'])
                ->numeric()
                ->rules(['required', 'integer'])
                ->castStateUsing(function (?string $state): ?int {
                    if (blank($state)) return null;
                    // Bersihkan karakter non-numerik (seperti titik atau koma)
                    $cleaned = preg_replace('/[^0-9]/', '', $state);
                    return $cleaned !== '' ? (int) $cleaned : null;
                }),
            ImportColumn::make('sumber_data')
                ->exampleHeader('sumber_data')
                ->examples(['Survey Pasar'])
                ->rules(['max:255']),
        ];
    }

    public function resolveRecord(): HargaPangan
    {
        $record = new HargaPangan();

        if (isset($this->options['creator'])) {
            $record->created_by = $this->options['creator'];
        }

        return $record;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Proses import harga pangan telah selesai, ' . Number::format($import->successful_rows) . ' baris berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diimpor.';
        }

        return $body;
    }
}
