<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>E-Ticket Virtual Run - #{{ $participant->bib_number ?: 'BIB' }} - {{ $participant->bib_name ?: $participant->full_name }}</title>
    <style>
        @page {
            margin: 12mm 12mm 12mm 12mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #0f172a;
            background-color: #ffffff;
            font-size: 10.5px;
            line-height: 1.4;
        }
        .ticket-wrapper {
            border: 2px solid #ea580c;
            border-radius: 12px;
            overflow: hidden;
            background: #ffffff;
        }
        
        /* HEADER */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            background: #0f172a;
            color: #ffffff;
        }
        .header-table td {
            padding: 14px 18px;
            vertical-align: middle;
        }
        .brand-title {
            font-size: 18px;
            font-weight: 800;
            letter-spacing: 1px;
            color: #ea580c;
            margin: 0;
            text-transform: uppercase;
        }
        .brand-sub {
            font-size: 9px;
            color: #94a3b8;
            margin: 2px 0 0 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .event-title-header {
            font-size: 13px;
            font-weight: 700;
            color: #ffffff;
            margin: 0;
            text-align: right;
        }
        .event-meta-header {
            font-size: 9.5px;
            color: #fdba74;
            margin: 3px 0 0 0;
            text-align: right;
        }

        /* BIB HERO BANNER */
        .bib-banner {
            background: #ea580c;
            color: #ffffff;
            text-align: center;
            padding: 12px 18px;
            border-top: 1px solid #c2410c;
            border-bottom: 2px dashed #9a3412;
        }
        .category-pill {
            display: inline-block;
            background: #7c2d12;
            color: #ffedd5;
            padding: 3px 12px;
            border-radius: 12px;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 4px;
        }
        .bib-number {
            font-size: 36px;
            font-weight: 900;
            letter-spacing: 3px;
            margin: 0;
            line-height: 1;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: #ffffff;
        }
        .bib-name {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 4px 0 0 0;
            color: #ffedd5;
        }

        /* TWO COLUMN CONTENT */
        .content-table {
            width: 100%;
            border-collapse: collapse;
        }
        .content-table td {
            vertical-align: top;
            padding: 14px 18px;
        }
        .left-col {
            width: 63%;
            border-right: 1px solid #e2e8f0;
        }
        .right-col {
            width: 37%;
            background: #f8fafc;
            text-align: center;
        }

        .section-heading {
            font-size: 10.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #ea580c;
            margin: 0 0 8px 0;
            border-bottom: 1px solid #fed7aa;
            padding-bottom: 4px;
        }

        /* DATA TABLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .data-table td {
            padding: 3.5px 2px;
            font-size: 10px;
            vertical-align: top;
        }
        .data-label {
            color: #64748b;
            font-weight: 500;
            width: 42%;
        }
        .data-value {
            color: #0f172a;
            font-weight: 700;
        }

        /* RACE PACK & SHIPPING BOX */
        .shipping-box {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 8px;
            padding: 8px 10px;
            margin-top: 8px;
        }
        .shipping-title {
            font-size: 9.5px;
            font-weight: 800;
            color: #c2410c;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 3px 0;
        }
        .shipping-text {
            font-size: 9px;
            color: #9a3412;
            margin: 0;
            line-height: 1.35;
        }

        /* QR CODE */
        .qr-box {
            padding: 4px 0;
        }
        .qr-instruction {
            font-size: 10px;
            font-weight: 800;
            color: #0f172a;
            margin: 6px 0 2px 0;
            text-transform: uppercase;
        }
        .qr-token {
            font-size: 8.5px;
            color: #64748b;
            font-family: monospace;
            margin: 0 0 6px 0;
            word-break: break-all;
        }
        .verified-badge {
            display: inline-block;
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
            border-radius: 20px;
            padding: 2.5px 8px;
            font-size: 8.5px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* NOTICE FOOTER */
        .footer-notice {
            background: #f1f5f9;
            padding: 7px 14px;
            border-top: 1px solid #e2e8f0;
            font-size: 8.5px;
            color: #475569;
            text-align: center;
            line-height: 1.3;
        }

        /* RUNNER'S VIRTUAL GUIDE SECTION */
        .guide-section {
            margin-top: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 14px;
            background: #fafafa;
        }
        .guide-heading {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            margin: 0 0 6px 0;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }
        .guide-steps {
            width: 100%;
            border-collapse: collapse;
        }
        .guide-steps td {
            vertical-align: top;
            font-size: 8.5px;
            padding: 4px 6px;
            line-height: 1.35;
        }
        .step-number {
            display: inline-block;
            width: 16px;
            height: 16px;
            line-height: 16px;
            background: #ea580c;
            color: #ffffff;
            font-weight: 800;
            border-radius: 50%;
            text-align: center;
            font-size: 8px;
            margin-right: 4px;
        }
        .step-title {
            font-weight: 800;
            color: #0f172a;
            display: inline;
        }
    </style>
</head>
<body>

    <div class="ticket-wrapper">
        <!-- 1. HEADER -->
        <table class="header-table">
            <tr>
                <td style="width: 50%;">
                    <div class="brand-title">VIRUN PASS</div>
                    <div class="brand-sub">Official Virtual Run E-Ticket &amp; Verification</div>
                </td>
                <td style="width: 50%;">
                    <div class="event-title-header">{{ $event->title }}</div>
                    <div class="event-meta-header">
                        Virtual Run &bull; Lari Kapan Saja &amp; Di Mana Saja
                    </div>
                </td>
            </tr>
        </table>

        @php
            $ebibBgBase64 = $event?->getEbibBackgroundBase64();
        @endphp
        <!-- 2. BIB HERO BANNER -->
        <div class="bib-banner" style="{{ $ebibBgBase64 ? 'background-image: url(' . $ebibBgBase64 . '); background-size: cover; background-position: center; border: 2px solid #ea580c; position: relative;' : '' }}">
            @if($ebibBgBase64)
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.45); z-index: 1;"></div>
            @endif
            <div style="position: relative; z-index: 2;">
                <div class="category-pill">
                    {{ $category->name }} ({{ $category->distance_km }} KM) &bull; {{ $event->submission_mode === 'CUMULATIVE' ? 'AKUMULASI JARAK' : 'SINGLE RUN' }}
                </div>
                <div class="bib-number" style="text-shadow: 0 2px 6px rgba(0,0,0,0.8);">{{ $participant->bib_number ?: 'SEDANG DI-GENERATE' }}</div>
                <div class="bib-name" style="text-shadow: 0 2px 4px rgba(0,0,0,0.8);">{{ $participant->bib_name ?: $participant->full_name }}</div>
            </div>
        </div>

        <!-- 3. MAIN DATA & QR CODE SECTION -->
        <table class="content-table">
            <tr>
                <!-- LEFT COLUMN: RUNNER & VIRTUAL RACE DETAILS -->
                <td class="left-col">
                    <div class="section-heading">Data Peserta &amp; Tantangan Lari</div>
                    <table class="data-table">
                        <tr>
                            <td class="data-label">Nomor e-BIB:</td>
                            <td class="data-value" style="font-size: 11px; font-weight: 900; color: #ea580c;">{{ $participant->bib_number ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="data-label">Nama di E-BIB:</td>
                            <td class="data-value">{{ $participant->bib_name ?: $participant->full_name }}</td>
                        </tr>
                        @if($participant->full_name && $participant->full_name !== $participant->bib_name)
                        <tr>
                            <td class="data-label">Nama Lengkap:</td>
                            <td class="data-value">{{ $participant->full_name }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td class="data-label">Kategori Lari:</td>
                            <td class="data-value">{{ $category->name }} ({{ $category->distance_km }} KM)</td>
                        </tr>
                        <tr>
                            <td class="data-label">Mode Tantangan:</td>
                            <td class="data-value">{{ $event->submission_mode === 'CUMULATIVE' ? 'Akumulasi Jarak (Bebas Cicil)' : 'Single Run (Satu Kali Selesai)' }}</td>
                        </tr>
                        <tr>
                            <td class="data-label">Periode Lari:</td>
                            <td class="data-value">
                                @php
                                    $raceStart = $event->race_start_date ? \Illuminate\Support\Carbon::parse($event->race_start_date) : ($event->event_start_date ? \Illuminate\Support\Carbon::parse($event->event_start_date) : null);
                                    $raceEnd = $event->race_end_date ? \Illuminate\Support\Carbon::parse($event->race_end_date) : ($event->event_end_date ? \Illuminate\Support\Carbon::parse($event->event_end_date) : null);
                                @endphp
                                {{ $raceStart ? $raceStart->format('d M Y') : 'Kapan Saja' }}
                                @if($raceEnd)
                                    s/d {{ $raceEnd->format('d M Y') }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="data-label">Jadwal Kirim Race Pack:</td>
                            <td class="data-value" style="color: #c2410c; font-weight: 800;">{{ $event->getRacepackDeliveryScheduleFormatted() }}</td>
                        </tr>
                        <tr>
                            <td class="data-label">Lokasi Lari:</td>
                            <td class="data-value">Di Mana Saja (Outdoor Rute Bebas atau Treadmill)</td>
                        </tr>
                        <tr>
                            <td class="data-label">Ukuran Jersey:</td>
                            <td class="data-value">{{ $jerseySize ? $jerseySize->size_name . ' (' . ucfirst($jerseySize->gender_type) . ')' : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="data-label">Kode Invoice / Order:</td>
                            <td class="data-value">{{ $order ? $order->order_code : 'INV-OFFICIAL' }}</td>
                        </tr>
                    </table>

                    <!-- RACE PACK & EXPEDITION DETAILS -->
                    <div class="shipping-box">
                        <div class="shipping-title">Pengiriman Race Pack &amp; Medali Finisher</div>
                        <p class="shipping-text">
                            <strong>Jadwal Pengiriman:</strong> <span style="color: #c2410c; font-weight: 800;">{{ $event->getRacepackDeliveryScheduleFormatted() }}</span><br>
                            Jersey Eksklusif &amp; Medali Logam Finisher dikirimkan langsung ke alamat peserta via ekspedisi <strong>SPX Express</strong>.
                            @if($order && $order->shippingAddress)
                                <br>
                                <strong>Alamat Tujuan:</strong> {{ $order->shippingAddress->destination_district }}, {{ $order->shippingAddress->destination_city }}
                                @if($order->shippingAddress->tracking_number)
                                    <br><strong>No. Resi SPX:</strong> {{ $order->shippingAddress->tracking_number }}
                                @endif
                            @else
                                <br>Pastikan data alamat pengiriman lengkap untuk kelancaran pengiriman paket lomba.
                            @endif
                        </p>
                    </div>
                </td>

                <!-- RIGHT COLUMN: QR CODE SCANNER VERIFICATION -->
                <td class="right-col">
                    <div class="section-heading" style="text-align: center;">Scan &amp; Lapor Hasil Lari</div>
                    <div class="qr-box">
                        <img src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" width="135" height="135" alt="QR Code Submission">
                        <div class="qr-instruction">Pindai untuk Lapor Lari</div>
                        <div class="qr-token">Token: {{ $participant->qr_token }}</div>
                        <div class="verified-badge">&check; Pembayaran Lunas</div>
                    </div>
                    <div style="font-size: 8px; color: #64748b; margin-top: 8px; line-height: 1.3;">
                        Pindai QR Code di atas menggunakan kamera smartphone untuk langsung membuka portal pelaporan aktivitas lari dan mengunggah bukti lari Anda.
                    </div>
                </td>
            </tr>
        </table>

        <!-- 4. FOOTER NOTICE -->
        <div class="footer-notice">
            Nomor BIB resmi diterbitkan secara unik untuk pelaporan virtual run. Peserta berhak atas E-Sertifikat Finisher dan Finisher Medal setelah target jarak terpenuhi serta diverifikasi oleh panitia.
        </div>
    </div>

    <!-- 5. PANDUAN PELARI VIRTUAL RUN (RUNNER'S GUIDE) -->
    <div class="guide-section">
        <div class="guide-heading">Panduan Aktivitas &amp; Pengiriman Hasil Lari (Virtual Run Guide)</div>
        <table class="guide-steps">
            <tr>
                <td style="width: 50%;">
                    <div>
                        <span class="step-number">1</span>
                        <div class="step-title">Rekam Lari dengan Aplikasi GPS</div>
                    </div>
                    <div style="color: #64748b; margin-top: 2px;">
                        Gunakan aplikasi seperti Strava, Garmin Connect, Nike Run Club (NRC), Apple Fitness, Suunto, Coros, atau foto layar monitor treadmill.
                    </div>
                </td>
                <td style="width: 50%;">
                    <div>
                        <span class="step-number">2</span>
                        <div class="step-title">Tuntaskan Jarak Kategori</div>
                    </div>
                    <div style="color: #64748b; margin-top: 2px;">
                        Lari sejauh <strong>{{ $category->distance_km }} KM</strong> dalam periode event. {{ $event->submission_mode === 'CUMULATIVE' ? 'Boleh dicicil beberapa kali sesi lari hingga target jarak tercapai.' : 'Diselesaikan dalam 1 kali sesi lari.' }}
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div>
                        <span class="step-number">3</span>
                        <div class="step-title">Unggah Tangkapan Layar (Submit)</div>
                    </div>
                    <div style="color: #64748b; margin-top: 2px;">
                        Pindai QR code tiket ini atau kunjungi menu Submit Lari, masukkan nomor BIB <strong>#{{ $participant->bib_number }}</strong>, sertakan bukti jarak, durasi waktu, dan foto lari.
                    </div>
                </td>
                <td>
                    <div>
                        <span class="step-number">4</span>
                        <div class="step-title">Leaderboard &amp; Finisher Medal</div>
                    </div>
                    <div style="color: #64748b; margin-top: 2px;">
                        Pantau peringkat Anda di Live Leaderboard dan unduh E-Sertifikat Finisher resmi. Medali finisher &amp; jersey akan dikirim via SPX Express.
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
