<x-filament-widgets::widget>
    <!-- Header tanpa section box -->
    <div class="flex items-center gap-2 mb-4">
        <x-heroicon-o-presentation-chart-line class="w-5 h-5 text-primary-500" />
        <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">
            Rentang Harga Komoditas Terbaru ({{ $tanggal_terbaru ? \Carbon\Carbon::parse($tanggal_terbaru)->translatedFormat('d F Y') : '-' }})
        </h2>
    </div>

    @if(empty($stats))
        <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada data harga terbaru.</p>
    @else
        <!-- Grid 3 Kolom -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($stats as $stat)
                <div class="p-4 rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-gray-900 flex flex-col gap-3 relative shadow-sm">
                    
                    <!-- Header / Komoditas -->
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-lg tracking-tight text-gray-950 dark:text-white">{{ $stat['komoditas'] }}</h3>
                        <div class="p-2 rounded-lg bg-primary-500/10 text-primary-600 dark:text-primary-400">
                            <x-heroicon-o-currency-dollar class="w-5 h-5" />
                        </div>
                    </div>

                    <!-- Harga Rata-rata -->
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Rata-rata Harga</p>
                        <p class="text-2xl font-black text-primary-600 dark:text-primary-400">Rp {{ number_format($stat['rata_rata'], 0, ',', '.') }}</p>
                    </div>

                    <!-- Separator -->
                    <div class="w-full border-t border-gray-200 dark:border-white/10 my-1"></div>

                    <!-- Harga Tertinggi & Terendah -->
                    <div class="flex justify-between items-start">
                        <!-- Terendah -->
                        <div class="flex flex-col w-1/2 pr-2 border-r border-gray-200 dark:border-white/10">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1 mb-1">
                                <x-heroicon-m-arrow-down-right class="w-3 h-3 text-success-500" /> Terendah
                            </p>
                            <p class="font-bold text-gray-950 dark:text-white">Rp {{ number_format($stat['terendah'], 0, ',', '.') }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 leading-tight line-clamp-2" title="{{ $stat['pasar_terendah'] }}">
                                {{ $stat['pasar_terendah'] }}
                            </p>
                        </div>
                        
                        <!-- Tertinggi -->
                        <div class="flex flex-col w-1/2 pl-2 text-right items-end">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1 mb-1">
                                <x-heroicon-m-arrow-up-right class="w-3 h-3 text-danger-500" /> Tertinggi
                            </p>
                            <p class="font-bold text-gray-950 dark:text-white">Rp {{ number_format($stat['tertinggi'], 0, ',', '.') }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-0.5 leading-tight line-clamp-2" title="{{ $stat['pasar_tertinggi'] }}">
                                {{ $stat['pasar_tertinggi'] }}
                            </p>
                        </div>
                    </div>
                    
                </div>
            @endforeach
        </div>
    @endif
</x-filament-widgets::widget>
