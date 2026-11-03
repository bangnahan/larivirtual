<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat! E-Sertifikat Finisher Anda</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 20px; background-color: #f1f5f9; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .header { background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%); color: #ffffff; padding: 32px 24px; text-align: center; }
        .badge { display: inline-block; background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.4); padding: 4px 14px; border-radius: 9999px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px; }
        .content { padding: 28px 24px; }
        .stats-grid { width: 100%; border-collapse: separate; border-spacing: 0; margin: 24px 0; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; }
        .stats-grid td { padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .stats-grid tr:last-child td { border-bottom: none; }
        .stat-label { color: #64748b; }
        .stat-value { font-weight: 700; color: #0f172a; text-align: right; }
        .cta-btn { display: inline-block; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff !important; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: 700; font-size: 15px; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25); text-align: center; }
        .footer { background: #f8fafc; padding: 20px 24px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="badge">Official Finisher</span>
            <h1 style="margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">🏆 SELAMAT, FINISHER!</h1>
            <p style="margin: 8px 0 0 0; color: #cbd5e1; font-size: 15px;">{{ $event->title ?? $participant->event?->title }}</p>
        </div>
        
        <div class="content">
            <p style="font-size: 16px; margin-top: 0;">Halo <strong>{{ $participant->full_name }}</strong>,</p>
            <p style="color: #475569;">
                Luar biasa! Seluruh jerih payah, keringat, dan konsistensi Anda telah berbuah manis. Anda telah berhasil menuntaskan seluruh target jarak lari dan resmi tercatat sebagai <strong>Finisher</strong> di ajang <strong>{{ $event->title ?? $participant->event?->title }}</strong>.
            </p>

            <table class="stats-grid">
                <tr>
                    <td class="stat-label">Nomor e-BIB</td>
                    <td class="stat-value" style="color: #2563eb; font-family: monospace; font-size: 16px;">#{{ $participant->bib_number }}</td>
                </tr>
                <tr>
                    <td class="stat-label">Kategori Lari</td>
                    <td class="stat-value">{{ $participant->category?->name ?? 'Virtual Run' }} ({{ $participant->category?->distance_km ?? 0 }} KM)</td>
                </tr>
                <tr>
                    <td class="stat-label">Total Jarak Tempuh</td>
                    <td class="stat-value">{{ number_format((float) $participant->total_distance_km, 2) }} KM</td>
                </tr>
                <tr>
                    <td class="stat-label">Total Durasi Lari</td>
                    <td class="stat-value">{{ gmdate('H:i:s', (int) $participant->total_duration_seconds) }}</td>
                </tr>
                <tr>
                    <td class="stat-label">Tanggal Selesai</td>
                    <td class="stat-value">{{ $participant->finished_at ? $participant->finished_at->format('d F Y, H:i') : now()->format('d F Y') }} WIB</td>
                </tr>
            </table>

            @php
                $targetUrl = !empty($downloadUrl) ? $downloadUrl : route('public.certificate.download', $participant->bib_number);
                if (str_contains($targetUrl, 'localhost') || str_contains($targetUrl, '127.0.0.1')) {
                    $targetUrl = str_replace(['http://localhost:8000', 'http://127.0.0.1:8000', 'http://localhost'], 'https://app.jelatix.com', $targetUrl);
                }
            @endphp

            <div style="text-align: center; margin: 32px 0;">
                <a href="{{ $targetUrl }}" target="_blank" class="cta-btn">
                    🏅 Unduh E-Sertifikat Finisher Resmi (PDF)
                </a>
            </div>

            <p style="margin-top: 24px; font-size: 13px; color: #64748b; line-height: 1.6;">
                📎 <strong>Lampiran:</strong> E-Sertifikat Finisher beresolusi tinggi Anda dalam format PDF juga telah dilampirkan langsung pada email ini untuk Anda simpan atau bagikan ke media sosial kebanggaan Anda.
            </p>
            <p style="margin: 0; font-size: 13px; color: #64748b; line-height: 1.6;">
                Kapan saja Anda membutuhkan dokumen ini kembali, Anda dapat mengakses menu <strong>Cek Progres & Tiket</strong> di website resmi Jelatix menggunakan Nomor BIB Anda.
            </p>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Jelatix Virtual Run') }}. Official Verification System.
        </div>
    </div>
</body>
</html>
