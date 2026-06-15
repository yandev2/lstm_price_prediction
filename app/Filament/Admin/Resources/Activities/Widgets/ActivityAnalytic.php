<?php

namespace App\Filament\Admin\Resources\Activities\Widgets;

use App\Filament\Admin\Resources\Activities\Pages\ListActivities;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActivityAnalytic extends StatsOverviewWidget
{
    use InteractsWithPageTable;

    protected function getTablePage(): string
    {
        return ListActivities::class;
    }

    public function getHeading(): ?string
    {
        return 'Analytic Activity';
    }
    public function getColumns(): array|int|null
    {
        return [
            'default' => 2,
            'sm' => 2,
            'md' => 2,
            'lg' => 3,
            'xl' => 5,
            '2xl' => 5
        ];
    }
    protected function getStats(): array
    {
        $created = $this->getPageTableQuery()->where('event', 'created')->count();
        $updated = $this->getPageTableQuery()->where('event', 'updated')->count();
        $deleted = $this->getPageTableQuery()->where('event', 'deleted')->count();
        $export = $this->getPageTableQuery()->where('event', 'export')->count();

        $total   = $this->getPageTableQuery()->count();

        $datas = [
            [
                "icon" =>  Heroicon::QueueList,
                "key" => 'All',
                "value" => $total,
                "keys_quey" => null,
                "attr" => 'primary',
                "query" => null
            ],
            [
                "icon" =>  Heroicon::PlusCircle,
                "key" => 'Created',
                "value" => $created,
                "keys_quey" => null,
                "attr" => 'success',
                "query" => null
            ],
            [
                "icon" =>  Heroicon::PencilSquare,
                "key" => 'Updated',
                "value" => $updated,
                "keys_quey" => null,
                "attr" => 'info',
                "query" => null
            ],
            [
                "icon" =>  Heroicon::Trash,
                "key" => 'Deleted',
                "value" => $deleted,
                "keys_quey" => null,
                "attr" => 'danger',
                "query" => null
            ],
            [
                "icon" =>  Heroicon::Printer,
                "key" => 'Export',
                "value" => $export,
                "keys_quey" => null,
                "attr" => 'warning',
                "query" => null
            ],
        ];
        $stats = [];

        foreach ($datas as $d) {
            $stats[] =  Stat::make($d['key'], '')
                ->value($d['value'])
                ->icon($d['icon'])
                ->color($d['attr'])
                ->chartColor($d['attr'])
                ->chart([10, 10])
                ->extraAttributes(['class' => 'stats-' . $d['attr']]);
        }

        return $stats;
    }
}
