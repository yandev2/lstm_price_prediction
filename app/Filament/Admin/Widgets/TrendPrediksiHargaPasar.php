<?php

namespace App\Filament\Admin\Widgets;

use App\Models\PrediksiHarga;
use App\Models\Komoditas;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use App\Filament\Traits\HasChartColors;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonPeriod;

class TrendPrediksiHargaPasar extends ChartWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;
    use HasChartColors;

    protected ?string $heading = 'Prediksi Harga Pangan';
    

    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '350px';

    public function getDescription(): ?string
    {
        $currentDate = isset($this->pageFilters['start_date']) ? Carbon::parse($this->pageFilters['start_date'])->translatedFormat('d M Y') : now()->translatedFormat('d M Y');
        $lastDate = isset($this->pageFilters['end_date']) ? Carbon::parse($this->pageFilters['end_date'])->translatedFormat('d M Y') : now()->addDays(7)->translatedFormat('d M Y');

        return "Trend prediksi harga dari {$currentDate} sampai {$lastDate}";
    }

    protected function getData(): array
    {
        $pasar = $this->pageFilters['pasar'] ?? null;
        $komoditas = $this->pageFilters['komoditas'] ?? null;

        $currentDate = isset($this->pageFilters['start_date'])
            ? Carbon::parse($this->pageFilters['start_date'])->startOfDay()
            : now()->startOfDay();

        // Karena ini adalah chart "Prediksi" (ke depan), kita otomatis tambahkan 7 hari dari filter end_date
        $lastDate = isset($this->pageFilters['end_date'])
            ? Carbon::parse($this->pageFilters['end_date'])->addDays(7)->endOfDay()
            : now()->addDays(7)->endOfDay();

        $komoditasList = Komoditas::query()
            ->when($komoditas, function ($query, $komoditas) {
                return $query->where('id', $komoditas);
            })
            ->get();

        $trendData = DB::table('prediksi_hargas')
            ->when($pasar, fn($query, $pasar) => $query->where('pasar_id', $pasar))
            ->when($komoditas, fn($query, $komoditas) => $query->where('komoditas_id', $komoditas))
            ->whereBetween('prediksi_harga_untuk_tanggal', [$currentDate->format('Y-m-d'), $lastDate->format('Y-m-d')])
            ->selectRaw('komoditas_id, DATE(prediksi_harga_untuk_tanggal) as date, AVG(harga_prediksi) as aggregate')
            ->groupBy('komoditas_id', DB::raw('DATE(prediksi_harga_untuk_tanggal)'))
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
                $dataPoints[] = $komoditasData->has($dateString) ? (float) $komoditasData->get($dateString)->aggregate : null;
            }

            $color = $this->chartColors[$index % count($this->chartColors)];

            $datasets[] = [
                'label' => 'Prediksi ' . $komoditasItem->nama_komoditas,
                'data' => $dataPoints,
                'borderColor' => $color,
                'backgroundColor' => $this->hexToRgba($color, 0.15),
                'fill' => false, 
                'borderWidth' => 3, 
                'borderDash' => [5, 5], // Garis putus-putus untuk menandakan prediksi
                'tension' => 0.45, 
                'cubicInterpolationMode' => 'monotone', 
                'pointRadius' => 3, 
                'pointHoverRadius' => 6, 
                'pointBackgroundColor' => '#ffffff',
                'pointBorderColor' => $color,
                'pointBorderWidth' => 2,
                'spanGaps' => true,
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
                        display: false
                    },
                    grid: {
                        display: true,
                        color: 'rgba(156, 163, 175, 0.15)',
                        drawTicks: false,
                        borderDash: [5, 5]
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
                        display: false
                    },
                    grid: {
                        display: false 
                    },
                    ticks: {
                        autoSkip: true, 
                        maxTicksLimit: 8,
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
                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
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
