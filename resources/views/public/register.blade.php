<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event->title }} - Pilih Tiket</title>
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
    fbq('track', 'ViewContent', {
        content_name: '{{ addslashes($event->title) }}',
        content_category: 'Virtual Run',
        content_ids: ['{{ $event->slug }}'],
        content_type: 'product'
    });
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ $event->meta_pixel_id }}&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
    @endif
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-extrabold text-lg text-slate-900 tracking-tight">
                <div class="w-8 h-8 rounded-lg bg-orange-50 border border-orange-200/60 p-1 flex items-center justify-center shadow-xs">
                    <span class="text-orange-600 font-black text-sm">VR</span>
                </div>
                <span class="truncate max-w-[220px] sm:max-w-md">{{ $event->title }}</span>
            </a>
            <a href="{{ route('home') }}" class="text-xs text-slate-500 hover:text-slate-800 transition font-medium flex items-center gap-1">
                &larr; <span class="hidden sm:inline">Kembali ke</span> Beranda
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 py-6 pb-28">
        <!-- Flash Messages & Errors -->
        @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl mb-6 text-sm">
                <div class="font-bold flex items-center gap-2 mb-1">
                    <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Perhatian:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $initialQuantities = [];
            $prices = [];
            $quotas = [];
            $savedTickets = session()->get("event_{$event->id}_tickets", []);

            foreach($event->categories as $cat) {
                $initialQuantities[$cat->id] = (int) ($savedTickets[$cat->id] ?? old("tickets.{$cat->id}", 0));
                $prices[$cat->id] = (float) $cat->getCurrentPrice();
                $quotas[$cat->id] = (int) $cat->available_slots;
            }

            $addonQuantities = [];
            $addonPrices = [];
            $addonQuotas = [];
            $savedAddons = session()->get("event_{$event->id}_addons", []);

            foreach($event->addons as $addon) {
                $addonQuantities[$addon->id] = (int) ($savedAddons[$addon->id] ?? old("addons.{$addon->id}", 0));
                $addonPrices[$addon->id] = (float) $addon->price;
                $addonQuotas[$addon->id] = $addon->isUnlimited() ? 999 : (int) $addon->available_stock;
            }
        @endphp

        <div x-data="{
            quantities: {{ json_encode($initialQuantities) }},
            prices: {{ json_encode($prices) }},
            quotas: {{ json_encode($quotas) }},
            addonQuantities: {{ json_encode($addonQuantities) }},
            addonPrices: {{ json_encode($addonPrices) }},
            addonQuotas: {{ json_encode($addonQuotas) }},
            changeQty(id, delta) {
                let current = this.quantities[id] || 0;
                let next = current + delta;
                if (next < 0) next = 0;
                if (next > this.quotas[id]) next = this.quotas[id];
                
                // Cek batas total maksimal 10 tiket per pemesanan
                let currentTotal = this.totalTickets;
                if (delta > 0 && currentTotal >= 10) {
                    alert('Maksimal pemesanan tiket adalah 10 tiket per transaksi.');
                    return;
                }
                
                this.quantities[id] = next;
            },
            changeAddonQty(id, delta) {
                let current = this.addonQuantities[id] || 0;
                let next = current + delta;
                if (next < 0) next = 0;
                let maxQuota = this.addonQuotas[id] !== undefined ? this.addonQuotas[id] : 999;
                if (next > maxQuota) next = maxQuota;

                this.addonQuantities[id] = next;
            },
            get totalTickets() {
                return Object.values(this.quantities).reduce((a, b) => a + Number(b), 0);
            },
            get totalAddons() {
                return Object.values(this.addonQuantities).reduce((a, b) => a + Number(b), 0);
            },
            get ticketPrice() {
                let sum = 0;
                for (let id in this.quantities) {
                    sum += (this.quantities[id] * this.prices[id]);
                }
                return sum;
            },
            get addonPrice() {
                let sum = 0;
                for (let id in this.addonQuantities) {
                    sum += (this.addonQuantities[id] * this.addonPrices[id]);
                }
                return sum;
            },
            get totalPrice() {
                return this.ticketPrice + this.addonPrice;
            },
            formatRupiah(num) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
            }
        }">
            <!-- Event Banner & Header -->
            <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-orange-950 text-white p-6 sm:p-8 rounded-2xl shadow-xl mb-8 border border-slate-700/50">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-500/20 border border-orange-500/30 text-orange-300 text-xs font-semibold mb-3">
                    <span class="inline-block w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span>
                    <span>REGISTRASI RESMI DIBUKA</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mb-2">{{ $event->title }}</h1>
                <p class="text-slate-300 text-sm mb-6 leading-relaxed max-w-2xl">
                    {{ $event->short_description ?: 'Tantangan lari virtual nasional. Selesaikan lari Anda di mana saja, lacak progres km Anda, dan raih Race Pack eksklusif yang dikirim langsung via SPX Express.' }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-4 border-t border-slate-700/60 text-xs">
                    <div class="flex items-center gap-2 text-slate-200">
                        <span class="text-base">📅</span>
                        <div>
                            <div class="text-slate-400 font-medium">Periode Lari</div>
                            <div class="font-bold">
                                @php
                                    $raceStart = $event->race_start_date ? \Illuminate\Support\Carbon::parse($event->race_start_date) : ($event->event_start_date ? \Illuminate\Support\Carbon::parse($event->event_start_date) : null);
                                    $raceEnd = $event->race_end_date ? \Illuminate\Support\Carbon::parse($event->race_end_date) : ($event->event_end_date ? \Illuminate\Support\Carbon::parse($event->event_end_date) : null);
                                @endphp
                                {{ $raceStart ? $raceStart->format('d M Y') : 'Segera Dimulai' }}
                                @if($raceEnd)
                                    - {{ $raceEnd->format('d M Y') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-slate-200">
                        <span class="text-base">📦</span>
                        <div>
                            <div class="text-slate-400 font-medium">Pengiriman Race Pack</div>
                            <div class="font-bold text-orange-300">{{ $event->getRacepackDeliveryScheduleFormatted() }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-slate-200">
                        <span class="text-base">⏰</span>
                        <div>
                            <div class="text-slate-400 font-medium">Mode Lomba</div>
                            <div class="font-bold">{{ $event->submission_mode === 'CUMULATIVE' ? 'Akumulasi Jarak' : 'Single Run' }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-slate-200">
                        <span class="text-base">📍</span>
                        <div>
                            <div class="text-slate-400 font-medium">Lokasi Venue</div>
                            <div class="font-bold truncate">{{ $event->race_location_name ?: 'Di Mana Saja (Virtual Run)' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stepper Navigation -->
            <div class="flex items-center justify-between mb-8 max-w-xl mx-auto text-xs font-semibold">
                <div class="flex items-center gap-2 text-orange-600">
                    <span class="w-7 h-7 rounded-full bg-orange-600 text-white flex items-center justify-center font-bold">1</span>
                    <span>Pilih Tiket</span>
                </div>
                <div class="h-0.5 flex-1 bg-slate-200 mx-3"></div>
                <div class="flex items-center gap-2 text-slate-400">
                    <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-bold">2</span>
                    <span>Data Peserta</span>
                </div>
                <div class="h-0.5 flex-1 bg-slate-200 mx-3"></div>
                <div class="flex items-center gap-2 text-slate-400">
                    <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center font-bold">3</span>
                    <span>Pembayaran</span>
                </div>
            </div>

            <!-- Form Submission to Step 2 -->
            <form action="{{ route('public.register.participants', $event->slug) }}" method="POST">
                @csrf

                <div class="space-y-4 mb-10">
                    @forelse($event->categories as $category)
                        @php
                            $isEarlyBird = $category->early_bird_price && $category->early_bird_end_date && now()->lessThanOrEqualTo($category->early_bird_end_date);
                            $currentPrice = $category->getCurrentPrice();
                            $availableQuota = $category->available_slots;
                            $isSoldOut = $availableQuota <= 0;
                            $distText = $category->distance_km ? rtrim(rtrim(number_format($category->distance_km, 1), '0'), '.') . 'K' : 'OPEN';
                        @endphp
                        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 hover:border-orange-300 transition shadow-sm hover:shadow-md flex flex-col md:flex-row md:items-center justify-between gap-5">
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2.5 mb-2">
                                    <span class="px-2.5 py-0.5 rounded-md bg-slate-900 text-white font-extrabold text-xs tracking-wider">
                                        {{ $distText }}
                                    </span>
                                    <h3 class="font-bold text-slate-900 text-lg">{{ $category->name }}</h3>
                                    
                                    @if($isEarlyBird && !$isSoldOut)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold border border-amber-300">
                                            🔥 Early Bird
                                        </span>
                                    @endif
                                    
                                    @if($isSoldOut)
                                        <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[11px] font-bold border border-rose-200">
                                            Sold Out
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-medium border border-emerald-200">
                                            Sisa {{ number_format($availableQuota) }} tiket
                                        </span>
                                    @endif
                                </div>

                                <p class="text-xs text-slate-500 mb-3">
                                    {{ $category->description ?: 'Kategori lomba lari virtual. Tuntaskan tantangan lari di rute favorit Anda dan raih paket finisher resmi.' }}
                                </p>

                                <!-- Paket Tiket Virtual Run Inclusions -->
                                <div class="flex flex-wrap gap-1.5 mb-3 text-[11px] text-slate-600 font-medium">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-orange-50 text-orange-800 border border-orange-200/60 font-semibold">
                                        <span>🎽</span> Jersey Dryfit
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200/60 font-semibold">
                                        <span>🏅</span> Medali Finisher Logam
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-800 border border-sky-200/60 font-semibold">
                                        <span>📦</span> Race Pack via SPX
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200/60 font-semibold">
                                        <span>📜</span> E-BIB &amp; E-Sertifikat
                                    </span>
                                </div>

                                <!-- Price Section -->
                                <div class="flex items-baseline gap-2">
                                    <span class="text-xl font-extrabold text-slate-900">
                                        Rp {{ number_format($currentPrice, 0, ',', '.') }}
                                    </span>
                                    @if($isEarlyBird && $category->normal_price > $category->early_bird_price)
                                        <span class="text-xs text-slate-400 line-through">
                                            Rp {{ number_format($category->normal_price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-slate-500">/ tiket</span>
                                </div>
                            </div>

                            <!-- Quantity Stepper -->
                            <div class="flex items-center justify-between md:justify-end gap-3 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100">
                                @if(!$isSoldOut)
                                    <div class="flex items-center rounded-xl border border-slate-300 bg-slate-50 overflow-hidden shadow-inner">
                                        <button type="button" @click="changeQty('{{ $category->id }}', -1)" class="w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-slate-200 active:bg-slate-300 font-black text-lg transition select-none">
                                            &minus;
                                        </button>
                                        <input type="number" 
                                               name="tickets[{{ $category->id }}]" 
                                               x-model.number="quantities['{{ $category->id }}']" 
                                               min="0" 
                                               :max="quotas['{{ $category->id }}']" 
                                               class="w-14 text-center bg-white h-10 border-x border-slate-300 font-bold text-slate-900 text-sm focus:outline-none focus:bg-orange-50/50" />
                                        <button type="button" @click="changeQty('{{ $category->id }}', 1)" class="w-10 h-10 flex items-center justify-center text-slate-600 hover:bg-slate-200 active:bg-slate-300 font-black text-lg transition select-none">
                                            &plus;
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs font-bold text-rose-500 bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-200">
                                        Habis Terjual
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-2xl p-8 border border-slate-200 text-center text-slate-500 text-sm">
                            Belum ada kategori tiket aktif untuk event ini.
                        </div>
                    @endforelse
                </div>

                @if($event->addons->isNotEmpty())
                    <!-- Section Add-on & Merchandise Resmi Event -->
                    <div class="mt-8 mb-10">
                        <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse"></span>
                                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight">Add-on &amp; Merchandise Event</h2>
                                    <span class="px-2.5 py-0.5 rounded-full bg-orange-100 text-orange-800 text-[11px] font-bold border border-orange-200">Opsional</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1">Lengkapi perlengkapan lari Anda dengan merchandise resmi edisi terbatas (jersey finisher, enamel pin, soft flask, dll).</p>
                            </div>
                            <span class="text-xs text-slate-400 font-medium hidden sm:inline">📦 Dikirim bersama paket Race Pack</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($event->addons as $addon)
                                @php
                                    $isUnlimited = $addon->isUnlimited();
                                    $availStock = $isUnlimited ? null : $addon->available_stock;
                                    $isAddonSoldOut = !$isUnlimited && $availStock <= 0;
                                @endphp
                                <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 hover:border-orange-300 transition shadow-sm hover:shadow-md flex flex-col justify-between gap-4">
                                    <div class="flex items-start gap-4">
                                        <!-- Product Thumbnail / Visual Icon -->
                                        <div class="shrink-0 w-20 h-20 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center overflow-hidden">
                                            @if($addon->image_path)
                                                <img src="{{ asset('storage/' . $addon->image_path) }}" alt="{{ $addon->name }}" class="w-full h-full object-cover">
                                            @else
                                                @php
                                                    $lowerName = strtolower($addon->name);
                                                    $emoji = '🎁';
                                                    if (str_contains($lowerName, 'jersey') || str_contains($lowerName, 'baju') || str_contains($lowerName, 'kaos')) {
                                                        $emoji = '🎽';
                                                    } elseif (str_contains($lowerName, 'pin') || str_contains($lowerName, 'enamel') || str_contains($lowerName, 'medali')) {
                                                        $emoji = '🏅';
                                                    } elseif (str_contains($lowerName, 'flask') || str_contains($lowerName, 'botol') || str_contains($lowerName, 'drink') || str_contains($lowerName, 'plask')) {
                                                        $emoji = '🍶';
                                                    } elseif (str_contains($lowerName, 'topi') || str_contains($lowerName, 'cap') || str_contains($lowerName, 'visor')) {
                                                        $emoji = '🧢';
                                                    }
                                                @endphp
                                                <span class="text-3xl select-none">{{ $emoji }}</span>
                                            @endif
                                        </div>

                                        <!-- Detail Addon -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2 mb-1">
                                                <h3 class="font-bold text-slate-900 text-sm sm:text-base leading-snug truncate">{{ $addon->name }}</h3>
                                                @if($isAddonSoldOut)
                                                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 text-[10px] font-bold border border-rose-200 shrink-0">
                                                        Habis
                                                    </span>
                                                @elseif(!$isUnlimited)
                                                    <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 text-[10px] font-semibold border border-amber-200 shrink-0">
                                                        Sisa {{ $availStock }}
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="text-xs text-slate-500 mb-2 leading-relaxed line-clamp-2">
                                                {{ $addon->description ?: 'Merchandise resmi pelengkap petualangan virtual run Anda.' }}
                                            </p>

                                            <div class="text-sm font-extrabold text-orange-600">
                                                Rp {{ number_format($addon->price, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bottom Action: Stepper -->
                                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                                        <span class="text-slate-400 font-medium">
                                            @if($addon->weight_grams)
                                                Berat: +{{ $addon->weight_grams }}gr
                                            @else
                                                Item Merchandise
                                            @endif
                                        </span>

                                        @if(!$isAddonSoldOut)
                                            <div class="flex items-center rounded-xl border border-slate-300 bg-slate-50 overflow-hidden shadow-inner">
                                                <button type="button" @click="changeAddonQty('{{ $addon->id }}', -1)" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-200 active:bg-slate-300 font-black text-base transition select-none">
                                                    &minus;
                                                </button>
                                                <input type="number" 
                                                       name="addons[{{ $addon->id }}]" 
                                                       x-model.number="addonQuantities['{{ $addon->id }}']" 
                                                       min="0" 
                                                       :max="addonQuotas['{{ $addon->id }}']" 
                                                       class="w-12 text-center bg-white h-8 border-x border-slate-300 font-bold text-slate-900 text-xs focus:outline-none focus:bg-orange-50/50" />
                                                <button type="button" @click="changeAddonQty('{{ $addon->id }}', 1)" class="w-8 h-8 flex items-center justify-center text-slate-600 hover:bg-slate-200 active:bg-slate-300 font-black text-base transition select-none">
                                                    &plus;
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-rose-500 bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200">
                                                Stok Habis
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Sticky Floating Bottom Summary -->
                <div class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-slate-200 p-4 z-40 shadow-2xl">
                    <div class="max-w-4xl mx-auto flex items-center justify-between gap-4">
                        <div>
                            <div class="text-xs text-slate-500">Total Pembelian:</div>
                            <div class="flex items-baseline gap-1.5 flex-wrap">
                                <span class="text-xl font-extrabold text-orange-600" x-text="formatRupiah(totalPrice)">Rp 0</span>
                                <span class="text-xs text-slate-500 font-medium" x-show="totalTickets > 0">
                                    (<span x-text="totalTickets"></span> tiket<span x-show="totalAddons > 0"> + <span x-text="totalAddons"></span> add-on</span>)
                                </span>
                            </div>
                        </div>

                        <button type="submit" 
                                :disabled="totalTickets === 0" 
                                :class="totalTickets > 0 ? 'bg-orange-600 hover:bg-orange-500 text-white shadow-lg shadow-orange-600/30 cursor-pointer active:scale-95' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                class="px-6 py-3 rounded-xl font-bold text-sm transition flex items-center gap-2">
                            <span>Isi Data Peserta</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto text-center text-xs text-slate-500">
        <div class="max-w-4xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} {{ $event->title }}. Platform Virtual Run oleh Jelatix &amp; Virun.</p>
            <p class="text-slate-400">Didukung oleh Tripay Payment Gateway &amp; SPX Express.</p>
        </div>
    </footer>
</body>
</html>
