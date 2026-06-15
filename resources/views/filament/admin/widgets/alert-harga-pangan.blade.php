<x-filament-widgets::widget>
    <div class="grid grid-cols-1 gap-4">
        @foreach ($this->alerts as $alert)
            <div class="relative overflow-hidden rounded-xl border p-5 shadow-sm transition-all duration-300 
                @if($alert['color'] === 'red') bg-red-50/80 border-red-200 dark:bg-red-950/20 dark:border-red-900/50 
                @elseif($alert['color'] === 'amber') bg-amber-50/80 border-amber-200 dark:bg-amber-950/20 dark:border-amber-900/50 
                @else bg-green-50/80 border-green-200 dark:bg-green-950/20 dark:border-green-900/50 @endif">
                
                {{-- Efek Animasi Denyut Sinyal Bahaya/Peringatan --}}
                @if($alert['status'] !== 'NORMAL')
                    <span class="absolute top-4 right-4 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 
                            @if($alert['color'] === 'red') bg-red-500 @else bg-amber-500 @endif"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 
                            @if($alert['color'] === 'red') bg-red-600 @else bg-amber-600 @endif"></span>
                    </span>
                @endif

                <div class="flex items-start gap-4">
                    {{-- Bagian Ikon Samping --}}
                    <div class="p-2 rounded-lg 
                        @if($alert['color'] === 'red') bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400
                        @elseif($alert['color'] === 'amber') bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400
                        @else bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-400 @endif">
                        @svg($alert['icon'], 'h-6 w-6')
                    </div>

                    {{-- Isi Pesan Utama --}}
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold stream-badge uppercase tracking-wider px-2 py-0.5 rounded-full
                                @if($alert['color'] === 'red') bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-300
                                @elseif($alert['color'] === 'amber') bg-amber-200 text-amber-800 dark:bg-amber-900 dark:text-amber-300
                                @else bg-green-200 text-green-800 dark:bg-green-900 dark:text-green-300 @endif">
                                {{ $alert['status'] }}
                            </span>
                        </div>
                        
                        <h3 class="text-base font-bold mt-2 text-gray-900 dark:text-white">
                            {{ $alert['pesan'] }}
                        </h3>
                        
                        <p class="text-sm mt-1 text-gray-600 dark:text-gray-400">
                            {{ $alert['keterangan'] }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>