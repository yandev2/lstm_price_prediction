<x-filament::widget>
    @if($alerts->count() > 0)
    <div class="space-y-3">
        @foreach($alerts as $alert)
        <div
            class="p-4 border-l-4 rounded-lg bg-danger-50 border-danger-500 dark:bg-danger-950/50 dark:border-danger-600">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div
                        class="p-2 rounded-full bg-danger-100 text-danger-600 dark:bg-danger-900/50 dark:text-danger-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>

                    <div>
                        <h4 class="text-sm font-bold text-danger-800 dark:text-danger-400">
                            Peringatan Anomali Harga! Status: <span class="uppercase">{{ $alert->status_anomali
                                }}</span>
                        </h4>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                            Komoditas <strong class="text-gray-900 dark:text-white">{{ $alert->komoditas->nama_komoditas
                                }}</strong>
                            di <strong class="text-gray-900 dark:text-white">{{ $alert->pasar->nama_pasar }}</strong>
                            terdeteksi mengalami selisih sebesar <span class="font-semibold text-danger-600">{{
                                $alert->selisih_persen }}%</span>.
                            Prediksi harga untuk tanggal {{
                            \Carbon\Carbon::parse($alert->prediksi_harga_untuk_tanggal)->format('d M Y') }} adalah
                            <strong>Rp {{ number_format($alert->harga_prediksi, 0, ',', '.') }}</strong>.
                        </p>
                        @if($alert->alert_harga)
                        <p class="mt-1 text-xs italic text-gray-500 dark:text-gray-400">
                            Catatan: {{ $alert->alert_harga }}
                        </p>
                        @endif
                    </div>
                </div>

                <x-filament::button tag="a" href="{{ $alert->url_detail }}" color="danger" size="sm"
                    icon="heroicon-m-eye">
                    Lihat
                </x-filament::button>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</x-filament::widget>