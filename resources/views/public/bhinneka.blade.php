<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BHINNEKA TUNGGAL IKA Virtual Run 42.195 KM — Budaya Run</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Rayakan persatuan Indonesia melalui event Bhinneka Tunggal Ika Virtual Run 42.195 KM. Bisa lari sekali submit atau dicicil. Raih Medali Zinc Alloy eksklusif & Jersey Adidas by 06 AM RUNNING.">
    <meta name="keywords" content="bhinneka tunggal ika virtual run, budaya run, virtual run indonesia, lari 42km dicicil, medali zinc alloy, jersey adidas running">
    <meta property="og:title" content="BHINNEKA TUNGGAL IKA Virtual Run 42.195 KM — Budaya Run">
    <meta property="og:description" content="Lari 42.195 KM di mana saja, bisa dicicil s.d 31 Desember 2026. Dapatkan Medali Fisik Zinc Alloy & Jersey Premium dikirim ke rumah.">
    <meta property="og:image" content="{{ asset('images/bhinneka/banner.jpg') }}">
    <meta property="og:type" content="website">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;700;800&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        patriotic: {
                            50: '#fef2f2',
                            100: '#fee2e2',
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                            950: '#450a0a',
                        },
                        gold: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .gold-gradient-text {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 50%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .red-gradient-bg {
            background: linear-gradient(135deg, #991b1b 0%, #b91c1c 50%, #dc2626 100%);
        }
        .hero-pattern {
            background-color: #0b0f19;
            background-image: radial-gradient(rgba(220, 38, 38, 0.15) 1px, transparent 0);
            background-size: 24px 24px;
        }
    </style>

    <!-- Meta Pixel Code (Budaya Run Original Pixels: 1014082418078174 & 2512919952549398) -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '1014082418078174');
    fbq('init', '2512919952549398');
    fbq('track', 'PageView');
    fbq('track', 'ViewContent', {
        content_name: 'BHINNEKA TUNGGAL IKA Virtual Run 42.195 KM',
        content_category: 'Virtual Run',
        content_ids: ['bhinneka'],
        content_type: 'product'
    });
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1014082418078174&ev=PageView&noscript=1" />
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=2512919952549398&ev=PageView&noscript=1" />
    </noscript>
    <!-- End Meta Pixel Code -->
</head>
<body 
    x-data="bhinnekaApp()" 
    class="bg-slate-950 text-slate-100 font-sans antialiased selection:bg-red-600 selection:text-white"
>
    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-red-900 via-patriotic-700 to-red-900 text-white text-xs font-semibold py-2 px-4 text-center border-b border-red-500/20 shadow-sm flex items-center justify-center gap-2">
        <span class="inline-block w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
        <span>🔥 Pendaftaran Dibuka s.d <strong>10 November 2026</strong> • Kuota Terbatas!</span>
        <a href="#paket" class="hidden sm:inline-flex items-center gap-1 underline underline-offset-2 hover:text-amber-300 ml-2 font-bold">
            Amankan Kuota Anda &rarr;
        </a>
    </div>

    <!-- Clean Modern Navigation Header -->
    <header class="sticky top-0 z-40 bg-slate-950/90 backdrop-blur-md border-b border-slate-800/80 transition duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('landing.bhinneka') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/bhinneka/logo.png') }}" alt="Budaya Run Logo" class="h-9 sm:h-11 w-auto object-contain transition group-hover:scale-105">
                <div class="flex flex-col">
                    <span class="text-[10px] sm:text-xs uppercase tracking-widest font-black text-amber-400">Budaya Run</span>
                    <span class="text-xs sm:text-sm font-extrabold text-white tracking-tight leading-none">Bhinneka 42.195 KM</span>
                </div>
            </a>

            <!-- Right Header Action (Clean & Focused) -->
            <div class="flex items-center gap-3">
                <a href="https://wa.me/6281916444458?text=Halo%20Admin%20Budaya%20Run,%20saya%20mau%20tanya%20seputar%20Bhinneka%20Tunggal%20Ika%20Virtual%20Run" target="_blank" rel="noopener noreferrer" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white bg-slate-900 border border-slate-800 hover:border-slate-700 transition">
                    <span class="text-emerald-400 font-bold">💬</span>
                    <span>Chat WA</span>
                </a>
                <a href="{{ route('public.register.show', $event->slug ?? 'bhinneka') }}" class="inline-flex items-center gap-2 px-4 sm:px-5 py-2.5 rounded-xl text-xs sm:text-sm font-extrabold text-white bg-gradient-to-r from-patriotic-600 to-red-600 hover:from-patriotic-500 hover:to-red-500 shadow-lg shadow-red-600/30 active:scale-95 transition">
                    <span>Daftar Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-pattern relative overflow-hidden pt-12 pb-20 sm:pt-16 sm:pb-28 border-b border-slate-800">
        <!-- Glow Orbs -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-red-600/15 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute top-1/3 left-1/3 w-[300px] h-[300px] bg-amber-500/10 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-10">
                <!-- Patriotic Pill Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-950/80 border border-red-500/30 text-red-200 text-xs font-bold mb-6 shadow-sm">
                    <span class="text-base">🇮🇩</span>
                    <span class="tracking-wide uppercase">Mari Kita Rayakan Persatuan Indonesia</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white mb-6 leading-[1.15]">
                    BHINNEKA TUNGGAL IKA <br class="hidden sm:inline">
                    <span class="gold-gradient-text">VIRTUAL RUN 42.195 KM</span>
                </h1>

                <!-- Subtitle / Tagline -->
                <p class="text-base sm:text-lg text-slate-300 leading-relaxed font-normal mb-8 max-w-2xl mx-auto">
                    Berbeda-beda tetap bersatu adalah jati diri kita. Taklukkan jarak marathon <strong>42.195 KM</strong> kapan saja dan di mana saja—<strong>bisa lari sekali submit atau dicicil</strong> hingga 31 Desember 2026.
                </p>

                <!-- Countdown Timer Card -->
                <div class="bg-slate-900/90 border border-slate-700/80 rounded-2xl p-5 sm:p-6 mb-8 max-w-xl mx-auto shadow-2xl backdrop-blur-md">
                    <div class="flex items-center justify-center gap-2 text-xs font-bold text-amber-400 mb-3 uppercase tracking-wider">
                        <svg class="w-4 h-4 animate-spin text-amber-400" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Pendaftaran Ditutup Dalam :</span>
                    </div>

                    <div class="grid grid-cols-4 gap-2 sm:gap-3 text-center">
                        <div class="bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-2">
                            <span class="font-mono text-2xl sm:text-4xl font-extrabold text-white" x-text="timeLeft.days">00</span>
                            <span class="block text-[10px] sm:text-xs text-slate-400 uppercase font-semibold mt-0.5">Hari</span>
                        </div>
                        <div class="bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-2">
                            <span class="font-mono text-2xl sm:text-4xl font-extrabold text-white" x-text="timeLeft.hours">00</span>
                            <span class="block text-[10px] sm:text-xs text-slate-400 uppercase font-semibold mt-0.5">Jam</span>
                        </div>
                        <div class="bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-2">
                            <span class="font-mono text-2xl sm:text-4xl font-extrabold text-white" x-text="timeLeft.minutes">00</span>
                            <span class="block text-[10px] sm:text-xs text-slate-400 uppercase font-semibold mt-0.5">Menit</span>
                        </div>
                        <div class="bg-slate-950/80 border border-slate-800 rounded-xl py-3 px-2">
                            <span class="font-mono text-2xl sm:text-4xl font-extrabold text-amber-400" x-text="timeLeft.seconds">00</span>
                            <span class="block text-[10px] sm:text-xs text-slate-400 uppercase font-semibold mt-0.5">Detik</span>
                        </div>
                    </div>
                </div>

                <!-- CTA Actions -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3.5">
                    <a href="{{ route('public.register.show', $event->slug ?? 'bhinneka') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl text-base font-extrabold text-white bg-gradient-to-r from-patriotic-600 via-red-600 to-patriotic-700 hover:from-patriotic-500 hover:to-red-500 shadow-xl shadow-red-600/30 active:scale-95 transition flex items-center justify-center gap-2.5">
                        <span>Daftar Sekarang & Amankan Kuota</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <a href="#racepack" class="w-full sm:w-auto px-6 py-4 rounded-xl text-base font-bold text-slate-200 bg-slate-900/90 hover:bg-slate-800 border border-slate-700/80 active:scale-95 transition flex items-center justify-center gap-2">
                        <span>Lihat Race Pack & Medali</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </a>
                </div>
            </div>

            <!-- Banner Showcase Frame -->
            <div class="relative max-w-4xl mx-auto rounded-3xl overflow-hidden border border-slate-700/80 shadow-2xl shadow-red-950/50 group">
                <img src="{{ asset('images/bhinneka/banner.jpg') }}" alt="Bhinneka Tunggal Ika Virtual Run Banner" class="w-full h-auto object-cover transform transition duration-500 group-hover:scale-[1.01]">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent pointer-events-none"></div>
                <div class="absolute bottom-4 left-4 right-4 sm:bottom-6 sm:left-6 sm:right-6 flex flex-wrap items-center justify-between gap-3 bg-slate-950/70 backdrop-blur-md p-3.5 sm:p-4 rounded-2xl border border-white/10 text-xs sm:text-sm">
                    <div class="flex items-center gap-2 text-slate-200 font-semibold">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>Official Event by Budaya Run</span>
                    </div>
                    <div class="text-amber-400 font-bold">
                        Pilihan: Sekali Lari / Cicil 42.195 KM
                    </div>
                </div>
            </div>

            <!-- Value Highlights Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 max-w-5xl mx-auto mt-12 sm:mt-16">
                <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-4 text-center hover:border-slate-700 transition">
                    <div class="text-2xl mb-1.5">🏃‍♂️</div>
                    <h3 class="font-bold text-white text-sm mb-0.5">Jarak 42.195 KM</h3>
                    <p class="text-xs text-slate-400">Bisa Sekali / Dicicil</p>
                </div>
                <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-4 text-center hover:border-slate-700 transition">
                    <div class="text-2xl mb-1.5">🏅</div>
                    <h3 class="font-bold text-white text-sm mb-0.5">Medali Zinc Alloy</h3>
                    <p class="text-xs text-slate-400">Import Quality Tebal</p>
                </div>
                <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-4 text-center hover:border-slate-700 transition">
                    <div class="text-2xl mb-1.5">🎽</div>
                    <h3 class="font-bold text-white text-sm mb-0.5">Jersey Dry-Fit</h3>
                    <p class="text-xs text-slate-400">Adidas by 06 AM RUNNING</p>
                </div>
                <div class="bg-slate-900/70 border border-slate-800 rounded-2xl p-4 text-center hover:border-slate-700 transition">
                    <div class="text-2xl mb-1.5">🚚</div>
                    <h3 class="font-bold text-white text-sm mb-0.5">Kirim ke Rumah</h3>
                    <p class="text-xs text-slate-400">Serentak via SPX Express</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Ketentuan Lari 42.195 KM -->
    <section id="ketentuan" class="py-16 sm:py-24 bg-slate-900/50 border-b border-slate-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-amber-400">Fleksibilitas Penuh</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-1.5 mb-4">
                    📜 Ketentuan Lari 42.195 KM
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    Event ini dirancang untuk semua kalangan—dari pelari marathon berpengalaman hingga pemula yang baru memulai perjalanan gaya hidup sehat.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Mode Sekali Submit -->
                <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 rounded-2xl p-6 sm:p-8 hover:border-red-500/40 transition duration-300">
                    <div class="w-12 h-12 rounded-xl bg-red-950/80 border border-red-500/30 flex items-center justify-center text-xl mb-5">
                        ⚡
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-white mb-2">1. Mode Sekali Selesai (Single Run)</h3>
                    <p class="text-slate-300 text-sm leading-relaxed mb-4">
                        Bagi Anda pelari tangguh yang ingin menguji ketahanan, selesaikan jarak marathon penuh <strong>42.195 KM</strong> dalam satu kali sesi lari di rute favorit Anda.
                    </p>
                    <ul class="text-xs text-slate-400 space-y-2">
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-400 font-bold">✓</span> Cocok untuk pelari jarak jauh & marathon enthusiast
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-400 font-bold">✓</span> Satu kali submit hasil lari (Strava/Garmin/GPS)
                        </li>
                    </ul>
                </div>

                <!-- Mode Dicicil (Cumulative) -->
                <div class="bg-gradient-to-br from-slate-900 to-slate-950 border border-slate-800 rounded-2xl p-6 sm:p-8 hover:border-amber-500/40 transition duration-300">
                    <div class="w-12 h-12 rounded-xl bg-amber-950/80 border border-amber-500/30 flex items-center justify-center text-xl mb-5">
                        🧩
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-white mb-2">2. Mode Dicicil (Multiple / Akumulasi)</h3>
                    <p class="text-slate-300 text-sm leading-relaxed mb-4">
                        Cicil jarak lari beberapa kali sesuai kenyamanan Anda selama periode lari (September s.d. <strong>31 Desember 2026</strong>). Misalnya 5 km x 8 kali, atau 3 km setiap sore!
                    </p>
                    <ul class="text-xs text-slate-400 space-y-2">
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-400 font-bold">✓</span> Sangat ramah untuk pemula & pelari santai
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-400 font-bold">✓</span> Tidak ada batasan minimal waktu tempuh (pace)
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="text-emerald-400 font-bold">✓</span> Jarak juga dapat ditempuh dengan jalan kaki santai
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Notes Banner -->
            <div class="mt-8 bg-amber-950/40 border border-amber-500/30 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-center gap-4 text-center sm:text-left">
                <div class="w-10 h-10 rounded-xl bg-amber-500/20 flex items-center justify-center shrink-0 text-xl">
                    💡
                </div>
                <div class="text-xs sm:text-sm text-amber-200">
                    <strong>Bebas Lokasi:</strong> Lari dapat dilakukan di jalan raya, jogging track, lapangan, taman, maupun di atas <em>treadmill</em> di rumah atau gym.
                </div>
            </div>
        </div>
    </section>

    <!-- Racepack Peserta Overview -->
    <section id="racepack" class="py-16 sm:py-24 bg-slate-950 border-b border-slate-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-black uppercase tracking-widest text-amber-400">Benefit Eksklusif</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-1.5 mb-4">
                    🎽 Race Pack Peserta
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    Setiap paket disiapkan dengan standar kualitas tinggi untuk mengapresiasi semangat persatuan dan dedikasi lari Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Item 1: Jersey -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-red-950/70 border border-red-500/30 flex items-center justify-center text-2xl mb-4">
                            👕
                        </div>
                        <h3 class="text-base font-extrabold text-white mb-2">Jersey Dry-Fit Premium</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Bahan Adidas by 06 AM RUNNING. Dingin, elastis, menyerap keringat maksimal, dan nyaman untuk lari jarak jauh.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-800 text-[11px] font-bold text-amber-400 uppercase">
                        Ukuran XS s.d 5XL
                    </div>
                </div>

                <!-- Item 2: Medali Zinc Alloy -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-amber-950/70 border border-amber-500/30 flex items-center justify-center text-2xl mb-4">
                            🏅
                        </div>
                        <h3 class="text-base font-extrabold text-white mb-2">Medali Zinc Alloy Asli</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Logam Zinc Alloy solid (bukan plastik/akrilik). Desain timbul 3D Garuda Bhinneka Tunggal Ika dengan pita lanyard eksklusif.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-800 text-[11px] font-bold text-amber-400 uppercase">
                        Import Quality Berat & Tebal
                    </div>
                </div>

                <!-- Item 3: E-BIB -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-blue-950/70 border border-blue-500/30 flex items-center justify-center text-2xl mb-4">
                            🎫
                        </div>
                        <h3 class="text-base font-extrabold text-white mb-2">E-BIB Otomatis</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Nomor dada virtual resmi terbit seketika saat pembayaran lunas. Tersedia di email dan bisa langsung diunduh di invoice.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-800 text-[11px] font-bold text-blue-400 uppercase">
                        Download Instan
                    </div>
                </div>

                <!-- Item 4: E-Sertifikat -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 flex flex-col justify-between hover:border-slate-700 transition">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-950/70 border border-emerald-500/30 flex items-center justify-center text-2xl mb-4">
                            📜
                        </div>
                        <h3 class="text-base font-extrabold text-white mb-2">E-Sertifikat Finisher</h3>
                        <p class="text-xs text-slate-400 leading-relaxed">
                            Sertifikat digital pencapaian 42.195 KM beresolusi tinggi dengan nama pelari dan nomor BIB resmi untuk kenang-kenangan.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-slate-800 text-[11px] font-bold text-emerald-400 uppercase">
                        Klaim Setelah Finish
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detail Jersey Section -->
    <section id="jersey" class="py-16 sm:py-24 bg-slate-900/40 border-b border-slate-800" x-data="{ activeJerseyTab: 0 }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <span class="text-xs font-black uppercase tracking-widest text-amber-400">Kualitas Tanpa Kompromi</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-1.5 mb-4">
                    🎽 Detail Jersey Bhinneka Tunggal Ika
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    Didesain khusus dengan corak persatuan Indonesia berbahan <strong>Dry-fit Adidas by 06 AM RUNNING</strong>. Tersedia dalam 3 pilihan model: <strong>Jersey Lengan Pendek, Jersey Lengan Panjang</strong>, dan <strong>Jersey Singlet Tanpa Kerah</strong>.
                </p>
            </div>

            <!-- Jersey Gallery Interactive -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8">
                <!-- Tab Controls -->
                <div class="flex flex-wrap items-center justify-center gap-2 mb-8">
                    <button 
                        @click="activeJerseyTab = 0" 
                        :class="activeJerseyTab === 0 ? 'bg-red-600 text-white font-extrabold border-red-500 shadow-md' : 'bg-slate-950 text-slate-300 font-semibold border-slate-800 hover:border-slate-700'"
                        class="px-4 py-2.5 rounded-xl text-xs sm:text-sm border transition flex items-center gap-2"
                    >
                        <span>👕 Jersey Lengan Pendek</span>
                    </button>
                    <button 
                        @click="activeJerseyTab = 1" 
                        :class="activeJerseyTab === 1 ? 'bg-red-600 text-white font-extrabold border-red-500 shadow-md' : 'bg-slate-950 text-slate-300 font-semibold border-slate-800 hover:border-slate-700'"
                        class="px-4 py-2.5 rounded-xl text-xs sm:text-sm border transition flex items-center gap-2"
                    >
                        <span>🧥 Jersey Lengan Panjang</span>
                    </button>
                    <button 
                        @click="activeJerseyTab = 2" 
                        :class="activeJerseyTab === 2 ? 'bg-red-600 text-white font-extrabold border-red-500 shadow-md' : 'bg-slate-950 text-slate-300 font-semibold border-slate-800 hover:border-slate-700'"
                        class="px-4 py-2.5 rounded-xl text-xs sm:text-sm border transition flex items-center gap-2"
                    >
                        <span>🎽 Jersey Singlet Tanpa Kerah</span>
                    </button>
                </div>

                <!-- Images Display -->
                <div class="relative max-w-3xl mx-auto rounded-2xl overflow-hidden border border-slate-800 bg-slate-950">
                    <div x-show="activeJerseyTab === 0" x-transition:enter="transition duration-300" class="text-center">
                        <img src="{{ asset('images/bhinneka/jersey-front-back.jpg') }}" alt="Jersey Lengan Pendek Bhinneka Tunggal Ika" class="w-full h-auto object-cover max-h-[500px] mx-auto rounded-xl">
                        <p class="text-xs text-slate-400 py-3 font-medium">Model <strong>Jersey Lengan Pendek</strong> (Short Sleeve) Bhinneka Tunggal Ika</p>
                    </div>
                    <div x-show="activeJerseyTab === 1" x-cloak x-transition:enter="transition duration-300" class="text-center">
                        <img src="{{ asset('images/bhinneka/jersey-texture.jpg') }}" alt="Jersey Lengan Panjang Bhinneka Tunggal Ika" class="w-full h-auto object-cover max-h-[500px] mx-auto rounded-xl">
                        <p class="text-xs text-slate-400 py-3 font-medium">Model <strong>Jersey Lengan Panjang</strong> (Long Sleeve) Bhinneka Tunggal Ika</p>
                    </div>
                    <div x-show="activeJerseyTab === 2" x-cloak x-transition:enter="transition duration-300" class="text-center">
                        <img src="{{ asset('images/bhinneka/jersey-detail.jpg') }}" alt="Jersey Singlet Tanpa Kerah Bhinneka Tunggal Ika" class="w-full h-auto object-cover max-h-[500px] mx-auto rounded-xl">
                        <p class="text-xs text-slate-400 py-3 font-medium">Model <strong>Jersey Singlet Tanpa Kerah</strong> (Sleeveless Running Singlet)</p>
                    </div>
                </div>

                <!-- Size Chart Modal Trigger -->
                <div class="mt-8 text-center">
                    <button 
                        @click="showSizeChart = true" 
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-950 hover:bg-slate-800 border border-slate-700 text-slate-200 hover:text-white text-xs sm:text-sm font-bold shadow-md transition"
                    >
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        <span>📐 Buka Panduan Ukuran Jersey (Size Chart XS - 5XL)</span>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Detail Medali Section (Video + Image) -->
    <section id="medali" class="py-16 sm:py-24 bg-slate-950 border-b border-slate-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-black uppercase tracking-widest text-amber-400">Piala Pencapaian Anda</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-1.5 mb-4">
                    🏅 Medali Eksklusif Zinc Alloy (Import Quality)
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    Bukan medali tipis biasa. Terbuat dari logam <strong>Zinc Alloy solid</strong> dengan ukiran lambang Garuda dan motif Nusantara berelief 3D timbul yang elegan.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <!-- Video Teaser Box -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                            Video Putaran 360° Medali
                        </span>
                        <span class="text-[11px] text-slate-400">Putar Video &rarr;</span>
                    </div>

                    <div class="relative rounded-2xl overflow-hidden bg-black aspect-square max-w-md mx-auto border border-slate-800 flex items-center justify-center">
                        <video 
                            id="medalVideo"
                            src="{{ asset('videos/bhinneka/medal-rotation.mp4') }}" 
                            class="w-full h-full object-cover" 
                            controls 
                            preload="metadata" 
                            playsinline
                        ></video>
                    </div>

                    <p class="text-xs text-slate-400 text-center mt-3">
                        Tonton video untuk melihat efek kilauan, ketebalan, dan detil ukiran 3D medali asli.
                    </p>
                </div>

                <!-- High-Res Medal Image Box -->
                <div class="bg-slate-900 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl flex flex-col justify-between h-full">
                    <div>
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">
                            Foto Resolusi Tinggi Medali & Lanyard
                        </div>

                        <div class="rounded-2xl overflow-hidden bg-slate-950 p-4 border border-slate-800 text-center">
                            <img src="{{ asset('images/bhinneka/medal-zinc-alloy.png') }}" alt="Medali Bhinneka Tunggal Ika Zinc Alloy" class="w-full h-auto max-h-[380px] object-contain mx-auto filter drop-shadow-2xl hover:scale-105 transition duration-300">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mt-6">
                        <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl text-center">
                            <span class="block text-xs text-slate-400">Material</span>
                            <strong class="text-sm text-white">Zinc Alloy Solid</strong>
                        </div>
                        <div class="bg-slate-950 border border-slate-800 p-3 rounded-xl text-center">
                            <span class="block text-xs text-slate-400">Lanyard Ribbon</span>
                            <strong class="text-sm text-white">Sublimasi Halus</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Timeline & Jadwal Event -->
    <section id="jadwal" class="py-16 sm:py-24 bg-slate-900/50 border-b border-slate-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-black uppercase tracking-widest text-amber-400">Jadwal Pelaksanaan</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-1.5 mb-4">
                    📅 Timeline & Jadwal Event
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    Catat tanggal penting agar Anda tidak melewatkan kesempatan mengamankan kuota dan race pack resmi.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Step 1 -->
                <div class="bg-slate-900 border-2 border-red-600/60 rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute -top-3 -right-3 w-10 h-10 bg-red-600/20 rounded-full flex items-center justify-center text-red-400 font-black text-xs">
                        01
                    </div>
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wide block mb-1">Fase Pendaftaran</span>
                    <h3 class="text-base font-extrabold text-white mb-2">20 Sep - 10 Nov 2026</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Pendaftaran peserta dibuka secara online. Amankan tiket sebelum kuota habis.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute -top-3 -right-3 w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center text-slate-400 font-black text-xs">
                        02
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Produksi</span>
                    <h3 class="text-base font-extrabold text-white mb-2">11 - 24 Oktober 2026</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Masa produksi jersey custom sesuai ukuran dan pencetakan medali Zinc Alloy.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute -top-3 -right-3 w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center text-slate-400 font-black text-xs">
                        03
                    </div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wide block mb-1">Pengiriman Racepack</span>
                    <h3 class="text-base font-extrabold text-white mb-2">Mulai 25 Oktober 2026</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Pengiriman paket serentak langsung ke alamat peserta via SPX Express.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute -top-3 -right-3 w-10 h-10 bg-slate-800 rounded-full flex items-center justify-center text-slate-400 font-black text-xs">
                        04
                    </div>
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wide block mb-1">Periode Lari</span>
                    <h3 class="text-base font-extrabold text-white mb-2">s.d 31 Desember 2026</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Waktu fleksibel bagi pelari untuk menyelesaikan 42.195 KM (single atau cicil) & submit bukti.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Paket Tiket & Registrasi Section -->
    <section id="paket" class="py-16 sm:py-24 bg-slate-950 border-b border-slate-800">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-black uppercase tracking-widest text-amber-400">Pilihan Paket Pendaftaran</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-1.5 mb-4">
                    🎟️ Pilih Paket Lomba Anda
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    Pilih paket yang paling sesuai dengan kebutuhan lari Anda. Seluruh paket mencakup nomor E-BIB resmi dan E-Sertifikat Finisher.
                </p>
            </div>

            <!-- Dynamic or Fallback Packages Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8 items-stretch max-w-5xl mx-auto">
                @if($event->categories && $event->categories->count() > 0)
                    @foreach($event->categories as $category)
                        <div class="bg-slate-900 border {{ $loop->first ? 'border-red-500 shadow-2xl shadow-red-950/60' : 'border-slate-800' }} rounded-3xl p-6 sm:p-8 flex flex-col justify-between relative group hover:border-slate-700 transition">
                            @if($loop->first)
                                <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-red-600 to-amber-500 text-white text-[11px] font-black uppercase tracking-wider px-3.5 py-1 rounded-full shadow-md">
                                    Paling Diminati (Best Value)
                                </div>
                            @endif

                            @php
                                $catIncludesJersey = (bool) ($category->includes_jersey ?? true);
                                $catIncludesMedal = (bool) ($category->includes_medal ?? true);
                            @endphp

                            <div>
                                <h3 class="text-xl font-extrabold text-white mb-1">{{ $category->name }}</h3>
                                <p class="text-xs text-slate-400 mb-2">Jarak Tantangan: {{ $category->distance_km ?? 42.195 }} KM</p>
                                <div class="mb-4">
                                    @if($catIncludesJersey && $catIncludesMedal)
                                        <span class="text-[11px] font-bold text-emerald-400 bg-emerald-950/80 border border-emerald-500/40 px-2.5 py-0.5 rounded-full inline-block">
                                            ✓ Medali + Jersey Lengkap
                                        </span>
                                    @elseif(!$catIncludesJersey && $catIncludesMedal)
                                        <span class="text-[11px] font-bold text-amber-400 bg-amber-950/80 border border-amber-500/40 px-2.5 py-0.5 rounded-full inline-block">
                                            🏅 Paket Medali Only
                                        </span>
                                    @elseif($catIncludesJersey && !$catIncludesMedal)
                                        <span class="text-[11px] font-bold text-indigo-300 bg-indigo-950/80 border border-indigo-500/40 px-2.5 py-0.5 rounded-full inline-block">
                                            👕 Paket Jersey Only
                                        </span>
                                    @else
                                        <span class="text-[11px] font-bold text-slate-300 bg-slate-800 border border-slate-700 px-2.5 py-0.5 rounded-full inline-block">
                                            📱 Paket E-Pass Digital
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-6 pb-6 border-b border-slate-800">
                                    <span class="text-xs text-slate-400 block mb-1">Biaya Pendaftaran</span>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-sm font-bold text-amber-400">Rp</span>
                                        <span class="text-3xl sm:text-4xl font-black text-white">{{ number_format($category->normal_price, 0, ',', '.') }}</span>
                                    </div>
                                    <span class="text-[11px] text-slate-500 mt-1 block">Tersedia {{ $category->quota }} Kuota Peserta</span>
                                </div>

                                <ul class="space-y-3 text-xs sm:text-sm text-slate-300 mb-8">
                                    @if($catIncludesJersey)
                                        <li class="flex items-center gap-2.5">
                                            <span class="text-emerald-400 font-bold">✓</span> <strong>Jersey Dry-Fit Adidas</strong> by 06 AM
                                        </li>
                                    @endif
                                    @if($catIncludesMedal)
                                        <li class="flex items-center gap-2.5">
                                            <span class="text-emerald-400 font-bold">✓</span> <strong>Medali Eksklusif Zinc Alloy</strong> (Import Quality)
                                        </li>
                                    @endif
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-emerald-400 font-bold">✓</span> E-BIB Nomor Virtual Resmi
                                    </li>
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-emerald-400 font-bold">✓</span> E-Sertifikat Finisher 42.195 KM
                                    </li>
                                    <li class="flex items-center gap-2.5">
                                        <span class="text-emerald-400 font-bold">✓</span> Kirim Hasil Lari (Single / Cicil)
                                    </li>
                                    @if($catIncludesJersey || $catIncludesMedal)
                                        <li class="flex items-center gap-2.5">
                                            <span class="text-emerald-400 font-bold">✓</span> Racepack Dikirim via SPX Express
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <a href="{{ route('public.register.show', $event->slug) }}" class="w-full py-3.5 px-4 rounded-xl text-center font-extrabold text-xs sm:text-sm {{ $loop->first ? 'bg-gradient-to-r from-red-600 to-patriotic-700 text-white hover:from-red-500 hover:to-patriotic-600 shadow-lg shadow-red-600/30' : 'bg-slate-800 hover:bg-slate-700 text-white border border-slate-700' }} active:scale-95 transition">
                                Pilih Paket Ini &rarr;
                            </a>
                        </div>
                    @endforeach
                @else
                    <!-- Fallback 3 Official Packages from live budayarun.com -->
                    <!-- Paket 1: Full Package -->
                    <div class="bg-slate-900 border-2 border-red-500/80 rounded-3xl p-6 sm:p-8 flex flex-col justify-between relative group shadow-2xl shadow-red-950/50">
                        <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 bg-gradient-to-r from-red-600 via-patriotic-600 to-amber-500 text-white text-[11px] font-black uppercase tracking-wider px-4 py-1 rounded-full shadow-md">
                            🔥 PALING DIMINATI • LENGKAP
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2 mt-2">
                                <h3 class="text-xl font-extrabold text-white">Paket Lengkap</h3>
                                <span class="text-xs bg-red-950/80 text-red-300 border border-red-500/30 px-2 py-0.5 rounded-full font-bold">Hemat Rp 100rb</span>
                            </div>
                            <p class="text-xs text-slate-400 mb-6">Jersey + Medali Zinc Alloy + E-BIB + E-Sertifikat</p>

                            <div class="mb-6 pb-6 border-b border-slate-800">
                                <span class="text-xs text-slate-400 block mb-1">Harga Promo</span>
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-sm font-bold text-amber-400">Rp</span>
                                    <span class="text-3xl sm:text-4xl font-black text-white">250.000</span>
                                    <span class="text-xs text-slate-500 line-through ml-1">Rp 350.000</span>
                                </div>
                                <span class="text-[11px] text-emerald-400 font-semibold mt-1 block">✓ All-In: Jersey & Medali Fisik</span>
                            </div>

                            <ul class="space-y-3 text-xs sm:text-sm text-slate-300 mb-8">
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> <strong>Jersey Dry-Fit Adidas</strong> by 06 AM RUNNING
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> <strong>Medali Eksklusif Zinc Alloy</strong> (Import Quality)
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> E-BIB Nomor Virtual Otomatis
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> E-Sertifikat Finisher 42.195 KM
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> Pengiriman via SPX Express
                                </li>
                            </ul>
                        </div>

                        <a href="{{ route('public.register.show', $event->slug ?? 'bhinneka') }}" class="w-full py-4 px-4 rounded-xl text-center font-extrabold text-sm bg-gradient-to-r from-red-600 to-patriotic-700 hover:from-red-500 hover:to-patriotic-600 text-white shadow-xl shadow-red-600/30 active:scale-95 transition flex items-center justify-center gap-2">
                            <span>Daftar Paket Lengkap</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    </div>

                    <!-- Paket 2: Jersey Only -->
                    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 flex flex-col justify-between hover:border-slate-700 transition">
                        <div>
                            <h3 class="text-xl font-extrabold text-white mb-2">Paket Jersey Only</h3>
                            <p class="text-xs text-slate-400 mb-6">Jersey + E-BIB + E-Sertifikat</p>

                            <div class="mb-6 pb-6 border-b border-slate-800">
                                <span class="text-xs text-slate-400 block mb-1">Harga Normal</span>
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-sm font-bold text-amber-400">Rp</span>
                                    <span class="text-3xl sm:text-4xl font-black text-white">150.000</span>
                                </div>
                                <span class="text-[11px] text-slate-400 mt-1 block">Tanpa Medali Fisik</span>
                            </div>

                            <ul class="space-y-3 text-xs sm:text-sm text-slate-300 mb-8">
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> <strong>Jersey Dry-Fit Adidas</strong> (XS - 5XL)
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> E-BIB Nomor Virtual Otomatis
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> E-Sertifikat Finisher 42.195 KM
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> Pengiriman Jersey via SPX Express
                                </li>
                            </ul>
                        </div>

                        <a href="{{ route('public.register.show', $event->slug ?? 'bhinneka') }}" class="w-full py-3.5 px-4 rounded-xl text-center font-bold text-sm bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 active:scale-95 transition">
                            Pilih Paket Jersey &rarr;
                        </a>
                    </div>

                    <!-- Paket 3: Medali Only -->
                    <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 flex flex-col justify-between hover:border-slate-700 transition">
                        <div>
                            <h3 class="text-xl font-extrabold text-white mb-2">Paket Medali Only</h3>
                            <p class="text-xs text-slate-400 mb-6">Medali Zinc Alloy + E-BIB + E-Sertifikat</p>

                            <div class="mb-6 pb-6 border-b border-slate-800">
                                <span class="text-xs text-slate-400 block mb-1">Harga Normal</span>
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-sm font-bold text-amber-400">Rp</span>
                                    <span class="text-3xl sm:text-4xl font-black text-white">150.000</span>
                                </div>
                                <span class="text-[11px] text-slate-400 mt-1 block">Tanpa Jersey</span>
                            </div>

                            <ul class="space-y-3 text-xs sm:text-sm text-slate-300 mb-8">
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> <strong>Medali Eksklusif Zinc Alloy</strong>
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> Lanyard Ribbon Sublimasi
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> E-BIB Nomor Virtual Otomatis
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> E-Sertifikat Finisher 42.195 KM
                                </li>
                                <li class="flex items-center gap-2.5">
                                    <span class="text-emerald-400 font-bold">✓</span> Pengiriman Medali via SPX Express
                                </li>
                            </ul>
                        </div>

                        <a href="{{ route('public.register.show', $event->slug ?? 'bhinneka') }}" class="w-full py-3.5 px-4 rounded-xl text-center font-bold text-sm bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 active:scale-95 transition">
                            Pilih Paket Medali &rarr;
                        </a>
                    </div>
                @endif
            </div>

            <div class="mt-10 text-center text-xs text-slate-400">
                🔒 Pembayaran aman otomatis diverifikasi melalui <strong>QRIS, Virtual Account BCA/Mandiri/BRI/BNI</strong>, dan Alfamart via Tripay.
            </div>
        </div>
    </section>

    <!-- Frequently Asked Questions (FAQ) -->
    <section id="faq" class="py-16 sm:py-24 bg-slate-900/50 border-b border-slate-800" x-data="{ openFaq: null }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-black uppercase tracking-widest text-amber-400">Pusat Bantuan</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-white mt-1.5 mb-4">
                    ❓ Frequently Asked Questions (FAQ)
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    Pertanyaan yang paling sering diajukan seputar pendaftaran, cicil lari, medali, dan sertifikat.
                </p>
            </div>

            <div class="space-y-3">
                <!-- FAQ 1 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden transition">
                    <button 
                        @click="openFaq = openFaq === 1 ? null : 1" 
                        class="w-full px-5 py-4 text-left font-bold text-white text-sm sm:text-base flex items-center justify-between gap-4"
                    >
                        <span>Apa itu Virtual Run?</span>
                        <span class="text-amber-400 text-lg transition duration-200" :class="openFaq === 1 ? 'rotate-180' : ''">▾</span>
                    </button>
                    <div x-show="openFaq === 1" x-cloak class="px-5 pb-5 text-xs sm:text-sm text-slate-300 leading-relaxed border-t border-slate-800/80 pt-3">
                        Virtual Run adalah lomba lari yang bisa Anda lakukan di mana saja dan kapan saja selama periode acara berlangsung. Anda bebas memilih rute sendiri, baik di jalan raya, jogging track, taman kota, maupun di atas <em>treadmill</em> di rumah atau gym.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden transition">
                    <button 
                        @click="openFaq = openFaq === 2 ? null : 2" 
                        class="w-full px-5 py-4 text-left font-bold text-white text-sm sm:text-base flex items-center justify-between gap-4"
                    >
                        <span>Apakah jarak 42.195 KM harus diselesaikan dalam satu waktu (sekaligus)?</span>
                        <span class="text-amber-400 text-lg transition duration-200" :class="openFaq === 2 ? 'rotate-180' : ''">▾</span>
                    </button>
                    <div x-show="openFaq === 2" x-cloak class="px-5 pb-5 text-xs sm:text-sm text-slate-300 leading-relaxed border-t border-slate-800/80 pt-3">
                        Tidak harus! Jarak 42.195 KM bisa diselesaikan sekaligus (single run) atau <strong>dicicil (multiple runs / akumulasi)</strong> selama rentang waktu September hingga 31 Desember 2026. Anda bebas mengatur ritme sesuai kesibukan dan kenyamanan tubuh Anda.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden transition">
                    <button 
                        @click="openFaq = openFaq === 3 ? null : 3" 
                        class="w-full px-5 py-4 text-left font-bold text-white text-sm sm:text-base flex items-center justify-between gap-4"
                    >
                        <span>Aplikasi pelacak lari (tracker) apa yang boleh digunakan?</span>
                        <span class="text-amber-400 text-lg transition duration-200" :class="openFaq === 3 ? 'rotate-180' : ''">▾</span>
                    </button>
                    <div x-show="openFaq === 3" x-cloak class="px-5 pb-5 text-xs sm:text-sm text-slate-300 leading-relaxed border-t border-slate-800/80 pt-3">
                        Anda bebas menggunakan aplikasi pelacak lari apa saja yang merekam jarak, waktu, dan tanggal, seperti <strong>Strava, Garmin Connect, Nike Run Club (NRC), Coros, Suunto, Zepp, Apple Fitness</strong>, maupun foto layar monitor <em>treadmill</em>.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden transition">
                    <button 
                        @click="openFaq = openFaq === 4 ? null : 4" 
                        class="w-full px-5 py-4 text-left font-bold text-white text-sm sm:text-base flex items-center justify-between gap-4"
                    >
                        <span>Bagaimana cara mengirimkan bukti lari (submit result)?</span>
                        <span class="text-amber-400 text-lg transition duration-200" :class="openFaq === 4 ? 'rotate-180' : ''">▾</span>
                    </button>
                    <div x-show="openFaq === 4" x-cloak class="px-5 pb-5 text-xs sm:text-sm text-slate-300 leading-relaxed border-t border-slate-800/80 pt-3">
                        Kunjungi menu <a href="{{ route('public.submit.index') }}" class="text-amber-400 font-bold underline">Submit Lari</a> di website ini, masukkan nomor BIB Anda, lalu isi tanggal, jarak (km), durasi lari, dan tautan aktivitas Strava/Google Drive atau screenshot lari Anda. Sistem akan mengakumulasi total jarak secara otomatis.
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden transition">
                    <button 
                        @click="openFaq = openFaq === 5 ? null : 5" 
                        class="w-full px-5 py-4 text-left font-bold text-white text-sm sm:text-base flex items-center justify-between gap-4"
                    >
                        <span>Kapan saya bisa mendapatkan E-Sertifikat?</span>
                        <span class="text-amber-400 text-lg transition duration-200" :class="openFaq === 5 ? 'rotate-180' : ''">▾</span>
                    </button>
                    <div x-show="openFaq === 5" x-cloak class="px-5 pb-5 text-xs sm:text-sm text-slate-300 leading-relaxed border-t border-slate-800/80 pt-3">
                        E-Sertifikat Finisher otomatis diterbitkan begitu akumulasi total jarak lari Anda mencapai 42.195 KM dan disetujui. Anda dapat langsung mengunduh file PDF E-Sertifikat 1 halaman berkualitas cetak melalui menu website.
                    </div>
                </div>

                <!-- FAQ 6 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden transition">
                    <button 
                        @click="openFaq = openFaq === 6 ? null : 6" 
                        class="w-full px-5 py-4 text-left font-bold text-white text-sm sm:text-base flex items-center justify-between gap-4"
                    >
                        <span>Kapan saya mendapatkan nomor E-BIB?</span>
                        <span class="text-amber-400 text-lg transition duration-200" :class="openFaq === 6 ? 'rotate-180' : ''">▾</span>
                    </button>
                    <div x-show="openFaq === 6" x-cloak class="px-5 pb-5 text-xs sm:text-sm text-slate-300 leading-relaxed border-t border-slate-800/80 pt-3">
                        Nomor E-BIB diterbitkan secara otomatis segera setelah transaksi pembayaran Anda lunas diverifikasi. Anda dapat mengunduh E-Ticket / Virtual Pass melalui email konfirmasi atau melalui menu <a href="{{ route('public.ticket.index') }}" class="text-amber-400 font-bold underline">Cek E-BIB</a>.
                    </div>
                </div>

                <!-- FAQ 7 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden transition">
                    <button 
                        @click="openFaq = openFaq === 7 ? null : 7" 
                        class="w-full px-5 py-4 text-left font-bold text-white text-sm sm:text-base flex items-center justify-between gap-4"
                    >
                        <span>Kapan Medali dan Jersey saya dikirimkan?</span>
                        <span class="text-amber-400 text-lg transition duration-200" :class="openFaq === 7 ? 'rotate-180' : ''">▾</span>
                    </button>
                    <div x-show="openFaq === 7" x-cloak class="px-5 pb-5 text-xs sm:text-sm text-slate-300 leading-relaxed border-t border-slate-800/80 pt-3">
                        Pengiriman Race Pack (Medali Zinc Alloy dan Jersey) dilakukan secara serentak setelah masa produksi selesai, yaitu mulai tanggal <strong>25 Oktober 2026</strong> langsung ke alamat tujuan yang Anda cantumkan.
                    </div>
                </div>

                <!-- FAQ 8 -->
                <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden transition">
                    <button 
                        @click="openFaq = openFaq === 8 ? null : 8" 
                        class="w-full px-5 py-4 text-left font-bold text-white text-sm sm:text-base flex items-center justify-between gap-4"
                    >
                        <span>Apakah biaya pendaftaran sudah termasuk ongkos kirim?</span>
                        <span class="text-amber-400 text-lg transition duration-200" :class="openFaq === 8 ? 'rotate-180' : ''">▾</span>
                    </button>
                    <div x-show="openFaq === 8" x-cloak class="px-5 pb-5 text-xs sm:text-sm text-slate-300 leading-relaxed border-t border-slate-800/80 pt-3">
                        Biaya pendaftaran belum termasuk ongkos kirim. Ongkos kirim dikalkulasikan secara otomatis dan transparan saat Anda memasukkan kecamatan dan kota pengiriman pada formulir checkout via kurir resmi <strong>SPX Express</strong>.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Size Chart Modal / Lightbox -->
    <div 
        x-show="showSizeChart" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="showSizeChart = false"
            class="bg-slate-900 border border-slate-700 rounded-3xl max-w-2xl w-full p-6 relative max-h-[90vh] overflow-y-auto shadow-2xl"
        >
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-xl">📐</span>
                    <h3 class="font-extrabold text-white text-base sm:text-lg">Tabel Ukuran Jersey (Size Chart)</h3>
                </div>
                <button @click="showSizeChart = false" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 text-lg font-bold">
                    ✕
                </button>
            </div>

            <div class="rounded-xl overflow-hidden bg-white p-2 border border-slate-700 text-center">
                <img src="{{ asset('images/bhinneka/size-chart.jpg') }}" alt="Tabel Ukuran Jersey Bhinneka Tunggal Ika" class="w-full h-auto mx-auto object-contain">
            </div>

            <p class="text-[11px] text-slate-400 text-center mt-4">
                Toleransi ukuran jahitan &plusmn; 1-2 cm. Pastikan memilih ukuran yang pas sebelum melakukan pembayaran.
            </p>
        </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div 
        x-show="showPrivacyModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="showPrivacyModal = false"
            class="bg-slate-900 border border-slate-700 rounded-3xl max-w-2xl w-full p-6 sm:p-8 relative max-h-[85vh] overflow-y-auto shadow-2xl text-slate-300 text-xs sm:text-sm leading-relaxed"
        >
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">🔒</span>
                    <div>
                        <h3 class="font-extrabold text-white text-base sm:text-lg">Kebijakan Privasi (Privacy Policy)</h3>
                        <span class="text-[11px] text-red-400 font-semibold">Budaya Run Indonesia</span>
                    </div>
                </div>
                <button @click="showPrivacyModal = false" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 text-lg font-bold transition">
                    ✕
                </button>
            </div>

            <div class="space-y-5">
                <p class="text-slate-400">
                    Budaya Run Indonesia berkomitmen tinggi melindungi privasi dan data pribadi seluruh peserta virtual run kami.
                </p>

                <div>
                    <h4 class="font-bold text-white text-sm mb-1.5 flex items-center gap-2">
                        <span class="text-red-400 font-black">1.</span> Informasi yang Dikumpulkan
                    </h4>
                    <p class="text-slate-400 mb-1.5">Kami mengumpulkan data berikut saat Anda mendaftar event:</p>
                    <ul class="list-disc list-inside space-y-1 pl-2 text-slate-300 text-xs">
                        <li>Identitas: Nama lengkap, nama di nomor dada (e-BIB), gender, tanggal lahir.</li>
                        <li>Kontak: Email aktif dan nomor WhatsApp untuk notifikasi e-BIB &amp; resi kurir.</li>
                        <li>Logistik: Alamat lengkap pengiriman kurir SPX Express (kecamatan, kota, kode pos).</li>
                        <li>Aktivitas Lari: Bukti jarak KM dan durasi lari dari aplikasi GPS / treadmill.</li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-white text-sm mb-1.5 flex items-center gap-2">
                        <span class="text-red-400 font-black">2.</span> Penggunaan &amp; Kerahasiaan Data
                    </h4>
                    <p class="text-slate-400 text-xs">
                        Data hanya digunakan untuk penerbitan E-BIB resmi, verifikasi pembayaran Tripay berizin Bank Indonesia, pencetakan medali &amp; jersey kustom, serta pengiriman paket racepack via SPX Express. Kami <strong>tidak pernah menjual atau menyewakan</strong> data pribadi Anda kepada pihak mana pun.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-white text-sm mb-1.5 flex items-center gap-2">
                        <span class="text-red-400 font-black">3.</span> Hak &amp; Koreksi Data
                    </h4>
                    <p class="text-slate-400 text-xs">
                        Peserta berhak memperbarui alamat pengiriman sebelum tanggal pengiriman serentak dimulai melalui WhatsApp resmi kami di <strong>0819-1644-4458</strong>.
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between">
                <a href="{{ route('public.privacy') }}" class="text-xs text-amber-400 hover:text-amber-300 underline font-medium">Buka Halaman Lengkap &rarr;</a>
                <button type="button" @click="showPrivacyModal = false" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Terms & Conditions Modal -->
    <div 
        x-show="showTermsModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="showTermsModal = false"
            class="bg-slate-900 border border-slate-700 rounded-3xl max-w-2xl w-full p-6 sm:p-8 relative max-h-[85vh] overflow-y-auto shadow-2xl text-slate-300 text-xs sm:text-sm leading-relaxed"
        >
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">📜</span>
                    <div>
                        <h3 class="font-extrabold text-white text-base sm:text-lg">Syarat &amp; Ketentuan (Terms &amp; Conditions)</h3>
                        <span class="text-[11px] text-amber-400 font-semibold">Bhinneka Tunggal Ika Virtual Run 42.195 KM</span>
                    </div>
                </div>
                <button @click="showTermsModal = false" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 text-lg font-bold transition">
                    ✕
                </button>
            </div>

            <div class="space-y-5">
                <div>
                    <h4 class="font-bold text-white text-sm mb-1.5 flex items-center gap-2">
                        <span class="text-amber-400 font-black">1.</span> Pendaftaran &amp; Pembayaran
                    </h4>
                    <p class="text-slate-400 text-xs">
                        Pendaftaran terbuka untuk umum. Pembayaran diproses otomatis melalui payment gateway Tripay (QRIS, VA Bank, Alfamart). Seluruh pembayaran bersifat <strong>final dan non-refundable</strong> karena alokasi produksi jersey kustom dan medali langsung dijalankan.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-white text-sm mb-1.5 flex items-center gap-2">
                        <span class="text-amber-400 font-black">2.</span> Format Lari &amp; Batas Submission
                    </h4>
                    <p class="text-slate-400 text-xs">
                        Jarak 42.195 KM dapat diselesaikan dalam 1 kali lari (single submit) atau dicicil akumulasi beberapa kali hingga <strong>31 Desember 2026 pukul 23:59 WIB</strong>. Hasil lari wajib diunggah melalui portal resmi <a href="{{ route('public.submit.index') }}" class="text-amber-400 underline">budayarun.com/submit</a>.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-white text-sm mb-1.5 flex items-center gap-2">
                        <span class="text-amber-400 font-black">3.</span> Pengiriman Race Pack via SPX Express
                    </h4>
                    <p class="text-slate-400 text-xs">
                        Pengiriman Race Pack (Medali Zinc Alloy Asli dan Jersey Adidas by 06 AM RUNNING) dilakukan secara serentak mulai <strong>25 Oktober 2026</strong> langsung ke alamat tujuan peserta. Nomor resi otomatis dapat dicek mandiri.
                    </p>
                </div>

                <div>
                    <h4 class="font-bold text-white text-sm mb-1.5 flex items-center gap-2">
                        <span class="text-amber-400 font-black">4.</span> Tanggung Jawab Fisik &amp; Kesehatan (Waiver)
                    </h4>
                    <p class="text-slate-400 text-xs">
                        Peserta menyatakan dalam kondisi fisik yang sehat dan siap menuntaskan tantangan lari mandiri. Penyelenggara dibebaskan dari segala bentuk tuntutan terkait cedera atau kendala kesehatan selama beraktivitas.
                    </p>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between">
                <a href="{{ route('public.terms') }}" class="text-xs text-amber-400 hover:text-amber-300 underline font-medium">Buka Halaman Lengkap &rarr;</a>
                <button type="button" @click="showTermsModal = false" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Contact Us Modal -->
    <div 
        x-show="showContactModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div 
            @click.away="showContactModal = false"
            class="bg-slate-900 border border-slate-700 rounded-3xl max-w-xl w-full p-6 sm:p-8 relative max-h-[85vh] overflow-y-auto shadow-2xl text-slate-300 text-xs sm:text-sm leading-relaxed"
        >
            <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-5">
                <div class="flex items-center gap-2.5">
                    <span class="text-xl">💬</span>
                    <div>
                        <h3 class="font-extrabold text-white text-base sm:text-lg">Hubungi Kami (Contact Us)</h3>
                        <span class="text-[11px] text-emerald-400 font-semibold">Budaya Run Customer Support</span>
                    </div>
                </div>
                <button @click="showContactModal = false" class="text-slate-400 hover:text-white p-1.5 rounded-lg hover:bg-slate-800 text-lg font-bold transition">
                    ✕
                </button>
            </div>

            <p class="text-slate-400 text-xs mb-5">
                Ada kendala teknis, pertanyaan pendaftaran, perubahan ukuran jersey, atau kerjasama komunitas? Tim panitia siap melayani Anda:
            </p>

            <div class="space-y-3.5 mb-6">
                <!-- WhatsApp -->
                <a href="https://wa.me/6281916444458?text=Halo%20Admin%20Budaya%20Run,%20saya%20mau%20tanya%20seputar%20Bhinneka%20Tunggal%20Ika%20Virtual%20Run" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="flex items-center justify-between p-3.5 rounded-2xl bg-emerald-950/40 border border-emerald-500/30 hover:border-emerald-500/70 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl">
                            💬
                        </div>
                        <div>
                            <div class="text-white font-bold text-xs sm:text-sm">WhatsApp Admin (Fast Response)</div>
                            <div class="text-emerald-400 font-mono text-xs">0819-1644-4458</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-emerald-400 group-hover:translate-x-1 transition">&rarr;</span>
                </a>

                <!-- Email -->
                <a href="mailto:admin@budayarun.com?subject=Tanya%20Bhinneka%20Virtual%20Run" 
                   class="flex items-center justify-between p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 hover:border-slate-700 transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl">
                            ✉️
                        </div>
                        <div>
                            <div class="text-white font-bold text-xs sm:text-sm">Email Resmi Panitia</div>
                            <div class="text-amber-400 font-mono text-xs">admin@budayarun.com</div>
                        </div>
                    </div>
                    <span class="text-xs font-bold text-slate-400 group-hover:translate-x-1 transition">&rarr;</span>
                </a>
            </div>

            <div class="p-3.5 bg-slate-950/60 border border-slate-800/80 rounded-2xl text-[11px] text-slate-400 space-y-1">
                <div>🕒 <strong>Jam Layanan:</strong> Setiap Hari (Senin - Minggu) 08.00 - 21.00 WIB</div>
                <div>📍 <strong>Penyelenggara:</strong> Budaya Run Indonesia • Jakarta</div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between">
                <a href="{{ route('public.contact') }}" class="text-xs text-amber-400 hover:text-amber-300 underline font-medium">Buka Halaman Kontak &rarr;</a>
                <button type="button" @click="showContactModal = false" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Sticky Mobile Bottom CTA Bar -->
    <div class="sm:hidden fixed bottom-0 left-0 right-0 z-30 bg-slate-950/95 border-t border-slate-800/90 backdrop-blur-md px-4 py-3 flex items-center justify-between gap-3 shadow-2xl">
        <div>
            <span class="text-[10px] text-slate-400 block uppercase font-bold">Mulai Dari</span>
            <div class="flex items-baseline gap-1">
                <span class="text-xs font-bold text-amber-400">Rp</span>
                <span class="text-lg font-black text-white">150.000</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a 
                href="https://wa.me/6281916444458?text=Halo%20Admin%20Budaya%20Run,%20saya%20mau%20tanya%20seputar%20Bhinneka%20Tunggal%20Ika%20Virtual%20Run" 
                target="_blank" 
                rel="noopener noreferrer" 
                class="w-10 h-10 rounded-xl bg-emerald-600/20 border border-emerald-500/40 text-emerald-400 flex items-center justify-center font-bold text-sm"
                title="Chat WhatsApp"
            >
                💬
            </a>
            <a 
                href="{{ route('public.register.show', $event->slug ?? 'bhinneka') }}" 
                class="px-5 py-2.5 rounded-xl text-xs font-extrabold text-white bg-gradient-to-r from-red-600 to-patriotic-700 shadow-md shadow-red-600/30 flex items-center gap-1.5"
            >
                <span>Daftar Sekarang</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>

    <!-- Floating WhatsApp Button (Desktop & Tablet) -->
    <a 
        href="https://wa.me/6281916444458?text=Halo%20Admin%20Budaya%20Run,%20saya%20mau%20tanya%20seputar%20Bhinneka%20Tunggal%20Ika%20Virtual%20Run" 
        target="_blank" 
        rel="noopener noreferrer" 
        class="hidden sm:flex fixed bottom-6 right-6 z-40 items-center gap-2.5 px-4 py-3 rounded-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-2xl shadow-emerald-600/40 border border-emerald-400/40 group active:scale-95 transition"
    >
        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
        <span>Tanya CS WhatsApp</span>
    </a>

    <!-- Comprehensive Mega Footer (Semua Menu & Navigasi Dipusatkan di Sini) -->
    <footer class="bg-black border-t border-slate-800 pt-16 pb-12 text-slate-400 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-8 pb-12 border-b border-slate-800/80">
                <!-- Kolom 1 & 2: Profil Brand & Kontak Resmi -->
                <div class="sm:col-span-2 lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/bhinneka/logo.png') }}" alt="Budaya Run Logo" class="h-10 w-auto object-contain">
                        <div>
                            <span class="text-base font-extrabold text-white tracking-tight block">Budaya Run</span>
                            <span class="text-xs text-amber-400 font-semibold tracking-wide">Pelari yang Berbudi dan Berdaya</span>
                        </div>
                    </div>
                    <p class="text-slate-400 text-xs leading-relaxed max-w-sm">
                        <strong>Bhinneka Tunggal Ika Virtual Run 42.195 KM</strong> adalah ajang perayaan persatuan bangsa Indonesia. Selesaikan tantangan lari 42.195 KM kapan saja dan di mana saja—bisa lari sekali submit maupun dicicil santai hingga 31 Desember 2026.
                    </p>
                    <div class="pt-2 space-y-2.5">
                        <div class="flex items-center gap-2 text-slate-300">
                            <span class="text-emerald-400 font-bold">📱 WhatsApp Admin:</span>
                            <a href="https://wa.me/6281916444458" target="_blank" class="text-white hover:text-emerald-400 font-semibold underline underline-offset-2">0819-1644-4458</a>
                        </div>
                        <div class="flex items-center gap-2 text-slate-300">
                            <span class="text-amber-400 font-bold">✉️ Email Resmi:</span>
                            <a href="mailto:admin@budayarun.com" class="text-slate-300 hover:text-white">admin@budayarun.com</a>
                        </div>
                        <div class="flex items-center gap-2 text-slate-300">
                            <span class="text-red-400 font-bold">📍 Penyelenggara:</span>
                            <span>Budaya Run Indonesia</span>
                        </div>
                    </div>
                </div>

                <!-- Kolom 3: Informasi Event & Lomba -->
                <div class="space-y-3">
                    <h4 class="text-sm font-extrabold text-white uppercase tracking-wider text-amber-400">Informasi Event</h4>
                    <ul class="space-y-2.5">
                        <li><a href="#ketentuan" class="hover:text-white transition flex items-center gap-1.5"><span class="text-slate-600">›</span> Ketentuan 42.195 KM</a></li>
                        <li><a href="#ketentuan" class="hover:text-white transition flex items-center gap-1.5"><span class="text-slate-600">›</span> Mode Sekali (Single)</a></li>
                        <li><a href="#ketentuan" class="hover:text-white transition flex items-center gap-1.5"><span class="text-slate-600">›</span> Mode Dicicil (Akumulasi)</a></li>
                        <li><a href="#jadwal" class="hover:text-white transition flex items-center gap-1.5"><span class="text-slate-600">›</span> Timeline & Jadwal Event</a></li>
                        <li><a href="#faq" class="hover:text-white transition flex items-center gap-1.5"><span class="text-slate-600">›</span> FAQ (Tanya Jawab)</a></li>
                    </ul>
                </div>

                <!-- Kolom 4: Race Pack & Jersey -->
                <div class="space-y-3">
                    <h4 class="text-sm font-extrabold text-white uppercase tracking-wider text-amber-400">Race Pack & Benefit</h4>
                    <ul class="space-y-2.5">
                        <li><a href="#racepack" class="hover:text-white transition flex items-center gap-1.5"><span class="text-slate-600">›</span> All-In Benefit Peserta</a></li>
                        <li><a href="#jersey" class="hover:text-white transition flex items-center gap-1.5"><span class="text-slate-600">›</span> Jersey Lengan Pendek</a></li>
                        <li><a href="#jersey" class="hover:text-white transition flex items-center gap-1.5"><span class="text-slate-600">›</span> Jersey Lengan Panjang</a></li>
                        <li><a href="#jersey" class="hover:text-white transition flex items-center gap-1.5"><span class="text-slate-600">›</span> Jersey Singlet Tanpa Kerah</a></li>
                        <li><a href="#medali" class="hover:text-white transition flex items-center gap-1.5"><span class="text-slate-600">›</span> Medali Zinc Alloy Solid</a></li>
                        <li><button type="button" @click="showSizeChart = true" class="text-left hover:text-white transition flex items-center gap-1.5 text-amber-300 font-semibold cursor-pointer"><span class="text-amber-400">📐</span> Size Chart (XS - 5XL)</button></li>
                    </ul>
                </div>

                <!-- Kolom 5: Portal Pelari -->
                <div class="space-y-3">
                    <h4 class="text-sm font-extrabold text-white uppercase tracking-wider text-amber-400">Portal Pelari</h4>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('public.register.show', $event->slug ?? 'bhinneka') }}" class="text-red-400 font-bold hover:text-red-300 transition flex items-center gap-1.5"><span>🎟️</span> Form Pendaftaran</a></li>
                        <li><a href="{{ route('public.submit.index') }}" class="hover:text-white transition flex items-center gap-1.5"><span>🏃</span> Submit Hasil Lari</a></li>
                        <li><a href="{{ route('public.ticket.index') }}" class="hover:text-white transition flex items-center gap-1.5"><span>🎫</span> Cek Nomor E-BIB</a></li>
                        <li><a href="{{ route('public.submit.index') }}" class="hover:text-white transition flex items-center gap-1.5"><span>📜</span> Unduh E-Sertifikat</a></li>
                    </ul>
                </div>

                <!-- Kolom 6: Bantuan & Legal -->
                <div class="space-y-3">
                    <h4 class="text-sm font-extrabold text-white uppercase tracking-wider text-amber-400">Bantuan & Legal</h4>
                    <ul class="space-y-2.5">
                        <li>
                            <button type="button" @click="showPrivacyModal = true" class="hover:text-white transition flex items-center gap-1.5 text-left group cursor-pointer">
                                <span class="text-slate-600 group-hover:text-amber-400 transition">›</span>
                                <span>Privacy Policy</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" @click="showTermsModal = true" class="hover:text-white transition flex items-center gap-1.5 text-left group cursor-pointer">
                                <span class="text-slate-600 group-hover:text-amber-400 transition">›</span>
                                <span>Term &amp; Condition</span>
                            </button>
                        </li>
                        <li>
                            <button type="button" @click="showContactModal = true" class="hover:text-white transition flex items-center gap-1.5 text-left group cursor-pointer">
                                <span class="text-slate-600 group-hover:text-amber-400 transition">›</span>
                                <span>Contact Us</span>
                            </button>
                        </li>
                        <li class="pt-1.5">
                            <a href="https://wa.me/6281916444458?text=Halo%20Admin%20Budaya%20Run,%20saya%20butuh%20bantuan" target="_blank" rel="noopener noreferrer" class="text-emerald-400 hover:text-emerald-300 font-semibold transition flex items-center gap-1.5">
                                <span>💬</span> WhatsApp CS 24/7
                            </a>
                        </li>
                        <li>
                            <a href="mailto:admin@budayarun.com" class="text-slate-400 hover:text-amber-400 transition flex items-center gap-1.5">
                                <span>✉️</span> Hubungi via Email
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Footnote & Legal Links -->
            <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-[11px] text-slate-500">
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-x-3 gap-y-2 text-slate-400">
                    <button type="button" @click="showPrivacyModal = true" class="hover:text-amber-400 text-slate-300 font-medium transition cursor-pointer">Privacy Policy</button>
                    <span class="text-slate-700">•</span>
                    <button type="button" @click="showTermsModal = true" class="hover:text-amber-400 text-slate-300 font-medium transition cursor-pointer">Term &amp; Condition</button>
                    <span class="text-slate-700">•</span>
                    <button type="button" @click="showContactModal = true" class="hover:text-amber-400 text-slate-300 font-medium transition cursor-pointer">Contact Us</button>
                    <span class="text-slate-700">•</span>
                    <span class="flex items-center gap-1 text-slate-400">🚚 Kurir: <strong>SPX Express</strong></span>
                    <span class="text-slate-700">•</span>
                    <span class="flex items-center gap-1 text-slate-400">💳 Payment: <strong>Tripay Gateway</strong></span>
                </div>
                <div class="text-center md:text-right text-slate-500">
                    &copy; {{ date('Y') }} Budaya Run Indonesia. Seluruh hak cipta dilindungi undang-undang.
                </div>
            </div>
        </div>
    </footer>

    <!-- Alpine.js Application Logic -->
    <script>
        function bhinnekaApp() {
            return {
                showSizeChart: false,
                showPrivacyModal: false,
                showTermsModal: false,
                showContactModal: false,
                // Target pendaftaran: 10 November 2026 23:59:59 WIB (UTC+7)
                targetDate: new Date("2026-11-10T23:59:59+07:00").getTime(),
                timeLeft: {
                    days: '00',
                    hours: '00',
                    minutes: '00',
                    seconds: '00'
                },
                init() {
                    this.updateCountdown();
                    setInterval(() => {
                        this.updateCountdown();
                    }, 1000);
                },
                updateCountdown() {
                    const now = new Date().getTime();
                    const diff = this.targetDate - now;

                    if (diff <= 0) {
                        this.timeLeft = { days: '00', hours: '00', minutes: '00', seconds: '00' };
                        return;
                    }

                    const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const s = Math.floor((diff % (1000 * 60)) / 1000);

                    this.timeLeft.days = String(d).padStart(2, '0');
                    this.timeLeft.hours = String(h).padStart(2, '0');
                    this.timeLeft.minutes = String(m).padStart(2, '0');
                    this.timeLeft.seconds = String(s).padStart(2, '0');
                }
            }
        }
    </script>
</body>
</html>
