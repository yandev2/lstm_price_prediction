<?php

namespace App\Livewire;

use App\Filament\Admin\Resources\PrediksiHargas\PrediksiHargaResource;
use App\Models\PrediksiHarga;
use Filament\Widgets\Widget;

class AlertPrediksiHarga extends Widget
{
    protected string $view = 'livewire.alert-prediksi-harga';
    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $alerts = PrediksiHarga::with(['komoditas', 'pasar'])
            ->whereDate('tanggal_prediksi', today())
            ->where('status_anomali', '!=', 'normal')
            ->get()
            ->map(function ($alert) {
                $alert->url_detail = PrediksiHargaResource::getUrl('view', ['record' => $alert]);
                return $alert;
            });

        return [
            'alerts' => $alerts,
        ];
    }
}
