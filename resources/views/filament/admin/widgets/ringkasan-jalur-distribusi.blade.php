<x-filament-widgets::widget class="fi-transparent">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
        
        {{-- Stat 1: Jalur Distribusi Utama --}}
        <div class="relative flex flex-col justify-between p-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden group transition duration-300 hover:shadow-md hover:ring-primary-500/30">
            <!-- Background Icon -->
            <div class="absolute -right-6 -top-6 text-primary-500/5 dark:text-primary-400/5 transition-transform duration-500 group-hover:scale-110 group-hover:rotate-12">
                <x-heroicon-o-map class="w-40 h-40" />
            </div>
            
            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center justify-center w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl text-primary-600 dark:text-primary-400 ring-1 ring-primary-500/20">
                        <x-heroicon-o-map class="w-5 h-5" />
                    </div>
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 tracking-wide uppercase">Jalur Distribusi Utama</h3>
                </div>
                
                @if($jalurTerpadat)
                    <div class="flex flex-col">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-col w-[40%]">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Pasar Asal</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white truncate" title="{{ $jalurTerpadat->pasarAsal->nama_pasar ?? 'N/A' }}">
                                    {{ $jalurTerpadat->pasarAsal->nama_pasar ?? 'N/A' }}
                                </span>
                            </div>
                            
                            <div class="flex flex-col items-center px-2 w-[20%]">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-50 dark:bg-primary-900/50 relative">
                                    <div class="absolute inset-0 rounded-full bg-primary-400/20 animate-ping"></div>
                                    <x-heroicon-o-arrow-right class="w-4 h-4 text-primary-600 dark:text-primary-400 relative z-10" />
                                </div>
                            </div>

                            <div class="flex flex-col text-right w-[40%]">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Pasar Tujuan</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white truncate" title="{{ $jalurTerpadat->pasarTujuan->nama_pasar ?? 'N/A' }}">
                                    {{ $jalurTerpadat->pasarTujuan->nama_pasar ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-white/5 flex items-center justify-between">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Volume Mobilisasi</span>
                            <div class="flex items-center gap-1.5 bg-primary-50 dark:bg-primary-500/10 px-2.5 py-1 rounded-lg ring-1 ring-primary-500/20">
                                <x-heroicon-m-chart-bar class="w-4 h-4 text-primary-600 dark:text-primary-400" />
                                <span class="text-sm font-bold text-primary-600 dark:text-primary-400">
                                    {{ number_format($jalurTerpadat->total_vol, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-center h-24">
                        <div class="text-lg font-medium text-gray-400 dark:text-gray-500">Belum ada data</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Stat 2: Transportasi Utama --}}
        <div class="relative flex flex-col justify-between p-6 bg-white dark:bg-gray-900 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden group transition duration-300 hover:shadow-md hover:ring-info-500/30">
            <!-- Background Icon -->
            <div class="absolute -right-6 -top-6 text-info-500/5 dark:text-info-400/5 transition-transform duration-500 group-hover:scale-110 group-hover:-rotate-12">
                <x-heroicon-o-truck class="w-40 h-40" />
            </div>

            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-info-50 dark:bg-info-500/10 rounded-xl text-info-600 dark:text-info-400 ring-1 ring-info-500/20">
                            <x-heroicon-o-truck class="w-5 h-5" />
                        </div>
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 tracking-wide uppercase">Transportasi Utama</h3>
                    </div>
                    
                    @if($transportasiFavorit)
                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-medium bg-green-50 dark:bg-green-500/10 text-green-600 dark:text-green-400 border border-green-200 dark:border-green-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                            Dominan
                        </span>
                    @endif
                </div>
                
                @if($transportasiFavorit)
                    <div class="flex flex-col h-full justify-between">
                        <div class="flex flex-col h-[52px] justify-center">
                            <div class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white capitalize truncate" title="{{ $transportasiFavorit->transportasi }}">
                                {{ $transportasiFavorit->transportasi }}
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Moda transportasi paling sering digunakan</span>
                        </div>
                        
                        <div class="mt-6 pt-4 mb-4 border-t border-gray-100 dark:border-white/5 flex flex-col gap-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Frekuensi Pengiriman</span>
                                <div class="flex items-center gap-1.5 text-sm font-bold text-gray-900 dark:text-white">
                                    <x-heroicon-m-arrow-path class="w-4 h-4 text-info-500" />
                                    {{ number_format($transportasiFavorit->total_pakai, 0, ',', '.') }} <span class="text-xs font-normal text-gray-500">kali</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Volume Angkut</span>
                                <div class="flex items-center gap-1.5 text-sm font-bold text-info-600 dark:text-info-400 bg-info-50 dark:bg-info-500/10 px-2 py-1 rounded-md ring-1 ring-info-500/20">
                                    <x-heroicon-m-cube class="w-4 h-4" />
                                    {{ number_format($transportasiFavorit->total_vol ?? 0, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-center h-24">
                        <div class="text-lg font-medium text-gray-400 dark:text-gray-500">Belum ada data</div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</x-filament-widgets::widget>
