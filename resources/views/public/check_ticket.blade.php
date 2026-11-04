<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Tiket &amp; E-BIB — LariVirtual.com</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Plus Jakarta Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-purple-950 border border-purple-800 p-1 flex items-center justify-center shadow-xs group-hover:scale-105 transition">
                    <img src="{{ asset('images/larivirtual/logo-icon-white.svg') }}" alt="LariVirtual" class="w-full h-full object-contain">
                </div>
                <div class="flex items-baseline">
                    <span class="text-lg font-black italic tracking-tight text-slate-900">lari<span class="text-orange-600">virtual</span></span>
                    <span class="text-xs font-extrabold text-purple-700">.com</span>
                </div>
            </a>
            <div class="flex items-center gap-3 text-xs font-semibold">
                <a href="{{ route('public.submit.index') }}" class="text-slate-600 hover:text-orange-600 transition">🏃 Submit Lari</a>
                <a href="{{ route('home') }}" class="text-orange-600 hover:text-orange-700 transition">Beranda</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12 flex-1 w-full">
        <!-- Hero Section -->
        <div class="text-center mb-8 sm:mb-10">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200 mb-3">
                Verifikasi Peserta
            </span>
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mb-2">
                Cek Status Tiket &amp; E-BIB
            </h1>
            <p class="text-slate-500 max-w-xl mx-auto text-xs sm:text-sm">
                Masukkan Kode Order, Email, No. WhatsApp, atau Nomor BIB yang Anda gunakan saat mendaftar untuk melihat nomor dada (BIB), melacak pengiriman race pack, dan mengunduh E-Ticket PDF.
            </p>
        </div>

        <!-- Error Alerts -->
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-xs mb-6 text-center font-bold">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Search Box -->
        <div class="bg-white border border-slate-200 rounded-3xl p-4 sm:p-6 shadow-sm mb-8 sm:mb-10">
            <form action="{{ route('public.ticket.search') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        name="query" 
                        value="{{ $search ?? '' }}" 
                        required 
                        placeholder="Ketik Kode Order (misal: JLTX-...), Email, No. WhatsApp, atau BIB..."
                        class="w-full bg-slate-50 border border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-2xl px-4 py-3.5 text-slate-900 placeholder-slate-400 text-xs sm:text-sm outline-none transition"
                    >
                </div>
                <button 
                    type="submit" 
                    class="bg-orange-600 hover:bg-orange-500 active:scale-95 text-white font-bold px-6 py-3.5 rounded-2xl text-xs sm:text-sm transition shadow-lg shadow-orange-600/25 flex items-center justify-center gap-2 cursor-pointer shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Cari Tiket Saya</span>
                </button>
            </form>
        </div>

        <!-- Search Results -->
        @if(isset($searched))
            @if(count($participants) > 0)
                <div class="space-y-4 sm:space-y-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span>Ditemukan {{ count($participants) }} Tiket Terdaftar &amp; Lunas</span>
                        </h2>
                    </div>

                    @foreach($participants as $p)
                        <div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-6 shadow-sm relative overflow-hidden transition hover:border-orange-300">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-5 sm:gap-6">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            &check; LUNAS / TERVERIFIKASI
                                        </span>

                                        <span class="text-xs text-slate-500 font-mono font-bold bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                                            Order: {{ $p->order?->order_code }}
                                        </span>
                                    </div>

                                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 mb-1">
                                        {{ $p->event?->title }}
                                    </h3>
                                    <p class="text-xs text-slate-500 mb-3">
                                        Kategori: <strong class="text-orange-600 font-bold">{{ $p->category?->name }} ({{ $p->category?->distance_km }} KM)</strong> 
                                        &bull; Mode: <strong>{{ $p->event?->submission_mode === 'CUMULATIVE' ? 'Akumulasi Jarak' : 'Single Run' }}</strong>
                                        &bull; Lokasi: Di Mana Saja (Virtual Run)
                                    </p>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                                        <div>
                                            <span class="text-slate-400 block text-[10px] font-semibold uppercase">Nama di E-BIB:</span>
                                            <span class="font-bold text-slate-900 font-mono">{{ $p->bib_name ?: $p->full_name }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block text-[10px] font-semibold uppercase">Ukuran Jersey:</span>
                                            <span class="font-bold text-slate-900">{{ $p->jerseySize ? $p->jerseySize->size_name . ' (' . ucfirst($p->jerseySize->gender_type) . ')' : '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-slate-400 block text-[10px] font-semibold uppercase">Paket Race Pack (SPX):</span>
                                            <span class="font-bold text-orange-600 block text-[11px]">{{ $p->event?->getRacepackDeliveryScheduleFormatted() }}</span>
                                            @php
                                                $shipping = $p->order?->shippingAddress;
                                            @endphp
                                            @if($shipping && $shipping->shipping_status === 'shipped')
                                                <span class="text-[10px] text-sky-600 font-bold">Resi: {{ $shipping->tracking_number ?: '-' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- BIB & Action Buttons -->
                                <div class="flex flex-col sm:flex-row md:flex-col items-center md:items-end justify-between gap-3 border-t md:border-t-0 md:border-l border-slate-200 pt-4 md:pt-0 md:pl-6 min-w-[210px]">
                                    @php
                                        $ebibBgUrl = $p->event?->ebib_background_path ? asset('storage/' . $p->event->ebib_background_path) : null;
                                    @endphp
                                    <div class="text-center md:text-right w-full sm:w-auto p-3 rounded-2xl relative overflow-hidden {{ $ebibBgUrl ? 'border border-orange-300 text-white shadow-xs' : 'border border-slate-200 bg-slate-50' }}" style="{{ $ebibBgUrl ? "background-image: url('{$ebibBgUrl}'); background-size: cover; background-position: center;" : '' }}">
                                        @if($ebibBgUrl)
                                            <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs"></div>
                                        @endif
                                        <div class="relative z-10">
                                            <span class="text-[9px] font-bold {{ $ebibBgUrl ? 'text-orange-300' : 'text-slate-400' }} uppercase tracking-widest block">Nomor Dada (E-BIB)</span>
                                            <span class="text-2xl sm:text-3xl font-black {{ $ebibBgUrl ? 'text-white' : 'text-slate-900' }} font-mono tracking-wider block my-0.5">
                                                {{ $p->bib_number ?: 'PROSES' }}
                                            </span>
                                            <span class="text-xs {{ $ebibBgUrl ? 'text-slate-200' : 'text-slate-500' }} font-semibold uppercase truncate block max-w-[180px]">
                                                {{ $p->bib_name ?: $p->full_name }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2 w-full">
                                        @if($p->bib_number)
                                            <a 
                                                href="{{ route('public.submit.index', ['bib' => $p->bib_number]) }}" 
                                                class="w-full inline-flex items-center justify-center gap-1.5 bg-orange-600 hover:bg-orange-500 active:scale-95 text-white font-bold px-4 py-2.5 rounded-xl text-xs transition shadow-md shadow-orange-600/20 cursor-pointer"
                                            >
                                                <span>🏃 Lapor / Submit Lari</span>
                                            </a>
                                        @endif

                                        <a 
                                            href="{{ route('public.ticket.download', $p->qr_token) }}" 
                                            class="w-full inline-flex items-center justify-center gap-1.5 bg-slate-100 hover:bg-slate-200 active:scale-95 text-slate-700 font-bold px-4 py-2 rounded-xl text-xs transition border border-slate-300 cursor-pointer"
                                        >
                                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            <span>Unduh E-Ticket PDF</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white border border-slate-200 rounded-3xl p-8 sm:p-12 text-center shadow-sm">
                    <div class="inline-flex p-3 rounded-full bg-rose-50 text-rose-600 mb-3 border border-rose-200">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900 mb-1">Tiket Tidak Ditemukan</h3>
                    <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto mb-4">
                        Tidak ditemukan pendaftaran yang lunas dengan kata kunci "<strong>{{ $search }}</strong>". Pastikan pembayaran Anda di Tripay telah berhasil dan masukkan Email, No. WhatsApp, BIB, atau Kode Order yang sesuai.
                    </p>
                </div>
            @endif
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto text-center text-xs text-slate-500">
        <div class="max-w-5xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} LariVirtual.com Platform. Hak Cipta Dilindungi.</p>
            <p class="text-slate-400">Didukung oleh Tripay Payment Gateway &amp; SPX Express.</p>
        </div>
    </footer>
</body>
</html>
