<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Distribusi;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class RingkasanJalurDistribusi extends Widget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;

    protected  string $view = 'filament.admin.widgets.ringkasan-jalur-distribusi';
    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $komoditas = $this->pageFilters['komoditas'] ?? null;
        $jalurTerpadat = Distribusi::query()
            ->when($komoditas, fn($q) => $q->where('komoditas_id', $komoditas))
            ->select('pasar_asal_id', 'pasar_tujuan_id', DB::raw('SUM(volume) as total_vol'))
            ->groupBy('pasar_asal_id', 'pasar_tujuan_id')
            ->orderBy('total_vol', 'desc')
            ->with(['pasarAsal', 'pasarTujuan'])
            ->first();

        $transportasiFavorit = Distribusi::query()
            ->when($komoditas, fn($q) => $q->where('komoditas_id', $komoditas))
            ->select('transportasi', DB::raw('count(*) as total_pakai'), DB::raw('SUM(volume) as total_vol'))
            ->groupBy('transportasi')
            ->orderBy('total_pakai', 'desc')
            ->first();

        return [
            'jalurTerpadat' => $jalurTerpadat,
            'transportasiFavorit' => $transportasiFavorit,
        ];
    }
}
