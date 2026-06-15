<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\AlertHargaPangan;
use App\Filament\Admin\Widgets\TrendHargaPasar;
use App\Filament\Admin\Widgets\RealtimeVolumeDistribusi;
use App\Filament\Admin\Widgets\HargaPanganRealtimeTable;
use App\Filament\Admin\Widgets\TabelAlurDistribusi;
use App\Filament\Admin\Widgets\GrafikVolumeDistribusi;
use App\Filament\Admin\Widgets\WelcomeBanner;

use App\Filament\Admin\Widgets\RingkasanHargaTerbaru;
use App\Filament\Admin\Widgets\RingkasanJalurDistribusi;
use App\Filament\Admin\Widgets\TabelPrediksiHarga;
use App\Models\Komoditas;
use App\Models\Pasar;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class DashboardPage extends Dashboard
{
    use HasFiltersForm;

    public function getHeaderWidgets(): array
    {
        return [
            WelcomeBanner::class,
            AlertHargaPangan::class,
        ];
    }
    public function getWidgets(): array
    {
        return [

            RingkasanHargaTerbaru::class,
            RingkasanJalurDistribusi::class,
            TrendHargaPasar::class,
            RealtimeVolumeDistribusi::class,
            GrafikVolumeDistribusi::class,
            HargaPanganRealtimeTable::class,
            TabelAlurDistribusi::class,
            TabelPrediksiHarga::class
        ];
    }
    public function getFooterWidgets(): array
    {
        return [];
    }

    public function filtersForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Schemas\Components\Group::make()
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                        'lg' => 4,
                    ])
                    ->columnSpanFull()
                    ->schema([
                        Select::make('komoditas')
                            ->label('Komoditas Pangan')
                            ->live()
                            ->options(Komoditas::pluck('nama_komoditas', 'id'))
                            ->afterStateUpdated(fn($state) => $this->filters['komoditas'] = $state),

                        Select::make('pasar')
                            ->label('Pilih Pasar')
                            ->live()
                            ->options(Pasar::pluck('nama_pasar', 'id'))
                            ->afterStateUpdated(fn($state) => $this->filters['pasar'] = $state),

                        DatePicker::make('start_date')
                            ->label('Dari Tanggal')
                            ->default(now()->subMonthsNoOverflow(1))
                            ->prefixIconColor('info')
                            ->maxDate(fn($get) => Carbon::parse($get('end_date')))
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                $this->filters['start_date'] = $state;
                            }),

                        DatePicker::make('end_date')
                            ->label('Sampai Tanggal')
                            ->minDate(fn($get) => Carbon::parse($get('start_date')))
                            ->default(now())
                            ->prefixIconColor('info')
                            ->live()
                            ->afterStateUpdated(fn($state) => $this->filters['end_date'] = $state),
                    ])
                    ->view('filament.admin.components.filter-dashboard')
            ]);
    }

    public function boot()
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn(): string => Blade::render($this->getAlert())
        );
    }
    protected function getAlert(): ?string
    {
        if (!session('show_alert_modal')) {
            return null;
        }

        session()->forget('show_alert_modal');
        if (!auth()->user()?->can('ViewAny:PrediksiHarga') && !auth()->user()?->can('View:PrediksiHarga')) {
            return null;
        }

        $hasAlerts = \App\Models\PrediksiHarga::whereDate('tanggal_prediksi', today(config('app.timezone')))
            ->where('status_anomali', '!=', 'normal')
            ->exists();

        if (!$hasAlerts) {
            return null;
        }

        return Blade::render('
        <script>
            document.addEventListener("livewire:navigated", () => {
                window.dispatchEvent(new CustomEvent("open-modal", { detail: { id: "anomali-harga-modal" } }));
            });
        </script>
        
        <x-filament::modal id="anomali-harga-modal" width="5xl">
            <x-slot name="heading">
                🚨 Peringatan Prediksi Anomali Harga Pangan Besok
            </x-slot>
        
            @livewire(\App\Livewire\AlertPrediksiHarga::class)
        
            <x-slot name="footer">
                <div class="flex justify-end">
                    <x-filament::button color="gray" x-on:click="$dispatch(\'close-modal\', { id: \'anomali-harga-modal\' })">
                        Tutup Peringatan
                    </x-filament::button>
                </div>
            </x-slot>
        </x-filament::modal>
        ');
    }
}
