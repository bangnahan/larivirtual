<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Participant;
use App\Services\EbibPdfService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EventPreviewController extends Controller
{
    public function previewEbib(Request $request, Event $event, EbibPdfService $ebibService): Response
    {
        // Jika terdapat parameter query dari live form, lakukan penyesuaian dinamis pada in-memory model
        $config = is_array($event->ebib_layout_config) ? $event->ebib_layout_config : [];

        if ($request->filled('text_color')) {
            $config['text_color'] = $request->query('text_color');
        }
        if ($request->filled('bib_top_mm')) {
            $config['bib_top_mm'] = (int) $request->query('bib_top_mm');
        }
        if ($request->filled('bib_font_size_pt')) {
            $config['bib_font_size_pt'] = (int) $request->query('bib_font_size_pt');
        }
        if ($request->filled('name_top_mm')) {
            $config['name_top_mm'] = (int) $request->query('name_top_mm');
        }
        if ($request->filled('name_font_size_pt')) {
            $config['name_font_size_pt'] = (int) $request->query('name_font_size_pt');
        }
        if ($request->has('show_event_title')) {
            $config['show_event_title'] = filter_var($request->query('show_event_title'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('show_category_badge')) {
            $config['show_category_badge'] = filter_var($request->query('show_category_badge'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('show_qr_code')) {
            $config['show_qr_code'] = filter_var($request->query('show_qr_code'), FILTER_VALIDATE_BOOLEAN);
        }
        if ($request->has('show_pin_holes')) {
            $config['show_pin_holes'] = filter_var($request->query('show_pin_holes'), FILTER_VALIDATE_BOOLEAN);
        }

        $event->ebib_layout_config = $config;

        $dummyParticipant = new Participant([
            'full_name' => 'Budi Santoso',
            'bib_name' => 'BUDI RUNS',
            'bib_number' => '1001',
            'qr_token' => 'SAMPLE-PREVIEW-TOKEN',
        ]);
        $dummyParticipant->setRelation('event', $event);

        $category = $event->categories()->first() ?: new EventCategory([
            'name' => '10K Virtual Run',
            'distance_km' => 10,
        ]);
        $category->setRelation('event', $event);
        $dummyParticipant->setRelation('category', $category);
        $dummyParticipant->setRelation('event', $event);

        $pdfBinary = $ebibService->generateEbibPdf($dummyParticipant, $event);

        return response($pdfBinary, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Preview-E-BIB-'.$event->slug.'.pdf"',
            'Cache-Control' => 'no-cache, private',
        ]);
    }
}
