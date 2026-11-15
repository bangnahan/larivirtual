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

        $qrToken = $participant->qr_token ?: 'VIRUN-PREVIEW';
        $qrCodeSvg = QrCode::format('svg')
            ->size(90)
            ->errorCorrection('M')
            ->generate($qrToken);

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
