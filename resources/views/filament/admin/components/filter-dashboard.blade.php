<style>
    /* Elevate the parent form's stacking context so dropdowns don't get trapped below widgets */
    form:has(#filter-dashboard-container) {
        position: relative;
        z-index: 50 !important;
    }
    div:has(> form:has(#filter-dashboard-container)) {
        position: relative;
        z-index: 50 !important;
    }
</style>
<div id="filter-dashboard-container" class="relative z-50 bg-white dark:bg-gray-900 rounded-2xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 p-5 md:p-6 mb-8">
    <!-- Abstract glow effects -->
    <div class="absolute inset-0 overflow-hidden rounded-2xl pointer-events-none">
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-primary-50 dark:bg-primary-500/5 rounded-full blur-3xl"></div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-info-50 dark:bg-info-500/5 rounded-full blur-3xl"></div>
    </div>
    
    <div class="relative z-10">
        <!-- Header & Action -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-5 border-b border-gray-100 dark:border-white/5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-500/10 flex items-center justify-center text-primary-600 dark:text-primary-400 ring-1 ring-primary-500/20">
                    <x-heroicon-o-funnel class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white">Filter Analitik</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Sesuaikan parameter data laporan di bawah</p>
                </div>
            </div>
            
            <button 
                type="button" 
                wire:click="$set('filters', [])" 
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-danger-50 dark:bg-danger-500/10 hover:bg-danger-100 dark:hover:bg-danger-500/20 text-danger-600 dark:text-danger-400 text-sm font-semibold rounded-lg transition-colors ring-1 ring-danger-500/20 shadow-sm"
            >
                <x-heroicon-m-arrow-path class="w-4 h-4" />
                Reset Filter
            </button>
        </div>

        <!-- Render form fields natively -->
        <div class="w-full">
            {{ $getChildComponentContainer() }}
        </div>
    </div>
</div>
