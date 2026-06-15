<?php

namespace App\Filament\Admin\Resources\HargaPangans\Widgets;

use App\Filament\Admin\Resources\HargaPangans\Pages\ListHargaPangans;
use App\Models\HargaPangan;
use App\Models\Komoditas;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SummaryHargaPangan extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected ?string $heading = 'Rentang Harga Komoditas Terbaru';
    protected function getTablePage(): string
    {
        return ListHargaPangans::class;
    }

    protected function getStats(): array
    {
        $stats = [];
        $tanggalTerbaru = HargaPangan::latest('tanggal')->value('tanggal');
        if (!$tanggalTerbaru) {
            return [];
        }

        $daftarKomoditas = Komoditas::all();

        foreach ($daftarKomoditas as $komoditas) {

            // 3. Cari Harga Tertinggi pada tanggal terbaru untuk komoditas ini
            $hargaTertinggi =  HargaPangan::where('tanggal', $tanggalTerbaru)
                ->where('komoditas_id', $komoditas->id)
                ->with('pasar')
                ->orderBy('harga', 'desc')
                ->first();

            // 4. Cari Harga Terendah pada tanggal terbaru untuk komoditas ini
            $hargaTerendah = HargaPangan::where('tanggal', $tanggalTerbaru)
                ->where('komoditas_id', $komoditas->id)
                ->with('pasar')
                ->orderBy('harga', 'asc')
                ->first();

            // JIKA pada tanggal tersebut komoditas ini memiliki data harga, buat Card Stat-nya
            if ($hargaTertinggi && $hargaTerendah) {

                $namaPasarMaju = $hargaTertinggi->pasar?->nama_pasar ?? 'Pasar Tidak Diketahui';
                $namaPasarMurah = $hargaTerendah->pasar?->nama_pasar ?? 'Pasar Tidak Diketahui';

                // Format nominal rupiah untuk tampilan yang rapi
                $formatTertinggi = 'Rp ' . number_format($hargaTertinggi->harga, 0, ',', '.');
                $formatTerendah = 'Rp ' . number_format($hargaTerendah->harga, 0, ',', '.');

                // Tentukan rata-rata harga untuk nilai utama Card (opsional, sebagai pemanis data utama)
                $hargaRataRata = HargaPangan::where('tanggal', $tanggalTerbaru)
                    ->where('komoditas_id', $komoditas->id)
                    ->avg('harga');
                $formatRataRata = 'Rp ' . number_format($hargaRataRata, 0, ',', '.');

                // 5. Masukkan ke dalam array stats menggunakan objek Stat Filament v3
                
                $stats[] = Stat::make($komoditas->nama_komoditas, $formatRataRata)
                    ->description("Tertinggi: {$formatTertinggi} ({$namaPasarMaju}) | Terendah: {$formatTerendah} ({$namaPasarMurah})")
                    ->color('warning'); // Warna jingga khas info pemda
            }
        }

        return $stats;
    }
}
