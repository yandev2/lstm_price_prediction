<?php

namespace App\Filament\Admin\Resources\HargaPangans\Widgets;

use App\Filament\Admin\Resources\HargaPangans\Pages\ListHargaPangans;
use App\Models\HargaPangan;
use App\Models\Komoditas;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\Widget;

class SummaryHargaPangan extends Widget
{
    use InteractsWithPageTable;

    protected  string $view = 'filament.admin.resources.harga-pangans.widgets.summary-harga-pangan';
    protected int | string | array $columnSpan = 'full';

    protected function getTablePage(): string
    {
        return ListHargaPangans::class;
    }

    protected function getViewData(): array
    {
        $stats = [];
        $tanggalTerbaru = HargaPangan::latest('tanggal')->value('tanggal');
        
        if (!$tanggalTerbaru) {
            return ['stats' => [], 'tanggal_terbaru' => null];
        }

        // Ambil SEMUA data harga pada tanggal terbaru secara bulk dalam 1 waktu
        // beserta relasi komoditas dan pasar (Eager Loading)
        $semuaHargaHariIni = HargaPangan::with(['komoditas', 'pasar'])
            ->where('tanggal', $tanggalTerbaru)
            ->get()
            ->groupBy('komoditas_id');

        foreach ($semuaHargaHariIni as $komoditasId => $hargaPanganGroup) {
            
            // Karena kita sudah eager load, kita bisa ambil relasi dari item pertama di group ini
            $komoditas = $hargaPanganGroup->first()->komoditas;

            if (!$komoditas) continue;

            // Cari Harga Tertinggi (dari RAM/Collection, bukan dari Database)
            $hargaTertinggi = $hargaPanganGroup->sortByDesc('harga')->first();
            
            // Cari Harga Terendah (dari RAM/Collection, bukan dari Database)
            $hargaTerendah = $hargaPanganGroup->sortBy('harga')->first();

            // Jika ada data tertinggi dan terendah
            if ($hargaTertinggi && $hargaTerendah) {
                
                $namaPasarMaju = $hargaTertinggi->pasar?->nama_pasar ?? 'Pasar Tidak Diketahui';
                $namaPasarMurah = $hargaTerendah->pasar?->nama_pasar ?? 'Pasar Tidak Diketahui';

                // Hitung rata-rata di RAM
                $hargaRataRata = $hargaPanganGroup->avg('harga');

                // Masukkan ke array stats
                $stats[] = [
                    'komoditas' => $komoditas->nama_komoditas,
                    'rata_rata' => $hargaRataRata,
                    'tertinggi' => $hargaTertinggi->harga,
                    'pasar_tertinggi' => $namaPasarMaju,
                    'terendah' => $hargaTerendah->harga,
                    'pasar_terendah' => $namaPasarMurah,
                ];
            }
        }

        return [
            'stats' => $stats,
            'tanggal_terbaru' => $tanggalTerbaru
        ];
    }
}
