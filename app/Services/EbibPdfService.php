<?php

namespace App\Services;

use App\Models\Participant;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EbibPdfService
{
    /**
     * Render E-BIB PDF binary untuk peserta (Format A5 Landscape standar nomor dada atletik)
     */
    public function generateEbibPdf(Participant $participant): string
    {
        $participant->loadMissing(['category.event', 'order']);

        $qrCodeSvg = QrCode::format('svg')
            ->size(90)
            ->errorCorrection('M')
            ->generate($participant->qr_token);

        $qrCodeBase64 = base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.ebib', [
            'participant' => $participant,
            'event' => $participant->event,
            'category' => $participant->category,
            'qrCodeBase64' => $qrCodeBase64,
        ])->setPaper('a5', 'landscape');

        return $pdf->output();
    }
}
