<?php

namespace Tests\Feature;

use App\Jobs\SendCertificateEmailJob;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\JerseySize;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\Participant;
use App\Services\CertificatePdfService;
use App\Services\MailketingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class FinisherCertificateManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Event $event;

    protected EventCategory $category;

    protected JerseySize $jerseySize;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $organizer = Organizer::create([
            'name' => 'Run Masters',
            'slug' => 'run-masters',
            'email' => 'admin@runmasters.com',
            'phone' => '081234567890',
            'commission_rate' => 0.00,
        ]);

        $this->event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Borobudur Virtual Marathon 2026',
            'slug' => 'borobudur-virtual-2026',
            'event_code' => 'BVM26',
            'activity_type' => 'RUN',
            'submission_mode' => 'MULTIPLE',
            'race_location_name' => 'Di Mana Saja',
            'registration_open_date' => now()->subDays(5),
            'registration_close_date' => now()->addDays(20),
            'event_start_date' => now()->subDays(2),
            'event_end_date' => now()->addDays(30),
            'race_start_date' => now()->subDays(2),
            'race_end_date' => now()->addDays(30),
            'status' => 'published',
        ]);

        $this->category = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => '10K Challenge',
            'distance_km' => 10.0,
            'normal_price' => 150000,
            'price' => 150000,
            'quota' => 100,
        ]);

        $this->jerseySize = JerseySize::create([
            'event_id' => $this->event->id,
            'size_name' => 'L',
            'gender_type' => 'unisex',
            'additional_price' => 0,
        ]);

        $this->order = Order::create([
            'order_code' => 'ORD-CERT-001',
            'event_id' => $this->event->id,
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081299988877',
            'status' => 'paid',
            'base_amount' => 150000,
            'total_amount' => 150000,
            'paid_at' => now(),
        ]);
    }

    private function createParticipant(bool $isFinisher = true): Participant
    {
        return Participant::create([
            'order_id' => $this->order->id,
            'event_category_id' => $this->category->id,
            'jersey_size_id' => $this->jerseySize->id,
            'bib_number' => '1088',
            'bib_name' => 'BUDI S',
            'full_name' => 'Budi Santoso',
            'gender' => 'male',
            'phone' => '081299988877',
            'email' => 'budi@example.com',
            'jersey_type' => 'pendek',
            'qr_token' => 'qr-token-test-1088',
            'total_distance_km' => $isFinisher ? 10.50 : 4.20,
            'total_duration_seconds' => $isFinisher ? 3600 : 1500,
            'finisher_status' => $isFinisher ? 'finisher' : 'in_progress',
            'finished_at' => $isFinisher ? now() : null,
        ]);
    }

    public function test_certificate_pdf_service_generates_valid_pdf_content(): void
    {
        $participant = $this->createParticipant(isFinisher: true);
        $service = app(CertificatePdfService::class);

        $pdfBinary = $service->generateCertificatePdf($participant);

        $this->assertNotEmpty($pdfBinary);
        $this->assertStringStartsWith('%PDF', $pdfBinary);
    }

    public function test_certificate_pdf_service_provides_streamed_download_response(): void
    {
        $participant = $this->createParticipant(isFinisher: true);
        $service = app(CertificatePdfService::class);

        $response = $service->downloadCertificate($participant);

        $this->assertEquals(200, $response->getStatusCode());
        $contentDisposition = $response->headers->get('content-disposition');
        $this->assertStringContainsString('E-Certificate-Finisher-1088.pdf', $contentDisposition);
    }

    public function test_public_route_allows_finisher_to_download_certificate(): void
    {
        $participant = $this->createParticipant(isFinisher: true);

        $response = $this->get(route('public.certificate.download', $participant->bib_number));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/pdf', $response->headers->get('content-type'));
    }

    public function test_public_route_blocks_non_finisher_from_downloading_certificate(): void
    {
        $participant = $this->createParticipant(isFinisher: false);

        $response = $this->get(route('public.certificate.download', $participant->bib_number));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_send_certificate_email_job_successfully_sends_email_to_finisher(): void
    {
        $participant = $this->createParticipant(isFinisher: true);

        $mockMailketing = Mockery::mock(MailketingService::class);
        $mockMailketing->shouldReceive('sendEmail')
            ->once()
            ->withArgs(function ($recipientEmail, $recipientName, $subject, $htmlContent, $attachmentBase64, $attachmentName, $attachmentUrl) use ($participant) {
                return $recipientEmail === 'budi@example.com'
                    && $recipientName === $participant->full_name
                    && str_contains($subject, 'Selamat Finisher')
                    && str_contains($htmlContent, '1088')
                    && ! empty($attachmentBase64)
                    && $attachmentName === 'E-Certificate-Finisher-1088.pdf';
            })
            ->andReturn(true);

        $this->app->instance(MailketingService::class, $mockMailketing);

        $job = new SendCertificateEmailJob($participant);
        $job->handle($mockMailketing, app(CertificatePdfService::class));
    }

    public function test_send_certificate_email_job_supports_custom_recipient_email(): void
    {
        $participant = $this->createParticipant(isFinisher: true);
        $customEmail = 'alternative@runner.com';

        $mockMailketing = Mockery::mock(MailketingService::class);
        $mockMailketing->shouldReceive('sendEmail')
            ->once()
            ->withArgs(function ($recipientEmail) use ($customEmail) {
                return $recipientEmail === $customEmail;
            })
            ->andReturn(true);

        $this->app->instance(MailketingService::class, $mockMailketing);

        $job = new SendCertificateEmailJob($participant, $customEmail);
        $job->handle($mockMailketing, app(CertificatePdfService::class));
    }

    public function test_send_certificate_email_job_skips_when_participant_not_finisher(): void
    {
        $participant = $this->createParticipant(isFinisher: false);

        $mockMailketing = Mockery::mock(MailketingService::class);
        $mockMailketing->shouldNotReceive('sendEmail');

        $this->app->instance(MailketingService::class, $mockMailketing);

        $job = new SendCertificateEmailJob($participant);
        $job->handle($mockMailketing, app(CertificatePdfService::class));
    }
}
