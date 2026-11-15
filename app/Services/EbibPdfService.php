<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Participant;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EbibPdfService
{
    /**
     * Render E-BIB PDF binary untuk peserta (Format A5 Landscape standar nomor dada atletik)
     */
    public function generateEbibPdf(Participant $participant, ?Event $event = null): string
    {
        $participant->loadMissing(['category.event', 'order']);

        $resolvedEvent = $event ?? $participant->event ?? $participant->category?->event;

        $targetQuery = $participant->bib_number ?: ($participant->qr_token ?: '1001');
        $verificationUrl = route('public.ticket.index', ['query' => $targetQuery]);

        $qrCodeSvg = QrCode::format('svg')
            ->size(90)
            ->errorCorrection('M')
            ->generate($verificationUrl);

        $qrCodeBase64 = base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.ebib', [
            'participant' => $participant,
            'event' => $resolvedEvent,
            'category' => $participant->category,
            'qrCodeBase64' => $qrCodeBase64,
        ])->setPaper('a5', 'landscape');

        return $pdf->output();
    }
}
