<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\Participant;
use App\Services\TicketPdfService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventCustomBackgroundTest extends TestCase
{
    use RefreshDatabase;

    protected Event $event;

    protected EventCategory $category;

    protected Participant $participant;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $organizer = Organizer::create([
            'name' => 'Komunitas Lari Indonesia',
            'slug' => 'komunitas-lari',
            'email' => 'admin@komunitaslari.id',
            'phone' => '08123456789',
        ]);

        $this->event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Borobudur Virtual Sunset Run 2026',
            'slug' => 'borobudur-virtual-sunset-2026',
            'event_type' => 'VIRTUAL',
            'submission_mode' => 'CUMULATIVE',
            'event_start_date' => now()->addDays(10),
            'event_end_date' => now()->addDays(30),
            'registration_open_date' => now()->subDays(5),
            'registration_close_date' => now()->addDays(5),
            'race_location_name' => 'Di Mana Saja (Virtual)',
            'status' => 'published',
        ]);

        $this->category = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => '10K Challenge',
            'distance_km' => 10.0,
            'normal_price' => 175000,
            'quota' => 500,
            'is_active' => true,
        ]);

        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'ORD-BG-TEST-001',
            'customer_name' => 'Rian Hidayat',
            'customer_email' => 'rian@hidayat.id',
            'customer_phone' => '081298765432',
            'subtotal' => 175000,
            'grand_total' => 175000,
            'status' => 'paid',
        ]);

        $this->participant = Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category->id,
            'full_name' => 'Rian Hidayat',
            'bib_name' => 'RIAN H.',
            'bib_number' => '1008',
            'id_type' => 'KTP',
            'id_number' => '3201010101019999',
            'gender' => 'male',
            'birth_date' => '1992-02-02',
            'phone' => '081298765432',
            'email' => 'rian@hidayat.id',
            'emergency_contact_name' => 'Keluarga',
            'emergency_contact_phone' => '081298765433',
            'emergency_contact_relation' => 'Keluarga',
            'qr_token' => 'token-rian-bg-test',
            'finisher_status' => 'finisher',
            'total_distance_km' => 10.5,
            'total_duration_seconds' => 3600,
            'finished_at' => now(),
        ]);
    }

    public function test_event_can_store_ebib_and_certificate_background_paths(): void
    {
        $fakeEbib = UploadedFile::fake()->image('ebib_template.jpg', 1200, 600);
        $ebibPath = $fakeEbib->store('events/ebib-backgrounds', 'public');

        $fakeCert = UploadedFile::fake()->image('certificate_template.png', 2000, 1414);
        $certPath = $fakeCert->store('events/certificate-backgrounds', 'public');

        $this->event->update([
            'ebib_background_path' => $ebibPath,
            'certificate_background_path' => $certPath,
        ]);

        $this->event->refresh();

        $this->assertEquals($ebibPath, $this->event->ebib_background_path);
        $this->assertEquals($certPath, $this->event->certificate_background_path);
        $this->assertTrue($this->event->hasEbibBackground());
        $this->assertTrue($this->event->hasCertificateBackground());

        // Pastikan getBase64 helper menghasilkan Data URI yang valid
        $ebibBase64 = $this->event->getEbibBackgroundBase64();
        $this->assertNotNull($ebibBase64);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $ebibBase64);

        $certBase64 = $this->event->getCertificateBackgroundBase64();
        $this->assertNotNull($certBase64);
        $this->assertStringStartsWith('data:image/png;base64,', $certBase64);
    }

    public function test_ticket_pdf_generates_successfully_with_custom_ebib_background(): void
    {
        $fakeEbib = UploadedFile::fake()->image('ebib_template.jpg', 800, 400);
        $ebibPath = $fakeEbib->store('events/ebib-backgrounds', 'public');

        $this->event->update([
            'ebib_background_path' => $ebibPath,
        ]);

        $ticketService = app(TicketPdfService::class);
        $pdfOutput = $ticketService->generateTicketPdf($this->participant);

        $this->assertNotEmpty($pdfOutput);
        $this->assertStringStartsWith('%PDF', $pdfOutput);
    }

    public function test_finisher_certificate_pdf_generates_successfully_with_custom_certificate_background(): void
    {
        $fakeCert = UploadedFile::fake()->image('cert_bg.jpg', 1000, 700);
        $certPath = $fakeCert->store('events/certificate-backgrounds', 'public');

        $this->event->update([
            'certificate_background_path' => $certPath,
        ]);

        $response = $this->get("/certificate/{$this->participant->bib_number}/download");
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');

        $pdfContent = $response->getContent();
        $this->assertStringStartsWith('%PDF', $pdfContent);
    }

    public function test_check_ticket_page_displays_custom_ebib_background(): void
    {
        $fakeEbib = UploadedFile::fake()->image('custom_ebib.jpg', 800, 400);
        $ebibPath = $fakeEbib->store('events/ebib-backgrounds', 'public');

        $this->event->update([
            'ebib_background_path' => $ebibPath,
        ]);

        $response = $this->post('/cek-tiket', [
            'query' => $this->participant->bib_number,
        ]);
        $response->assertStatus(200);
        $response->assertSee($ebibPath);
    }
}
