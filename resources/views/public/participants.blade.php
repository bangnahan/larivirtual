<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - Data Peserta & Pembayaran</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <!-- Plus Jakarta Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    @if($event->meta_pixel_id)
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ $event->meta_pixel_id }}');
    fbq('track', 'PageView');
    fbq('track', 'InitiateCheckout', {
        content_name: '{{ addslashes($event->title) }}',
        currency: 'IDR',
        value: {{ (float) ($totalPrice + ($totalAddonsPrice ?? 0)) }}
    });
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ $event->meta_pixel_id }}&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
    @endif
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased" x-data="{
    subtotalTickets: {{ (int) $totalPrice }},
    subtotalAddons: {{ (int) ($totalAddonsPrice ?? 0) }},
    shippingCost: 0,
    platformFee: 5000,
    showSizeChart: false,
    selectedPayment: '{{ old('payment_method', 'QRIS') }}',
    get grandTotal() {
        return this.subtotalTickets + this.subtotalAddons + this.shippingCost + this.platformFee;
    },
    formatRupiah(num) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
    },
    copyPicToFirstRunner() {
        let picName = document.getElementById('buyerNameInput')?.value || '';
        let picPhone = document.getElementById('buyerPhoneInput')?.value || '';

        if (!picName) {
            alert('Mohon isi Nama Lengkap Pemesan (PIC) terlebih dahulu.');
            return;
        }

        let p0Bib = document.getElementById('p_0_bib_name');
        if (p0Bib) p0Bib.value = picName.toUpperCase();

        // Juga salin ke penerima paket jika masih kosong
        let recName = document.getElementById('recipientNameInput');
        let recPhone = document.getElementById('recipientPhoneInput');
        if (recName && !recName.value) recName.value = picName;
        if (recPhone && !recPhone.value) recPhone.value = picPhone;
    }
}">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('public.register.show', $event->slug) }}" class="flex items-center gap-2.5 font-extrabold text-lg text-slate-900 tracking-tight">
                <div class="w-8 h-8 rounded-lg bg-orange-50 border border-orange-200/60 p-1 flex items-center justify-center shadow-xs">
                    <span class="text-orange-600 font-black text-sm">VR</span>
                </div>
                <span class="truncate max-w-[220px] sm:max-w-md">{{ $event->title }}</span>
            </a>
            <a href="{{ route('public.register.show', $event->slug) }}" class="text-xs text-orange-600 hover:text-orange-700 transition font-bold flex items-center gap-1">
                &larr; Ubah Tiket
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 py-6 pb-36">
        <!-- Error Alerts -->
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl mb-6 text-sm">
                <div class="font-bold flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Mohon lengkapi data formulir berikut:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Stepper Navigation -->
        <div class="flex items-center justify-between mb-8 max-w-xl mx-auto text-xs font-semibold">
            <a href="{{ route('public.register.show', $event->slug) }}" class="flex items-center gap-2 text-emerald-600 hover:underline">
                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold">✓</span>
                <span>Pilih Tiket</span>
            </a>
            <div class="h-0.5 flex-1 bg-emerald-500 mx-3"></div>
            <div class="flex items-center gap-2 text-orange-600">
                <span class="w-7 h-7 rounded-full bg-orange-600 text-white flex items-center justify-center font-bold">2</span>
                <span>Data Peserta</span>
            </div>
            <div class="h-0.5 flex-1 bg-slate-200 mx-3"></div>
            <div class="flex items-center gap-2 text-slate-400">
                <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-bold">3</span>
                <span>Pembayaran</span>
            </div>
        </div>

        <!-- Ticket Summary Banner -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 mb-8 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="space-y-2">
                <div>
                    <div class="text-xs font-semibold text-slate-500 mb-1">Tiket yang Dipilih:</div>
                    <div class="flex flex-wrap items-center gap-2">
                        @foreach($selectedTickets as $catId => $data)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-orange-50 border border-orange-200 text-orange-950 font-bold text-xs">
                                <span class="w-5 h-5 rounded-md bg-slate-900 text-white text-[10px] flex items-center justify-center font-black">
                                    {{ $data['qty'] }}x
                                </span>
                                <span>{{ $data['category']->name }}</span>
                                <span class="text-slate-500 font-normal">({{ number_format($data['subtotal'], 0, ',', '.') }})</span>
                            </span>
                        @endforeach
                    </div>
                </div>

                @if(!empty($selectedAddons))
                    <div>
                        <div class="text-xs font-semibold text-slate-500 mb-1">Add-on &amp; Merchandise Tambahan:</div>
                        <div class="flex flex-wrap items-center gap-2">
                            @foreach($selectedAddons as $addonId => $data)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-50 border border-amber-200 text-amber-950 font-bold text-xs">
                                    <span class="w-5 h-5 rounded-md bg-amber-700 text-white text-[10px] flex items-center justify-center font-black">
                                        {{ $data['qty'] }}x
                                    </span>
                                    <span>{{ $data['addon']->name }}</span>
                                    <span class="text-slate-500 font-normal">({{ number_format($data['subtotal'], 0, ',', '.') }})</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <div class="text-left md:text-right shrink-0 pt-2 md:pt-0 border-t md:border-t-0 border-slate-100 w-full md:w-auto">
                <div class="text-xs text-slate-500">Subtotal Item:</div>
                <div class="text-lg font-black text-slate-900">
                    Rp {{ number_format($totalPrice + ($totalAddonsPrice ?? 0), 0, ',', '.') }}
                </div>
                @if(!empty($totalAddonsPrice) && $totalAddonsPrice > 0)
                    <div class="text-[10px] text-slate-400">
                        Tiket: Rp {{ number_format($totalPrice, 0, ',', '.') }} + Add-on: Rp {{ number_format($totalAddonsPrice, 0, ',', '.') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Checkout Form -->
        <form action="{{ route('public.register.checkout', $event->slug) }}" method="POST" id="checkoutForm">
            @csrf

            @if(!empty($selectedAddons))
                @foreach($selectedAddons as $addonId => $data)
                    <input type="hidden" name="addons[{{ $addonId }}]" value="{{ $data['qty'] }}">
                @endforeach
            @endif

            <!-- 1. Data Pemesan (PIC Utama) -->
            <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 p-5 sm:p-7 shadow-sm mb-6 sm:mb-8">
                <div class="flex items-center justify-between mb-1 gap-2">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-full bg-slate-900 text-white font-black text-xs flex items-center justify-center shrink-0">1</span>
                        <span>Data Pemesan (PIC Utama)</span>
                    </h2>
                    <span class="text-[11px] font-bold text-orange-700 bg-orange-100/80 px-2.5 py-0.5 rounded-full border border-orange-200">
                        Kontak Pembayaran
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-5">
                    Invoice resmi Tripay, tanda terima, dan seluruh arsip E-Ticket akan dikirimkan ke kontak penanggung jawab ini.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Pemesan *</label>
                        <input 
                            type="text" 
                            name="buyer_name" 
                            id="buyerNameInput" 
                            value="{{ old('buyer_name') }}" 
                            required 
                            placeholder="Contoh: Budi Santoso"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Pemesan *</label>
                        <input 
                            type="email" 
                            name="buyer_email" 
                            id="buyerEmailInput" 
                            value="{{ old('buyer_email') }}" 
                            required 
                            placeholder="budi@example.com"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                        >
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp Pemesan *</label>
                        <input 
                            type="tel" 
                            name="buyer_phone" 
                            id="buyerPhoneInput" 
                            value="{{ old('buyer_phone') }}" 
                            required 
                            placeholder="0812xxxxxxxx"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                        >
                    </div>
                </div>
            </section>

            <!-- 2. Formulir Data Pelari -->
            <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 p-5 sm:p-7 shadow-sm mb-6 sm:mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2.5">
                            <span class="w-7 h-7 rounded-full bg-slate-900 text-white font-black text-xs flex items-center justify-center shrink-0">2</span>
                            <span>Data Pelari &amp; Nomor Dada (BIB)</span>
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Lengkapi data diri seluruh {{ $totalCount }} pelari untuk keperluan pencetakan E-BIB, E-Sertifikat, dan asuransi.
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button 
                            type="button" 
                            @click="copyPicToFirstRunner()" 
                            class="text-xs bg-orange-50 hover:bg-orange-100 text-orange-700 font-bold px-3.5 py-2 rounded-xl border border-orange-200 transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                            <span>📋</span>
                            <span>Salin PIC ke Pelari #1</span>
                        </button>
                    </div>
                </div>

                <div class="space-y-6">
                    @foreach($participantSlots as $slot)
                        @php
                            $i = $slot['index'];
                            $cat = $slot['category'];
                            $distText = $cat->distance_km ? rtrim(rtrim(number_format($cat->distance_km, 1), '0'), '.') . 'K' : 'OPEN';
                        @endphp
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5 sm:p-6 transition hover:border-orange-300">
                            <!-- Header Kartu Pelari -->
                            <div class="flex flex-wrap items-center justify-between gap-2.5 mb-5 pb-3 border-b border-slate-200">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-lg bg-orange-600 text-white font-extrabold text-xs flex items-center justify-center shadow-xs">
                                        #{{ $i + 1 }}
                                    </span>
                                    <h3 class="font-extrabold text-slate-900 text-sm sm:text-base">
                                        Pelari {{ $i + 1 }} dari {{ $totalCount }}
                                    </h3>
                                    <span class="px-2.5 py-0.5 rounded-md bg-slate-900 text-white font-black text-[11px] tracking-wider">
                                        {{ $distText }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-600">
                                        {{ $cat->name }}
                                    </span>
                                </div>
                                <span class="text-xs font-bold text-slate-700 bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                                    Rp {{ number_format($cat->getCurrentPrice(), 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Hidden Category ID -->
                            <input type="hidden" name="participants[{{ $i }}][category_id]" value="{{ $cat->id }}">

                            <!-- Form Pelari (Cukup: Nama di E-BIB, Jenis Kelamin, Size Jersey) -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <!-- 1. Nama di E-BIB -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        Nama di E-BIB *
                                    </label>
                                    <input 
                                        type="text" 
                                        id="p_{{ $i }}_bib_name"
                                        name="participants[{{ $i }}][bib_name]" 
                                        value="{{ old("participants.{$i}.bib_name") }}" 
                                        required 
                                        maxlength="30" 
                                        placeholder="Contoh: FAJAR PRATAMA" 
                                        class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition uppercase font-semibold"
                                    >
                                    <p class="text-[10px] text-slate-500 mt-1">Dicetak pada nomor dada &amp; sertifikat.</p>
                                </div>

                                <!-- 2. Jenis Kelamin -->
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1">
                                        Jenis Kelamin *
                                    </label>
                                    <select 
                                        name="participants[{{ $i }}][gender]" 
                                        required 
                                        class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition font-medium">
                                        <option value="male" {{ old("participants.{$i}.gender") == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="female" {{ old("participants.{$i}.gender") == 'female' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    <p class="text-[10px] text-slate-500 mt-1">Untuk kategori leaderboard pelari.</p>
                                </div>

                                <!-- 3. Ukuran Jersey -->
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-xs font-bold text-slate-700">
                                            Size Jersey *
                                        </label>
                                        <button 
                                            type="button" 
                                            @click="showSizeChart = true" 
                                            class="text-[11px] font-bold text-orange-600 hover:text-orange-700 underline cursor-pointer">
                                            Size Chart 📏
                                        </button>
                                    </div>
                                    <select 
                                        name="participants[{{ $i }}][jersey_size_id]" 
                                        required 
                                        class="w-full bg-white border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition font-medium">
                                        <option value="">-- Pilih Ukuran Jersey --</option>
                                        @foreach($event->jerseySizes as $size)
                                            <option value="{{ $size->id }}" {{ old("participants.{$i}.jersey_size_id") == $size->id ? 'selected' : '' }}>
                                                {{ $size->size_name }} (LD: {{ $size->chest_width_cm }} cm, P: {{ $size->body_length_cm }} cm)
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-slate-500 mt-1">Jersey dryfit eksklusif finisher.</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- 3. Alamat Pengiriman Race Pack Fisik (SPX Express) -->
            <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 p-5 sm:p-7 shadow-sm mb-6 sm:mb-8">
                <div class="flex items-center justify-between mb-1 gap-2">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-full bg-slate-900 text-white font-black text-xs flex items-center justify-center shrink-0">3</span>
                        <span>Alamat Pengiriman Race Pack (SPX Express)</span>
                    </h2>
                    <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                        📦 Dikirim ke Rumah
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-5">
                    Jersey, medali, dan seluruh perlengkapan lari {{ $totalCount }} peserta akan dikirim dalam 1 paket via SPX Regular Express.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Penerima Paket *</label>
                        <input 
                            type="text" 
                            name="recipient_name" 
                            id="recipientNameInput" 
                            value="{{ old('recipient_name') }}" 
                            required 
                            placeholder="Nama penerima paket" 
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                        >
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp Penerima *</label>
                        <input 
                            type="tel" 
                            name="recipient_phone" 
                            id="recipientPhoneInput" 
                            value="{{ old('recipient_phone') }}" 
                            required 
                            placeholder="0812xxxxxxxx" 
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                        >
                    </div>
                </div>

                <!-- Autocomplete Kota & Kecamatan SPX -->
                <div class="mb-4 relative">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Cari Kota &amp; Kecamatan Tujuan (SPX Official Rates) *</label>
                    <div class="relative">
                        <input 
                            type="text" 
                            id="spxDestinationInput" 
                            value="{{ old('destination_district') ? old('destination_district') . ', ' . old('destination_city') : '' }}"
                            placeholder="Ketik nama kota atau kecamatan (contoh: Kuta, Gambir, Sukmajaya, Serpong, Bandung...)" 
                            autocomplete="off"
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                        >
                        <div id="spxSearchingSpinner" class="hidden absolute right-3.5 top-3">
                            <svg class="animate-spin h-4 w-4 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        </div>
                    </div>
                    
                    <!-- Dropdown Hasil Autocomplete -->
                    <div id="spxResultsDropdown" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-2xl z-30 max-h-60 overflow-y-auto no-scrollbar">
                    </div>

                    <!-- Hidden inputs untuk checkout POST -->
                    <input type="hidden" name="destination_city" id="destCityHidden" value="{{ old('destination_city') }}">
                    <input type="hidden" name="destination_district" id="destDistrictHidden" value="{{ old('destination_district') }}">
                </div>

                <!-- Kartu Hasil Ongkir SPX -->
                <div id="spxShippingResultCard" class="hidden bg-orange-50/60 p-4 rounded-xl border border-orange-200 mb-4">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-orange-600 text-white flex items-center justify-center font-black font-mono text-xs shadow-xs">
                                SPX
                            </div>
                            <div>
                                <div class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                    <span>SPX Regular Express</span>
                                    <span id="spxSlaBadge" class="text-[10px] bg-white text-orange-700 border border-orange-200 px-2 py-0.5 rounded-full font-mono font-bold">
                                        Estimasi 2-3 Hari
                                    </span>
                                </div>
                                <div class="text-xs text-slate-600">
                                    Origin: Tangerang ➔ <span id="spxDestText" class="text-slate-900 font-semibold">-</span> (<span id="spxWeightText">{{ $totalCount * 500 }} gr</span>)
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-slate-500 font-medium">Ongkos Kirim</div>
                            <div id="spxCostDisplay" class="text-base font-black text-emerald-600 font-mono">
                                Rp 0
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Lengkap Pengiriman *</label>
                        <textarea 
                            name="address_detail" 
                            id="addressDetailInput" 
                            rows="2" 
                            required 
                            placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, patokan lokasi..." 
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                        >{{ old('address_detail') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode Pos (Opsional)</label>
                        <input 
                            type="text" 
                            name="postal_code" 
                            id="postalCodeInput" 
                            value="{{ old('postal_code') }}" 
                            placeholder="Contoh: 15111" 
                            class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm text-slate-900 placeholder-slate-400 outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition"
                        >
                    </div>
                </div>
            </section>

            <!-- 4. Pilihan Metode Pembayaran (Dropdown Sederhana Tanpa Icon) -->
            <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 p-5 sm:p-7 shadow-sm mb-6 sm:mb-8">
                <div class="flex items-center justify-between mb-1 gap-2">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded-full bg-slate-900 text-white font-black text-xs flex items-center justify-center shrink-0">4</span>
                        <span>Metode Pembayaran (Tripay)</span>
                    </h2>
                    <span class="text-[11px] font-bold text-orange-700 bg-orange-50 px-2.5 py-0.5 rounded-full border border-orange-200">
                        Otomatis &amp; Real-time
                    </span>
                </div>
                <p class="text-xs text-slate-500 mb-4">
                    Pilih kanal pembayaran resmi Tripay. Transaksi akan terkonfirmasi secara instan setelah pembayaran berhasil.
                </p>

                @php
                    $groupedChannels = [];
                    foreach ($paymentChannels as $ch) {
                        $grp = $ch['group'] ?? 'Lainnya';
                        $groupedChannels[$grp][] = $ch;
                    }
                @endphp

                <div class="max-w-md">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Pilih Kanal Pembayaran *</label>
                    <select 
                        name="payment_method" 
                        required 
                        class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-900 outline-none focus:bg-white focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition cursor-pointer">
                        @foreach($groupedChannels as $groupName => $channels)
                            <optgroup label="{{ $groupName }}">
                                @foreach($channels as $channel)
                                    <option value="{{ $channel['code'] }}" {{ old('payment_method', 'QRIS') == $channel['code'] ? 'selected' : '' }}>
                                        {{ $channel['name'] }} ({{ $channel['code'] }})
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1.5">Instruksi pembayaran resmi akan tampil seketika setelah checkout.</p>
                </div>
            </section>

            <!-- 5. Syarat & Ketentuan (Waiver) -->
            <section class="bg-white rounded-2xl sm:rounded-3xl border border-slate-200 p-5 sm:p-7 shadow-sm mb-6 sm:mb-8">
                <h2 class="text-sm font-bold text-slate-900 mb-2">Syarat, Ketentuan &amp; Waiver Perlombaan</h2>
                <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs text-slate-600 mb-4 max-h-32 overflow-y-auto leading-relaxed">
                    {{ $event->waiver_content ?: 'Dengan mendaftar lomba virtual ini, saya menyatakan dalam kondisi kesehatan jasmani dan rohani yang baik. Saya memahami segala risiko yang mungkin timbul selama aktivitas lari mandiri di jalan raya atau lokasi publik adalah tanggung jawab pribadi saya sepenuhnya.' }}
                </div>
                <label class="flex items-start gap-3 text-xs text-slate-700 cursor-pointer">
                    <input 
                        type="checkbox" 
                        name="waiver_accepted" 
                        value="1" 
                        required 
                        {{ old('waiver_accepted') ? 'checked' : '' }}
                        class="mt-0.5 rounded border-slate-300 text-orange-600 focus:ring-orange-500 w-4 h-4 cursor-pointer"
                    >
                    <span>Saya telah membaca, memahami, dan menyetujui seluruh ketentuan lomba serta waiver keselamatan di atas atas nama seluruh peserta terdaftar.</span>
                </label>
            </section>

            <!-- Sticky Bottom Summary Bar -->
            <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-200 p-4 z-40 shadow-2xl">
                <div class="max-w-4xl mx-auto flex items-center justify-between gap-4">
                    <div>
                        <div class="text-[11px] text-slate-500 flex items-center gap-1.5 flex-wrap">
                            <span>Tiket: <strong class="text-slate-700" x-text="formatRupiah(subtotalTickets)"></strong></span>
                            <span x-show="subtotalAddons > 0">&bull;</span>
                            <span x-show="subtotalAddons > 0">Add-on: <strong class="text-slate-700" x-text="formatRupiah(subtotalAddons)"></strong></span>
                            <span>&bull;</span>
                            <span>SPX: <strong class="text-slate-700" x-text="formatRupiah(shippingCost)"></strong></span>
                            <span>&bull;</span>
                            <span>Fee: <strong>Rp 5.000</strong></span>
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <span class="text-xl sm:text-2xl font-black text-orange-600" x-text="formatRupiah(grandTotal)">
                                Rp {{ number_format($totalPrice + ($totalAddonsPrice ?? 0) + 5000, 0, ',', '.') }}
                            </span>
                            <span class="text-xs text-slate-500 font-medium">
                                ({{ $totalCount }} Tiket @if(!empty($totalAddonsCount) && $totalAddonsCount > 0) + {{ $totalAddonsCount }} Add-on @endif)
                            </span>
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        class="bg-orange-600 hover:bg-orange-500 active:scale-95 text-white font-bold text-sm px-6 py-3 rounded-xl transition shadow-lg shadow-orange-600/30 flex items-center gap-2 cursor-pointer">
                        <span>Lanjut ke Pembayaran Tripay</span>
                        <span>&rarr;</span>
                    </button>
                </div>
            </div>
        </form>
    </main>

    <!-- Modal Panduan Ukuran Kaos (Size Chart) -->
    <div x-show="showSizeChart" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-xs" 
         style="display: none;">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 relative" @click.away="showSizeChart = false">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-xl">📏</span>
                    <h3 class="font-extrabold text-base text-slate-900">Panduan Ukuran Jersey Dryfit</h3>
                </div>
                <button type="button" @click="showSizeChart = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">
                    &times;
                </button>
            </div>
            
            <p class="text-xs text-slate-500 mb-4">
                Ukur lebar dada (dari ketiak kiri ke ketiak kanan) pada kaos yang biasa Anda kenakan dalam posisi mendatar.
            </p>

            <div class="overflow-x-auto rounded-xl border border-slate-200 mb-4">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3">Ukuran</th>
                            <th class="py-2.5 px-3">Lebar Dada (cm)</th>
                            <th class="py-2.5 px-3">Panjang Kaos (cm)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        @foreach($event->jerseySizes as $size)
                            <tr class="hover:bg-orange-50/50">
                                <td class="py-2 px-3 font-bold text-slate-900">{{ $size->size_name }}</td>
                                <td class="py-2 px-3 font-mono">{{ $size->chest_width_cm }} cm</td>
                                <td class="py-2 px-3 font-mono">{{ $size->body_length_cm }} cm</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="button" @click="showSizeChart = false" class="w-full bg-slate-900 text-white font-bold py-2.5 rounded-xl text-xs hover:bg-slate-800 transition">
                Tutup Panduan
            </button>
        </div>
    </div>

    <!-- SPX Shipping Calculation Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const destInput = document.getElementById('spxDestinationInput');
            const destDropdown = document.getElementById('spxResultsDropdown');
            const destSpinner = document.getElementById('spxSearchingSpinner');
            const destCityHidden = document.getElementById('destCityHidden');
            const destDistrictHidden = document.getElementById('destDistrictHidden');
            const shippingResultCard = document.getElementById('spxShippingResultCard');
            const spxSlaBadge = document.getElementById('spxSlaBadge');
            const spxDestText = document.getElementById('spxDestText');
            const spxCostDisplay = document.getElementById('spxCostDisplay');

            const totalWeightGrams = {{ $totalCount * 500 }};
            let searchTimeout = null;

            // Jika ada data old() sebelumnya, hitung otomatis saat load
            if (destCityHidden.value && destDistrictHidden.value) {
                calculateSpxRate(destCityHidden.value, destDistrictHidden.value);
            }

            destInput.addEventListener('input', function () {
                const query = this.value.trim();
                clearTimeout(searchTimeout);

                if (query.length < 2) {
                    destDropdown.classList.add('hidden');
                    destDropdown.innerHTML = '';
                    return;
                }

                destSpinner.classList.remove('hidden');

                searchTimeout = setTimeout(() => {
                    fetch(`/api/shipping/spx/search?q=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(res => {
                            destSpinner.classList.add('hidden');
                            if (!res.data || res.data.length === 0) {
                                destDropdown.innerHTML = '<div class="p-3 text-xs text-slate-400 text-center">Kecamatan/kota tidak ditemukan.</div>';
                                destDropdown.classList.remove('hidden');
                                return;
                            }

                            let html = '';
                            res.data.forEach(item => {
                                html += `
                                    <div class="spx-item px-4 py-2.5 hover:bg-orange-50 cursor-pointer border-b border-slate-100 last:border-0 transition"
                                         data-city="${item.destination_city}"
                                         data-district="${item.destination_district}">
                                        <div class="text-xs font-bold text-slate-900">${item.destination_district}</div>
                                        <div class="text-[11px] text-slate-500">${item.destination_city}</div>
                                    </div>
                                `;
                            });
                            destDropdown.innerHTML = html;
                            destDropdown.classList.remove('hidden');

                            document.querySelectorAll('.spx-item').forEach(el => {
                                el.addEventListener('click', function () {
                                    const city = this.dataset.city;
                                    const district = this.dataset.district;
                                    destInput.value = `${district}, ${city}`;
                                    destCityHidden.value = city;
                                    destDistrictHidden.value = district;
                                    destDropdown.classList.add('hidden');

                                    calculateSpxRate(city, district);
                                });
                            });
                        })
                        .catch(() => {
                            destSpinner.classList.add('hidden');
                        });
                }, 300);
            });

            document.addEventListener('click', function (e) {
                if (!destInput.contains(e.target) && !destDropdown.contains(e.target)) {
                    destDropdown.classList.add('hidden');
                }
            });

            function calculateSpxRate(city, district) {
                fetch('/api/shipping/spx/calculate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        destination_city: city,
                        destination_district: district,
                        total_weight_grams: totalWeightGrams
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const cost = data.data.shipping_cost;
                        const sla = data.data.sla_days;

                        spxDestText.innerText = `${district}, ${city}`;
                        spxSlaBadge.innerText = `Estimasi ${sla} Hari`;
                        spxCostDisplay.innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(cost);
                        shippingResultCard.classList.remove('hidden');

                        // Update Alpine.js reactive state
                        if (window.Alpine) {
                            const body = document.querySelector('body');
                            if (body && body._x_dataStack && body._x_dataStack[0]) {
                                body._x_dataStack[0].shippingCost = cost;
                            }
                        }
                    }
                })
                .catch(err => {
                    console.error('SPX shipping error:', err);
                });
            }
        });
    </script>
</body>
</html>
