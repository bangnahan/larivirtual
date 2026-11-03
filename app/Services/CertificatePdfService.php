<?php

namespace App\Services;

use App\Models\Participant;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\Response;

class CertificatePdfService
{
    /**
     * Render E-Certificate Finisher PDF binary
     */
    public function generateCertificatePdf(Participant $participant): string
    {
        $participant->loadMissing(['category.event', 'submissions']);

        $pdf = Pdf::loadView('pdf.certificate', [
            'participant' => $participant,
            'event' => $participant->event,
            'category' => $participant->category,
        ])->setPaper('a4', 'landscape');

        return $pdf->output();
    }

    /**
     * Download response E-Certificate PDF untuk browser
     */
    public function downloadCertificate(Participant $participant): Response
    {
        $participant->loadMissing(['category.event', 'submissions']);

        $pdf = Pdf::loadView('pdf.certificate', [
            'participant' => $participant,
            'event' => $participant->event,
            'category' => $participant->category,
        ])->setPaper('a4', 'landscape');

        $fileName = 'E-Certificate-Finisher-'.($participant->bib_number ?: $participant->id).'.pdf';

        return $pdf->download($fileName);
    }
}
