<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Distribusi;
use App\Models\HargaPangan;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class AlertHargaPangan extends Widget
{
    use HasWidgetShield;
    protected string $view = 'filament.admin.widgets.alert-harga-pangan';
    protected int|string|array $columnSpan = 'full';

    public array $alerts = [];

    public static function canView(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }
        $permissionName = 'View:AlertHargaPangan';
        return $user->can($permissionName) && HargaPangan::exists();
    }

    public function mount(): void
    {
        $this->alerts = $this->generateAlerts();
    }

    protected function generateAlerts(): array
    {
        $results = [];

        // =========================================================================
        // RADAR 1 & 4: DETEKSI FLUKTUASI HARGA MIKRO (PER TITIK PASAR)
        // =========================================================================
        $komoditasTerbaru = HargaPangan::query()
            ->with(['komoditas', 'pasar'])
            ->whereIn('id', function ($query) {
                $query->select(DB::raw('MAX(id)'))
                    ->from('harga_pangans')
                    ->groupBy(['pasar_id', 'komoditas_id']);
            })
            ->get();

        if ($komoditasTerbaru->isNotEmpty()) {
            $latestIds = $komoditasTerbaru->pluck('id')->toArray();
            
            $previousPricesIds = HargaPangan::select(DB::raw('MAX(id) as max_id'))
                ->whereNotIn('id', $latestIds)
                ->groupBy(['pasar_id', 'komoditas_id'])
                ->pluck('max_id')->toArray();

            $previousPrices = HargaPangan::whereIn('id', $previousPricesIds)
                ->get()
                ->keyBy(function ($item) {
                    return $item->pasar_id . '_' . $item->komoditas_id;
                });
        } else {
            $previousPrices = collect();
        }

        foreach ($komoditasTerbaru as $record) {
            $key = $record->pasar_id . '_' . $record->komoditas_id;
            $hargaSebelumnya = $previousPrices->has($key) ? $previousPrices->get($key)->harga : 0;

            if ($hargaSebelumnya > 0) {
                $persentase = (($record->harga - $hargaSebelumnya) / $hargaSebelumnya) * 100;

                // RADAR 1: JIKA HARGA MELONJAK (>= 5%) -> DANGER (MERAH)
                if ($persentase >= 5) {
                    $results[] = [
                        'tipe' => 'lonjakan',
                        'status' => 'DANGER',
                        'nama' => $record->komoditas?->nama_komoditas ?? 'Komoditas',
                        'lokasi' => $record->pasar?->nama_pasar ?? 'Pasar',
                        'pesan' => "Harga " . ($record->komoditas?->nama_komoditas) . " melonjak " . round($persentase, 1) . "% di " . ($record->pasar?->nama_pasar) . "!",
                        'keterangan' => "Harga saat ini Rp " . number_format($record->harga, 0, ',', '.') . " (Sebelumnya Rp " . number_format($hargaSebelumnya, 0, ',', '.') . "). Butuh intervensi operasi pasar segera.",
                        'color' => 'red',
                        'icon' => 'heroicon-m-exclamation-triangle',
                    ];
                }

                // RADAR 4 (BARU): JIKA HARGA ANJLOK DRASTIS (<= -5%) -> WARNING (AMBER)
                elseif ($persentase <= -5) {
                    $results[] = [
                        'tipe' => 'anjlok',
                        'status' => 'WARNING',
                        'nama' => $record->komoditas?->nama_komoditas ?? 'Komoditas',
                        'lokasi' => $record->pasar?->nama_pasar ?? 'Pasar',
                        'pesan' => "Harga " . ($record->komoditas?->nama_komoditas) . " anjlok drastis " . round(abs($persentase), 1) . "% di " . ($record->pasar?->nama_pasar) . ".",
                        'keterangan' => "Harga drop menjadi Rp " . number_format($record->harga, 0, ',', '.') . " (Sebelumnya Rp " . number_format($hargaSebelumnya, 0, ',', '.') . "). Indikasi oversupply atau kendala serapan pasar lokal, berisiko merugikan petani.",
                        'color' => 'amber',
                        'icon' => 'heroicon-m-arrow-trending-down',
                    ];
                }
            }
        }

        // =========================================================================
        // RADAR 2: DETEKSI GANGGUAN PASOKAN LOGISTIK (DISTRIBUSI DROP)
        // =========================================================================
        $volHariIni = Distribusi::whereDate('tanggal', now()->today())->sum('volume') ?? 0;
        $volKemarin = Distribusi::whereDate('tanggal', now()->yesterday())->sum('volume') ?? 0;

        if ($volKemarin > 0) {
            $dropPersen = (($volKemarin - $volHariIni) / $volKemarin) * 100;
            if ($dropPersen >= 30) {
                $results[] = [
                    'tipe' => 'distribusi',
                    'status' => 'WARNING',
                    'nama' => 'Logistik Pangan',
                    'lokasi' => 'Jalur Distribusi Daerah',
                    'pesan' => "Arus mobilisasi pasokan pangan menurun drastis sebesar " . round($dropPersen, 1) . "% hari ini!",
                    'keterangan' => "Total volume masuk hanya " . number_format($volHariIni, 0, ',', '.') . " Kg dibanding kemarin (" . number_format($volKemarin, 0, ',', '.') . " Kg). Indikasi hambatan rute logistik.",
                    'color' => 'amber',
                    'icon' => 'heroicon-m-truck',
                ];
            }
        }

        // =========================================================================
        // RADAR 3: KONDISI KESELURUHAN NORMAL (HIJAU)
        // =========================================================================
        if (empty($results)) {
            $results[] = [
                'tipe' => 'stabil',
                'status' => 'NORMAL',
                'nama' => 'Sistem Pangan',
                'lokasi' => 'Seluruh Wilayah',
                'pesan' => "Sistem Stabilitas Pangan Kondisi Aman & Stabil",
                'keterangan' => "Radar E-PANGKAL tidak mendeteksi adanya anomali lonjakan harga ekstrem maupun kelangkaan pasokan distribusi hari ini.",
                'color' => 'green',
                'icon' => 'heroicon-m-check-circle',
            ];
        }

        return $results;
    }
}
