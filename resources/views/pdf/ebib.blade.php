<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>E-BIB {{ $participant->bib_number }} - {{ $participant->bib_name ?: $participant->full_name }}</title>
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
            color: #ffffff;
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

        /* Default Decorative Background if no image uploaded */
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

        /* Pin Hole Marks for realistic physical printing */
        .pin-hole {
            position: absolute;
            width: 6mm;
            height: 6mm;
            border: 1.5px dashed rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            z-index: 10;
        }
        .pin-top-left { top: 6mm; left: 6mm; }
        .pin-top-right { top: 6mm; right: 6mm; }
        .pin-bottom-left { bottom: 6mm; left: 6mm; }
        .pin-bottom-right { bottom: 6mm; right: 6mm; }

        /* Content Container */
        .content-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 210mm;
            height: 148mm;
            z-index: 5;
            padding: 10mm 14mm 8mm 14mm;
            display: table;
        }
        .content-wrapper {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        /* Event Header */
        .event-header {
            margin-bottom: 2mm;
        }
        .event-title {
            font-size: 14pt;
            font-weight: 900;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.8);
            margin: 0;
        }
        .category-badge {
            display: inline-block;
            background: #ea580c;
            color: #ffffff;
            font-size: 9pt;
            font-weight: 800;
            letter-spacing: 1px;
            padding: 2mm 6mm;
            border-radius: 4mm;
            text-transform: uppercase;
            margin-top: 1.5mm;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }

        /* Giant Hero BIB Number */
        .bib-box {
            margin: 3mm 0 2mm 0;
            line-height: 1;
        }
        .bib-number {
            font-size: 64pt;
            font-weight: 900;
            letter-spacing: 4px;
            color: #ffffff;
            font-family: 'Arial Black', Impact, 'Helvetica Neue', sans-serif;
            text-shadow: 0 4px 10px rgba(0, 0, 0, 0.9), 0 0 20px rgba(0,0,0,0.6);
            margin: 0;
            padding: 0;
        }

        /* Runner Name */
        .runner-box {
            margin-top: 1mm;
            margin-bottom: 4mm;
        }
        .runner-name {
            font-size: 18pt;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #ffedd5;
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.9);
            margin: 0;
        }

        /* Footer Meta & QR Section */
        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
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
            color: rgba(255, 255, 255, 0.8);
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.8);
        }
        .qr-image {
            width: 18mm;
            height: 18mm;
            background: #ffffff;
            padding: 1mm;
            border-radius: 2mm;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.5);
            display: inline-block;
        }
        .qr-caption {
            font-size: 6pt;
            color: rgba(255, 255, 255, 0.75);
            margin-top: 1mm;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.8);
        }
    </style>
</head>
<body>

    @php
        $ebibBgBase64 = $event?->getEbibBackgroundBase64();
    @endphp

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

    <!-- CORNER PIN HOLES (SIMBOL NOMOR DADA LARI) -->
    <div class="pin-hole pin-top-left"></div>
    <div class="pin-hole pin-top-right"></div>
    <div class="pin-hole pin-bottom-left"></div>
    <div class="pin-hole pin-bottom-right"></div>

    <!-- CONTENT LAYER -->
    <div class="content-layer">
        <div class="content-wrapper">
            
            <!-- 1. Header: Event & Kategori -->
            <div class="event-header">
                <h1 class="event-title">{{ $event->title }}</h1>
                <div class="category-badge">
                    {{ $category->name }} ({{ $category->distance_km }} KM) &bull; {{ $event->submission_mode === 'CUMULATIVE' ? 'AKUMULASI JARAK' : 'SINGLE RUN' }}
                </div>
            </div>

            <!-- 2. Hero BIB Number -->
            <div class="bib-box">
                <div class="bib-number">{{ $participant->bib_number ?: 'RUNNER' }}</div>
            </div>

            <!-- 3. Runner Name -->
            <div class="runner-box">
                <div class="runner-name">{{ $participant->bib_name ?: $participant->full_name }}</div>
            </div>

            <!-- 4. Footer & QR Code -->
            <table class="footer-table">
                <tr>
                    <td class="footer-left">
                        <div class="event-type-tag">
                            OFFICIAL RACER &bull; VIRTUAL RUN CHALLENGE<br>
                            Periode: {{ $event->race_start_date ? \Illuminate\Support\Carbon::parse($event->race_start_date)->format('d M Y') : 'Kapan Saja' }}
                            @if($event->race_end_date)
                                s/d {{ \Illuminate\Support\Carbon::parse($event->race_end_date)->format('d M Y') }}
                            @endif
                        </div>
                    </td>
                    <td class="footer-right">
                        @if(!empty($qrCodeBase64))
                            <img class="qr-image" src="data:image/svg+xml;base64,{{ $qrCodeBase64 }}" alt="QR Code">
                            <div class="qr-caption">Scan Verifikasi</div>
                        @endif
                    </td>
                </tr>
            </table>

        </div>
    </div>

</body>
</html>
