<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Komoditas;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class RingkasanHargaTerbaru extends Widget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;

    protected  string $view = 'filament.admin.widgets.ringkasan-harga-terbaru';
    protected int | string | array $columnSpan = 'full';

    public function getDescription(): ?string
    {
        $currentDate = isset($this->pageFilters['start_date']) ? Carbon::parse($this->pageFilters['start_date'])->translatedFormat('l, d F Y') : now()->subMonthsNoOverflow(1)->translatedFormat('l, d F Y');
        $lastDate = isset($this->pageFilters['end_date']) ? Carbon::parse($this->pageFilters['end_date'])->translatedFormat('l, d F Y') : now()->translatedFormat('l, d F Y');

        return "Rangkuman statistik harga dari {$currentDate} sampai {$lastDate}";
    }

    protected function getViewData(): array
    {
        $pasar = $this->pageFilters['pasar'] ?? null;
        $komoditas = $this->pageFilters['komoditas'] ?? null;
        $currentDate = isset($this->pageFilters['start_date']) ? Carbon::parse($this->pageFilters['start_date']) : now()->subMonthsNoOverflow(1);
        $lastDate = isset($this->pageFilters['end_date']) ? Carbon::parse($this->pageFilters['end_date']) : now();

        $baseQuery = Komoditas::query()
            ->when($komoditas, function ($q, $komoditas) {
                return $q->where('id', $komoditas);
            })
            ->withAvg(['hargaPangan' => function ($query) use ($currentDate, $lastDate, $pasar) {
                $query->where('tanggal', '>=', $currentDate)
                      ->where('tanggal', '<=', $lastDate)
                      ->when($pasar, fn($q, $pasar) => $q->where('pasar_id', $pasar));
            }], 'harga')
            ->withMax(['hargaPangan' => function ($query) use ($currentDate, $lastDate, $pasar) {
                $query->where('tanggal', '>=', $currentDate)
                      ->where('tanggal', '<=', $lastDate)
                      ->when($pasar, fn($q, $pasar) => $q->where('pasar_id', $pasar));
            }], 'harga')
            ->withMin(['hargaPangan' => function ($query) use ($currentDate, $lastDate, $pasar) {
                $query->where('tanggal', '>=', $currentDate)
                      ->where('tanggal', '<=', $lastDate)
                      ->when($pasar, fn($q, $pasar) => $q->where('pasar_id', $pasar));
            }], 'harga')
            ->addSelect(['harga_terbaru' => \App\Models\HargaPangan::select('harga')
                ->whereColumn('komoditas_id', 'komoditas.id')
                ->where('tanggal', '>=', $currentDate)
                ->where('tanggal', '<=', $lastDate)
                ->when($pasar, fn($q, $pasar) => $q->where('pasar_id', $pasar))
                ->orderBy('tanggal', 'desc')
                ->limit(1)
            ])
            ->addSelect(['harga_sebelumnya' => \App\Models\HargaPangan::select('harga')
                ->whereColumn('komoditas_id', 'komoditas.id')
                ->where('tanggal', '>=', $currentDate)
                ->where('tanggal', '<=', $lastDate)
                ->when($pasar, fn($q, $pasar) => $q->where('pasar_id', $pasar))
                ->orderBy('tanggal', 'desc')
                ->skip(1)
                ->limit(1)
            ])
            ->get();

        $statsData = [];
        foreach ($baseQuery as $komoditas) {

            if (is_null($komoditas->harga_pangan_avg_harga)) {
                continue;
            }

            $hargaRataRata = $komoditas->harga_pangan_avg_harga ?? 0;
            $hargaTertinggi = $komoditas->harga_pangan_max_harga ?? 0;
            $hargaTerendah = $komoditas->harga_pangan_min_harga ?? 0;

            $persentasePerubahan = 0;
            $statusPerubahan = 'stabil';

            $hargaBaru = $komoditas->harga_terbaru;
            $hargaLama = $komoditas->harga_sebelumnya;

            if (!is_null($hargaBaru) && !is_null($hargaLama) && $hargaLama > 0) {

                $persentasePerubahan = (($hargaBaru - $hargaLama) / $hargaLama) * 100;

                if ($persentasePerubahan > 0) {
                    $statusPerubahan = 'naik';
                } elseif ($persentasePerubahan < 0) {
                    $statusPerubahan = 'turun';
                }
            }

            $statsData[] = [
                'nama' => $komoditas->nama_komoditas,
                'hargaRataRata' => $hargaRataRata,
                'hargaTertinggi' => $hargaTertinggi,
                'hargaTerendah' => $hargaTerendah,
                'persentasePerubahan' => round($persentasePerubahan, 1),
                'statusPerubahan' => $statusPerubahan,
            ];
        }

        return [
            'heading' => 'Ringkasan Harga Terbaru',
            'description' => $this->getDescription(),
            'statsData' => $statsData,
        ];
    }
}
