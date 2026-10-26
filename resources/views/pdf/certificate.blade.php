<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>E-Certificate Finisher — {{ $participant->full_name }}</title>
    @php
        $event = $participant->category?->event ?? $participant->event;
        $certBgBase64 = $event?->getCertificateBackgroundBase64();
    @endphp
    <style>
        @page {
            size: a4 landscape;
            margin: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #0f172a;
            color: #ffffff;
            -webkit-print-color-adjust: exact;
        }
        .cert-container {
            width: 297mm;
            height: 210mm;
            box-sizing: border-box;
            position: relative;
            padding: 16mm 20mm;
            @if($certBgBase64)
            background: transparent;
            border: none;
            @else
            background: radial-gradient(circle at 50% 30%, #1e293b 0%, #0f172a 100%);
            border: 8mm solid #090d16;
            @endif
        }
        @if($certBgBase64)
        .cert-border, .cert-corner {
            display: none;
        }
        .cert-title {
            text-shadow: 0 2px 8px rgba(0,0,0,0.9);
        }
        .recipient-name {
            text-shadow: 0 2px 8px rgba(0,0,0,0.9);
        }
        .stats-table {
            background: rgba(15, 23, 42, 0.85) !important;
        }
        @endif
        .cert-border {
            position: absolute;
            top: 6mm;
            left: 6mm;
            right: 6mm;
            bottom: 6mm;
            border: 1px solid rgba(245, 158, 11, 0.4);
            pointer-events: none;
        }
        .cert-corner {
            position: absolute;
            width: 14mm;
            height: 14mm;
            border: 2px solid #f59e0b;
        }
        .corner-tl { top: 5mm; left: 5mm; border-right: none; border-bottom: none; }
        .corner-tr { top: 5mm; right: 5mm; border-left: none; border-bottom: none; }
        .corner-bl { bottom: 5mm; left: 5mm; border-right: none; border-top: none; }
        .corner-br { bottom: 5mm; right: 5mm; border-left: none; border-top: none; }

        .header {
            text-align: center;
            margin-top: 6mm;
        }
        .brand-badge {
            display: inline-block;
            background: rgba(245, 158, 11, 0.15);
            color: #f59e0b;
            font-size: 11pt;
            font-weight: 700;
            letter-spacing: 3px;
            padding: 4px 18px;
            border-radius: 20px;
            text-transform: uppercase;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .cert-title {
            font-size: 32pt;
            font-weight: 900;
            letter-spacing: 2px;
            margin: 8px 0 2px 0;
            text-transform: uppercase;
            color: #ffffff;
        }
        .cert-subtitle {
            font-size: 12pt;
            color: #94a3b8;
            letter-spacing: 4px;
            text-transform: uppercase;
        }
        .awarded-to {
            font-size: 11pt;
            color: #cbd5e1;
            margin-top: 8mm;
            text-align: center;
            letter-spacing: 1px;
        }
        .recipient-name {
            font-size: 28pt;
            font-weight: 800;
            color: #38bdf8;
            text-align: center;
            margin: 3mm 0 2mm 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .category-text {
            text-align: center;
            font-size: 13pt;
            color: #f8fafc;
            margin-bottom: 6mm;
        }
        .highlight-cat {
            color: #f59e0b;
            font-weight: 700;
        }

        .stats-table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            text-align: center;
            background: rgba(30, 41, 59, 0.7);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .stats-table td {
            padding: 10px 15px;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
        }
        .stats-table td:last-child {
            border-right: none;
        }
        .stat-label {
            font-size: 9pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 3px;
        }
        .stat-val {
            font-size: 16pt;
            font-weight: 800;
            color: #ffffff;
        }

        .footer {
            position: absolute;
            bottom: 15mm;
            left: 20mm;
            right: 20mm;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
        .footer-left {
            text-align: left;
            width: 33%;
            vertical-align: bottom;
        }
        .footer-center {
            text-align: center;
            width: 34%;
            vertical-align: bottom;
        }
        .footer-right {
            text-align: right;
            width: 33%;
            vertical-align: bottom;
        }
        .sign-title {
            font-size: 9pt;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sign-name {
            font-size: 11pt;
            font-weight: 700;
            color: #f8fafc;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <div class="cert-container">
        @if($certBgBase64)
            <div style="position: absolute; top: 0; left: 0; width: 297mm; height: 210mm; z-index: -10;">
                <img src="{{ $certBgBase64 }}" style="width: 297mm; height: 210mm; display: block;">
            </div>
        @endif

        <div class="cert-border"></div>
        <div class="cert-corner corner-tl"></div>
        <div class="cert-corner corner-tr"></div>
        <div class="cert-corner corner-bl"></div>
        <div class="cert-corner corner-br"></div>

        <div class="header">
            <span class="brand-badge">Official Finisher Certificate</span>
            <div class="cert-title">SERTIFIKAT PENGHARGAAN</div>
            <div class="cert-subtitle">{{ $participant->event?->title ?? 'VIRTUAL RUN CHALLENGE' }}</div>
        </div>

        <div class="awarded-to">DIBERIKAN DENGAN BANGGA KEPADA:</div>
        <div class="recipient-name">{{ $participant->full_name }}</div>
        <div class="category-text">
            Telah menyelesaikan tantangan lari kategori <span class="highlight-cat">{{ $participant->category?->name ?? 'Virtual Run' }}</span> dengan semangat, dedikasi, dan pantang menyerah.
        </div>

        <table class="stats-table">
            <tr>
                <td>
                    <div class="stat-label">NOMOR E-BIB</div>
                    <div class="stat-val" style="color: #f59e0b;">#{{ $participant->bib_number }}</div>
                </td>
                <td>
                    <div class="stat-label">TOTAL JARAK</div>
                    <div class="stat-val">{{ number_format((float) $participant->total_distance_km, 2) }} KM</div>
                </td>
                <td>
                    <div class="stat-label">TOTAL DURASI</div>
                    <div class="stat-val">{{ gmdate('H:i:s', (int) $participant->total_duration_seconds) }}</div>
                </td>
                <td>
                    <div class="stat-label">TANGGAL FINISHER</div>
                    <div class="stat-val">{{ $participant->finished_at ? $participant->finished_at->format('d M Y') : now()->format('d M Y') }}</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td class="footer-left">
                        <div class="sign-title">VERIFIKASI SISTEM</div>
                        <div class="sign-name">STATUS: OFFICIAL FINISHER</div>
                        <div style="font-size: 8pt; color: #64748b; margin-top: 2px;">TOKEN: {{ substr($participant->qr_token, 0, 16) }}</div>
                    </td>
                    <td class="footer-center">
                        <div style="font-size: 24pt;">🏅</div>
                        <div style="font-size: 8pt; color: #f59e0b; font-weight: bold; letter-spacing: 2px;">FINISHER MEDALIST</div>
                    </td>
                    <td class="footer-right">
                        <div class="sign-title">RACE DIRECTOR</div>
                        <div class="sign-name">TIM MANAJEMEN VIRTUAL RUN</div>
                        <div style="font-size: 8pt; color: #64748b; margin-top: 2px;">{{ config('app.name', 'Virtual Run Platform') }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
