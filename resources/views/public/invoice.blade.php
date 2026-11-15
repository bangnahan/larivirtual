<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Pembayaran: {{ $order->order_code }} - {{ $order->event?->title }}</title>
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

    @if($order->event?->meta_pixel_id)
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
    fbq('init', '{{ $order->event->meta_pixel_id }}');
    fbq('track', 'PageView');
    @if($order->isPaid())
    fbq('track', 'Purchase', {
        content_name: '{{ addslashes($order->event->title) }}',
        currency: 'IDR',
        value: {{ (float) $order->grand_total }},
        content_type: 'product',
        order_id: '{{ $order->order_code }}'
    }, { eventID: '{{ $order->order_code }}' });
    @endif
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id={{ $order->event->meta_pixel_id }}&ev=PageView&noscript=1"
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
                <span class="truncate max-w-[200px] sm:max-w-md">{{ $order->event?->title ?: 'Virtual Run' }}</span>
            </a>
            <div class="flex items-center gap-2">
                <span class="text-xs font-mono font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200">
                    {{ $order->order_code }}
                </span>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-2xl mx-auto px-4 sm:px-6 py-6 sm:py-8 flex-1 w-full">
        <!-- Stepper Navigation (Step 3: Pembayaran) -->
        <div class="flex items-center justify-between mb-8 max-w-xl mx-auto text-xs font-semibold">
            <div class="flex items-center gap-2 text-emerald-600">
                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold">✓</span>
                <span>Pilih Tiket</span>
            </div>
            <div class="h-0.5 flex-1 bg-emerald-500 mx-3"></div>
            <div class="flex items-center gap-2 text-emerald-600">
                <span class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold">✓</span>
                <span>Data Peserta</span>
            </div>
            <div class="h-0.5 flex-1 {{ $order->isPaid() ? 'bg-emerald-500' : 'bg-orange-500' }} mx-3"></div>
            <div class="flex items-center gap-2 {{ $order->isPaid() ? 'text-emerald-600' : 'text-orange-600' }}">
                <span class="w-7 h-7 rounded-full {{ $order->isPaid() ? 'bg-emerald-600' : 'bg-orange-600' }} text-white flex items-center justify-center font-bold">
                    {{ $order->isPaid() ? '✓' : '3' }}
                </span>
                <span>{{ $order->isPaid() ? 'Lunas' : 'Pembayaran' }}</span>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3.5 rounded-2xl text-xs sm:text-sm mb-6 text-center font-bold shadow-xs">
                {{ session('success') }}
            </div>
        @endif

        @if($order->isPaid())
            <!-- PAID SUCCESS CARD (PEMBAYARAN LUNAS) -->
            <div class="bg-white border border-emerald-200 rounded-3xl p-6 sm:p-8 shadow-sm text-center mb-8">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-emerald-200 shadow-xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200 mb-2">
                    Pembayaran Berhasil
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-1">PEMBAYARAN LUNAS!</h1>
                <p class="text-xs sm:text-sm text-slate-500 mb-6">Pendaftaran Anda untuk event <strong>{{ $order->event?->title }}</strong> telah resmi terkonfirmasi.</p>

                <!-- Runner Cards with E-BIB -->
                <div class="space-y-4 mb-6">
                    @foreach($order->participants as $p)
                        <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-orange-950 text-white rounded-2xl p-5 text-center shadow-md border border-slate-700/50">
                            <span class="text-[11px] text-orange-300 font-bold uppercase tracking-widest block">Nomor Dada (E-BIB) Resmi:</span>
                            <span class="text-3xl sm:text-4xl font-extrabold text-white font-mono tracking-wider my-1.5 block">
                                {{ $p->bib_number ?: 'SEDANG DI-GENERATE' }}
                            </span>
                            <span class="text-xs font-semibold text-slate-200 uppercase block truncate mb-1">
                                {{ $p->full_name ?: $p->bib_name }} &bull; {{ $p->category?->name }}
                            </span>
                            <span class="text-[11px] text-orange-200 font-medium block mb-4">
                                Jersey: {{ $p->jersey_full_spec }}
                            </span>

                            <div class="flex flex-col sm:flex-row gap-2.5 justify-center">
                                @if($p->bib_number)
                                    <a 
                                        href="{{ route('public.submit.index', ['bib' => $p->bib_number]) }}" 
                                        class="bg-orange-600 hover:bg-orange-500 active:scale-95 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition shadow-md shadow-orange-600/30 flex items-center justify-center gap-1.5 cursor-pointer"
                                    >
                                        <span>🏃 Lapor / Submit Aktivitas Lari</span>
                                    </a>
                                @endif
                                <a 
                                    href="{{ route('public.ticket.download', $p->qr_token) }}" 
                                    class="bg-white/10 hover:bg-white/20 active:scale-95 text-white font-bold py-2.5 px-4 rounded-xl text-xs transition border border-white/20 flex items-center justify-center gap-1.5 cursor-pointer"
                                >
                                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    <span>Unduh E-Ticket PDF</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Shipping Summary (SPX) -->
                @if($order->shippingAddress)
                    <div class="bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200 text-xs text-left mb-6">
                        <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-slate-200 flex-wrap gap-2">
                            <span class="font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Pengiriman Race Pack ({{ $order->shippingAddress->courier_name }} {{ $order->shippingAddress->service_type }})</span>
                            </span>
                            <span class="text-[10px] font-mono px-2.5 py-0.5 rounded-full uppercase font-bold
                                {{ $order->shippingAddress->shipping_status === 'shipped' ? 'bg-sky-100 text-sky-700 border border-sky-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                Status: {{ $order->shippingAddress->shipping_status }}
                            </span>
                        </div>
                        <div class="text-slate-600 space-y-1.5 leading-relaxed">
                            <div>Jadwal Pengiriman: <strong class="text-orange-600 font-bold">{{ $order->event?->getRacepackDeliveryScheduleFormatted() }}</strong></div>
                            <div>Penerima: <strong class="text-slate-900">{{ $order->shippingAddress->recipient_name }}</strong> ({{ $order->shippingAddress->recipient_phone }})</div>
                            <div>Tujuan: {{ $order->shippingAddress->destination_district }}, {{ $order->shippingAddress->destination_city }}</div>
                            <div class="text-slate-500">{{ $order->shippingAddress->address_detail }}</div>
                            @if($order->shippingAddress->tracking_number)
                                <div class="pt-2 text-emerald-600 font-mono font-bold text-sm">
                                    No. Resi SPX: {{ $order->shippingAddress->tracking_number }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Add-ons Summary (Jika Ada) -->
                @if($order->addons->isNotEmpty())
                    <div class="bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200 text-xs text-left mb-6">
                        <div class="flex items-center justify-between mb-2.5 pb-2 border-b border-slate-200">
                            <span class="font-bold text-slate-900 flex items-center gap-1.5">
                                <span>🎁</span>
                                <span>Add-on &amp; Merchandise Dipesan:</span>
                            </span>
                            <span class="text-[10px] font-mono text-slate-700 bg-white px-2 py-0.5 rounded-full border border-slate-200 font-bold">
                                {{ $order->addons->sum('quantity') }} Item
                            </span>
                        </div>
                        <div class="space-y-1.5">
                            @foreach($order->addons as $addonItem)
                                <div class="flex items-center justify-between bg-white p-2.5 rounded-xl border border-slate-200 text-xs">
                                    <span class="font-bold text-slate-800">{{ $addonItem->quantity }}x {{ $addonItem->addon_name }}</span>
                                    <span class="font-bold text-slate-900 font-mono">Rp {{ number_format($addonItem->total_price, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="mt-4 text-xs text-slate-500">
                    E-Ticket dan ringkasan order telah dikirimkan ke email <strong>{{ $order->customer_email }}</strong>.
                </div>

                <form action="{{ route('public.order.resend_ticket', $order->order_code) }}" method="POST" class="mt-3">
                    @csrf
                    <button 
                        type="submit" 
                        class="inline-flex items-center gap-1.5 text-xs font-bold text-orange-600 hover:text-orange-700 bg-orange-50 hover:bg-orange-100 border border-orange-200 px-3.5 py-2 rounded-xl transition cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span>Kirim Ulang E-Ticket ke Email</span>
                    </button>
                </form>
            </div>
        @else
            <!-- PENDING PAYMENT INVOICE (MENUNGGU PEMBAYARAN) -->
            @php
                $remainingSeconds = max(0, $order->expired_at ? (int) now()->diffInSeconds($order->expired_at, false) : 3600);
                $isExpired = ($remainingSeconds <= 0);
            @endphp

            <div class="bg-white border border-slate-200 rounded-3xl p-5 sm:p-7 shadow-sm mb-8">
                <!-- Status & Timer Banner -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
                    <div>
                        <span class="text-[11px] text-slate-500 uppercase tracking-wider block font-semibold">Batas Waktu Bayar</span>
                        <span id="countdownTimer" class="text-lg font-black {{ $isExpired ? 'text-rose-600' : 'text-orange-600' }} font-mono">
                            {{ $isExpired ? '00:00 (KADALUARSA)' : sprintf('%02d:%02d', (int) floor($remainingSeconds / 60), ((int) $remainingSeconds) % 60) }}
                        </span>
                    </div>
                    @if($isExpired)
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200">
                            Waktu Bayar Habis
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                            <span>Menunggu Pembayaran</span>
                        </span>
                    @endif
                </div>

                <!-- Total Amount with 1-Click Copy -->
                <div class="text-center mb-6 bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200">
                    <span class="text-xs text-slate-500 uppercase tracking-wider block mb-1 font-semibold">Total yang Harus Dibayar</span>
                    <div class="flex flex-wrap items-center justify-center gap-2.5 my-1">
                        <div class="text-2xl sm:text-4xl font-extrabold text-slate-900 font-mono">
                            Rp {{ number_format($order->grand_total, 0, ',', '.') }}
                        </div>
                        <button 
                            type="button" 
                            onclick="copyAmount('{{ (int)$order->grand_total }}')" 
                            class="px-2.5 py-1 rounded-lg bg-white hover:bg-orange-50 text-slate-700 hover:text-orange-600 border border-slate-300 text-xs font-bold transition active:scale-95 cursor-pointer flex items-center gap-1 shadow-xs"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                            <span id="copyAmountBtnText">Salin Nominal</span>
                        </button>
                    </div>
                    <span class="text-xs text-slate-500">
                        Metode: <strong class="text-slate-800 font-bold uppercase">{{ $order->tripay_payment_method }}</strong>
                    </span>
                </div>

                <!-- External Tripay Checkout Button (Jika Tersedia) -->
                @if($order->tripay_checkout_url && !str_contains($order->tripay_checkout_url, url("/orders/{$order->order_code}")))
                    <div class="mb-6">
                        <a 
                            href="{{ $order->tripay_checkout_url }}" 
                            target="_blank" 
                            class="w-full bg-gradient-to-r from-orange-600 to-amber-600 hover:from-orange-500 hover:to-amber-500 active:scale-95 text-white font-bold py-3.5 px-5 rounded-2xl text-xs sm:text-sm transition shadow-lg shadow-orange-600/25 flex items-center justify-center gap-2 cursor-pointer"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            <span>Buka Halaman Pembayaran Resmi Tripay &rarr;</span>
                        </a>
                    </div>
                @endif

                <!-- Payment Details Card (QRIS vs VA vs Minimarket) -->
                @if(str_starts_with($order->tripay_payment_method, 'QRIS') && $order->tripay_qr_url)
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 sm:p-6 text-slate-900 text-center mb-6">
                        <div class="inline-block p-3 bg-white rounded-2xl border border-slate-200 shadow-sm mb-3">
                            <img src="{{ $order->tripay_qr_url }}" alt="QRIS Code" class="max-w-[220px] sm:max-w-[260px] w-full aspect-square mx-auto rounded-lg object-contain">
                        </div>
                        <p class="text-xs text-slate-600 font-medium mb-3 max-w-sm mx-auto leading-relaxed">
                            Buka aplikasi m-Banking atau E-Wallet (BCA, Mandiri, BRI, BNI, GoPay, OVO, DANA, ShopeePay) lalu arahkan kamera ke kode QR di atas.
                        </p>
                        <a href="{{ $order->tripay_qr_url }}" target="_blank" download="QRIS-{{ $order->order_code }}.png" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs font-bold transition shadow-xs">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>Unduh Gambar QRIS</span>
                        </a>
                    </div>
                @elseif($order->tripay_pay_code)
                    @php
                        $isRetail = in_array($order->tripay_payment_method, ['ALFAMART', 'INDOMARET', 'ALFAMIDI']);
                    @endphp
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-5 text-center mb-6">
                        <span class="text-xs text-slate-500 uppercase tracking-wider block mb-1 font-semibold">
                            {{ $isRetail ? 'Kode Pembayaran Kasir Minimarket' : 'Nomor Virtual Account Bank' }} ({{ $order->tripay_payment_method }})
                        </span>
                        <div class="my-3 flex flex-col sm:flex-row items-center justify-center gap-2 sm:gap-3 bg-white p-3.5 rounded-xl border border-slate-300 shadow-xs">
                            <span id="vaCode" class="text-xl sm:text-2xl font-black text-slate-900 font-mono tracking-wider break-all select-all">{{ $order->tripay_pay_code }}</span>
                            <button 
                                type="button" 
                                id="copyVaBtn"
                                onclick="copyVaCode()" 
                                class="px-3.5 py-1.5 rounded-lg bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200 text-xs font-bold transition active:scale-95 cursor-pointer shrink-0 flex items-center gap-1.5"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>
                                <span id="copyVaBtnText">Salin Nomor VA</span>
                            </button>
                        </div>
                        <p class="text-xs text-slate-500">
                            {{ $isRetail ? 'Sebutkan pembayaran Tripay ke kasir dan tunjukkan kode di atas.' : 'Transfer sesuai nominal ke nomor Virtual Account sebelum batas waktu berakhir.' }}
                        </p>
                    </div>
                @endif

                <!-- Panduan Pembayaran Interaktif (Accordion) -->
                <div class="mb-6 bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden">
                    <div class="p-3.5 sm:p-4 border-b border-slate-200 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-2">
                            <span>📖</span>
                            <span>Panduan Cara Pembayaran</span>
                        </span>
                        <span class="text-[10px] text-slate-500">Klik untuk melihat</span>
                    </div>

                    <div class="divide-y divide-slate-200 text-xs">
                        @if(str_starts_with($order->tripay_payment_method, 'QRIS'))
                            <details class="group p-3 sm:p-4 cursor-pointer" open>
                                <summary class="font-bold text-orange-600 flex items-center justify-between list-none">
                                    <span>Langkah Pembayaran via QRIS (Semua Bank &amp; E-Wallet)</span>
                                    <span class="transition group-open:rotate-180 text-slate-400">&darr;</span>
                                </summary>
                                <ol class="list-decimal list-inside text-slate-600 space-y-1.5 mt-3 leading-relaxed pl-1">
                                    <li>Buka aplikasi m-Banking (BCA, Mandiri, BRI, BNI) atau E-Wallet (GoPay, OVO, DANA, ShopeePay).</li>
                                    <li>Pilih menu <strong>Scan / Bayar / QRIS</strong>.</li>
                                    <li>Arahkan kamera ke kode QR di layar atau upload file gambar QR yang telah diunduh.</li>
                                    <li>Pastikan nama merchant tertera <strong>Tripay</strong> dan nominal sesuai <strong>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</strong>.</li>
                                    <li>Masukkan PIN transaksi Anda untuk menyelesaikan pembayaran.</li>
                                </ol>
                            </details>
                        @elseif(in_array($order->tripay_payment_method, ['ALFAMART', 'INDOMARET', 'ALFAMIDI']))
                            <details class="group p-3 sm:p-4 cursor-pointer" open>
                                <summary class="font-bold text-orange-600 flex items-center justify-between list-none">
                                    <span>Langkah Pembayaran di Kasir Gerai Ritel</span>
                                    <span class="transition group-open:rotate-180 text-slate-400">&darr;</span>
                                </summary>
                                <ol class="list-decimal list-inside text-slate-600 space-y-1.5 mt-3 leading-relaxed pl-1">
                                    <li>Kunjungi gerai <strong>{{ $order->tripay_payment_method }}</strong> terdekat.</li>
                                    <li>Sampaikan kepada kasir bahwa Anda ingin membayar tagihan merchant <strong>Tripay</strong>.</li>
                                    <li>Tunjukkan Kode Pembayaran: <strong class="text-slate-900 font-mono">{{ $order->tripay_pay_code }}</strong>.</li>
                                    <li>Bayar sesuai nominal dan simpan struk fisik bukti pembayaran.</li>
                                </ol>
                            </details>
                        @else
                            <!-- Virtual Account Instructions -->
                            <details class="group p-3 sm:p-4 cursor-pointer" open>
                                <summary class="font-bold text-orange-600 flex items-center justify-between list-none">
                                    <span>Bayar via Mobile Banking (m-Banking)</span>
                                    <span class="transition group-open:rotate-180 text-slate-400">&darr;</span>
                                </summary>
                                <ol class="list-decimal list-inside text-slate-600 space-y-1.5 mt-3 leading-relaxed pl-1">
                                    <li>Buka aplikasi Mobile Banking Anda.</li>
                                    <li>Pilih menu <strong>Transfer</strong> &gt; <strong>Virtual Account</strong>.</li>
                                    <li>Masukkan Nomor VA: <strong class="text-slate-900 font-mono">{{ $order->tripay_pay_code }}</strong>.</li>
                                    <li>Pastikan nominal tagihan sesuai persis <strong>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</strong>.</li>
                                    <li>Selesaikan transaksi dengan PIN Anda. Sistem otomatis memverifikasi dalam beberapa detik.</li>
                                </ol>
                            </details>

                            <details class="group p-3 sm:p-4 cursor-pointer">
                                <summary class="font-bold text-slate-700 hover:text-orange-600 flex items-center justify-between list-none">
                                    <span>Bayar via Mesin ATM</span>
                                    <span class="transition group-open:rotate-180 text-slate-400">&darr;</span>
                                </summary>
                                <ol class="list-decimal list-inside text-slate-600 space-y-1.5 mt-3 leading-relaxed pl-1">
                                    <li>Masukkan kartu ATM dan PIN Anda di mesin ATM.</li>
                                    <li>Pilih menu <strong>Transaksi Lainnya</strong> &gt; <strong>Transfer</strong> &gt; <strong>Ke Rekening Virtual Account</strong>.</li>
                                    <li>Ketikkan nomor VA: <strong class="text-slate-900 font-mono">{{ $order->tripay_pay_code }}</strong> lalu tekan Benar.</li>
                                    <li>Selesaikan pembayaran dan simpan struk transaksi.</li>
                                </ol>
                            </details>
                        @endif
                    </div>
                </div>

                <!-- Order Detail Summary -->
                <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 text-xs space-y-3 mb-6">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Event Lari:</span>
                        <span class="font-bold text-slate-900">{{ $order->event?->title }}</span>
                    </div>

                    <div class="border-t border-slate-200 pt-3">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px]">Daftar Pelari Terdaftar:</span>
                            <span class="text-[10px] font-mono text-slate-700 bg-white px-2 py-0.5 rounded-full border border-slate-200 font-bold">
                                {{ $order->participants->count() }} Orang
                            </span>
                        </div>
                        <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                            @foreach($order->participants as $idx => $p)
                                <div class="bg-white p-2.5 rounded-xl border border-slate-200 flex items-center justify-between">
                                    <div class="min-w-0 pr-2">
                                        <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5 truncate">
                                            <span class="w-4 h-4 rounded-full bg-orange-100 text-orange-700 text-[10px] flex items-center justify-center font-bold shrink-0">{{ $idx + 1 }}</span>
                                            <span class="truncate">{{ $p->full_name ?: $p->bib_name }}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">
                                            BIB: <strong class="text-slate-700 font-mono uppercase">{{ $p->bib_name ?: $p->full_name }}</strong>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-bold text-slate-900 block font-mono">{{ $p->category?->name }}</span>
                                        <span class="text-[10px] text-slate-500 block">Jersey: {{ $p->jersey_full_spec }}</span>
                                        @if(($p->jersey_surcharge ?? 0) > 0)
                                            <span class="text-[10px] font-bold text-orange-600 block font-mono">(+Rp {{ number_format($p->jersey_surcharge, 0, ',', '.') }})</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($order->addons->isNotEmpty())
                        <div class="border-t border-slate-200 pt-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-slate-500 font-bold uppercase tracking-wider text-[10px]">Add-on &amp; Merchandise:</span>
                                <span class="text-[10px] font-mono text-slate-700 bg-white px-2 py-0.5 rounded-full border border-slate-200 font-bold">
                                    {{ $order->addons->sum('quantity') }} Item
                                </span>
                            </div>
                            <div class="space-y-2">
                                @foreach($order->addons as $addonItem)
                                    <div class="bg-white p-2.5 rounded-xl border border-slate-200 flex items-center justify-between">
                                        <div class="min-w-0 pr-2">
                                            <div class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                                                <span class="text-sm">🎁</span>
                                                <span>{{ $addonItem->quantity }}x {{ $addonItem->addon_name }}</span>
                                            </div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">
                                                @ Rp {{ number_format($addonItem->unit_price, 0, ',', '.') }}
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0 font-bold text-slate-900 text-xs font-mono">
                                            Rp {{ number_format($addonItem->total_price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Rincian Biaya -->
                    <div class="border-t border-slate-200 pt-3 space-y-1.5 text-xs text-slate-600">
                        @php
                            $ticketsSubtotal = $order->subtotal - ($order->addons_total ?? 0) - ($order->jersey_surcharges_total ?? 0);
                        @endphp
                        <div class="flex justify-between">
                            <span>Subtotal Tiket ({{ $order->participants->count() }}x):</span>
                            <span class="font-medium text-slate-800">Rp {{ number_format($ticketsSubtotal, 0, ',', '.') }}</span>
                        </div>
                        @if(($order->jersey_surcharges_total ?? 0) > 0)
                            <div class="flex justify-between text-orange-700 font-medium">
                                <span class="flex items-center gap-1">
                                    <span>👕</span>
                                    <span>Upgrade Jersey (Lengan / Size):</span>
                                </span>
                                <span class="font-bold text-orange-600 font-mono">+Rp {{ number_format($order->jersey_surcharges_total, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if(($order->addons_total ?? 0) > 0)
                            <div class="flex justify-between">
                                <span>Subtotal Add-on &amp; Merchandise:</span>
                                <span class="font-medium text-slate-800">Rp {{ number_format($order->addons_total, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($order->shipping_cost > 0)
                            <div class="flex justify-between">
                                <span>Ongkos Kirim (SPX):</span>
                                <span class="font-medium text-slate-800">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($order->platform_fee > 0)
                            <div class="flex justify-between">
                                <span>Biaya Layanan Platform:</span>
                                <span class="font-medium text-slate-800">Rp {{ number_format($order->platform_fee, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-2 border-t border-slate-200 font-extrabold text-slate-900 text-sm">
                            <span>Total Pembayaran:</span>
                            <span class="text-orange-600 font-mono">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Button & Polling -->
                <div class="space-y-3">
                    <!-- Tombol Cek Status Pembayaran -->
                    <button 
                        type="button" 
                        onclick="checkPaymentStatus()" 
                        class="w-full bg-orange-600 hover:bg-orange-500 active:scale-95 text-white font-bold py-3.5 px-4 rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-2 cursor-pointer shadow-lg shadow-orange-600/25"
                    >
                        <svg id="refreshIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        <span>Saya Sudah Bayar (Cek Status Sekarang)</span>
                    </button>
                </div>
            </div>
        @endif
    </main>

    <!-- Auto Polling Script -->
    @if(!$order->isPaid())
    <script>
        let countdown = {{ (int) $remainingSeconds }};
        const timerEl = document.getElementById('countdownTimer');

        if (countdown > 0) {
            const timerInterval = setInterval(() => {
                if (countdown > 0) {
                    countdown--;
                    const m = Math.floor(countdown / 60);
                    const s = countdown % 60;
                    if (timerEl) {
                        timerEl.innerText = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                    }
                } else {
                    clearInterval(timerInterval);
                    if (timerEl) {
                        timerEl.innerText = '00:00 (KADALUARSA)';
                        timerEl.className = 'text-lg font-black text-rose-600 font-mono';
                    }
                }
            }, 1000);
        }

        async function checkPaymentStatus() {
            const btn = event?.currentTarget;
            if (btn) btn.disabled = true;
            try {
                const res = await fetch('/orders/{{ $order->order_code }}/status');
                const data = await res.json();
                if (data.is_paid) {
                    window.location.reload();
                } else {
                    alert('Status pembayaran saat ini: ' + data.status.toUpperCase() + '.\n\nSistem belum mendeteksi pembayaran masuk. Jika Anda baru saja transfer, mohon tunggu 1-2 menit agar Tripay memverifikasi transaksi.');
                }
            } catch (e) {
                console.error(e);
            } finally {
                if (btn) btn.disabled = false;
            }
        }

        // Auto polling every 5 seconds
        setInterval(async () => {
            try {
                const res = await fetch('/orders/{{ $order->order_code }}/status');
                const data = await res.json();
                if (data.is_paid) {
                    window.location.reload();
                }
            } catch (e) {}
        }, 5000);

        function copyVaCode() {
            const el = document.getElementById('vaCode');
            const code = el ? el.innerText.trim() : '{{ $order->tripay_pay_code }}';
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(code).then(() => {
                    const btnText = document.getElementById('copyVaBtnText');
                    if (btnText) {
                        btnText.innerText = 'Tersalin!';
                        setTimeout(() => { btnText.innerText = 'Salin Nomor VA'; }, 2000);
                    }
                }).catch(() => {
                    prompt('Salin nomor ini:', code);
                });
            } else {
                prompt('Salin nomor ini:', code);
            }
        }

        function copyAmount(amt) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(amt).then(() => {
                    const btnText = document.getElementById('copyAmountBtnText');
                    if (btnText) {
                        btnText.innerText = 'Tersalin!';
                        setTimeout(() => { btnText.innerText = 'Salin Nominal'; }, 2000);
                    }
                }).catch(() => {
                    prompt('Salin nominal ini:', amt);
                });
            } else {
                prompt('Salin nominal ini:', amt);
            }
        }
    </script>
    @endif
</body>
</html>
