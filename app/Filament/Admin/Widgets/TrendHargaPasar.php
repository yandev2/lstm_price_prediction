<?php

namespace App\Filament\Admin\Widgets;

use App\Models\HargaPangan;
use App\Models\Komoditas;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use App\Filament\Traits\HasChartColors;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class TrendHargaPasar extends ChartWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;
    use HasChartColors;

    protected ?string $heading = '📊 Trend Harga Pasar (Harian)';
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '350px';

    public function getDescription(): ?string
    {
        $currentDate = isset($this->pageFilters['start_date']) ? Carbon::parse($this->pageFilters['start_date'])->translatedFormat('F Y') : now()->subMonthsNoOverflow(1)->translatedFormat('F Y');
        $lastDate = isset($this->pageFilters['end_date']) ? Carbon::parse($this->pageFilters['end_date'])->translatedFormat('F Y') : now()->translatedFormat('F Y');

        return "Trend pergerakan harga dari {$currentDate} sampai {$lastDate}";
    }
    protected function getData(): array
    {
        $pasar = $this->pageFilters['pasar'] ?? null;
        $komoditas = $this->pageFilters['komoditas'] ?? null;

        $currentDate = isset($this->pageFilters['start_date'])
            ? Carbon::parse($this->pageFilters['start_date'])->startOfMonth() // Kunci ke tanggal 1 di bulan tersebut
            : now()->subMonthsNoOverflow(1)->startOfMonth();

        $lastDate = isset($this->pageFilters['end_date'])
            ? Carbon::parse($this->pageFilters['end_date'])->endOfMonth()
            : now()->endOfMonth();

        // Base query untuk Harga Pangan dengan filter pasar
        $baseQuery = HargaPangan::query()
            ->when($pasar, function ($query, $pasar) {
                return $query->where('pasar_id', $pasar);
            });

        $komoditasList = Komoditas::query()
            ->when($komoditas, function ($query, $komoditas) {
                return $query->where('id', $komoditas);
            })
            ->get();

        $trendData = DB::table('harga_pangans')
            ->when($pasar, fn($query, $pasar) => $query->where('pasar_id', $pasar))
            ->when($komoditas, fn($query, $komoditas) => $query->where('komoditas_id', $komoditas))
            ->whereBetween('tanggal', [$currentDate->startOfDay(), $lastDate->endOfDay()])
            ->selectRaw('komoditas_id, DATE(tanggal) as date, AVG(harga) as aggregate')
            ->groupBy('komoditas_id', DB::raw('DATE(tanggal)'))
            ->get()
            ->groupBy('komoditas_id');

        $period = CarbonPeriod::create($currentDate->startOfDay(), $lastDate->endOfDay());
        $labels = [];
        foreach ($period as $date) {
            $labels[] = $date->translatedFormat('d M Y');
        }

        $datasets = [];

        foreach ($komoditasList as $index => $komoditasItem) {
            $komoditasData = $trendData->get($komoditasItem->id, collect())->keyBy('date');
            
            $dataPoints = [];
            foreach ($period as $date) {
                $dateString = $date->format('Y-m-d');
                $dataPoints[] = $komoditasData->has($dateString) ? (float) $komoditasData->get($dateString)->aggregate : 0;
            }

            $color = $this->chartColors[$index % count($this->chartColors)];

            $datasets[] = [
                'label' => $komoditasItem->nama_komoditas,
                'data' => $dataPoints,
                'borderColor' => $color,

                'backgroundColor' =>$this->hexToRgba($color, 0.15), // sedikit lebih pekat

                'fill' => true, 
                'borderWidth' => 3, // Garis lebih tebal agar terlihat bold
                'tension' => 0.45, // Lengkungan lebih halus
                'cubicInterpolationMode' => 'monotone', // Mengalir lebih natural
                'pointRadius' => 0, // Titik dihilangkan saat tidak disorot agar bersih
                'pointHoverRadius' => 6, // Titik membesar saat di-hover
                'pointBackgroundColor' => '#ffffff',
                'pointBorderColor' => $color,
                'pointBorderWidth' => 2,
                'skipNull' => true
            ];
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
                duration: 1200,
                easing: 'easeOutQuart',
                loop: false
            },
            layout: {
                padding: {
                    top: 10,
                    bottom: 10
                }
            },
            scales: {
                y: {
                    beginAtZero: false, 
                    border: {
                        display: false // Hilangkan garis sumbu utama Y
                    },
                    grid: {
                        display: true,
                        color: 'rgba(156, 163, 175, 0.15)', // Warna grid lebih soft
                        drawTicks: false,
                        borderDash: [5, 5] // Grid putus-putus modern
                    },
                    ticks: {
                        padding: 10,
                        color: '#9ca3af',
                        callback: function(value) {
                            return 'Rp ' + Number(value).toLocaleString('id-ID');
                        }
                    }
                },
                x: {
                    border: {
                        display: false // Hilangkan garis sumbu utama X
                    },
                    grid: {
                        display: false 
                    },
                    ticks: {
                        autoSkip: true, 
                        maxTicksLimit: 8, // Kurangi jumlah label X agar tidak padat
                        maxRotation: 0,
                        minRotation: 0,
                        padding: 10,
                        color: '#9ca3af'
                    }
                }
            },
            plugins: {
                legend: {
                    align: 'center',
                    position: 'bottom',
                    labels: {
                        useBorderRadius: true,
                        borderRadius: 4,
                        boxWidth: 12,
                        boxHeight: 12,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.95)', // Warna slate-900 gelap elegan
                    titleColor: '#f1f5f9',
                    bodyColor: '#e2e8f0',
                    enabled: true,
                    padding: 16,
                    cornerRadius: 12,
                    bodySpacing: 8,
                    titleMarginBottom: 12,
                    boxPadding: 6,
                    intersect: false, 
                    mode: 'index', 
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            const value = Math.round(context.raw || 0);
                            return ' ' + label + 'Rp ' + Number(value).toLocaleString('id-ID');         
                        }
                    }
                }
            }
        }
        JS;

        return RawJs::make($js);
    }
}
