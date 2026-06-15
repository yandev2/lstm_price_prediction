<?php

namespace App\Filament\Admin\Resources\Distribusis\Widgets;

use App\Filament\Admin\Resources\Distribusis\Pages\ManageDistribusis;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageTable;

class VolumeDistribusi extends ChartWidget
{
    use InteractsWithPageTable;

    protected ?string $heading = 'Chart Volume Distribusi';
    protected int | string | array $columnSpan = 'full';
    protected bool $isCollapsible = true;
    protected ?string $maxHeight = '230px';
    protected function getTablePage(): string
    {
        return ManageDistribusis::class;
    }
    protected function getData(): array
    {
        $distribusiData = $this->getPageTableQuery()->reorder()
            ->orderBy('tanggal', 'asc')->get();

        $groupedData = $distribusiData->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
        })->map(function ($items, $tanggal) {
            return [
                'label_tanggal' => \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y'),
                'total_volume' => $items->sum('volume'),
            ];
        });

        $labels = $groupedData->pluck('label_tanggal')->toArray();
        $volumes = $groupedData->pluck('total_volume')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Volume Distribusi',
                    'data' => $volumes,
                    'borderColor' => '#028df0',
                    'backgroundColor' => '#028df020',
                    'fill' => 'start',
                ],
            ],
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
                       min: 0,
               
                       ticks: {
                         
                           precision: 0,
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
                                   const value = context.raw;
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
