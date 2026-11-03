<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Certificate Finisher — {{ $participant->full_name }}</title>
    @php
        $event = $participant->category?->event ?? $participant->event;
        $certBgBase64 = $event?->getCertificateBackgroundBase64();
        $finishedDate = $participant->finished_at ? $participant->finished_at->format('d M Y') : now()->format('d M Y');
    @endphp
    <style>
        @page {
            size: 297mm 210mm;
            margin: 0;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        html, body {
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            background-color: #030712;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #ffffff;
            -webkit-print-color-adjust: exact;
        }
        .cert-container {
            position: relative;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
            @if($certBgBase64)
            background: transparent;
            @else
            background: radial-gradient(circle at 50% 35%, #1e293b 0%, #0f172a 55%, #020617 100%);
            @endif
        }

        @if($certBgBase64)
        .title-main, .participant-name, .title-sub, .desc-text {
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.95);
        }
        .stats-table {
            background: rgba(15, 23, 42, 0.92) !important;
        }
        @else
        .border-outer {
            position: absolute;
            top: 8mm;
            left: 8mm;
            width: 281mm;
            height: 194mm;
            border: 2px solid #d97706;
            pointer-events: none;
        }
        .border-inner {
            position: absolute;
            top: 11mm;
            left: 11mm;
            width: 275mm;
            height: 188mm;
            border: 1px solid rgba(245, 158, 11, 0.4);
            pointer-events: none;
        }
        .corner {
            position: absolute;
            width: 16mm;
            height: 16mm;
            border: 3px solid #f59e0b;
            pointer-events: none;
        }
        .corner-tl { top: 7mm; left: 7mm; border-right: none; border-bottom: none; }
        .corner-tr { top: 7mm; right: 7mm; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 7mm; left: 7mm; border-right: none; border-top: none; }
        .corner-br { bottom: 7mm; right: 7mm; border-left: none; border-top: none; }
        @endif

        /* Header Section */
        .header-box {
            position: absolute;
            top: 18mm;
            left: 15mm;
            width: 267mm;
            text-align: center;
        }
        .badge-pill {
            display: inline-block;
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            font-size: 8.5pt;
            font-weight: 700;
            letter-spacing: 3px;
            padding: 4px 18px;
            border-radius: 20px;
            text-transform: uppercase;
            border: 1px solid rgba(245, 158, 11, 0.45);
        }
        .title-main {
            font-size: 28pt;
            font-weight: 900;
            letter-spacing: 3px;
            margin: 4.5mm 0 1.5mm 0;
            text-transform: uppercase;
            color: #ffffff;
        }
        .title-sub {
            font-size: 11.5pt;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 4px;
            text-transform: uppercase;
        }

        /* Recipient Section */
        .recipient-box {
            position: absolute;
            top: 63mm;
            left: 15mm;
            width: 267mm;
            text-align: center;
        }
        .awarded-label {
            font-size: 9.5pt;
            color: #cbd5e1;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-bottom: 2.5mm;
        }
        .participant-name {
            font-size: 32pt;
            font-weight: 900;
            color: #38bdf8;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 3.5mm;
            line-height: 1.1;
        }
        .desc-text {
            font-size: 11pt;
            color: #e2e8f0;
            line-height: 1.6;
            width: 220mm;
            margin: 0 auto;
        }
        .highlight {
            color: #f59e0b;
            font-weight: 700;
        }

        /* Stats Card Section */
        .stats-box {
            position: absolute;
            top: 122mm;
            left: 15mm;
            width: 267mm;
            text-align: center;
        }
        .stats-table {
            width: 236mm;
            margin: 0 auto;
            border-collapse: collapse;
            background: rgba(15, 23, 42, 0.9);
            border: 1.5px solid rgba(245, 158, 11, 0.35);
            border-radius: 8px;
        }
        .stats-table td {
            padding: 10px 16px;
            text-align: center;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }
        .stats-table td:last-child {
            border-right: none;
        }
        .stat-lbl {
            font-size: 8pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 3px;
        }
        .stat-val {
            font-size: 16pt;
            font-weight: 800;
            color: #ffffff;
        }

        /* Footer Section */
        .footer-bar {
            position: absolute;
            top: 166mm;
            left: 18mm;
            width: 261mm;
            border-collapse: collapse;
        }
        .footer-bar td {
            vertical-align: middle;
        }
        .foot-left {
            width: 35%;
            text-align: left;
        }
        .foot-center {
            width: 30%;
            text-align: center;
        }
        .foot-right {
            width: 35%;
            text-align: right;
        }
        .sign-lbl {
            font-size: 7.5pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 3px;
        }
        .sign-val {
            font-size: 10.5pt;
            font-weight: 700;
            color: #f8fafc;
        }
        .token-text {
            font-size: 7.5pt;
            color: #64748b;
            font-family: monospace;
            margin-top: 3px;
        }
        .finisher-badge-box {
            display: inline-block;
            border: 1.5px solid #f59e0b;
            background: rgba(245, 158, 11, 0.15);
            padding: 6px 18px;
            border-radius: 6px;
        }
        .finisher-badge-title {
            font-size: 8.5pt;
            font-weight: 800;
            color: #fbbf24;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .star-icon {
            display: inline-block;
            vertical-align: middle;
            margin: 0 2px;
        }
    </style>
</head>
<body>
    <div class="cert-container">
        @if($certBgBase64)
            <div style="position: absolute; top: 0; left: 0; width: 297mm; height: 210mm; z-index: -10;">
                <img src="{{ $certBgBase64 }}" style="width: 297mm; height: 210mm; display: block;">
            </div>
        @else
            <div class="border-outer"></div>
            <div class="border-inner"></div>
            <div class="corner corner-tl"></div>
            <div class="corner corner-tr"></div>
            <div class="corner corner-bl"></div>
            <div class="corner corner-br"></div>
        @endif

        <!-- 1. Header & Title -->
        <div class="header-box">
            <div class="badge-pill">Official Finisher Certificate</div>
            <div class="title-main">SERTIFIKAT PENGHARGAAN</div>
            <div class="title-sub">{{ $participant->event?->title ?? 'VIRTUAL RUN CHALLENGE' }}</div>
        </div>

        <!-- 2. Recipient Name & Accomplishment -->
        <div class="recipient-box">
            <div class="awarded-label">Diberikan dengan bangga kepada:</div>
            <div class="participant-name">{{ $participant->full_name }}</div>
            <div class="desc-text">
                Telah berhasil menyelesaikan seluruh target tantangan lari kategori <span class="highlight">{{ $participant->category?->name ?? 'Virtual Run' }}</span> ({{ $participant->category?->distance_km ?? 0 }} KM) dengan dedikasi, sportivitas, dan pantang menyerah.
            </div>
        </div>

        <!-- 3. Race Stats -->
        <div class="stats-box">
            <table class="stats-table">
                <tr>
                    <td>
                        <div class="stat-lbl">NOMOR E-BIB</div>
                        <div class="stat-val" style="color: #f59e0b;">#{{ $participant->bib_number }}</div>
                    </td>
                    <td>
                        <div class="stat-lbl">TOTAL JARAK</div>
                        <div class="stat-val">{{ number_format((float) $participant->total_distance_km, 2) }} KM</div>
                    </td>
                    <td>
                        <div class="stat-lbl">TOTAL DURASI</div>
                        <div class="stat-val">{{ gmdate('H:i:s', (int) $participant->total_duration_seconds) }}</div>
                    </td>
                    <td>
                        <div class="stat-lbl">TANGGAL SELESAI</div>
                        <div class="stat-val">{{ $finishedDate }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- 4. Footer & Official Verification -->
        <table class="footer-bar">
            <tr>
                <td class="foot-left">
                    <div class="sign-lbl">VERIFIKASI SISTEM</div>
                    <div class="sign-val">STATUS: OFFICIAL FINISHER</div>
                    <div class="token-text">TOKEN: {{ substr($participant->qr_token, 0, 16) }}</div>
                </td>
                <td class="foot-center">
                    <div class="finisher-badge-box">
                        <span class="star-icon">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="#fbbf24">
                                <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" />
                            </svg>
                        </span>
                        <span class="finisher-badge-title">OFFICIAL FINISHER</span>
                        <span class="star-icon">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="#fbbf24">
                                <polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26" />
                            </svg>
                        </span>
                    </div>
                </td>
                <td class="foot-right">
                    <div class="sign-lbl">RACE DIRECTOR</div>
                    <div class="sign-val">TIM MANAJEMEN VIRTUAL RUN</div>
                    <div class="token-text">{{ config('app.name', 'Virtual Run Platform') }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
