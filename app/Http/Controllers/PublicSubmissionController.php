<?php

namespace App\Http\Controllers;

use App\Models\ActivitySubmission;
use App\Models\Participant;
use App\Services\CertificatePdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicSubmissionController extends Controller
{
    /**
     * Tampilkan Universal Submission Portal (Zero-Login / Cukup Nomor E-BIB)
     */
    public function index(Request $request): View
    {
        $bib = trim((string) ($request->input('bib') ?: old('bib_number', '')));
        $participant = null;
        $error = null;

        if (! empty($bib)) {
            $participant = Participant::where('bib_number', $bib)
                ->with([
                    'category.event',
                    'jerseySize',
                    'submissions' => fn ($q) => $q->orderBy('activity_date', 'desc')->orderBy('id', 'desc'),
                ])
                ->first();

            if (! $participant) {
                $error = "Nomor E-BIB '{$bib}' tidak ditemukan. Pastikan Anda memasukkan nomor E-BIB yang benar.";
            }
        }

        return view('public.submit', compact('participant', 'bib', 'error'));
    }

    /**
     * Simpan aktivitas lari (Strava/GDrive link atau Upload Screenshot) dan pemicu Pop-up Motivasi
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        // 1. Normalisasi input sebelum validasi
        $rawBib = trim((string) $request->input('bib_number', ''));
        $rawDistance = $request->input('distance_km');
        if (is_string($rawDistance)) {
            // Ubah koma menjadi titik (contoh 5,2 -> 5.2)
            $rawDistance = trim(str_replace(',', '.', $rawDistance));
        }

        $rawProofUrl = trim((string) $request->input('proof_url', ''));
        if ($rawProofUrl !== '' && ! preg_match('#^https?://#i', $rawProofUrl)) {
            // Otomatis tambahkan https:// jika user mengetik strava.com/... atau strava.app.link/...
            $rawProofUrl = 'https://'.$rawProofUrl;
        }

        $request->merge([
            'bib_number' => $rawBib,
            'distance_km' => $rawDistance,
            'proof_url' => $rawProofUrl !== '' ? $rawProofUrl : null,
        ]);

        $validated = $request->validate([
            'bib_number' => ['required', 'string', 'exists:participants,bib_number'],
            'activity_date' => ['required', 'date'],
            'distance_km' => ['required', 'numeric', 'min:0.01', 'max:500'],
            'hours' => ['nullable', 'integer', 'min:0', 'max:99'],
            'minutes' => ['nullable', 'integer', 'min:0', 'max:59'],
            'seconds' => ['nullable', 'integer', 'min:0', 'max:59'],
            'proof_url' => ['nullable', 'url', 'max:1000', 'required_without:proof_image'],
            'proof_image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp,heic', 'max:1024', 'required_without:proof_url'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'bib_number.required' => 'Nomor E-BIB wajib diisi.',
            'bib_number.exists' => 'Nomor E-BIB tidak terdaftar di sistem.',
            'activity_date.required' => 'Tanggal sesi lari wajib diisi.',
            'distance_km.required' => 'Jarak tempuh lari wajib diisi.',
            'distance_km.numeric' => 'Jarak tempuh harus berupa angka desimal (contoh: 5.25).',
            'distance_km.min' => 'Jarak tempuh minimal 0.01 KM.',
            'proof_url.required_without' => 'Harap sertakan link bukti lari (Strava/Garmin/Drive) ATAU unggah foto bukti lari.',
            'proof_url.url' => 'Format URL bukti lari tidak valid (contoh: https://www.strava.com/activities/12345).',
            'proof_image.required_without' => 'Harap unggah screenshot bukti GPS/treadmill ATAU sertakan link aktivitas publik.',
            'proof_image.image' => 'File bukti harus berupa gambar (JPG, PNG, WEBP).',
            'proof_image.max' => 'Ukuran file foto bukti maksimal 1 MB.',
        ]);

        $participant = Participant::where('bib_number', $validated['bib_number'])->firstOrFail();

        $hours = (int) ($validated['hours'] ?? 0);
        $minutes = (int) ($validated['minutes'] ?? 0);
        $seconds = (int) ($validated['seconds'] ?? 0);
        $durationSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;

        $distanceKm = (float) $validated['distance_km'];
        $calculatedPace = ($distanceKm > 0 && $durationSeconds > 0)
            ? round(($durationSeconds / 60) / $distanceKm, 2)
            : null;

        // Simpan file gambar jika diunggah
        $proofImagePath = null;
        if ($request->hasFile('proof_image') && $request->file('proof_image')->isValid()) {
            $proofImagePath = $request->file('proof_image')->store('submissions/proofs', 'public');
        }

        $submission = ActivitySubmission::create([
            'participant_id' => $participant->id,
            'activity_date' => $validated['activity_date'],
            'distance_km' => $distanceKm,
            'duration_seconds' => $durationSeconds,
            'calculated_pace' => $calculatedPace,
            'proof_url' => $validated['proof_url'] ?? null,
            'proof_image_path' => $proofImagePath,
            'notes' => $validated['notes'] ?? null,
            'validation_status' => 'approved',
        ]);

        // Hitung ulang akumulasi progres jarak & status finisher
        $participant->recalculateProgress();
        $participant->refresh();

        // Buat data pesan motivasi dinamis
        $progressPct = $participant->progressPercentage();
        $isFinisher = $participant->isFinisher();
        $remainingKm = $participant->remainingDistance();

        if ($isFinisher) {
            $modalTitle = '🏆 LUAR BIASA! TARGET TUNTAS 100% — SELAMAT FINISHER!';
            $modalMessage = 'Dedikasi, keringat, dan konsistensimu terbayar lunas! Kamu resmi menjadi Finisher. Unduh sertifikat kebanggaanmu sekarang dan rayakan pencapaian hebat ini!';
            $badgeTier = 'FINISHER';
        } elseif ($progressPct >= 70) {
            $modalTitle = '⚡ Sedikit Lagi Jadi Finisher!';
            $modalMessage = "Sisa {$remainingKm} km terakhir sudah di depan mata! Di sinilah mental juara diuji. Kumpulkan sisa energimu dan tuntaskan perjuangan hebat ini!";
            $badgeTier = 'FINAL_PUSH';
        } elseif ($progressPct >= 30) {
            $modalTitle = '🔥 Momentummu Makin Tak Terbendung!';
            $modalMessage = "Kamu sudah melangkah sejauh {$participant->total_distance_km} km. Napasmu kian teratur, tekadmu kian membaja. Jangan kasih kendor, garis finish menantimu!";
            $badgeTier = 'BUILDING_RHYTHM';
        } else {
            $modalTitle = '🚀 Langkah Awal yang Luar Biasa!';
            $modalMessage = 'Perjalanan ribuan kilometer selalu dimulai dari satu langkah pertama. Komitmenmu sudah terbukti hari ini. Jaga ritme dan nikmati setiap langkahmu!';
            $badgeTier = 'THE_SPARK';
        }

        $modalData = [
            'title' => $modalTitle,
            'message' => $modalMessage,
            'tier' => $badgeTier,
            'is_finisher' => $isFinisher,
            'added_km' => number_format($distanceKm, 2),
            'total_km' => number_format((float) $participant->total_distance_km, 2),
            'remaining_km' => number_format($remainingKm, 2),
            'progress_percent' => $progressPct,
            'target_km' => number_format($participant->targetDistance(), 2),
            'certificate_url' => route('public.certificate.download', $participant->bib_number),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'modal' => $modalData,
            ]);
        }

        return redirect()->route('public.submit.index', ['bib' => $participant->bib_number])
            ->with('success', 'Catatan lari sebesar '.number_format($distanceKm, 2).' KM berhasil disimpan!')
            ->with('motivational_modal', $modalData);
    }

    /**
     * Unduh E-Certificate Finisher resmi
     */
    public function downloadCertificate(string $bibNumber, CertificatePdfService $certificatePdfService)
    {
        $participant = Participant::where('bib_number', $bibNumber)
            ->with(['category.event'])
            ->firstOrFail();

        if (! $participant->isFinisher()) {
            return back()->with('error', 'Sertifikat hanya dapat diunduh setelah Anda menyelesaikan target jarak kategori.');
        }

        return $certificatePdfService->downloadCertificate($participant);
    }
}
