<x-filament-widgets::widget class="fi-transparent">
        
    <div class="mb-6 flex flex-col gap-1.5 px-1">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-primary-500/10 flex items-center justify-center text-primary-600 dark:text-primary-400">
                <x-heroicon-o-presentation-chart-bar class="w-5 h-5" />
            </div>
            <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                {{ $heading }}
            </h2>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 ml-10">
            {{ $description }}
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
        @forelse($statsData as $stat)
            @php
                $isNaik = $stat['statusPerubahan'] === 'naik';
                $isTurun = $stat['statusPerubahan'] === 'turun';
                
                if ($isNaik) {
                    $colorClass = 'text-danger-600 dark:text-danger-400';
                    $bgColorClass = 'bg-danger-50 dark:bg-danger-500/10';
                    $ringClass = 'ring-danger-500/20';
                    $icon = 'heroicon-m-arrow-trending-up';
                    $sign = '+';
                } elseif ($isTurun) {
                    $colorClass = 'text-success-600 dark:text-success-400';
                    $bgColorClass = 'bg-success-50 dark:bg-success-500/10';
                    $ringClass = 'ring-success-500/20';
                    $icon = 'heroicon-m-arrow-trending-down';
                    $sign = '';
                } else {
                    $colorClass = 'text-gray-500 dark:text-gray-400';
                    $bgColorClass = 'bg-gray-50 dark:bg-gray-500/10';
                    $ringClass = 'ring-gray-500/20';
                    $icon = 'heroicon-m-minus';
                    $sign = '';
                }
            @endphp

            <div class="relative flex flex-col justify-between p-5 bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden hover:shadow-md hover:ring-primary-500/30 transition duration-300 group">
                
                <!-- Glow Effect -->
                <div class="absolute -right-12 -top-12 w-32 h-32 rounded-full opacity-20 blur-2xl transition-all duration-700 group-hover:scale-150 {{ $isNaik ? 'bg-danger-500' : ($isTurun ? 'bg-success-500' : 'bg-gray-500') }}"></div>

                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 line-clamp-2 leading-tight pr-2" title="{{ $stat['nama'] }}">
                            {{ $stat['nama'] }}
                        </h3>
                        <div class="flex-shrink-0 flex items-center gap-1 px-2 py-1 rounded-md text-xs font-bold {{ $colorClass }} {{ $bgColorClass }} ring-1 {{ $ringClass }}">
                            <x-dynamic-component :component="$icon" class="w-3.5 h-3.5" />
                            <span>{{ $sign }}{{ $stat['persentasePerubahan'] }}%</span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-1 mb-5">
                        <div class="text-[11px] uppercase font-bold tracking-wider text-gray-400 dark:text-gray-500">Rata-rata</div>
                        <div class="text-2xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400 mr-0.5">Rp</span>{{ number_format($stat['hargaRataRata'], 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-white/5 grid grid-cols-2 gap-3">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-1 mb-1 text-[10px] uppercase font-bold tracking-wider text-gray-400 dark:text-gray-500">
                                <x-heroicon-m-arrow-up-right class="w-3 h-3 text-gray-400" />
                                Tertinggi
                            </div>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                {{ number_format($stat['hargaTertinggi'], 0, ',', '.') }}
                            </span>
                        </div>
                        
                        <div class="flex flex-col pl-3 border-l border-gray-100 dark:border-white/5">
                            <div class="flex items-center gap-1 mb-1 text-[10px] uppercase font-bold tracking-wider text-gray-400 dark:text-gray-500">
                                <x-heroicon-m-arrow-down-right class="w-3 h-3 text-gray-400" />
                                Terendah
                            </div>
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                {{ number_format($stat['hargaTerendah'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-12 px-4 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 ring-1 ring-gray-950/5 dark:ring-white/10">
                <div class="w-16 h-16 mb-4 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center">
                    <x-heroicon-o-inbox class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada data komoditas</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Data harga komoditas akan muncul di sini</p>
            </div>
        @endforelse
    </div>
        
</x-filament-widgets::widget>
