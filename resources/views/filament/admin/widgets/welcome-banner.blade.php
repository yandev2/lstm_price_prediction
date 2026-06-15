<x-filament-widgets::widget>
    @php
    $data = $this->getUserData();
    @endphp

    <div
        class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-primary-600 to-primary-500 p-8 shadow-lg dark:from-primary-900 dark:to-primary-800">
        {{-- Elemen Dekorasi Abstrak (Background) --}}
        <div class="absolute -right-10 -top-10 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -left-10 -bottom-10 h-64 w-64 rounded-full bg-primary-400/20 blur-3xl"></div>

        <div class="relative z-10">
            {{-- Header: Tanggal & Logout --}}
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2 text-primary-100 font-medium text-sm tracking-wide">
                    <x-heroicon-m-calendar class="h-4 w-4" />
                    {{ $data['date'] }}
                </div>

                {{-- Tombol Logout Pojok Kanan Atas --}}
                <form action="{{ route('filament.admin.auth.logout') }}" method="post" class="inline">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-white backdrop-blur-md transition-all hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/50">
                        <span>Keluar</span>
                        <x-heroicon-m-arrow-right-on-rectangle class="h-4 w-4" />
                    </button>
                </form>
            </div>

            {{-- Body: Ucapan Selamat Datang --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-6">
                    {{-- Avatar Simbolis --}}
                    <div class="hidden sm:block">
                        @if($data['avatar'])
                        {{-- Jika Foto Avatar Tersedia --}}
                        <img src="{{ $data['avatar'] }}" alt="Avatar {{ $data['name'] }}"
                            class="h-20 w-20 rounded-2xl object-cover border border-white/30 shadow-lg backdrop-blur-lg bg-white/10"
                            onerror="this.style.display='none'; document.getElementById('fallback-avatar').style.display='flex';">

                        {{-- Cadangan tak terlihat jika sewaktu-waktu link gambar corrupt/error --}}
                        <div id="fallback-avatar" style="display: none;"
                            class="h-20 w-20 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-lg border border-white/30 shadow-inner">
                            <x-heroicon-o-user class="h-10 w-10 text-white" />
                        </div>
                        @else
                        {{-- Jika Avatar Kosong, Tampilkan Icon Bawaan Anda --}}
                        <div
                            class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-lg border border-white/30 shadow-inner">
                            <x-heroicon-o-user class="h-10 w-10 text-white" />
                        </div>
                        @endif
                    </div>

                    <div>
                        <p
                            class="text-primary-100 text-lg font-medium opacity-90 uppercase tracking-widest text-xs mb-1">
                            {{ $data['time_greeting'] }},
                        </p>
                        <h2 class="text-3xl font-bold text-white tracking-tight md:text-4xl">
                            {{ $data['name'] }}
                        </h2>
                        <div class="mt-2 flex items-center gap-3">
                            <span
                                class="inline-flex items-center rounded-md bg-white/20 px-2 py-1 text-xs font-bold uppercase tracking-wider text-white backdrop-blur-md border border-white/20">
                                {{ $data['role'] }}
                            </span>
                            <span class="h-1 w-1 rounded-full bg-white/40"></span>
                            <span class="text-primary-100 text-sm font-medium italic">Otoritas Akses Sistem
                                E-PANGKAL</span>
                        </div>
                    </div>
                </div>

                {{-- Status Sistem Quick-View (Opsional) --}}
                <div class="hidden lg:block border-l border-white/20 pl-8">
                    <p class="text-primary-100 text-xs font-bold uppercase tracking-widest opacity-70 mb-2">Status
                        Sistem</p>
                    <div class="flex items-center gap-2 text-white font-bold text-lg">
                        <span class="relative flex h-3 w-3">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        Monitoring Aktif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>