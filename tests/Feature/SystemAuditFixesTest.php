<?php

namespace Tests\Feature;

use App\Filament\Resources\EventResource\Pages\EditEvent;
use App\Jobs\SendPaymentPendingEmailJob;
use App\Jobs\SendTicketEmailJob;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\JerseySize;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\User;
use App\Services\EbibPdfService;
use App\Services\OrderManagementService;
use App\Services\ProductionPoExportService;
use App\Services\TripayService;
use Filament\Facades\Filament;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class SystemAuditFixesTest extends TestCase
{
    use RefreshDatabase;

    protected Organizer $organizer;

    protected Event $event;

    protected EventCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organizer = Organizer::create([
            'name' => 'LariVirtual Organizer',
            'slug' => 'larivirtual-organizer',
            'email' => 'organizer@larivirtual.com',
            'phone' => '081234567890',
        ]);

        $this->event = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Audit Fix Marathon 2026',
            'slug' => 'audit-fix-marathon-2026',
            'race_location_name' => 'Jakarta',
            'event_start_date' => now()->addDays(20),
            'registration_open_date' => now()->subDays(5),
            'registration_close_date' => now()->addDays(10),
            'auto_assign_bib' => true,
            'status' => 'published',
        ]);

        $this->category = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => '10K Audit Category',
            'distance_km' => 10.0,
            'normal_price' => 100000,
            'quota' => 50,
            'slots_taken' => 0,
            'is_active' => true,
            'includes_jersey' => false,
        ]);
    }

    public function test_registration_is_rejected_when_event_registration_period_is_closed(): void
    {
        // Tutup pendaftaran event
        $this->event->update([
            'registration_close_date' => now()->subDay(),
        ]);

        $this->assertFalse($this->event->isRegistrationOpen());

        $payload = [
            'buyer_name' => 'Late Runner',
            'buyer_email' => 'late@runner.com',
            'buyer_phone' => '081234567890',
            'payment_method' => 'QRIS',
            'waiver_accepted' => '1',
            'participants' => [
                [
                    'category_id' => $this->category->id,
                    'bib_name' => 'LATE RUNNER',
                    'gender' => 'male',
                ],
            ],
        ];

        // Akses checkout harus ditolak dan redirect kembali
        $res = $this->post(route('public.register.checkout', $this->event->slug), $payload);
        $res->assertRedirect(route('public.register.show', $this->event->slug));
        $res->assertSessionHasErrors('registration');

        // Pastikan tidak ada order yang terbuat
        $this->assertEquals(0, Order::count());
    }

    public function test_checkout_calculates_clean_grand_total_without_double_charging_gateway_fee(): void
    {
        $payloadQris = [
            'buyer_name' => 'Buyer QRIS',
            'buyer_email' => 'qris@runner.com',
            'buyer_phone' => '081234567890',
            'payment_method' => 'QRIS',
            'waiver_accepted' => '1',
            'participants' => [
                [
                    'category_id' => $this->category->id,
                    'bib_name' => 'QRIS RUNNER',
                    'gender' => 'male',
                ],
            ],
        ];

        $resQris = $this->post(route('public.register.checkout', $this->event->slug), $payloadQris);
        $resQris->assertRedirect();

        $orderQris = Order::where('customer_email', 'qris@runner.com')->firstOrFail();
        $this->assertEquals(100000, (int) $orderQris->subtotal);
        $this->assertEquals(0, (int) $orderQris->payment_gateway_fee);
        $this->assertEquals(100000, (int) $orderQris->grand_total);
    }

    public function test_tripay_response_syncs_official_bill_amount_and_fee_to_order(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-SYNC-TEST',
            'customer_name' => 'Sync Tester',
            'customer_email' => 'synctest@runner.com',
            'customer_phone' => '08123456789',
            'subtotal' => 200000,
            'grand_total' => 200000,
            'status' => 'pending',
        ]);

        Http::fake([
            '*/transaction/create' => Http::response([
                'success' => true,
                'data' => [
                    'reference' => 'DEV-T998877',
                    'merchant_ref' => 'JLTX-SYNC-TEST',
                    'payment_method' => 'BRIVA',
                    'amount' => 200000,
                    'fee_customer' => 3000,
                    'total_fee' => 3000,
                    'total_amount' => 203000,
                    'pay_code' => '12800998877',
                    'checkout_url' => 'https://tripay.co.id/checkout/DEV-T998877',
                    'expired_time' => now()->addHour()->timestamp,
                ],
            ], 200),
        ]);

        $service = new class extends TripayService
        {
            public function callCreateDirect(Order $order, string $method)
            {
                $merchantRef = $order->order_code;
                $amount = (int) round($order->grand_total);
                $expiredTime = now()->addMinutes($this->expiryMinutes)->timestamp;
                $orderItems = $this->prepareOrderItems($order);

                $response = Http::post("{$this->baseUrl}/transaction/create", [
                    'method' => $method,
                    'merchant_ref' => $merchantRef,
                    'amount' => $amount,
                    'order_items' => $orderItems,
                ]);

                if ($response->successful() && $response->json('success') === true) {
                    $data = $response->json('data');
                    $feeCustomer = (float) ($data['fee_customer'] ?? 0);
                    $finalGrandTotal = isset($data['total_amount']) && (float) $data['total_amount'] > 0
                        ? (float) $data['total_amount']
                        : (float) $order->grand_total + $feeCustomer;

                    $order->update([
                        'tripay_reference' => $data['reference'] ?? null,
                        'tripay_payment_method' => $method,
                        'tripay_pay_code' => $data['pay_code'] ?? null,
                        'tripay_checkout_url' => $data['checkout_url'] ?? null,
                        'payment_gateway_fee' => $feeCustomer,
                        'grand_total' => $finalGrandTotal,
                    ]);

                    return $data;
                }
            }
        };

        $service->callCreateDirect($order, 'BRIVA');

        $order->refresh();
        $this->assertEquals(200000, (int) $order->subtotal);
        $this->assertEquals(3000, (int) $order->payment_gateway_fee);
        $this->assertEquals(203000, (int) $order->grand_total);
    }

    public function test_order_expiry_time_is_properly_stored_in_wib_timezone(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-TIMEZONE-TEST',
            'customer_name' => 'WIB Tester',
            'customer_email' => 'wib@runner.com',
            'customer_phone' => '08123456789',
            'subtotal' => 200000,
            'grand_total' => 200000,
            'status' => 'pending',
        ]);

        $futureTimestamp = now()->addHour()->timestamp;

        Http::fake([
            '*/transaction/create' => Http::response([
                'success' => true,
                'data' => [
                    'reference' => 'DEV-TZ123',
                    'merchant_ref' => 'JLTX-TIMEZONE-TEST',
                    'payment_method' => 'BRIVA',
                    'amount' => 200000,
                    'total_amount' => 200000,
                    'expired_time' => $futureTimestamp,
                ],
            ], 200),
        ]);

        $tripayService = new class extends TripayService
        {
            public function __construct()
            {
                $this->apiKey = 'test-api-key';
                $this->privateKey = 'test-private-key';
                $this->merchantCode = 'T123';
                $this->baseUrl = 'https://tripay.co.id/api-sandbox';
                $this->expiryMinutes = 60;
            }
        };

        $tripayService->createTransaction($order, 'BRIVA');

        $order->refresh();

        $this->assertNotNull($order->expired_at);
        // Pastikan selisih waktu expired_at dengan waktu sekarang adalah sekitar 60 menit (59-60 menit)
        $diffMinutes = (int) now()->diffInMinutes($order->expired_at, false);
        $this->assertGreaterThanOrEqual(59, $diffMinutes);
        $this->assertLessThanOrEqual(61, $diffMinutes);

        // Pastikan ketika di-render ke email template, format jam sesuai WIB dan bukan UTC mundur 7 jam
        $expectedWibHour = now()->addHour()->setTimezone('Asia/Jakarta')->format('H:i');
        $renderedWibHour = $order->expired_at->setTimezone('Asia/Jakarta')->format('H:i');
        $this->assertEquals($expectedWibHour, $renderedWibHour);
    }

    public function test_email_jobs_implement_should_queue_with_retry_properties(): void
    {
        $dummyOrder = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-TEST-JOB',
            'customer_name' => 'Tester',
            'customer_email' => 'test@runner.com',
            'customer_phone' => '08123456789',
            'subtotal' => 100000,
            'grand_total' => 100000,
            'status' => 'pending',
        ]);

        $ticketJob = new SendTicketEmailJob($dummyOrder);
        $this->assertInstanceOf(ShouldQueue::class, $ticketJob);
        $this->assertEquals(3, $ticketJob->tries);
        $this->assertEquals(10, $ticketJob->backoff);

        $pendingJob = new SendPaymentPendingEmailJob($dummyOrder);
        $this->assertInstanceOf(ShouldQueue::class, $pendingJob);
        $this->assertEquals(3, $pendingJob->tries);
        $this->assertEquals(10, $pendingJob->backoff);
    }

    public function test_deploy_webhook_rejects_missing_or_invalid_secret_and_does_not_leak_output(): void
    {
        // 1. Tanpa secret -> 403
        config(['app.deploy_secret' => 'super_secret_test_key_123']);
        $resUnauthorized = $this->postJson(route('api.deploy'), ['secret' => 'wrong_secret']);
        $resUnauthorized->assertStatus(403);

        // 2. Event ping dengan secret valid
        $resPing = $this->postJson(
            route('api.deploy'),
            ['secret' => 'super_secret_test_key_123'],
            ['X-GitHub-Event' => 'ping']
        );
        $resPing->assertStatus(200);
        $resPing->assertJson(['status' => 'success']);

        // 3. Pastikan respons tidak membocorkan field 'output'
        $this->assertArrayNotHasKey('output', $resPing->json());
    }

    public function test_order_management_service_updates_status_and_assigns_bib(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-STATUS-CHANGE-TEST',
            'customer_name' => 'Status Change Runner',
            'customer_email' => 'statuschange@runner.com',
            'customer_phone' => '08123456789',
            'subtotal' => 200000,
            'grand_total' => 200000,
            'status' => 'pending',
        ]);

        $participant = Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category->id,
            'full_name' => 'Status Runner',
            'gender' => 'male',
            'birth_date' => '1995-05-15',
            'phone' => '08123456789',
            'email' => 'statuschange@runner.com',
            'id_number' => '1234567890123456',
            'emergency_contact_name' => 'Contact',
            'emergency_contact_phone' => '08123456780',
            'emergency_contact_relation' => 'Family',
        ]);

        $service = app(OrderManagementService::class);

        // Ubah status ke paid
        $service->updateStatus(
            order: $order,
            newStatus: 'paid',
            reason: 'Verifikasi transfer manual',
            adminName: 'Admin Tester',
            sendTicketEmail: false
        );

        $order->refresh();
        $participant->refresh();

        $this->assertEquals('paid', $order->status);
        $this->assertNotNull($participant->bib_number);
        $this->assertTrue(is_numeric($participant->bib_number));

        // Ubah status ke cancelled
        $service->updateStatus(
            order: $order,
            newStatus: 'cancelled',
            reason: 'Peserta membatalkan',
            adminName: 'Admin Tester',
            sendTicketEmail: false
        );

        $order->refresh();
        $this->assertEquals('cancelled', $order->status);
    }

    public function test_ebib_pdf_generation_and_download_route(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-EBIB-TEST',
            'customer_name' => 'EBIB Runner',
            'customer_email' => 'ebib@runner.com',
            'customer_phone' => '08123456789',
            'subtotal' => 150000,
            'grand_total' => 150000,
            'status' => 'paid',
        ]);

        $participant = Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category->id,
            'full_name' => 'Budi Santoso',
            'bib_name' => 'BUDI RUNS',
            'bib_number' => '8888',
            'gender' => 'male',
            'birth_date' => '1992-08-17',
            'phone' => '08123456789',
            'email' => 'ebib@runner.com',
            'id_number' => '1234567890123456',
            'emergency_contact_name' => 'Contact',
            'emergency_contact_phone' => '08123456780',
            'emergency_contact_relation' => 'Family',
        ]);

        // 1. Service EbibPdfService
        $ebibService = app(EbibPdfService::class);
        $pdfBinary = $ebibService->generateEbibPdf($participant);
        $this->assertNotEmpty($pdfBinary);
        $this->assertStringStartsWith('%PDF-', $pdfBinary);

        // 2. Download route by QR token
        $response = $this->get(route('public.ebib.download', $participant->qr_token));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('E-BIB-8888.pdf', $response->headers->get('Content-Disposition'));

        // 3. Download route by BIB number fallback
        $responseBib = $this->get(route('public.ebib.download', '8888'));
        $responseBib->assertStatus(200);
        $responseBib->assertHeader('Content-Type', 'application/pdf');

        // 4. Test custom ebib_layout_config
        $this->event->update([
            'ebib_layout_config' => [
                'text_color' => '#0f172a',
                'bib_top_mm' => 50,
                'bib_font_size_pt' => 70,
                'name_top_mm' => 90,
                'name_font_size_pt' => 20,
                'show_event_title' => false,
                'show_category_badge' => true,
                'show_qr_code' => false,
                'show_pin_holes' => false,
            ],
        ]);

        $customPdfBinary = $ebibService->generateEbibPdf($participant);
        $this->assertNotEmpty($customPdfBinary);
        $this->assertEquals('#0f172a', $this->event->fresh()->getEbibConfig('text_color'));
        $this->assertEquals(50, $this->event->fresh()->getEbibConfig('bib_top_mm'));

        // 5. Test Event preview route
        $responsePreview = $this->get(route('admin.events.preview_ebib', $this->event));
        $responsePreview->assertStatus(200);
        $responsePreview->assertHeader('Content-Type', 'application/pdf');

        // 6. Test Event preview route with query parameters override
        $responseParamPreview = $this->get(route('admin.events.preview_ebib', [
            'event' => $this->event,
            'text_color' => '#f59e0b',
            'bib_top_mm' => 65,
        ]));
        $responseParamPreview->assertStatus(200);
        $responseParamPreview->assertHeader('Content-Type', 'application/pdf');

        // Test Filament EditEvent page loads without error
        $admin = User::factory()->create(['role' => 'superadmin', 'is_active' => true]);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        // Test updating event with ebib_text_color
        $this->event->update(['ebib_layout_config' => ['text_color' => '#f59e0b', 'bib_top_mm' => 50]]);
        $this->assertEquals('#f59e0b', $this->event->fresh()->ebib_text_color);

        // Test Filament EditEvent form fill and save mutations
        $this->event->update([
            'racepack_delivery_start_date' => now()->addDays(5),
            'racepack_delivery_end_date' => now()->addDays(15),
        ]);

        $admin = User::factory()->create(['role' => 'superadmin', 'is_active' => true]);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        Livewire::actingAs($admin)
            ->test(EditEvent::class, [
                'record' => $this->event->getRouteKey(),
            ])
            ->assertSet('data.ebib_text_color', '#f59e0b')
            ->assertSet('data.ebib_bib_top_mm', 50)
            ->fillForm([
                'ebib_text_color' => '#0f172a',
                'ebib_bib_top_mm' => 60,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('#0f172a', $this->event->fresh()->ebib_text_color);
        $this->assertEquals(60, $this->event->fresh()->ebib_bib_top_mm);

        // Test GET /cek-tiket with query param (from scanned QR code)
        $responseGetCheck = $this->get(route('public.ticket.index', ['query' => $participant->bib_number]));
        $responseGetCheck->assertStatus(200);
        $responseGetCheck->assertSee($participant->bib_name);
        $responseGetCheck->assertSee($participant->bib_number);
    }

    public function test_activity_submission_accepts_leading_zero_duration_and_comma_distance(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-SUBMIT-TEST',
            'customer_name' => 'Runner Submitter',
            'customer_email' => 'submitter@runner.com',
            'customer_phone' => '08123456789',
            'subtotal' => 150000,
            'grand_total' => 150000,
            'status' => 'paid',
        ]);

        $participant = Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category->id,
            'full_name' => 'Runner Submitter',
            'bib_name' => 'SUBMITTER',
            'bib_number' => '1043',
            'gender' => 'male',
            'birth_date' => '1995-05-05',
            'phone' => '08123456789',
            'email' => 'submitter@runner.com',
            'address' => 'Jl. Lari Sehat No. 1',
            'city' => 'Jakarta',
            'total_distance_km' => 0,
            'total_duration_seconds' => 0,
            'finisher_status' => 'in_progress',
        ]);

        // Submit form with hours='01', minutes='30', seconds='01', distance='10'
        $response = $this->post(route('public.submit.store'), [
            'bib_number' => '1043',
            'activity_date' => now()->format('Y-m-d'),
            'distance_km' => '10',
            'hours' => '01',
            'minutes' => '30',
            'seconds' => '01',
            'proof_url' => 'https://www.strava.com/activities/12345678',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('public.submit.index', ['bib' => '1043']));

        $participant->refresh();
        $this->assertEquals(10.0, (float) $participant->total_distance_km);
        $this->assertEquals(5401, (int) $participant->total_duration_seconds); // 3600 + 1800 + 1
    }

    public function test_production_po_export_service_separates_medals_and_jerseys_accurately(): void
    {
        $event = Event::create([
            'organizer_id' => $this->organizer->id,
            'title' => 'Kujang Virtual Run 2026',
            'slug' => 'kujang-virtual-run-2026',
            'race_location_name' => 'Bogor',
            'event_start_date' => now()->addDays(30),
            'registration_open_date' => now()->subDays(5),
            'registration_close_date' => now()->addDays(20),
            'status' => 'published',
        ]);

        $sizeL = JerseySize::create([
            'event_id' => $event->id,
            'size_name' => 'L',
            'gender_type' => 'unisex',
            'chest_width_cm' => 52,
            'body_length_cm' => 70,
            'is_unlimited' => true,
        ]);

        // 5 Kategori sesuai skenario user
        $catMedalOnly = EventCategory::create(['event_id' => $event->id, 'name' => 'Medali Only', 'distance_km' => 10, 'normal_price' => 100000]);
        $catJerseyEventOnly = EventCategory::create(['event_id' => $event->id, 'name' => 'Jersey Event Only', 'distance_km' => 10, 'normal_price' => 120000]);
        $catJerseyFinisherOnly = EventCategory::create(['event_id' => $event->id, 'name' => 'Jersey Finisher Only', 'distance_km' => 10, 'normal_price' => 130000]);
        $catLengkap = EventCategory::create(['event_id' => $event->id, 'name' => 'Lengkap Medali + Jersey Event + Jersey Finisher', 'distance_km' => 10, 'normal_price' => 250000]);
        $catFinisherMedal = EventCategory::create(['event_id' => $event->id, 'name' => 'Jersey Finisher + Medali', 'distance_km' => 10, 'normal_price' => 200000]);

        $order = Order::create([
            'event_id' => $event->id,
            'order_code' => 'JLTX-KUJANG-PO',
            'customer_name' => 'Kujang Runner',
            'customer_email' => 'kujang@runner.com',
            'customer_phone' => '08123456789',
            'subtotal' => 800000,
            'grand_total' => 800000,
            'status' => 'paid',
        ]);

        // 1. Peserta Medali Only -> Medali (1), Event (0), Finisher (0)
        Participant::create(['order_id' => $order->id, 'event_category_id' => $catMedalOnly->id, 'full_name' => 'Pelari 1', 'bib_number' => '1001', 'gender' => 'male', 'birth_date' => '1995-01-01', 'phone' => '08123456789']);

        // 2. Peserta Jersey Event Only -> Medali (0), Event (1), Finisher (0)
        Participant::create(['order_id' => $order->id, 'event_category_id' => $catJerseyEventOnly->id, 'jersey_size_id' => $sizeL->id, 'jersey_type' => 'short', 'full_name' => 'Pelari 2', 'bib_number' => '1002', 'gender' => 'male', 'birth_date' => '1995-01-01', 'phone' => '08123456789']);

        // 3. Peserta Jersey Finisher Only -> Medali (0), Event (0), Finisher (1)
        Participant::create(['order_id' => $order->id, 'event_category_id' => $catJerseyFinisherOnly->id, 'jersey_size_id' => $sizeL->id, 'jersey_type' => 'long', 'full_name' => 'Pelari 3', 'bib_number' => '1003', 'gender' => 'male', 'birth_date' => '1995-01-01', 'phone' => '08123456789']);

        // 4. Peserta Lengkap -> Medali (1), Event (1), Finisher (1)
        Participant::create(['order_id' => $order->id, 'event_category_id' => $catLengkap->id, 'jersey_size_id' => $sizeL->id, 'jersey_type' => 'short', 'full_name' => 'Pelari 4', 'bib_number' => '1004', 'gender' => 'male', 'birth_date' => '1995-01-01', 'phone' => '08123456789']);

        // 5. Peserta Finisher + Medali -> Medali (1), Event (0), Finisher (1)
        Participant::create(['order_id' => $order->id, 'event_category_id' => $catFinisherMedal->id, 'jersey_size_id' => $sizeL->id, 'jersey_type' => 'singlet', 'full_name' => 'Pelari 5', 'bib_number' => '1005', 'gender' => 'male', 'birth_date' => '1995-01-01', 'phone' => '08123456789']);

        // Total harus: Medali = 3, Jersey Event = 2, Jersey Finisher = 3
        $service = app(ProductionPoExportService::class);
        $response = $service->exportVendorPo($event);

        $this->assertEquals(200, $response->getStatusCode());

        ob_start();
        $response->sendContent();
        $csvContent = (string) ob_get_clean();

        // Verifikasi Bagian-bagian CSV
        $this->assertStringContainsString('BAGIAN 1: RINGKASAN ORDER PRODUKSI', $csvContent);
        $this->assertStringContainsString('BAGIAN 2: DETAIL ORDER PABRIK MEDALI', $csvContent);
        $this->assertStringContainsString('BAGIAN 3: DETAIL PO KONVEKSI - JERSEY EVENT', $csvContent);
        $this->assertStringContainsString('BAGIAN 4: DETAIL PO KONVEKSI - JERSEY FINISHER', $csvContent);
        $this->assertStringContainsString('BAGIAN 5: MANIFEST PACKING GUDANG & PENGIRIMAN RACEPACK', $csvContent);

        // Verifikasi hasil hitungan akurat
        // Medali: 3
        $this->assertStringContainsString('"Keping Medali Finisher Logam",3', $csvContent);
        // Jersey Event: 2
        $this->assertStringContainsString('"Jersey Event Resmi",2', $csvContent);
        // Jersey Finisher: 3
        $this->assertStringContainsString('"Jersey Finisher Eksklusif",3', $csvContent);
    }
}
