<?php

namespace App\Jobs;

use App\Models\Participant;
use App\Services\CertificatePdfService;
use App\Services\MailketingService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCertificateEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Participant $participant,
        public ?string $recipientEmail = null
    ) {}

    public function handle(MailketingService $mailketing, CertificatePdfService $certificatePdfService): void
    {
        $participant = $this->participant->fresh(['category.event', 'submissions', 'order']);

        if (! $participant || ! $participant->isFinisher()) {
            Log::warning("SendCertificateEmailJob skipped: Participant #{$this->participant->id} is not a finisher (status: {$participant?->finisher_status})");

            return;
        }

        // Tentukan base URL kanonikal publik (gunakan https://app.jelatix.com jika di CLI/local)
        $baseUrl = config('app.url', 'https://app.jelatix.com');
        if (str_contains($baseUrl, 'localhost') || str_contains($baseUrl, '127.0.0.1')) {
            $baseUrl = 'https://app.jelatix.com';
        }
        $baseUrl = rtrim($baseUrl, '/');

        $storageDir = storage_path('app/public/certificates');
        if (! file_exists($storageDir)) {
            @mkdir($storageDir, 0755, true);
        }

        try {
            $pdfBinary = $certificatePdfService->generateCertificatePdf($participant);
            $pdfBase64 = base64_encode($pdfBinary);

            // Simpan salinan PDF ke storage publik
            $pdfFileName = "E-Certificate-{$participant->bib_number}-{$participant->qr_token}.pdf";
            @file_put_contents("{$storageDir}/{$pdfFileName}", $pdfBinary);

            $directPdfUrl = "{$baseUrl}/storage/certificates/{$pdfFileName}";
            $downloadRouteUrl = "{$baseUrl}/certificate/{$participant->bib_number}/download";

            $targetEmail = ! empty($this->recipientEmail)
                ? $this->recipientEmail
                : (! empty($participant->email) ? $participant->email : $participant->order?->customer_email);

            if (empty($targetEmail)) {
                Log::warning("SendCertificateEmailJob aborted: Participant #{$participant->id} has no valid email address.");

                return;
            }

            $subject = "🏅 Selamat Finisher! E-Sertifikat Resmi: {$participant->event?->title} - {$participant->bib_number}";
            $htmlContent = view('emails.finisher_certificate', [
                'participant' => $participant,
                'event' => $participant->event,
                'downloadUrl' => $downloadRouteUrl,
                'directPdfUrl' => $directPdfUrl,
            ])->render();

            $sent = $mailketing->sendEmail(
                recipientEmail: $targetEmail,
                recipientName: $participant->full_name,
                subject: $subject,
                htmlContent: $htmlContent,
                attachmentBase64: $pdfBase64,
                attachmentName: "E-Certificate-Finisher-{$participant->bib_number}.pdf",
                attachmentUrl: $directPdfUrl
            );

            if ($sent) {
                Log::info("E-Certificate email sent successfully to {$targetEmail} for finisher #{$participant->id} (BIB: {$participant->bib_number}) - URL: {$downloadRouteUrl}");
            } else {
                Log::warning("E-Certificate email sending returned false for finisher #{$participant->id} ({$targetEmail})");
            }
        } catch (Exception $e) {
            Log::error("Failed to send certificate email to finisher #{$participant->id}: ".$e->getMessage());
        }
    }
}
