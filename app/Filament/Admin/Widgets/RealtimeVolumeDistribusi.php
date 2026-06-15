<?php

namespace App\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class RealtimeVolumeDistribusi extends ChartWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;

    protected ?string $heading = '🚚 Realtime Volume Distribusi';
    protected ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 'full';

    protected ?array $cachedDistributionData = null;

    protected function getDistributionData(): array
    {
        if ($this->cachedDistributionData !== null) {
            return $this->cachedDistributionData;
        }

        $komoditas = $this->pageFilters['komoditas'] ?? null;

        $dataHariIni = DB::table('distribusis')
            ->when($komoditas, function ($query, $komoditas) {
                return $query->where('komoditas_id', $komoditas);
            })
            ->whereDate('tanggal', now()->today())
            ->select(
                DB::raw('EXTRACT(HOUR FROM created_at) as jam'),
                DB::raw('SUM(volume) as total_volume')
            )
            ->groupBy('jam')
            ->pluck('total_volume', 'jam')
            ->toArray();

        // Query Kemarin
        $dataKemarin = DB::table('distribusis')
            ->when($komoditas, function ($query, $komoditas) {
                return $query->where('komoditas_id', $komoditas);
            })
            ->whereDate('tanggal', now()->yesterday())
            ->select(
                DB::raw('EXTRACT(HOUR FROM created_at) as jam'),
                DB::raw('SUM(volume) as total_volume')
            )
            ->groupBy('jam')
            ->pluck('total_volume', 'jam')
            ->toArray();

        $labels = [];
        $volumesHariIni = [];
        $volumesKemarin = [];
        $totalHariIni = 0;
        $totalKemarin = 0;

        for ($i = 0; $i < 24; $i++) {
            $labels[] = sprintf('%02d:00', $i);
            $valHariIni = isset($dataHariIni[$i]) ? round((float) $dataHariIni[$i], 2) : 0;
            $valKemarin = isset($dataKemarin[$i]) ? round((float) $dataKemarin[$i], 2) : 0;

            $volumesHariIni[] = $valHariIni;
            $volumesKemarin[] = $valKemarin;

            $totalHariIni += $valHariIni;
            $totalKemarin += $valKemarin;
        }

        // Hitung teks deskripsi secara realtime di dalam array penampung
        if ($totalKemarin > 0) {
            $selisihPersen = (($totalHariIni - $totalKemarin) / $totalKemarin) * 100;
            $selisihPersen = round($selisihPersen, 2);

            if ($selisihPersen > 0) {
                $deskripsi = "📈 Meningkat {$selisihPersen}% dibanding periode jam yang sama kemarin.";
            } elseif ($selisihPersen < 0) {
                $selisihPersenAbsolut = abs($selisihPersen);
                $deskripsi = "📉 Menurun {$selisihPersenAbsolut}% dibanding periode jam yang sama kemarin.";
            } else {
                $deskripsi = "⚪ Volume distribusi stabil (sama persis dengan kemarin).";
            }
        } else {
            $deskripsi = $totalHariIni > 0
                ? "🟢 Ada aktivitas masuk hari ini sebesar " . number_format($totalHariIni, 0, ',', '.') . " (Kemarin kosong)."
                : "⚪ Belum ada data distribusi masuk untuk hari ini maupun kemarin.";
        }

        $this->cachedDistributionData = [
            'volumesHariIni' => $volumesHariIni,
            'volumesKemarin' => $volumesKemarin,
            'labels' => $labels,
            'deskripsi' => $deskripsi,
        ];

        return $this->cachedDistributionData;
    }

    public function getDescription(): ?string
    {
        // Panggil data terpusat untuk mengambil string deskripsi terupdate
        return $this->getDistributionData()['deskripsi'];
    }

    protected function getData(): array
    {
        $distribution = $this->getDistributionData();

        return [
            'datasets' => [
                [
                    'label' => 'Hari Ini',
                    'data' => $distribution['volumesHariIni'],
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.35,
                   'borderWidth' => 1.5, 
                ],
                [
                    'label' => 'Kemarin',
                    'data' => $distribution['volumesKemarin'],
                    'borderColor' => '#94a3b8',
                    'backgroundColor' => 'transparent',
                    'borderDash' => [5, 5],
                    'tension' => 0.3,
                ],
            ],
            'labels' => $distribution['labels'],
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
            interaction: {
                mode: 'index',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(226, 232, 240, 0.5)'
                    },
                    ticks: {
                        callback: function(value) {
                            return Number(value).toLocaleString('id-ID');
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        autoSkip: true,
                        maxTicksLimit: 12, // Hanya munculkan label per 2 jam agar sumbu X bersih
                        maxRotation: 0
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    backgroundColor: 'rgba(30, 41, 59, 0.95)',
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            const value = context.raw || 0;
                            return ' ' + label + Number(value).toLocaleString('id-ID');         
                        }
                    }
                }
            }
        }
        JS;

        return RawJs::make($js);
    }
}
