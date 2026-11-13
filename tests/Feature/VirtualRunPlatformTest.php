<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\JerseySize;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\SpxShippingRate;
use App\Services\BibGeneratorService;
use App\Services\SpxShippingService;
use Database\Seeders\SpxRateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VirtualRunPlatformTest extends TestCase
{
    use RefreshDatabase;

    protected Event $event;

    protected EventCategory $category5k;

    protected EventCategory $category10k;

    protected JerseySize $jerseyM;

    protected function setUp(): void
    {
        parent::setUp();

        $organizer = Organizer::create([
            'name' => 'Virtual Run Indonesia',
            'slug' => 'virtual-run-id',
            'email' => 'admin@virtualrun.id',
            'phone' => '081299998888',
            'commission_rate' => 0.00,
        ]);

        $this->event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Nusantara Virtual Challenge 2026',
            'slug' => 'nusantara-virtual-2026',
            'event_code' => 'NVC26',
            'activity_type' => 'RUN',
            'submission_mode' => 'CUMULATIVE',
            'race_location_name' => 'Virtual / Di Mana Saja',
            'registration_open_date' => now()->subDay(),
            'registration_close_date' => now()->addDays(30),
            'event_start_date' => now()->subDay(),
            'event_end_date' => now()->addDays(60),
            'race_start_date' => now()->subDay(),
            'race_end_date' => now()->addDays(60),
            'auto_assign_bib' => true,
            'status' => 'published',
        ]);

        $this->category5k = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => '5K Fun Virtual',
            'distance_km' => 5.00,
            'price' => 150000,
            'normal_price' => 150000,
            'base_weight_grams' => 500,
            'quota' => 500,
            'slots_taken' => 0,
            'bib_prefix' => null,
            'bib_start_number' => 1001,
            'is_active' => true,
        ]);

        $this->category10k = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => '10K Challenge',
            'distance_km' => 10.00,
            'price' => 200000,
            'normal_price' => 200000,
            'base_weight_grams' => 500,
            'quota' => 500,
            'slots_taken' => 0,
            'bib_prefix' => null,
            'bib_start_number' => 1001,
            'is_active' => true,
        ]);

        $this->jerseyM = JerseySize::create([
            'event_id' => $this->event->id,
            'size_name' => 'M',
            'gender_type' => 'unisex',
            'stock' => 100,
            'allocated_stock' => 0,
        ]);

        // Seed sample SPX rate
        SpxShippingRate::create([
            'origin_city' => 'KAB. TANGERANG',
            'destination_city' => 'KAB. BADUNG',
            'destination_district' => 'KUTA',
            'hemat_rate_per_kg' => 19300,
            'hemat_sla_days' => 10,
            'regular_rate_per_kg' => 30000,
            'regular_sla_days' => 6,
            'search_keyword' => 'kuta kab. badung',
        ]);
    }

    public function test_spx_shipping_service_calculates_correctly(): void
    {
        $service = app(SpxShippingService::class);
        $result = $service->calculateShipping('KAB. BADUNG', 'KUTA', 1000);

        $this->assertEquals('SPX', $result['courier']);
        $this->assertEquals('SPX Regular', $result['service']);
        $this->assertEquals(30000, $result['shipping_cost']);
        $this->assertEquals(6, $result['sla_days']);
    }

    public function test_api_spx_search_and_calculate_endpoints(): void
    {
        $searchRes = $this->getJson('/api/shipping/spx/search?q=kuta');
        $searchRes->assertStatus(200);
        $searchRes->assertJsonFragment(['destination_district' => 'KUTA']);

        $calcRes = $this->postJson('/api/shipping/spx/calculate', [
            'destination_city' => 'KAB. BADUNG',
            'destination_district' => 'KUTA',
            'total_weight_grams' => 1500,
        ]);

        $calcRes->assertStatus(200);
        // 1500 gr dibulatkan jadi 2 kg * 30.000 = 60.000
        $calcRes->assertJsonPath('data.shipping_cost', 60000);
    }

    public function test_spx_rate_seeder_imports_all_districts_from_gzipped_data(): void
    {
        $seeder = new SpxRateSeeder;
        $seeder->run();

        $this->assertGreaterThanOrEqual(7000, SpxShippingRate::count());

        $response = $this->getJson('/api/shipping/spx/search?q=gambir');
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $this->assertNotEmpty($response->json('data'));
    }

    public function test_checkout_with_multi_runners_and_spx_shipping(): void
    {
        $payload = [
            'buyer_name' => 'Fajar Pratama',
            'buyer_email' => 'fajar@runner.id',
            'buyer_phone' => '081234567890',
            'payment_method' => 'QRIS',
            'waiver_accepted' => '1',
            'recipient_name' => 'Fajar Pratama',
            'recipient_phone' => '081234567890',
            'destination_city' => 'KAB. BADUNG',
            'destination_district' => 'KUTA',
            'postal_code' => '80361',
            'address_detail' => 'Jl. Pantai Kuta No. 88',
            'participants' => [
                [
                    'category_id' => $this->category5k->id,
                    'jersey_size_id' => $this->jerseyM->id,
                    'full_name' => 'Fajar Pratama',
                    'bib_name' => 'FAJAR P',
                    'id_type' => 'KTP',
                    'id_number' => '3271012345670001',
                    'gender' => 'male',
                    'birth_date' => '1995-05-15',
                    'phone' => '081234567890',
                    'email' => 'fajar@runner.id',
                    'emergency_contact_name' => 'Ratna',
                    'emergency_contact_phone' => '081234567899',
                    'emergency_contact_relation' => 'Istri',
                ],
                [
                    'category_id' => $this->category10k->id,
                    'jersey_size_id' => $this->jerseyM->id,
                    'full_name' => 'Budi Santoso',
                    'bib_name' => 'BUDI S',
                    'id_type' => 'KTP',
                    'id_number' => '3271012345670002',
                    'gender' => 'male',
                    'birth_date' => '1993-08-20',
                    'phone' => '081234567891',
                    'email' => 'budi@runner.id',
                    'emergency_contact_name' => 'Siti',
                    'emergency_contact_phone' => '081234567898',
                    'emergency_contact_relation' => 'Ibu',
                ],
            ],
        ];

        $res = $this->post("/events/{$this->event->slug}/checkout", $payload);

        $order = Order::where('customer_email', 'fajar@runner.id')->first();
        $this->assertNotNull($order);
        // Subtotal: 150.000 + 200.000 = 350.000
        // Weight: 2 * 500 = 1000 gr = 1 kg -> Ongkir: 30.000
        // Gateway Fee: 3.410 (QRIS: 750 + 0.7% dari 380.000) -> Grand Total: 383.410
        $this->assertEquals(350000, (int) $order->subtotal);
        $this->assertEquals(30000, (int) $order->shipping_cost);
        $this->assertEquals(3410, (int) $order->payment_gateway_fee);
        $this->assertEquals(383410, (int) $order->grand_total);

        // Verifikasi tabel order_shipping_addresses
        $this->assertNotNull($order->shippingAddress);
        $this->assertEquals('KUTA', $order->shippingAddress->destination_district);
        $this->assertEquals('SPX', $order->shippingAddress->courier_name);
        $this->assertEquals(30000, (int) $order->shippingAddress->shipping_cost);

        // Simulasi pelunasan pembayaran
        $simulateRes = $this->post("/orders/{$order->order_code}/simulate");
        $simulateRes->assertRedirect("/orders/{$order->order_code}");

        $order->refresh();
        $this->assertTrue($order->isPaid());

        // Verifikasi E-BIB diterbitkan otomatis sederhana mulai dari 1001 secara berurutan
        $participants = $order->participants()->orderBy('id')->get();
        $this->assertCount(2, $participants);
        $this->assertEquals('1001', $participants[0]->bib_number);
        $this->assertEquals('1002', $participants[1]->bib_number);
    }

    public function test_bib_sequential_numbering_starts_at_1001_globally(): void
    {
        $bibService = app(BibGeneratorService::class);

        $participant1 = Participant::create([
            'event_category_id' => $this->category5k->id,
            'full_name' => 'Runner Alpha',
            'id_type' => 'KTP',
            'id_number' => '1111111111111111',
            'gender' => 'male',
            'birth_date' => '1990-01-01',
            'phone' => '08111111111',
            'email' => 'alpha@run.id',
            'emergency_contact_name' => 'Parent',
            'emergency_contact_phone' => '08111111112',
            'emergency_contact_relation' => 'Parent',
            'qr_token' => 'token-alpha-123',
        ]);

        $bib1 = $bibService->autoAssignBib($participant1);
        $this->assertEquals('1001', $bib1);

        $participant2 = Participant::create([
            'event_category_id' => $this->category10k->id,
            'full_name' => 'Runner Beta',
            'id_type' => 'KTP',
            'id_number' => '2222222222222222',
            'gender' => 'female',
            'birth_date' => '1992-02-02',
            'phone' => '08222222222',
            'email' => 'beta@run.id',
            'emergency_contact_name' => 'Sibling',
            'emergency_contact_phone' => '08222222223',
            'emergency_contact_relation' => 'Sibling',
            'qr_token' => 'token-beta-123',
        ]);

        $bib2 = $bibService->autoAssignBib($participant2);
        $this->assertEquals('1002', $bib2);
    }

    public function test_universal_submission_portal_and_motivational_popup(): void
    {
        // Buat peserta terdaftar dengan BIB 1001
        $participant = Participant::create([
            'event_category_id' => $this->category5k->id,
            'full_name' => 'Dewi Anggraini',
            'bib_number' => '1001',
            'id_type' => 'KTP',
            'id_number' => '3333333333333333',
            'gender' => 'female',
            'birth_date' => '1996-06-06',
            'phone' => '08333333333',
            'email' => 'dewi@run.id',
            'emergency_contact_name' => 'Friend',
            'emergency_contact_phone' => '08333333334',
            'emergency_contact_relation' => 'Friend',
            'qr_token' => 'token-dewi-123',
        ]);

        // 1. Akses portal /submit zero-login dengan nomor E-BIB
        $viewRes = $this->get('/submit?bib=1001');
        $viewRes->assertStatus(200);
        $viewRes->assertSee('Dewi Anggraini');
        $viewRes->assertSee('5K Fun Virtual');

        // 2. Submit catatan lari 3.0 KM (60% target)
        $submit1 = $this->post('/submit', [
            'bib_number' => '1001',
            'activity_date' => date('Y-m-d'),
            'distance_km' => 3.00,
            'hours' => 0,
            'minutes' => 18,
            'seconds' => 30,
            'proof_url' => 'https://www.strava.com/activities/1234567890',
            'notes' => 'Lari sore santai',
        ]);

        $submit1->assertRedirect('/submit?bib=1001');
        $submit1->assertSessionHas('motivational_modal');

        $participant->refresh();
        $this->assertEquals(3.00, (float) $participant->total_distance_km);
        $this->assertEquals(60.0, $participant->progressPercentage());
        $this->assertEquals('in_progress', $participant->finisher_status);

        // 3. Submit catatan lari 2.5 KM lagi (akumulasi 5.5 KM >= 5.0 KM -> Finisher!)
        $submit2 = $this->post('/submit', [
            'bib_number' => '1001',
            'activity_date' => date('Y-m-d'),
            'distance_km' => 2.50,
            'hours' => 0,
            'minutes' => 15,
            'seconds' => 0,
            'proof_url' => 'https://drive.google.com/file/d/abcdef123/view',
        ]);

        $submit2->assertRedirect('/submit?bib=1001');
        $participant->refresh();
        $this->assertTrue($participant->isFinisher());
        $this->assertEquals('finisher', $participant->finisher_status);
        $this->assertNotNull($participant->finished_at);

        // 4. Unduh sertifikat finisher
        $certRes = $this->get("/certificate/{$participant->bib_number}/download");
        $certRes->assertStatus(200);
        $certRes->assertHeader('content-type', 'application/pdf');
    }

    public function test_submission_with_screenshot_image_file_upload(): void
    {
        Storage::fake('public');

        $participant = Participant::create([
            'event_category_id' => $this->category5k->id,
            'full_name' => 'Budi Santoso',
            'bib_number' => '1002',
            'id_type' => 'KTP',
            'id_number' => '3201010101010001',
            'gender' => 'male',
            'birth_date' => '1995-05-05',
            'phone' => '081234567890',
            'email' => 'budi@santoso.id',
            'emergency_contact_name' => 'Istri',
            'emergency_contact_phone' => '081234567891',
            'emergency_contact_relation' => 'Istri',
            'qr_token' => 'token-budi-123',
        ]);

        $file = UploadedFile::fake()->image('strava_screenshot.jpg', 800, 600);

        $response = $this->post('/submit', [
            'bib_number' => '1002',
            'activity_date' => date('Y-m-d'),
            'distance_km' => '4.20',
            'hours' => 0,
            'minutes' => 25,
            'seconds' => 0,
            'proof_image' => $file,
            'notes' => 'Lari sore di GBK',
        ]);

        $response->assertRedirect('/submit?bib=1002');
        $response->assertSessionHas('success');
        $response->assertSessionHas('motivational_modal');

        $this->assertDatabaseHas('activity_submissions', [
            'participant_id' => $participant->id,
            'distance_km' => 4.20,
            'validation_status' => 'approved',
        ]);

        $participant->refresh();
        $this->assertEquals(4.20, (float) $participant->total_distance_km);
    }

    public function test_submission_rejects_file_exceeding_1mb(): void
    {
        Storage::fake('public');

        $participant = Participant::create([
            'event_category_id' => $this->category5k->id,
            'full_name' => 'Budi Santoso',
            'bib_number' => '1002',
            'id_type' => 'KTP',
            'id_number' => '3201010101010001',
            'gender' => 'male',
            'birth_date' => '1995-05-05',
            'phone' => '081234567890',
            'email' => 'budi@santoso.id',
            'emergency_contact_name' => 'Istri',
            'emergency_contact_phone' => '081234567891',
            'emergency_contact_relation' => 'Istri',
            'qr_token' => 'token-budi-123',
        ]);

        // Buat file gambar palsu berukuran 1500 KB (> 1024 KB / 1 MB)
        $oversizedFile = UploadedFile::fake()->image('large_strava.jpg')->size(1500);

        $response = $this->post('/submit', [
            'bib_number' => '1002',
            'activity_date' => date('Y-m-d'),
            'distance_km' => '4.20',
            'hours' => 0,
            'minutes' => 25,
            'seconds' => 0,
            'proof_image' => $oversizedFile,
        ]);

        $response->assertSessionHasErrors([
            'proof_image' => 'Ukuran file foto bukti maksimal 1 MB.',
        ]);

        $this->assertDatabaseMissing('activity_submissions', [
            'participant_id' => $participant->id,
        ]);
    }

    public function test_submission_with_comma_decimal_and_url_without_https(): void
    {
        $participant = Participant::create([
            'event_category_id' => $this->category5k->id,
            'full_name' => 'Citra Lestari',
            'bib_number' => '1003',
            'id_type' => 'KTP',
            'id_number' => '3201010101010002',
            'gender' => 'female',
            'birth_date' => '1998-08-08',
            'phone' => '081234567892',
            'email' => 'citra@lestari.id',
            'emergency_contact_name' => 'Kakak',
            'emergency_contact_phone' => '081234567893',
            'emergency_contact_relation' => 'Kakak',
            'qr_token' => 'token-citra-123',
        ]);

        // Input desimal dengan koma '5,25' dan URL tanpa 'https://'
        $response = $this->post('/submit', [
            'bib_number' => '1003',
            'activity_date' => date('Y-m-d'),
            'distance_km' => '5,25',
            'hours' => 0,
            'minutes' => 30,
            'seconds' => 0,
            'proof_url' => 'strava.app.link/citra-run-5k',
        ]);

        $response->assertRedirect('/submit?bib=1003');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('activity_submissions', [
            'participant_id' => $participant->id,
            'distance_km' => 5.25,
            'proof_url' => 'https://strava.app.link/citra-run-5k',
        ]);

        $participant->refresh();
        $this->assertTrue($participant->isFinisher());
    }

    public function test_public_leaderboard_displays_rankings(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'ORD-LB-TEST',
            'customer_name' => 'Fast Runner',
            'customer_email' => 'fast@runner.id',
            'customer_phone' => '081234567899',
            'subtotal' => 150000,
            'grand_total' => 155000,
            'status' => 'paid',
        ]);

        $runner = Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category5k->id,
            'full_name' => 'Gita Juara',
            'bib_number' => '1005',
            'id_type' => 'KTP',
            'id_number' => '4444444444444444',
            'gender' => 'female',
            'birth_date' => '1998-08-08',
            'phone' => '08444444444',
            'email' => 'gita@run.id',
            'emergency_contact_name' => 'Coach',
            'emergency_contact_phone' => '08444444445',
            'emergency_contact_relation' => 'Coach',
            'qr_token' => 'token-gita-123',
            'total_distance_km' => 5.20,
            'total_duration_seconds' => 1500,
            'finisher_status' => 'finisher',
            'finished_at' => now(),
        ]);

        $res = $this->get("/events/{$this->event->slug}/leaderboard");
        $res->assertStatus(200);
        $res->assertSee('Gita Juara');
        $res->assertSee('E-BIB #1005');
        $res->assertSee('FINISHER');
    }

    public function test_public_ticket_selection_page_displays_correctly(): void
    {
        $res = $this->get("/events/{$this->event->slug}/register");
        $res->assertStatus(200);
        $res->assertSee($this->event->title);
        $res->assertSee('Pilih Tiket');
        $res->assertSee('5K Fun Virtual');
        $res->assertSee('10K Challenge');
        $res->assertSee('Isi Data Peserta');

        // Test alias /event/{slug}
        $aliasRes = $this->get("/event/{$this->event->slug}");
        $aliasRes->assertStatus(200);
    }

    public function test_submitting_zero_tickets_redirects_back_with_error(): void
    {
        $res = $this->post("/events/{$this->event->slug}/participants", [
            'tickets' => [
                $this->category5k->id => 0,
                $this->category10k->id => 0,
            ],
        ]);

        $res->assertRedirect(route('public.register.show', $this->event->slug));
        $res->assertSessionHasErrors(['tickets']);
    }

    public function test_submitting_valid_tickets_shows_participants_form_with_exact_slots(): void
    {
        $res = $this->post("/events/{$this->event->slug}/participants", [
            'tickets' => [
                $this->category5k->id => 2,
                $this->category10k->id => 1,
            ],
        ]);

        $res->assertStatus(200);
        $res->assertSee('Pelari 1 dari 3');
        $res->assertSee('Pelari 2 dari 3');
        $res->assertSee('Pelari 3 dari 3');
        $res->assertSee('Lanjut ke Pembayaran Tripay');
    }

    public function test_checkout_with_simplified_virtual_run_participants_data(): void
    {
        $payload = [
            'buyer_name' => 'Dimas Anggara',
            'buyer_email' => 'dimas@runner.id',
            'buyer_phone' => '081233334444',
            'payment_method' => 'BCAVA',
            'waiver_accepted' => '1',
            'recipient_name' => 'Dimas Anggara',
            'recipient_phone' => '081233334444',
            'destination_city' => 'KAB. BADUNG',
            'destination_district' => 'KUTA',
            'address_detail' => 'Sunset Road No. 12',
            'participants' => [
                [
                    'category_id' => $this->category5k->id,
                    'jersey_size_id' => $this->jerseyM->id,
                    'bib_name' => 'DIMAS A',
                    'gender' => 'male',
                ],
            ],
        ];

        $res = $this->post("/events/{$this->event->slug}/checkout", $payload);
        $order = Order::where('customer_email', 'dimas@runner.id')->first();
        $this->assertNotNull($order);
        $this->assertEquals(1, $order->participants()->count());

        $participant = $order->participants->first();
        $this->assertEquals('DIMAS A', $participant->bib_name);
        $this->assertEquals('DIMAS A', $participant->full_name);
        $this->assertEquals('male', $participant->gender);
        $this->assertEquals($this->jerseyM->id, $participant->jersey_size_id);
    }
}
