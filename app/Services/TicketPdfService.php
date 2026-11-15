<?php

namespace App\Services;

use App\Models\Participant;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketPdfService
{
    /**
     * Render E-Ticket PDF binary untuk peserta
     */
    public function generateTicketPdf(Participant $participant): string
    {
        $targetQuery = $participant->bib_number ?: ($participant->qr_token ?: (string) $participant->id);
        $verificationUrl = route('public.ticket.index', ['query' => $targetQuery]);

        $qrCodeSvg = QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($verificationUrl);

        $qrCodeBase64 = base64_encode($qrCodeSvg);

        $pdf = Pdf::loadView('pdf.ticket', [
            'participant' => $participant,
            'event' => $participant->event,
            'category' => $participant->category,
            'jerseySize' => $participant->jerseySize,
            'order' => $participant->order,
            'qrCodeBase64' => $qrCodeBase64,
        ])->setPaper('a4', 'portrait');

        return $pdf->output();
    }
}
