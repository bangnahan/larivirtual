<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>E-BIB {{ $participant->bib_number }} - {{ $participant->bib_name ?: $participant->full_name }}</title>
    @php
        $ebibBgBase64 = $event?->getEbibBackgroundBase64();
        $textColor = $event?->getEbibConfig('text_color', '#ffffff');
        $bibTopMm = (int) $event?->getEbibConfig('bib_top_mm', 45);
        $bibFontSizePt = (int) $event?->getEbibConfig('bib_font_size_pt', 64);
        $nameTopMm = (int) $event?->getEbibConfig('name_top_mm', 82);
        $nameFontSizePt = (int) $event?->getEbibConfig('name_font_size_pt', 18);
        $showEventTitle = (bool) $event?->getEbibConfig('show_event_title', false);
        $showCategoryBadge = (bool) $event?->getEbibConfig('show_category_badge', true);
        $showQrCode = (bool) $event?->getEbibConfig('show_qr_code', true);
        $showPinHoles = (bool) $event?->getEbibConfig('show_pin_holes', true);
        
        $isDarkText = in_array(strtolower($textColor), ['#000000', '#0f172a', '#1e293b', '#334155', 'black']);
        $textShadow = $isDarkText ? 'none' : '0 3px 8px rgba(0, 0, 0, 0.85)';
    @endphp
    <style>
        @page {
            margin: 0;
            size: a5 landscape;
        }
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
        }
        body {
            margin: 0;
            padding: 0;
            width: 210mm;
            height: 148mm;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #0f172a;
            color: {{ $textColor }};
            position: relative;
            overflow: hidden;
        }

        /* Full Bleed Background */
        .bg-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 210mm;
            height: 148mm;
            z-index: 1;
        }
        .bg-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .default-bg {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #0f172a 100%);
            position: relative;
        }
        .stripe-accent {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8mm;
            background: linear-gradient(90deg, #ea580c 0%, #f97316 50%, #ea580c 100%);
        }
        .bottom-accent {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 5mm;
            background: #ea580c;
        }

        /* Pin Hole Marks */
        .pin-hole {
            position: absolute;
            width: 6mm;
            height: 6mm;
            border: 1.5px dashed {{ $isDarkText ? 'rgba(0, 0, 0, 0.3)' : 'rgba(255, 255, 255, 0.4)' }};
            border-radius: 50%;
            z-index: 10;
        }
        .pin-top-left { top: 6mm; left: 6mm; }
        .pin-top-right { top: 6mm; right: 6mm; }
        .pin-bottom-left { bottom: 6mm; left: 6mm; }
        .pin-bottom-right { bottom: 6mm; right: 6mm; }

        /* Top Header Area */
        .top-header {
            position: absolute;
            top: 10mm;
            left: 12mm;
            right: 12mm;
            text-align: center;
            z-index: 5;
        }
        .event-title {
            font-size: 13pt;
            font-weight: 900;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: {{ $textColor }};
            text-shadow: {{ $textShadow }};
            margin: 0;
        }
        .category-badge {
            display: inline-block;
            background: #ea580c;
            color: #ffffff;
            font-size: 8.5pt;
            font-weight: 800;
            letter-spacing: 1px;
            padding: 1.5mm 5mm;
            border-radius: 3mm;
            text-transform: uppercase;
            margin-top: 1.5mm;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }

        /* Dynamic Positioned BIB Box */
        .bib-box {
            position: absolute;
            top: {{ $bibTopMm }}mm;
            left: 10mm;
            right: 10mm;
            text-align: center;
            line-height: 1;
            z-index: 5;
        }
        .bib-number {
            font-size: {{ $bibFontSizePt }}pt;
            font-weight: 900;
            letter-spacing: 4px;
            color: {{ $textColor }};
            font-family: 'Arial Black', Impact, 'Helvetica Neue', sans-serif;
            text-shadow: {{ $textShadow }};
            margin: 0;
            padding: 0;
        }

        /* Dynamic Positioned Runner Name Box */
        .runner-box {
            position: absolute;
            top: {{ $nameTopMm }}mm;
            left: 10mm;
            right: 10mm;
            text-align: center;
            line-height: 1.2;
            z-index: 5;
        }
        .runner-name {
            font-size: {{ $nameFontSizePt }}pt;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: {{ $textColor }};
            text-shadow: {{ $textShadow }};
            margin: 0;
        }

        /* Footer Meta & QR Section */
        .footer-layer {
            position: absolute;
            bottom: 8mm;
            left: 12mm;
            right: 12mm;
            z-index: 5;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-left {
            text-align: left;
            vertical-align: bottom;
            width: 75%;
        }
        .footer-right {
            text-align: right;
            vertical-align: bottom;
            width: 25%;
        }
        .event-type-tag {
            font-size: 7.5pt;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: {{ $textColor }};
            opacity: 0.85;
            text-shadow: {{ $textShadow }};
        }
        .qr-image {
            width: 17mm;
            height: 17mm;
            background: #ffffff;
            padding: 1mm;
            border-radius: 2mm;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
            display: inline-block;
        }
        .qr-caption {
            font-size: 5.5pt;
            color: {{ $textColor }};
            opacity: 0.75;
            margin-top: 1mm;
            letter-spacing: 0.5px;
            text-shadow: {{ $textShadow }};
        }
    </style>
</head>
<body>

    <!-- BACKGROUND LAYER -->
    <div class="bg-layer">
        @if($ebibBgBase64)
            <img class="bg-image" src="{{ $ebibBgBase64 }}" alt="E-BIB Background">
        @else
            <div class="default-bg">
                <div class="stripe-accent"></div>
                <div class="bottom-accent"></div>
            </div>
        @endif
    </div>

    <!-- CORNER PIN HOLES -->
    @if($showPinHoles)
        <div class="pin-hole pin-top-left"></div>
        <div class="pin-hole pin-top-right"></div>
        <div class="pin-hole pin-bottom-left"></div>
        <div class="pin-hole pin-bottom-right"></div>
    @endif

    <!-- TOP HEADER -->
    @if($showEventTitle || $showCategoryBadge)
        <div class="top-header">
            @if($showEventTitle)
                <h1 class="event-title">{{ $event->title }}</h1>
            @endif
            @if($showCategoryBadge)
                <div class="category-badge">
                    {{ $category->name }} ({{ $category->distance_km }} KM)
                </div>
            @endif
        </div>
    @endif

    <!-- HERO BIB NUMBER (DENGAN JARAK VERTIKAL DINAMIS) -->
    <div class="bib-box">
        <div class="bib-number">{{ $participant->bib_number ?: 'RUNNER' }}</div>
    </div>

    <!-- RUNNER NAME (DENGAN JARAK VERTIKAL DINAMIS) -->
    <div class="runner-box">
        <div class="runner-name">{{ $participant->bib_name ?: $participant->full_name }}</div>
    </div>

    <!-- FOOTER META & QR CODE -->
    @if($showQrCode || empty($ebibBgBase64))
        <div class="footer-layer">
            <table class="footer-table">
                <tr>
                    <td class="footer-left">
                        @if(empty($ebibBgBase64))
                            <div class="event-type-tag">
                                OFFICIAL RACER &bull; VIRTUAL RUN CHALLENGE<br>
                                Periode: {{ $event->race_start_date ? \Illuminate\Support\Carbon::parse($event->race_start_date)->format('d M Y') : 'Kapan Saja' }}
                                @if($event->race_end_date)
                                    s/d {{ \Illuminate\Support\Carbon::parse($event->race_end_date)->format('d M Y') }}
                                @endif
                            </div>
                        @endif
                    </td>
                    <td class="footer-right">
                        @if($showQrCode && !empty($qrCodeBase64))
                            <img class="qr-image" src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR Code">
                            <div class="qr-caption">Scan Verifikasi</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    @endif

</body>
</html>
