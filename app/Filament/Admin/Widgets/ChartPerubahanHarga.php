<?php

namespace App\Filament\Admin\Widgets;

use App\Models\HistoryHargaPangan;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Filament\Traits\HasChartColors;
use Illuminate\Database\Eloquent\Collection;

class ChartPerubahanHarga extends ChartWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;
    use HasChartColors;

    protected ?string $heading = 'Chart Perubahan Harga';
    protected ?string $maxHeight = '300px';

    protected ?Collection $cachedChartData = null;

    protected function getChartDataCollection(): Collection
    {
        if ($this->cachedChartData !== null) {
            return $this->cachedChartData;
        }

        $pasar = $this->pageFilters['pasar'] ?? null;
        $komoditas = $this->pageFilters['komoditas'] ?? null;
        $currentDate = isset($this->pageFilters['start_date']) ?  Carbon::parse($this->pageFilters['start_date']) : now()->subMonthsNoOverflow(1);
        $lastDate = isset($this->pageFilters['end_date']) ?  Carbon::parse($this->pageFilters['end_date']) : now();

        $this->cachedChartData = HistoryHargaPangan::with('komoditas')
            ->when($komoditas, function ($query, $komoditas) {
                return $query->where('komoditas_id', $komoditas);
            })
            ->when($pasar, function ($query, $pasar) {
                return $query->where('pasar_id', $pasar);
            })
            ->whereDate('tanggal_perubahan', '>=', $currentDate)
            ->whereDate('tanggal_perubahan', '<=', $lastDate)
            ->orderBy('tanggal_perubahan')
            ->get();

        return $this->cachedChartData;
    }

    public function getDescription(): ?string
    {
        $data = $this->getChartDataCollection()->groupBy('komoditas_id');

        $highestIncrease = null;
        $highestDecrease = null;

        foreach ($data as $items) {

            $latest = $items->last();

            if ($latest->harga_lama <= 0) {
                continue;
            }

            $persentase =
                (($latest->harga_baru - $latest->harga_lama)
                    / $latest->harga_lama) * 100;

            $persentase = round($persentase, 2);

            // kenaikan terbesar
            if (
                $persentase > 0 &&
                (
                    !$highestIncrease ||
                    $persentase > $highestIncrease['persentase']
                )
            ) {

                $highestIncrease = [
                    'nama' => $latest->komoditas->nama_komoditas,
                    'persentase' => $persentase,
                ];
            }

            // penurunan terbesar
            if (
                $persentase < 0 &&
                (
                    !$highestDecrease ||
                    $persentase < $highestDecrease['persentase']
                )
            ) {

                $highestDecrease = [
                    'nama' => $latest->komoditas->nama_komoditas,
                    'persentase' => $persentase,
                ];
            }
        }

        $maxNaik =
            $highestIncrease['persentase'] ?? 0;

        $maxTurun =
            abs($highestDecrease['persentase'] ?? 0);

        // Semua stabil
        if (
            $maxNaik < 2 &&
            $maxTurun < 2
        ) {

            return '🟢 Harga seluruh komoditas relatif stabil';
        }

        if ($maxTurun > $maxNaik) {

            return "📉 {$highestDecrease['nama']} mengalami penurunan terbesar sebesar {$maxTurun}%";
        }

        return "📈 {$highestIncrease['nama']} mengalami kenaikan tertinggi sebesar {$maxNaik}%";
    }


    protected function getData(): array
    {
        $data = $this->getChartDataCollection();

        $rawLabels = $data
            ->pluck('tanggal_perubahan')
            ->unique()
            ->values();

        $labels = $rawLabels
            ->map(
                fn($tanggal) =>
                Carbon::parse($tanggal)
                    ->format('d-m-Y')
            )
            ->toArray();

        $grouped = $data->groupBy(
            fn($item) => $item->komoditas->nama_komoditas
        );

        $datasets = [];
        $index = 0;

        foreach ($grouped as $namaKomoditas => $items) {

            $values = [];

            foreach ($rawLabels as $tanggal) {

                $record = $items->firstWhere(
                    'tanggal_perubahan',
                    $tanggal
                );

                if ($record && $record->harga_lama > 0) {

                    $persentase =
                        (($record->harga_baru - $record->harga_lama)
                            / $record->harga_lama) * 100;

                    $values[] = round($persentase, 2);
                } else {

                    $values[] = null;
                }
            }

            $color =
                $this->chartColors[$index % count($this->chartColors)];

            $datasets[] = [
                'fill' => true,
                'label' => $namaKomoditas,
                'data' => $values,
                'borderColor' => $color,
                'backgroundColor' => $this->hexToRgba($color, 0.3),
            ];

            $index++;
        }


        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): RawJs|array
    {
        $js = <<<'JS'
               {
                   aspectRatio: 0.8,
                   maintainAspectRatio: false,
                   responsive: true,
                   
                   animation: {
                       duration: 1000,
                       easing: 'easeOutQuart',
                       loop: false
                   },
                   scales: {
                       y: {
                           beginAtZero: true,
               
                       ticks: {
                          
                            autoSkip: true
                       },
                         grid: {
                               display: true
                           },
                       },
                       x: {
                           grid: {
                               display: false
                           },
                           ticks: {
                               autoSkip: true
                           }
                       }
                   },
                   plugins: {
                       legend: {
                           align: 'center',
                           position: 'bottom',
                           labels: {
                               useBorderRadius: true,
                               borderRadius: 3,
                               boxWidth: 17,
                               boxHeight: 17,
                               padding: 18,
                               font: {
                                   size: 10
                               }
                           }
                       },
                       tooltip: {
                           backgroundColor: 'rgba(30, 41, 59, 0.9)',
                           enabled: true,
                           padding: 12,
                           bodySpacing: 8,
                           titleMarginBottom: 10,
                           boxPadding: 5,
                           intersect: false,
                           callbacks: {
                               label: function(context) {
                                  let label = context.dataset.label || '';
                                   if (label) {
                                       label += ': ';
                                   }
                                   const prefix =context.raw < 0
                                          ? 'penurunan'
                                          : 'kenaikan';
                                    const value = prefix + ' ' + Math.abs(context.raw) + '%';
                                   return  label + value;         
                               }
                           }
                       }
                   }
               }
               JS;

        return RawJs::make($js);
    }
}
