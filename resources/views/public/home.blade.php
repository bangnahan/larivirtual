<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virun — Platform Virtual Run &amp; Event Lari</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col font-sans antialiased selection:bg-orange-100 selection:text-orange-900">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-orange-50 border border-orange-200 p-1 flex items-center justify-center">
                    <span class="text-orange-600 font-black text-sm">VR</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-lg font-black tracking-tight text-slate-900">VIRUN</span>
                    <span class="hidden sm:inline-block text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-orange-50 text-orange-700 border border-orange-200">
                        Virtual Run
                    </span>
                </div>
            </a>
            
            <nav class="flex items-center gap-2 sm:gap-3 text-xs sm:text-sm">
                <a href="{{ route('public.submit.index') }}" class="font-bold text-orange-600 hover:text-orange-700 transition flex items-center gap-1.5 px-3 py-2 rounded-xl bg-orange-50 border border-orange-200/80 hover:bg-orange-100/70">
                    <svg class="w-4 h-4 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span>Submit Lari</span>
                </a>
                <a href="{{ route('public.ticket.index') }}" class="font-semibold text-slate-600 hover:text-slate-900 transition flex items-center gap-1.5 px-3 py-2 rounded-xl hover:bg-slate-100">
                    <svg class="w-4 h-4 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    <span class="hidden sm:inline">Cek E-BIB</span>
                    <span class="sm:hidden">Tiket</span>
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
        <!-- Hero Section -->
        <section class="max-w-4xl mx-auto px-4 sm:px-6 pt-12 sm:pt-16 pb-10 sm:pb-14 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-slate-200 text-slate-700 text-xs font-semibold mb-5 shadow-xs">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Platform Lari Virtual Tanpa Ribet</span>
            </div>

            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-slate-900 mb-4 leading-tight">
                Lari Di Mana Saja, Raih Medali &amp; E-Sertifikat.
            </h1>

            <p class="text-sm sm:text-base text-slate-600 max-w-xl mx-auto mb-8 leading-relaxed">
                Pilih event virtual favoritmu, dapatkan nomor E-BIB otomatis, submit hasil lari (Strava/Garmin/Treadmill), dan paket race pack dikirim langsung ke rumah via SPX Express.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full max-w-sm sm:max-w-2xl mx-auto">
                <a href="#events" class="w-full sm:w-auto bg-orange-600 hover:bg-orange-500 active:scale-95 text-white font-bold px-6 py-3.5 rounded-xl text-sm transition shadow-md shadow-orange-600/20 flex items-center justify-center gap-2 whitespace-nowrap">
                    <span>Lihat Event Lari</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </a>
                <a href="{{ route('public.submit.index') }}" class="w-full sm:w-auto bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 active:scale-95 text-slate-800 font-bold px-6 py-3.5 rounded-xl text-sm transition shadow-xs flex items-center justify-center gap-2 whitespace-nowrap">
                    <span>🏃 Lapor / Submit Lari</span>
                </a>
                <a href="{{ route('public.ticket.index') }}" class="w-full sm:w-auto bg-white hover:bg-slate-50 border border-slate-200 hover:border-slate-300 active:scale-95 text-slate-700 font-bold px-5 py-3.5 rounded-xl text-sm transition shadow-xs flex items-center justify-center gap-2 whitespace-nowrap">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    <span>Cek E-BIB</span>
                </a>
            </div>
        </section>

        <!-- Active Events Section -->
        <section id="events" class="max-w-6xl mx-auto px-4 sm:px-6 py-10 border-t border-slate-200">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Event Lari Aktif</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pilih tantangan lari dan amankan kuota pendaftaranmu.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($events as $event)
                    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:border-orange-300 transition duration-200 flex flex-col justify-between shadow-xs hover:shadow-md">
                        <div>
                            <!-- Card Banner Image -->
                            <div class="h-44 relative overflow-hidden bg-slate-100">
                                @if($event->banner_path)
                                    <img 
                                        src="{{ asset('storage/' . $event->banner_path) }}" 
                                        alt="{{ $event->title }}" 
                                        class="w-full h-full object-cover"
                                    >
                                @else
                                    <img 
                                        src="https://images.unsplash.com/photo-1530549387789-4c1017266635?w=700&auto=format&fit=crop&q=80" 
                                        alt="{{ $event->title }}" 
                                        class="w-full h-full object-cover"
                                    >
                                @endif

                                <div class="absolute top-3 left-3 right-3 flex justify-between items-start">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-600 text-white shadow-xs">
                                        Pendaftaran Dibuka
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-mono font-semibold bg-white/90 text-slate-700 backdrop-blur-xs border border-slate-200 shadow-xs">
                                        {{ $event->event_start_date->format('d M Y') }}
                                    </span>
                                </div>
                            </div>

                            <div class="p-5 space-y-3">
                                <div>
                                    <span class="text-[10px] text-orange-600 font-bold uppercase tracking-wider block">Virtual Run</span>
                                    <h3 class="text-base font-extrabold text-slate-900 line-clamp-1 mt-0.5">{{ $event->title }}</h3>
                                </div>

                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                                    {{ $event->short_description ?: 'Event lari virtual terbuka untuk umum. Selesaikan tantangan dan dapatkan paket race pack resmi.' }}
                                </p>

                                <!-- Categories List -->
                                <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Kategori &amp; Biaya:</span>
                                    <div class="space-y-1.5">
                                        @foreach($event->categories as $cat)
                                            <div class="bg-slate-50 border border-slate-200/70 rounded-xl px-3 py-2 text-xs flex items-center justify-between">
                                                <span class="font-bold text-slate-800">{{ $cat->name }} ({{ $cat->distance_km }} KM)</span>
                                                <span class="text-orange-600 font-bold font-mono">Rp {{ number_format($cat->getCurrentPrice(), 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                @if($event->racepack_delivery_schedule)
                                    <div class="bg-orange-50/70 border border-orange-200/60 rounded-xl px-3 py-2 text-[11px] text-orange-800 flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-orange-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                                        <span class="truncate">Kirim Race Pack: <strong>{{ $event->getRacepackDeliveryScheduleFormatted() }}</strong></span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="p-5 pt-0 space-y-2">
                            <a href="{{ route('public.register.show', $event->slug) }}" class="w-full bg-orange-600 hover:bg-orange-500 active:scale-95 text-white font-bold py-2.5 rounded-xl text-xs transition flex items-center justify-center gap-1.5 shadow-sm shadow-orange-600/20">
                                <span>Daftar Sekarang &rarr;</span>
                            </a>
                            <a href="{{ route('public.leaderboard.show', $event->slug) }}" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-semibold py-2 rounded-xl text-xs transition flex items-center justify-center gap-1">
                                <span>🏆 Lihat Leaderboard</span>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-16 bg-white rounded-2xl border border-slate-200">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3 text-xl">
                            🏃
                        </div>
                        <h4 class="font-bold text-slate-800 mb-1">Belum Ada Event Aktif</h4>
                        <p class="text-slate-500 text-xs">Event virtual run baru akan segera dibuka.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 3 Simple Steps -->
        <section class="max-w-6xl mx-auto px-4 sm:px-6 py-12 border-t border-slate-200">
            <div class="text-center mb-8">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900">Cara Kerja Virtual Run</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Sederhana dan mudah diikuti dari mana saja.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <div class="w-9 h-9 rounded-xl bg-orange-100 text-orange-700 font-black flex items-center justify-center text-sm mb-3">
                        1
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Daftar &amp; Dapatkan E-BIB</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Pilih kategori jarak dan jersey, bayar via QRIS otomatis, dan peroleh nomor dada E-BIB instan.
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 font-black flex items-center justify-center text-sm mb-3">
                        2
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Lari &amp; Submit Bukti</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Lari di jalanan atau treadmill. Cukup masukkan nomor E-BIB lalu kirim link Strava, Garmin, atau foto stopwatch.
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <div class="w-9 h-9 rounded-xl bg-sky-100 text-sky-700 font-black flex items-center justify-center text-sm mb-3">
                        3
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Medali &amp; E-Sertifikat</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Setelah tervalidasi, unduh E-Sertifikat resmi dan paket Race Pack beserta Medali dikirim via SPX Express.
                    </p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200 py-6 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 text-center sm:text-left">
            <div>
                &copy; {{ date('Y') }} <strong>Virun Platform</strong>. Hak Cipta Dilindungi.
            </div>
            <div class="flex flex-wrap items-center justify-center sm:justify-end gap-x-4 gap-y-2 text-slate-600 font-medium">
                <a href="{{ route('public.privacy') }}" class="hover:text-orange-600 transition">Privacy Policy</a>
                <a href="{{ route('public.terms') }}" class="hover:text-orange-600 transition">Term &amp; Condition</a>
                <a href="{{ route('public.contact') }}" class="hover:text-orange-600 transition">Contact Us</a>
                <a href="{{ route('public.ticket.index') }}" class="hover:text-orange-600 transition">Cek E-BIB</a>
                <a href="{{ route('public.submit.index') }}" class="hover:text-orange-600 transition">Submit Lari</a>
            </div>
        </div>
    </footer>
</body>
</html>
