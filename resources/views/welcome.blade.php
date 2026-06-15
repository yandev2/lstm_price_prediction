<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Prediksi Harga Pangan (LSTM) - Kota Lubuklinggau</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
    @endif

    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        .dark .glass-panel {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .blob {
            position: absolute;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.5;
            animation: float 10s infinite ease-in-out alternate;
        }
        @keyframes float {
            0% { transform: translateY(0px) scale(1); }
            100% { transform: translateY(-40px) scale(1.1); }
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body class="antialiased bg-slate-50 dark:bg-[#0a0a0a] text-slate-800 dark:text-slate-200 overflow-x-hidden selection:bg-blue-500 selection:text-white">

    <!-- Background Blobs -->
    <div class="blob bg-blue-400 dark:bg-blue-600 w-[500px] h-[500px] rounded-full top-[-10%] left-[-10%]"></div>
    <div class="blob bg-purple-400 dark:bg-purple-600 w-[400px] h-[400px] rounded-full top-[20%] right-[-10%]" style="animation-delay: -5s;"></div>

    <!-- Navigation -->
    <nav class="fixed w-full z-50 glass-panel transition-all duration-300 shadow-sm dark:shadow-none">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3 cursor-pointer group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div class="font-bold text-xl tracking-tight text-slate-900 dark:text-white">
                    E-PANGKAL <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-500">AI</span>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/app') }}" class="px-6 py-2.5 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-medium text-sm hover:scale-105 hover:shadow-xl transition-all duration-300">
                            Masuk Dashboard
                        </a>
                    @else
                        <a href="{{ url('/app/login') }}" class="px-6 py-2.5 rounded-full bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-medium text-sm hover:scale-105 hover:shadow-xl transition-all duration-300">
                            Login Admin
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 px-6 min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
            
            <!-- Text Content -->
            <div class="space-y-8 relative z-10">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-panel border-blue-200 dark:border-blue-500/30 text-blue-700 dark:text-blue-300 text-sm font-medium animate-fade-in-up">
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-600"></span>
                    </span>
                    Sistem Pemantauan Aktif Kota Lubuklinggau
                </div>
                
                <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.1] animate-fade-in-up" style="animation-delay: 100ms;">
                    Prediksi Harga <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">Lebih Cerdas & Akurat</span>
                </h1>
                
                <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed max-w-xl animate-fade-in-up" style="animation-delay: 200ms;">
                    Memanfaatkan teknologi <strong>Long Short-Term Memory (LSTM)</strong> untuk memprediksi fluktuasi harga komoditas pangan di pasar-pasar Kota Lubuklinggau. Jaga stabilitas ekonomi daerah dengan analisa data yang canggih.
                </p>
                
                <div class="flex flex-wrap gap-4 pt-4 animate-fade-in-up" style="animation-delay: 300ms;">
                    <a href="{{ url('/app') }}" class="px-8 py-4 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2">
                        Mulai Monitoring
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                    <a href="#fitur" class="px-8 py-4 rounded-full glass-panel font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-300">
                        Pelajari Fitur
                    </a>
                </div>
            </div>

            <!-- Dashboard Mockup Illustration -->
            <div class="relative z-10 animate-fade-in-up" style="animation-delay: 400ms;">
                <div class="relative rounded-2xl glass-panel p-2 shadow-2xl border border-white/60 dark:border-white/10 transform rotate-1 hover:rotate-0 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-tr from-blue-500/10 to-purple-500/10 rounded-2xl animate-pulse"></div>
                    <div class="bg-white dark:bg-slate-900 rounded-xl overflow-hidden shadow-inner border border-slate-200 dark:border-slate-800 relative z-10">
                        <!-- Mac-like Header -->
                        <div class="bg-slate-100 dark:bg-[#111] px-4 py-3 flex items-center gap-2 border-b border-slate-200 dark:border-slate-800">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            <div class="ml-4 text-xs font-medium text-slate-400">LSTM Prediction Dashboard</div>
                        </div>
                        <!-- Mockup Content -->
                        <div class="p-6 space-y-6">
                            <div class="flex justify-between items-center">
                                <div class="space-y-2">
                                    <div class="h-5 w-32 bg-slate-200 dark:bg-slate-800 rounded-md"></div>
                                    <div class="h-3 w-24 bg-slate-100 dark:bg-slate-800/50 rounded-md"></div>
                                </div>
                                <div class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full text-xs font-bold flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                    AI Active
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="h-24 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700/50 p-4 flex flex-col justify-between">
                                    <div class="h-2 w-12 bg-slate-200 dark:bg-slate-700 rounded"></div>
                                    <div class="h-5 w-20 bg-slate-800 dark:bg-slate-300 rounded"></div>
                                </div>
                                <div class="h-24 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700/50 p-4 flex flex-col justify-between">
                                    <div class="h-2 w-16 bg-slate-200 dark:bg-slate-700 rounded"></div>
                                    <div class="h-5 w-24 bg-slate-800 dark:bg-slate-300 rounded"></div>
                                </div>
                                <div class="h-24 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700/50 p-4 flex flex-col justify-between">
                                    <div class="h-2 w-10 bg-slate-200 dark:bg-slate-700 rounded"></div>
                                    <div class="h-5 w-16 bg-slate-800 dark:bg-slate-300 rounded"></div>
                                </div>
                            </div>
                            <!-- Mock Chart Container -->
                            <div class="h-44 bg-slate-50 dark:bg-slate-800/30 rounded-xl border border-slate-100 dark:border-slate-700/50 relative overflow-hidden flex items-end p-4 gap-3">
                                <!-- Mock Chart Bars -->
                                <div class="w-1/6 bg-blue-200 dark:bg-blue-900/50 rounded-t-md transition-all duration-1000" style="height: 30%"></div>
                                <div class="w-1/6 bg-blue-300 dark:bg-blue-800/50 rounded-t-md transition-all duration-1000" style="height: 50%"></div>
                                <div class="w-1/6 bg-blue-400 dark:bg-blue-600/50 rounded-t-md transition-all duration-1000" style="height: 65%"></div>
                                <div class="w-1/6 bg-blue-500 rounded-t-md shadow-[0_0_15px_rgba(59,130,246,0.5)] relative transition-all duration-1000" style="height: 85%">
                                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-2 py-0.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-[10px] rounded font-bold">Hari ini</div>
                                </div>
                                <div class="w-1/6 bg-slate-200 dark:bg-slate-700 rounded-t-md border-2 border-dashed border-indigo-400 dark:border-indigo-500 relative transition-all duration-1000" style="height: 70%">
                                     <div class="absolute -top-6 left-1/2 -translate-x-1/2 text-indigo-500 text-[10px] font-bold">Prediksi</div>
                                </div>
                                <div class="w-1/6 bg-slate-200 dark:bg-slate-700 rounded-t-md border-2 border-dashed border-indigo-400 dark:border-indigo-500 transition-all duration-1000" style="height: 60%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="py-24 bg-white dark:bg-[#111] border-t border-slate-100 dark:border-slate-800/50 relative z-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">Teknologi Cerdas Untuk Stabilitas</h2>
                <p class="text-slate-600 dark:text-slate-400">Sistem dirancang secara khusus menggabungkan rekam jejak harga dan Artificial Intelligence untuk analisa yang lebih tajam dan akurat.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="p-8 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-[#0a0a0a] hover:shadow-xl hover:border-blue-500/30 transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Model Prediksi AI (LSTM)</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">Algoritma Deep Learning canggih yang mampu mengenali tren pergerakan harga dari data historis untuk memprediksi harga masa depan.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="p-8 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-[#0a0a0a] hover:shadow-xl hover:border-purple-500/30 transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Pemantauan Real-Time</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">Update harian harga pangan dari pasar utama di Lubuklinggau, divisualisasikan dalam dashboard yang interaktif dan mudah dibaca.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="p-8 rounded-2xl border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-[#0a0a0a] hover:shadow-xl hover:border-amber-500/30 transition-all duration-300 group">
                    <div class="w-14 h-14 rounded-xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Sistem Peringatan Dini</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed text-sm">Radar otomatis (Early Warning System) yang memberikan notifikasi peringatan jika terdeteksi anomali lonjakan harga yang ekstrem.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800 py-12 relative z-20">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div class="font-bold text-white tracking-tight">E-PANGKAL</div>
            </div>
            <div class="text-slate-400 text-sm text-center md:text-right">
                <p class="mb-1">
                    &copy; {{ date('Y') }} Sistem Monitoring Harga Pangan Kota Lubuklinggau.<br> Didukung Teknologi AI (LSTM).
                </p>
                <p class="text-xs text-slate-500 mt-2">
                    Developer Contact: <a href="https://github.com/yandev2" target="_blank" rel="noopener noreferrer" class="text-blue-400 hover:text-blue-300 transition-colors">yandev2</a>
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
