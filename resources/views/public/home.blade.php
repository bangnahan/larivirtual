<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LariVirtual.com — Run Anywhere. Race Together.</title>
    
    <!-- Meta SEO & OpenGraph -->
    <meta name="description" content="Platform Virtual Run Resmi di Indonesia. Run Anywhere. Race Together. Lari di mana saja, kapan saja, cicil jaraknya, dan raih Medali Fisik Zinc Alloy serta Jersey Eksklusif dikirim ke rumah.">
    <meta name="keywords" content="larivirtual, lari virtual indonesia, virtual run 2026, bhinneka virtual run, medali virtual run, jersey lari, strava virtual run">
    <meta property="og:title" content="LariVirtual.com — Run Anywhere. Race Together.">
    <meta property="og:description" content="Platform Virtual Run Resmi di Indonesia. Lari di mana saja, kapan saja, dan dapatkan medali fisik serta finisher jersey premium.">
    <meta property="og:image" content="{{ asset('images/larivirtual/branding-key-visual.jpg') }}">
    <meta property="og:type" content="website">

    <!-- Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,700;1,800&family=JetBrains+Mono:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN with Custom Brand Tokens -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            purple: {
                                950: '#140024',
                                900: '#22003b',
                                850: '#2a0845',
                                800: '#38006b',
                                700: '#4c1d95',
                                600: '#6b21a8',
                                500: '#7c3aed',
                                100: '#f3e8ff',
                                50: '#faf5ff',
                            },
                            orange: {
                                900: '#7c2d12',
                                700: '#c2410c',
                                600: '#ea580c',
                                500: '#ff5400',
                                400: '#ff7a00',
                                300: '#fdba74',
                                100: '#ffedd5',
                                50: '#fff7ed',
                            }
                        }
                    },
                    boxShadow: {
                        'glow-orange': '0 0 25px -3px rgba(255, 84, 0, 0.45)',
                        'glow-purple': '0 0 35px -5px rgba(107, 33, 168, 0.45)',
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .bg-grid-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.08) 1px, transparent 1px);
            background-size: 24px 24px;
        }
        .speed-ribbon {
            background: linear-gradient(135deg, #22003b 0%, #38006b 40%, #ff5400 85%, #ff7a00 100%);
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex flex-col font-sans antialiased selection:bg-brand-orange-500 selection:text-white" x-data="{ mobileMenu: false }">

    <!-- Top Notification Bar -->
    <div class="bg-gradient-to-r from-brand-purple-950 via-brand-purple-900 to-brand-orange-700 text-white text-[11px] sm:text-xs py-2 px-4 border-b border-brand-purple-800/60">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="flex items-center gap-2 font-medium">
                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full bg-brand-orange-500 text-white font-extrabold text-[10px] tracking-wider uppercase animate-pulse">
                    LIVE
                </span>
                <span>Pendaftaran <strong>Bhinneka Virtual Run 42K</strong> Masih Dibuka! Lari bisa dicicil s.d 31 Des 2026.</span>
            </div>
            <a href="{{ route('public.ticket.index') }}" class="hidden md:inline-flex items-center gap-1 font-bold text-brand-orange-300 hover:text-white transition">
                <span>Cek Nomor E-BIB Anda</span>
                <span>&rarr;</span>
            </a>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="bg-brand-purple-950/90 backdrop-blur-md border-b border-brand-purple-850 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between">
            
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-gradient-to-br from-brand-purple-800 to-brand-purple-950 border border-brand-purple-700/80 p-1.5 flex items-center justify-center shadow-lg shadow-brand-purple-950/50 group-hover:scale-105 transition">
                    <img src="{{ asset('images/larivirtual/logo-icon-white.svg') }}" alt="LariVirtual Icon" class="w-full h-full object-contain">
                </div>
                <div class="flex flex-col">
                    <div class="flex items-baseline">
                        <span class="text-xl sm:text-2xl font-black italic tracking-tight text-white">lari<span class="text-brand-orange-400">virtual</span></span>
                        <span class="text-xs sm:text-sm font-extrabold text-brand-purple-400">.com</span>
                    </div>
                    <span class="text-[9px] uppercase tracking-widest font-bold text-slate-400 -mt-1">Virtual Run Platform</span>
                </div>
            </a>

            <!-- Desktop Nav Items -->
            <nav class="hidden lg:flex items-center gap-8 text-sm font-semibold text-slate-300">
                <a href="#events" class="hover:text-brand-orange-400 transition">Event Aktif</a>
                <a href="#cara-kerja" class="hover:text-brand-orange-400 transition">Cara Ikutan</a>
                <a href="#racepack" class="hover:text-brand-orange-400 transition">Medali &amp; Jersey</a>
                <a href="#faq" class="hover:text-brand-orange-400 transition">F.A.Q</a>
            </nav>

            <!-- Action Buttons -->
            <div class="hidden sm:flex items-center gap-3">
                <a href="{{ route('public.ticket.index') }}" class="px-4 py-2.5 rounded-xl border border-brand-purple-700/80 hover:border-brand-purple-500 bg-brand-purple-900/60 hover:bg-brand-purple-800/80 text-xs font-bold text-slate-200 transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    <span>Cek E-BIB</span>
                </a>
                <a href="{{ route('public.submit.index') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-brand-orange-500 via-brand-orange-600 to-brand-orange-500 hover:from-brand-orange-400 hover:to-brand-orange-600 text-xs font-black tracking-wide text-white shadow-lg shadow-brand-orange-600/30 hover:shadow-brand-orange-500/50 hover:scale-[1.02] active:scale-95 transition flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <span>Submit Hasil Lari</span>
                </a>
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 rounded-xl text-slate-300 hover:text-white hover:bg-brand-purple-800/60 focus:outline-none">
                <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Mobile Nav Menu -->
        <div x-show="mobileMenu" x-cloak class="lg:hidden bg-brand-purple-950 border-b border-brand-purple-800 px-4 pt-3 pb-6 space-y-3">
            <a href="#events" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg font-bold text-slate-200 hover:bg-brand-purple-900">Event Aktif</a>
            <a href="#cara-kerja" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg font-bold text-slate-200 hover:bg-brand-purple-900">Cara Ikutan</a>
            <a href="#racepack" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg font-bold text-slate-200 hover:bg-brand-purple-900">Medali &amp; Jersey</a>
            <a href="#faq" @click="mobileMenu = false" class="block px-3 py-2 rounded-lg font-bold text-slate-200 hover:bg-brand-purple-900">F.A.Q</a>
            <div class="pt-4 border-t border-brand-purple-800/80 grid grid-cols-2 gap-2">
                <a href="{{ route('public.ticket.index') }}" class="py-2.5 px-3 text-center rounded-xl border border-brand-purple-700 bg-brand-purple-900 text-xs font-bold text-slate-200">Cek E-BIB</a>
                <a href="{{ route('public.submit.index') }}" class="py-2.5 px-3 text-center rounded-xl bg-brand-orange-500 text-xs font-black text-white">Submit Lari</a>
            </div>
        </div>
    </header>

    <!-- HERO SECTION (Sesuai Key Visual Branding) -->
    <section class="relative overflow-hidden bg-brand-purple-950 pt-10 pb-20 lg:pt-16 lg:pb-28">
        
        <!-- Background Glows & Dynamic Ribbon Sweeps -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[1200px] h-[550px] bg-gradient-to-r from-brand-purple-800/40 via-brand-purple-600/30 to-brand-orange-600/25 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute inset-0 bg-grid-pattern opacity-40 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Left Column: Copywriting & Pillars -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    
                    <!-- Pill Badge -->
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-brand-purple-900/80 border border-brand-purple-700 text-brand-orange-300 text-xs font-bold shadow-inner">
                        <span class="w-2 h-2 rounded-full bg-brand-orange-400 animate-ping"></span>
                        <span>Platform Virtual Run Resmi Indonesia</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.1] text-white">
                        Run Anywhere.<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-orange-400 via-brand-orange-500 to-amber-300">Race Together.</span>
                    </h1>

                    <!-- Subheadline -->
                    <p class="text-lg sm:text-xl font-medium text-slate-300 max-w-2xl mx-auto lg:mx-0">
                        Virtual Run Event for Every Runner. Lari dari mana saja, kapan saja, cicil kilometernya, dan raih <strong class="text-white">Medali Logam Zinc Alloy</strong> serta <strong class="text-white">Jersey Resmi</strong> dikirimkan ke rumah Anda.
                    </p>

                    <!-- 3 Core Pillars (Sesuai Visual: Join, Track, Earn) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 pb-2">
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-brand-purple-900/40 border border-brand-purple-800/60 backdrop-blur-xs">
                            <div class="w-10 h-10 rounded-xl bg-brand-purple-800/80 text-brand-orange-400 flex items-center justify-center text-lg shrink-0">
                                📅
                            </div>
                            <div class="text-left">
                                <h4 class="text-xs font-extrabold text-white">Join Virtual Run</h4>
                                <p class="text-[11px] text-slate-400">Pilih kategori &amp; daftar</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-brand-purple-900/40 border border-brand-purple-800/60 backdrop-blur-xs">
                            <div class="w-10 h-10 rounded-xl bg-brand-purple-800/80 text-brand-orange-400 flex items-center justify-center text-lg shrink-0">
                                📊
                            </div>
                            <div class="text-left">
                                <h4 class="text-xs font-extrabold text-white">Track Your Run</h4>
                                <p class="text-[11px] text-slate-400">Pakai Strava, Garmin, dll</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-brand-purple-900/40 border border-brand-purple-800/60 backdrop-blur-xs">
                            <div class="w-10 h-10 rounded-xl bg-brand-purple-800/80 text-brand-orange-400 flex items-center justify-center text-lg shrink-0">
                                🏆
                            </div>
                            <div class="text-left">
                                <h4 class="text-xs font-extrabold text-white">Earn Your Medal</h4>
                                <p class="text-[11px] text-slate-400">Medali fisik &amp; e-sertifikat</p>
                            </div>
                        </div>
                    </div>

                    <!-- Call to Actions -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="#events" class="w-full sm:w-auto px-8 py-4 rounded-2xl bg-gradient-to-r from-brand-orange-500 via-brand-orange-600 to-brand-orange-500 hover:from-brand-orange-400 hover:to-brand-orange-500 text-white font-black text-base shadow-xl shadow-brand-orange-600/30 hover:shadow-brand-orange-500/50 hover:scale-[1.02] active:scale-95 transition flex items-center justify-center gap-3">
                            <span>Daftar Event Sekarang</span>
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                        <a href="{{ route('public.submit.index') }}" class="w-full sm:w-auto px-7 py-4 rounded-2xl bg-brand-purple-900/70 hover:bg-brand-purple-850 border border-brand-purple-700/80 text-slate-200 hover:text-white font-extrabold text-sm transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-brand-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            <span>Submit Aktivitas Lari</span>
                        </a>
                    </div>

                    <!-- Runner Social Proof -->
                    <div class="pt-4 flex items-center justify-center lg:justify-start gap-4 text-xs text-slate-400">
                        <div class="flex -space-x-2">
                            <span class="inline-block w-8 h-8 rounded-full bg-brand-orange-500 border-2 border-brand-purple-950 flex items-center justify-center font-bold text-[10px] text-white">🏃‍♂️</span>
                            <span class="inline-block w-8 h-8 rounded-full bg-brand-purple-600 border-2 border-brand-purple-950 flex items-center justify-center font-bold text-[10px] text-white">🏃‍♀️</span>
                            <span class="inline-block w-8 h-8 rounded-full bg-amber-500 border-2 border-brand-purple-950 flex items-center justify-center font-bold text-[10px] text-white">🎖️</span>
                        </div>
                        <div>
                            <span class="text-white font-extrabold">{{ number_format($totalParticipants) }}+ Pelari</span> telah bergabung dari 38 provinsi di Indonesia
                        </div>
                    </div>
                </div>

                <!-- Right Column: Visual Mockup Showcase (Key Visual Mockup) -->
                <div class="lg:col-span-5 relative">
                    <div class="relative mx-auto max-w-md lg:max-w-none">
                        
                        <!-- Glowing backdrop ring -->
                        <div class="absolute -inset-1 rounded-3xl bg-gradient-to-tr from-brand-orange-500 to-brand-purple-600 opacity-30 blur-xl"></div>
                        
                        <!-- Main Visual Card -->
                        <div class="relative rounded-3xl overflow-hidden border border-brand-purple-700/70 bg-brand-purple-900/60 shadow-2xl backdrop-blur-md">
                            
                            <!-- Key Visual Image -->
                            <img src="{{ asset('images/larivirtual/branding-key-visual.jpg') }}" alt="LariVirtual Brand Visual" class="w-full h-auto object-cover">

                            <!-- Overlay Ribbon Badge -->
                            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-brand-purple-950 via-brand-purple-950/90 to-transparent p-5 pt-10">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-[10px] uppercase tracking-wider font-extrabold text-brand-orange-400">Official Mobile App &amp; Web</div>
                                        <div class="text-sm font-extrabold text-white">Your Virtual Run. Your Way.</div>
                                    </div>
                                    <span class="px-3 py-1.5 rounded-xl bg-brand-orange-500 text-white text-[11px] font-black uppercase tracking-wider">
                                        GPS Synced
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Stat Badge 1 (Top Right) -->
                        <div class="absolute -top-4 -right-4 bg-brand-purple-900/90 border border-brand-purple-700 rounded-2xl p-3 shadow-xl backdrop-blur-md flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-brand-orange-500/20 text-brand-orange-400 flex items-center justify-center text-lg">
                                ⚡
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Total Kilometer</div>
                                <div class="text-sm font-black text-white">{{ number_format($totalDistanceKm) }} KM</div>
                            </div>
                        </div>

                        <!-- Floating Stat Badge 2 (Bottom Left) -->
                        <div class="absolute -bottom-4 -left-4 bg-brand-purple-900/90 border border-brand-purple-700 rounded-2xl p-3 shadow-xl backdrop-blur-md flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-brand-purple-600/30 text-brand-purple-300 flex items-center justify-center text-lg">
                                🏅
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase">Finisher Medals</div>
                                <div class="text-sm font-black text-white">{{ number_format($totalFinishers) }}+ Terkirim</div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- QUICK RUNNER ACCESS WIDGET (Tiga Alat Utama Peserta) -->
    <section class="bg-brand-purple-900/70 border-y border-brand-purple-800/80 py-6 relative z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                
                <!-- Tool 1: Cek E-BIB & Tiket -->
                <a href="{{ route('public.ticket.index') }}" class="group p-4 rounded-2xl bg-brand-purple-950/60 hover:bg-brand-purple-950 border border-brand-purple-800 hover:border-brand-orange-500/50 transition flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-brand-purple-800/80 group-hover:bg-brand-orange-500 text-brand-orange-400 group-hover:text-white flex items-center justify-center shrink-0 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-white group-hover:text-brand-orange-400 transition">Cek Nomor E-BIB &amp; Tiket</h4>
                        <p class="text-xs text-slate-400">Cari berdasarkan Email atau Kode Pesanan</p>
                    </div>
                </a>

                <!-- Tool 2: Submit Lari -->
                <a href="{{ route('public.submit.index') }}" class="group p-4 rounded-2xl bg-brand-purple-950/60 hover:bg-brand-purple-950 border border-brand-purple-800 hover:border-brand-orange-500/50 transition flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-brand-purple-800/80 group-hover:bg-brand-orange-500 text-brand-orange-400 group-hover:text-white flex items-center justify-center shrink-0 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-white group-hover:text-brand-orange-400 transition">Submit Hasil Aktivitas Lari</h4>
                        <p class="text-xs text-slate-400">Upload screenshot Strava, Garmin, Treadmill</p>
                    </div>
                </a>

                <!-- Tool 3: Leaderboard & Cek Status -->
                <a href="#events" class="group p-4 rounded-2xl bg-brand-purple-950/60 hover:bg-brand-purple-950 border border-brand-purple-800 hover:border-brand-orange-500/50 transition flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-brand-purple-800/80 group-hover:bg-brand-orange-500 text-brand-orange-400 group-hover:text-white flex items-center justify-center shrink-0 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-extrabold text-white group-hover:text-brand-orange-400 transition">Peringkat &amp; Leaderboard</h4>
                        <p class="text-xs text-slate-400">Pantau progres kilometer penamat lomba</p>
                    </div>
                </a>

            </div>
        </div>
    </section>

    <!-- FEATURED ACTIVE EVENT SECTION -->
    <section id="events" class="py-20 bg-slate-900 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="inline-block px-3 py-1 rounded-full bg-brand-orange-500/20 text-brand-orange-400 text-xs font-black uppercase tracking-wider mb-3">
                    Featured Virtual Run
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    Event Lari Virtual Pilihan Anda
                </h2>
                <p class="mt-3 text-slate-400 text-sm sm:text-base">
                    Ikuti tantangan lari virtual nasional. Bebas pilih tanggal lari, selesaikan dalam sekali jalan atau dicicil hingga periode selesai.
                </p>
            </div>

            @if($featuredEvent)
                <!-- Featured Card -->
                <div class="rounded-3xl bg-gradient-to-b from-brand-purple-950 to-brand-purple-900 border border-brand-purple-700/80 overflow-hidden shadow-2xl">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
                        
                        <!-- Event Banner / Graphic -->
                        <div class="lg:col-span-5 relative min-h-[300px] lg:min-h-full overflow-hidden bg-brand-purple-900">
                            @if(file_exists(public_path('images/bhinneka/banner.jpg')))
                                <img src="{{ asset('images/bhinneka/banner.jpg') }}" alt="{{ $featuredEvent->title }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ asset('images/larivirtual/hero-banner-wide.jpg') }}" alt="{{ $featuredEvent->title }}" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-brand-purple-950 via-brand-purple-950/40 to-transparent lg:hidden"></div>
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 rounded-xl bg-brand-orange-500 text-white font-black text-xs uppercase tracking-wider shadow-md">
                                    Pendaftaran Buka
                                </span>
                            </div>
                        </div>

                        <!-- Event Info & Action -->
                        <div class="lg:col-span-7 p-6 sm:p-8 lg:p-10 flex flex-col justify-between">
                            <div class="space-y-4">
                                <div class="flex items-center gap-3 text-xs text-brand-orange-400 font-extrabold uppercase tracking-wider">
                                    <span>🏃 42.195 KM (Bisa Dicicil)</span>
                                    <span>•</span>
                                    <span>📍 Di Mana Saja</span>
                                    <span>•</span>
                                    <span>📅 s.d 31 Des 2026</span>
                                </div>

                                <h3 class="text-2xl sm:text-3xl font-black text-white leading-tight">
                                    {{ $featuredEvent->title }}
                                </h3>

                                <p class="text-sm text-slate-300 leading-relaxed">
                                    {{ $featuredEvent->short_description ?? 'Mari kita rayakan persatuan Indonesia melalui event Bhinneka Tunggal Ika Virtual Run 42.195 KM. Lari dapat diselesaikan dalam 1 waktu atau dicicil beberapa kali.' }}
                                </p>

                                <!-- Categories Preview -->
                                <div class="pt-2">
                                    <h4 class="text-xs uppercase font-extrabold text-slate-400 tracking-wider mb-3">Pilihan Paket Pendaftaran:</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($featuredEvent->categories as $category)
                                            <div class="p-3.5 rounded-2xl bg-brand-purple-900/50 border border-brand-purple-700/60 flex items-center justify-between">
                                                <div>
                                                    <div class="text-xs font-extrabold text-white">{{ $category->name }}</div>
                                                    <div class="text-[11px] text-slate-400">
                                                        {{ $category->includes_jersey && $category->includes_medal ? 'Medali Zinc + Jersey' : ($category->includes_medal ? 'Medali Saja' : 'Kategori Khusus') }}
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <div class="text-xs sm:text-sm font-black text-brand-orange-400">
                                                        Rp {{ number_format($category->early_bird_price ?? $category->normal_price, 0, ',', '.') }}
                                                    </div>
                                                    @if($category->early_bird_price)
                                                        <div class="text-[10px] text-slate-400 line-through">
                                                            Rp {{ number_format($category->normal_price, 0, ',', '.') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="pt-8 flex flex-col sm:flex-row items-center gap-3">
                                <a href="{{ route('public.register.show', $featuredEvent->slug) }}" class="w-full sm:w-auto px-7 py-3.5 rounded-xl bg-gradient-to-r from-brand-orange-500 to-brand-orange-600 hover:from-brand-orange-400 hover:to-brand-orange-500 text-white font-extrabold text-sm shadow-lg shadow-brand-orange-600/30 active:scale-95 transition text-center flex items-center justify-center gap-2">
                                    <span>Daftar Event Ini Sekarang</span>
                                    <span>&rarr;</span>
                                </a>
                                <a href="{{ route('landing.bhinneka') }}" class="w-full sm:w-auto px-6 py-3.5 rounded-xl border border-brand-purple-700 bg-brand-purple-950/60 hover:bg-brand-purple-800 text-slate-200 text-sm font-bold transition text-center">
                                    Lihat Detail &amp; Race Pack
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>

    <!-- CARA IKUTAN VIRTUAL RUN (3 LANGKAH MUDAH) -->
    <section id="cara-kerja" class="py-20 bg-brand-purple-950 relative overflow-hidden">
        <div class="absolute inset-0 bg-grid-pattern opacity-30 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-3 py-1 rounded-full bg-brand-purple-800/80 border border-brand-purple-700 text-brand-orange-400 text-xs font-black uppercase tracking-wider mb-3">
                    Mudah &amp; Fleksibel
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    Cara Mengikuti Virtual Run
                </h2>
                <p class="mt-3 text-slate-400 text-sm sm:text-base">
                    Ikuti lomba lari virtual tanpa harus berdesakan di garis start. Lari dengan ritme Anda sendiri.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Step 1 -->
                <div class="relative p-6 sm:p-8 rounded-3xl bg-brand-purple-900/40 border border-brand-purple-800 hover:border-brand-orange-500/50 transition">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-purple-800 to-brand-purple-950 border border-brand-purple-700 flex items-center justify-center text-2xl font-black text-brand-orange-400 mb-6 shadow-lg shadow-brand-purple-950/60">
                        01
                    </div>
                    <h3 class="text-xl font-black text-white mb-3">Pilih Event &amp; Registrasi</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Tentukan event yang ingin diikuti, pilih kategori (dengan Jersey atau Medali saja), isi data diri, dan selesaikan pembayaran aman via QRIS / Virtual Account.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="relative p-6 sm:p-8 rounded-3xl bg-brand-purple-900/40 border border-brand-purple-800 hover:border-brand-orange-500/50 transition">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-purple-800 to-brand-purple-950 border border-brand-purple-700 flex items-center justify-center text-2xl font-black text-brand-orange-400 mb-6 shadow-lg shadow-brand-purple-950/60">
                        02
                    </div>
                    <h3 class="text-xl font-black text-white mb-3">Lari Di Mana Saja &amp; Kapan Saja</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Rekam aktivitas lari di jalan raya, taman, atau treadmill menggunakan aplikasi apa saja (Strava, Garmin, Apple Watch, Nike, dll). Boleh dicicil hingga target jarak tercapai.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="relative p-6 sm:p-8 rounded-3xl bg-brand-purple-900/40 border border-brand-purple-800 hover:border-brand-orange-500/50 transition">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-brand-purple-800 to-brand-purple-950 border border-brand-purple-700 flex items-center justify-center text-2xl font-black text-brand-orange-400 mb-6 shadow-lg shadow-brand-purple-950/60">
                        03
                    </div>
                    <h3 class="text-xl font-black text-white mb-3">Submit &amp; Terima Medali Fisik</h3>
                    <p class="text-sm text-slate-300 leading-relaxed">
                        Upload tangkapan layar lari Anda di menu submit. Setelah terverifikasi, Medali Logam Zinc Alloy &amp; Finisher Jersey langsung dikirim via SPX Express ke alamat Anda.
                    </p>
                </div>

            </div>
        </div>
    </section>

    <!-- RACEPACK & SWAG SHOWCASE (Sesuai Visual Merchandise) -->
    <section id="racepack" class="py-20 bg-slate-900 border-t border-brand-purple-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-3 py-1 rounded-full bg-brand-orange-500/20 text-brand-orange-400 text-xs font-black uppercase tracking-wider mb-3">
                    Premium Finisher Swag
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    Koleksi Merchandise Resmi Larivirtual
                </h2>
                <p class="mt-3 text-slate-400 text-sm sm:text-base">
                    Setiap tetes keringat Anda berharga. Kami menghadirkan medali dan jersey berstandar event lari maraton nasional.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Swag 1: Finisher Medal -->
                <div class="rounded-3xl bg-brand-purple-950/80 border border-brand-purple-800 overflow-hidden group hover:border-brand-orange-500/50 transition">
                    <div class="h-64 bg-slate-950 overflow-hidden flex items-center justify-center p-4">
                        <img src="{{ asset('images/larivirtual/merch-medal.jpg') }}" alt="Medali Finisher Zinc Alloy" class="h-full object-contain group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6">
                        <span class="text-[10px] font-black uppercase tracking-widest text-brand-orange-400">Zinc Alloy 3D Relief</span>
                        <h4 class="text-lg font-extrabold text-white mt-1">Medali Logam Finisher Eksklusif</h4>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Bahan logam zinc alloy berat dan kokoh, berdiameter 7.5 cm dengan tali lanyard woven custom bermotif gradasi ungu-oranye.
                        </p>
                    </div>
                </div>

                <!-- Swag 2: Official Jersey -->
                <div class="rounded-3xl bg-brand-purple-950/80 border border-brand-purple-800 overflow-hidden group hover:border-brand-orange-500/50 transition">
                    <div class="h-64 bg-slate-950 overflow-hidden flex items-center justify-center p-4">
                        <img src="{{ asset('images/larivirtual/merch-jersey.jpg') }}" alt="Jersey Lari Larivirtual" class="h-full object-contain group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6">
                        <span class="text-[10px] font-black uppercase tracking-widest text-brand-orange-400">Dry-Fit Microdot Tech</span>
                        <h4 class="text-lg font-extrabold text-white mt-1">Jersey Lari Resmi Larivirtual</h4>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Bahan ultra ringan, anti-bakteri, menyerap keringat dengan cepat. Dilengkapi aksen grafis diagonal swoosh ungu dan oranye khas Larivirtual.
                        </p>
                    </div>
                </div>

                <!-- Swag 3: Running Cap & E-Certificate -->
                <div class="rounded-3xl bg-brand-purple-950/80 border border-brand-purple-800 overflow-hidden group hover:border-brand-orange-500/50 transition">
                    <div class="h-64 bg-slate-950 overflow-hidden flex items-center justify-center p-4">
                        <img src="{{ asset('images/larivirtual/merch-cap.jpg') }}" alt="Topi Lari Larivirtual" class="h-full object-contain group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-6">
                        <span class="text-[10px] font-black uppercase tracking-widest text-brand-orange-400">Official Runner Accessories</span>
                        <h4 class="text-lg font-extrabold text-white mt-1">Topi Pelari &amp; E-Sertifikat Resmi</h4>
                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            Topi lari breathable dengan aksen bordir logo runner Larivirtual, serta E-Certificate resmi berpencapaian waktu dan jarak personal.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- TRUST BADGES & INTEGRATIONS -->
    <section class="py-12 bg-brand-purple-950 border-t border-brand-purple-850">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center text-xs font-bold uppercase tracking-wider text-slate-400 mb-8">
                Didukung Ekosistem Lari &amp; Logistik Terpercaya di Indonesia
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6 items-center text-center">
                <div class="p-4 rounded-2xl bg-brand-purple-900/40 border border-brand-purple-800">
                    <div class="text-base font-extrabold text-white">SPX Express</div>
                    <div class="text-[11px] text-brand-orange-400 font-medium">Pengiriman Otomatis Berresi</div>
                </div>
                <div class="p-4 rounded-2xl bg-brand-purple-900/40 border border-brand-purple-800">
                    <div class="text-base font-extrabold text-white">Tripay Gateway</div>
                    <div class="text-[11px] text-brand-orange-400 font-medium">QRIS &amp; Virtual Account Instan</div>
                </div>
                <div class="p-4 rounded-2xl bg-brand-purple-900/40 border border-brand-purple-800">
                    <div class="text-base font-extrabold text-white">Semua GPS Tracker</div>
                    <div class="text-[11px] text-brand-orange-400 font-medium">Strava, Garmin, Apple Watch</div>
                </div>
                <div class="p-4 rounded-2xl bg-brand-purple-900/40 border border-brand-purple-800">
                    <div class="text-base font-extrabold text-white">100% Guaranteed</div>
                    <div class="text-[11px] text-brand-orange-400 font-medium">Medali Pasti Sampai ke Rumah</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ SECTION -->
    <section id="faq" class="py-20 bg-slate-900 relative" x-data="{ activeFaq: null }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
            
            <div class="text-center mb-14">
                <span class="inline-block px-3 py-1 rounded-full bg-brand-purple-800/80 border border-brand-purple-700 text-brand-orange-400 text-xs font-black uppercase tracking-wider mb-3">
                    Pertanyaan Umum
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-white tracking-tight">
                    Frequently Asked Questions
                </h2>
            </div>

            <div class="space-y-4">
                
                <!-- FAQ 1 -->
                <div class="rounded-2xl bg-brand-purple-950/70 border border-brand-purple-800 overflow-hidden">
                    <button @click="activeFaq = activeFaq === 1 ? null : 1" class="w-full p-5 text-left font-extrabold text-white flex items-center justify-between gap-4">
                        <span class="text-sm sm:text-base">Apakah saya harus menyelesaikan jarak 42.195 KM sekaligus dalam satu hari?</span>
                        <span class="text-brand-orange-400 text-xl font-bold" x-text="activeFaq === 1 ? '−' : '+'">+</span>
                    </button>
                    <div x-show="activeFaq === 1" x-cloak class="px-5 pb-5 text-xs sm:text-sm text-slate-300 leading-relaxed border-t border-brand-purple-900 pt-3">
                        Tidak perlu! Event Virtual Run kami berbasis <strong>kumulatif (bisa dicicil)</strong>. Anda bisa berlari misalnya 5 KM hari ini, 7 KM minggu depan, dan seterusnya hingga total mencapai 42.195 KM sebelum periode lomba berakhir.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="rounded-2xl bg-brand-purple-950/70 border border-brand-purple-800 overflow-hidden">
                    <button @click="activeFaq = activeFaq === 2 ? null : 2" class="w-full p-5 text-left font-extrabold text-white flex items-center justify-between gap-4">
                        <span class="text-sm sm:text-base">Aplikasi apa saja yang diterima untuk merekam aktivitas lari?</span>
                        <span class="text-brand-orange-400 text-xl font-bold" x-text="activeFaq === 2 ? '−' : '+'">+</span>
                    </button>
                    <div x-show="activeFaq === 2" x-cloak class="px-5 pb-5 text-xs sm:text-sm text-slate-300 leading-relaxed border-t border-brand-purple-900 pt-3">
                        Kami menerima bukti lari dari aplikasi apa pun yang merekam jarak dan waktu tempuh, seperti <strong>Strava, Garmin Connect, Nike Run Club, Apple Fitness, Coros, Suunto</strong>, atau foto layar odometer Treadmill bagi Anda yang lari indoor.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="rounded-2xl bg-brand-purple-950/70 border border-brand-purple-800 overflow-hidden">
                    <button @click="activeFaq = activeFaq === 3 ? null : 3" class="w-full p-5 text-left font-extrabold text-white flex items-center justify-between gap-4">
                        <span class="text-sm sm:text-base">Kapan Medali dan Jersey dikirimkan ke rumah?</span>
                        <span class="text-brand-orange-400 text-xl font-bold" x-text="activeFaq === 3 ? '−' : '+'">+</span>
                    </button>
                    <div x-show="activeFaq === 3" x-cloak class="px-5 pb-5 text-xs sm:text-sm text-slate-300 leading-relaxed border-t border-brand-purple-900 pt-3">
                        Pengiriman Race Pack dilakukan serentak menggunakan <strong>SPX Express</strong> sesuai jadwal periode pengiriman yang tertera pada masing-masing event. Anda akan menerima notifikasi nomor resi pelacakan resmi via WhatsApp dan Email.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="rounded-2xl bg-brand-purple-950/70 border border-brand-purple-800 overflow-hidden">
                    <button @click="activeFaq = activeFaq === 4 ? null : 4" class="w-full p-5 text-left font-extrabold text-white flex items-center justify-between gap-4">
                        <span class="text-sm sm:text-base">Bagaimana jika saya adalah pelari pemula?</span>
                        <span class="text-brand-orange-400 text-xl font-bold" x-text="activeFaq === 4 ? '−' : '+'">+</span>
                    </button>
                    <div x-show="activeFaq === 4" x-cloak class="px-5 pb-5 text-xs sm:text-sm text-slate-300 leading-relaxed border-t border-brand-purple-900 pt-3">
                        Sangat bisa! Virtual run kami didesain inklusif untuk siapa saja. Tidak ada batasan cut-off time (COT) harian yang memberatkan. Anda juga boleh menempuh jarak dengan berjalan kaki (*brisk walking*).
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-brand-purple-950 border-t border-brand-purple-850 pt-16 pb-12 text-slate-400 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                
                <!-- Col 1: Brand Info -->
                <div class="md:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-brand-purple-800 border border-brand-purple-700 p-1.5 flex items-center justify-center">
                            <img src="{{ asset('images/larivirtual/logo-icon-white.svg') }}" alt="LariVirtual Icon" class="w-full h-full object-contain">
                        </div>
                        <div class="flex items-baseline">
                            <span class="text-xl font-black italic tracking-tight text-white">lari<span class="text-brand-orange-400">virtual</span></span>
                            <span class="text-xs font-extrabold text-brand-purple-400">.com</span>
                        </div>
                    </div>
                    <p class="text-slate-400 max-w-md leading-relaxed">
                        Platform Virtual Run modern di Indonesia. Lari di mana saja, kapan saja, rayakan pencapaian bersama ribuan pelari dari seluruh nusantara.
                    </p>
                    <div class="text-[11px] text-slate-400">
                        Slogan Resmi: <strong class="text-white italic">"Run Anywhere. Race Together."</strong>
                    </div>
                </div>

                <!-- Col 2: Navigasi Peserta -->
                <div class="space-y-3">
                    <h5 class="text-white font-black uppercase tracking-wider text-xs">Akses Peserta</h5>
                    <ul class="space-y-2">
                        <li><a href="{{ route('public.ticket.index') }}" class="hover:text-brand-orange-400 transition">Cek E-BIB &amp; Tiket</a></li>
                        <li><a href="{{ route('public.submit.index') }}" class="hover:text-brand-orange-400 transition">Submit Bukti Lari</a></li>
                        <li><a href="#events" class="hover:text-brand-orange-400 transition">Event Aktif</a></li>
                        <li><a href="#racepack" class="hover:text-brand-orange-400 transition">Koleksi Medali &amp; Jersey</a></li>
                    </ul>
                </div>

                <!-- Col 3: Bantuan & Legal -->
                <div class="space-y-3">
                    <h5 class="text-white font-black uppercase tracking-wider text-xs">Bantuan &amp; Info</h5>
                    <ul class="space-y-2">
                        <li><a href="{{ route('public.contact') }}" class="hover:text-brand-orange-400 transition">Hubungi Kami (WhatsApp)</a></li>
                        <li><a href="{{ route('public.terms') }}" class="hover:text-brand-orange-400 transition">Syarat &amp; Ketentuan</a></li>
                        <li><a href="{{ route('public.privacy') }}" class="hover:text-brand-orange-400 transition">Kebijakan Privasi</a></li>
                        <li><a href="/izinmasukbang" class="text-slate-400 hover:text-slate-200 transition">Portal Panitia</a></li>
                    </ul>
                </div>

            </div>

            <!-- Bottom Copyright -->
            <div class="pt-8 border-t border-brand-purple-900 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-400">
                <div>
                    &copy; {{ date('Y') }} <strong>LariVirtual.com</strong>. All Rights Reserved.
                </div>
                <div class="flex items-center gap-4">
                    <span>Virtual Run Platform Indonesia</span>
                    <span>•</span>
                    <span class="text-brand-orange-400 font-bold">SPX Express Delivery</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
