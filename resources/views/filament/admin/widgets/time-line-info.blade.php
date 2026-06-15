<x-filament-widgets::widget>
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
        {{-- Header Section dengan aksen border-left --}}
        <div class="relative p-6 flex gap-4 items-start border-l-4 border-primary-600 bg-gradient-to-r from-primary-50 to-transparent dark:from-primary-950/20">
            
            {{-- Icon Container --}}
            <div class="flex-shrink-0 p-3 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-primary-100 dark:border-primary-700">
                <x-heroicon-o-shield-check class="w-6 h-6 text-primary-600 dark:text-primary-400" />
            </div>

            {{-- Text Content --}}
            <div class="space-y-1">
                <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 tracking-wide uppercase">
                    Log Riwayat Perubahan Harga
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    Seluruh catatan perubahan harga pangan tersimpan secara 
                    <span class="font-semibold text-primary-700 dark:text-primary-400 underline decoration-primary-300 underline-offset-4">permanen</span>. 
                    Dokumen audit vital ini tidak dapat diubah atau dihapus untuk menjamin akurasi pelacakan data.
                </p>
            </div>

            {{-- Badge Penanda (Optional) --}}
            <div class="absolute top-4 right-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-900/50 dark:text-primary-300">
                    Audit Log
                </span>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>