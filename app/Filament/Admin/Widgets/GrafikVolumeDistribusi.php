<?php

namespace App\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class GrafikVolumeDistribusi extends ChartWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;

    protected ?string $heading = '📊 Total Volume Distribusi Bulanan (Per Hari)';
    protected int | string | array $columnSpan = 'full';
    protected ?string $maxHeight = '350px';

    protected function getData(): array
    {
        $komoditas = $this->pageFilters['komoditas'] ?? null;

        // Filter Rentang Tanggal dari Page Filter
        $currentDate = isset($this->pageFilters['start_date']) ? Carbon::parse($this->pageFilters['start_date'])->startOfDay() : now()->subMonthsNoOverflow(1)->startOfDay();
        $lastDate = isset($this->pageFilters['end_date']) ? Carbon::parse($this->pageFilters['end_date'])->endOfDay() : now()->endOfDay();

        $dataDistribusi = DB::table('distribusis')
            ->when($komoditas, fn($q) => $q->where('komoditas_id', $komoditas))
            ->whereBetween('tanggal', [$currentDate, $lastDate])
            ->select('tanggal', DB::raw('SUM(volume) as total_volume'))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'asc')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Volume Masuk/Keluar',
                    'data' => $dataDistribusi->pluck('total_volume')->toArray(),
                    'backgroundColor' => 'rgba(16, 185, 129, 0.2)',
                    'borderColor' => '#10B981',
                    'borderWidth' => 2,
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $dataDistribusi->map(fn($d) => date('d M', strtotime($d->tanggal)))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
                       min: 0,
            
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
                                   const value = context.raw ;
                                   return ' ' + label + value;         
                               }
                           }
                       }
                   }
               }
               JS;

        return RawJs::make($js);
    }
}
