<?php

namespace Tests\Feature;

use App\Jobs\SendMetaCapiPurchaseJob;
use App\Models\Event;
use App\Models\EventAddon;
use App\Models\EventCategory;
use App\Models\JerseySize;
use App\Models\Order;
use App\Models\OrderAddon;
use App\Models\OrderShippingAddress;
use App\Models\Organizer;
use App\Models\Participant;
use App\Services\MetaCapiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MetaCapiTest extends TestCase
{
    use RefreshDatabase;

    protected Event $event;

    protected EventCategory $category10k;

    protected JerseySize $jerseyM;

    protected EventAddon $addonBottle;

    protected function setUp(): void
    {
        parent::setUp();

        $organizer = Organizer::create([
            'name' => 'Virun Organizer CAPI',
            'slug' => 'virun-capi-org',
            'email' => 'admin@viruncapi.org',
            'phone' => '081299990000',
        ]);

        $this->event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Bali Sunset Virtual Run 2026',
            'slug' => 'bali-sunset-vr-2026',
            'event_code' => 'BSVR26',
            'activity_type' => 'RUN',
            'submission_mode' => 'SINGLE',
            'race_location_name' => 'Pantai Kuta, Bali',
            'registration_open_date' => now()->subDays(5),
            'registration_close_date' => now()->addDays(20),
            'event_start_date' => now()->addDays(25),
            'event_end_date' => now()->addDays(30),
            'race_start_date' => now()->addDays(25),
            'race_end_date' => now()->addDays(30),
            'status' => 'published',
            'meta_pixel_id' => '128495039201948',
            'meta_capi_access_token' => 'EAAG_TEST_ACCESS_TOKEN_SAMPLE_123',
            'meta_capi_test_event_code' => 'TEST99881',
            'is_meta_capi_enabled' => true,
        ]);

        $this->category10k = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => '10K Sunset Run',
            'distance_km' => 10.00,
            'normal_price' => 175000,
            'quota' => 500,
            'slots_taken' => 0,
            'bib_start_number' => 1001,
            'is_active' => true,
        ]);

        $this->jerseyM = JerseySize::create([
            'event_id' => $this->event->id,
            'size_name' => 'M',
            'gender_type' => 'unisex',
            'chest_width_cm' => 50,
            'body_length_cm' => 70,
            'is_unlimited' => true,
        ]);

        $this->addonBottle = EventAddon::create([
            'event_id' => $this->event->id,
            'name' => 'Botol Minum Lipat 500ml',
            'price' => 75000,
            'weight_grams' => 100,
            'is_unlimited' => true,
            'is_active' => true,
        ]);
    }

    public function test_event_has_meta_capi_helper_checks_credentials(): void
    {
        $this->assertTrue($this->event->hasMetaCapi());

        // Jika dinonaktifkan
        $this->event->update(['is_meta_capi_enabled' => false]);
        $this->assertFalse($this->event->fresh()->hasMetaCapi());

        // Jika aktif tapi pixel ID kosong
        $this->event->update(['is_meta_capi_enabled' => true, 'meta_pixel_id' => null]);
        $this->assertFalse($this->event->fresh()->hasMetaCapi());
    }

    public function test_meta_capi_purchase_does_not_fire_when_order_is_pending(): void
    {
        Http::fake();

        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'VR-PENDING-001',
            'customer_name' => 'Belum Bayar',
            'customer_email' => 'pending@test.com',
            'customer_phone' => '081234567890',
            'subtotal' => 175000,
            'grand_total' => 175000,
            'status' => 'pending', // PENDING!
        ]);

        $service = app(MetaCapiService::class);
        $result = $service->sendPurchaseEvent($order);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString("Order belum berstatus 'paid'", $result['message']);
        $this->assertNull($order->fresh()->meta_capi_purchase_sent_at);

        Http::assertNothingSent();
    }

    public function test_meta_capi_purchase_fires_successfully_when_order_is_paid(): void
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'events_received' => 1,
                'messages' => [],
                'fbtrace_id' => 'FB_TRACE_TEST_123',
            ], 200),
        ]);

        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'VR-PAID-001',
            'customer_name' => 'Rangga Sasana',
            'customer_email' => 'rangga@sundaimpire.com',
            'customer_phone' => '081298765432',
            'subtotal' => 250000,
            'addons_total' => 75000,
            'shipping_cost' => 15000,
            'grand_total' => 340000,
            'status' => 'paid',
            'paid_at' => now(),
            'ip_address' => '114.124.200.15',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
            'fbp' => 'fb.1.168923456.12345',
            'fbc' => 'fb.1.168923456.AbCdEfGh123',
        ]);

        OrderShippingAddress::create([
            'order_id' => $order->id,
            'recipient_name' => 'Rangga Sasana',
            'recipient_phone' => '081298765432',
            'province' => 'Jawa Barat',
            'destination_city' => 'Bandung',
            'destination_district' => 'Coblong',
            'postal_code' => '40132',
            'address_detail' => 'Jl. Dago Asri No. 5',
        ]);

        Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category10k->id,
            'jersey_size_id' => $this->jerseyM->id,
            'bib_number' => '1001',
            'bib_name' => 'RANGGA S',
            'full_name' => 'Rangga Sasana',
            'gender' => 'male',
            'email' => 'rangga@sundaimpire.com',
            'phone' => '081298765432',
            'qr_token' => 'QR-RANGGA-1',
        ]);

        OrderAddon::create([
            'order_id' => $order->id,
            'event_addon_id' => $this->addonBottle->id,
            'addon_name' => 'Botol Minum Lipat 500ml',
            'quantity' => 1,
            'unit_price' => 75000,
            'total_price' => 75000,
        ]);

        $service = app(MetaCapiService::class);
        $result = $service->sendPurchaseEvent($order);

        $this->assertTrue($result['success']);
        $this->assertNotNull($order->fresh()->meta_capi_purchase_sent_at);

        Http::assertSent(function ($request) use ($order) {
            $url = $request->url();
            $data = $request->data();

            // Cek URL endpoint Graph API & pixel ID
            $this->assertStringContainsString('128495039201948', $url);
            $this->assertStringContainsString('EAAG_TEST_ACCESS_TOKEN_SAMPLE_123', $url);

            // Cek payload
            $this->assertEquals('TEST99881', $data['test_event_code'] ?? null);
            $eventItem = $data['data'][0];

            $this->assertEquals('Purchase', $eventItem['event_name']);
            $this->assertEquals($order->order_code, $eventItem['event_id']);
            $this->assertEquals('website', $eventItem['action_source']);

            // User data ter-hash
            $userData = $eventItem['user_data'];
            $this->assertEquals([hash('sha256', 'rangga@sundaimpire.com')], $userData['em']);
            $this->assertEquals([hash('sha256', '6281298765432')], $userData['ph']);
            $this->assertEquals([hash('sha256', 'rangga')], $userData['fn']);
            $this->assertEquals([hash('sha256', 'sasana')], $userData['ln']);
            $this->assertEquals([hash('sha256', 'bandung')], $userData['ct']);
            $this->assertEquals('114.124.200.15', $userData['client_ip_address']);
            $this->assertEquals('fb.1.168923456.12345', $userData['fbp']);
            $this->assertEquals('fb.1.168923456.AbCdEfGh123', $userData['fbc']);

            // Custom data belanja
            $customData = $eventItem['custom_data'];
            $this->assertEquals(340000, $customData['value']);
            $this->assertEquals('IDR', $customData['currency']);
            $this->assertEquals($order->order_code, $customData['order_id']);
            $this->assertCount(2, $customData['contents']); // 1 tiket + 1 addon

            return true;
        });
    }

    public function test_meta_capi_purchase_is_idempotent_and_does_not_double_send(): void
    {
        Http::fake();

        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'VR-DOUBLE-001',
            'customer_name' => 'Double Test',
            'customer_email' => 'double@test.com',
            'customer_phone' => '081299991111',
            'subtotal' => 175000,
            'grand_total' => 175000,
            'status' => 'paid',
            'paid_at' => now(),
            'meta_capi_purchase_sent_at' => now()->subMinutes(10), // Sudah pernah terkirim!
        ]);

        $service = app(MetaCapiService::class);
        $result = $service->sendPurchaseEvent($order);

        $this->assertTrue($result['success']);
        $this->assertTrue($result['already_sent']);
        Http::assertNothingSent();
    }

    public function test_send_meta_capi_purchase_job_skips_unpaid_order(): void
    {
        Http::fake();

        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'VR-JOB-PENDING',
            'customer_name' => 'Job Pending',
            'customer_email' => 'job@test.com',
            'customer_phone' => '081299992222',
            'subtotal' => 175000,
            'grand_total' => 175000,
            'status' => 'pending',
        ]);

        $job = new SendMetaCapiPurchaseJob($order);
        $job->handle(app(MetaCapiService::class));

        Http::assertNothingSent();
        $this->assertNull($order->fresh()->meta_capi_purchase_sent_at);
    }

    public function test_tripay_webhook_paid_triggers_meta_capi_purchase(): void
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response(['events_received' => 1], 200),
            'https://api.mailketing.co.id/*' => Http::response(['status' => 'success'], 200),
            'api.mailketing.co.id/*' => Http::response(['status' => 'success'], 200),
        ]);

        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'VR-TRIPAY-CAPI-001',
            'customer_name' => 'Tripay Buyer',
            'customer_email' => 'buyer@tripay.com',
            'customer_phone' => '081234500000',
            'subtotal' => 175000,
            'grand_total' => 175000,
            'status' => 'pending',
        ]);

        Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category10k->id,
            'jersey_size_id' => $this->jerseyM->id,
            'bib_name' => 'BUYER',
            'full_name' => 'Tripay Buyer',
            'gender' => 'male',
            'email' => 'buyer@tripay.com',
            'phone' => '081234500000',
            'qr_token' => 'QR-BUYER-01',
        ]);

        $privateKey = config('tripay.private_key');
        $payload = [
            'reference' => 'DEV-T99887766',
            'merchant_ref' => 'VR-TRIPAY-CAPI-001',
            'payment_method' => 'QRIS',
            'payment_method_code' => 'QRIS',
            'total_amount' => 175000,
            'status' => 'PAID',
            'paid_at' => now()->timestamp,
        ];
        $rawJson = json_encode($payload);
        $signature = hash_hmac('sha256', $rawJson, $privateKey);

        $response = $this->call(
            'POST',
            '/api/webhooks/tripay',
            [],
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_X_CALLBACK_SIGNATURE' => $signature,
                'HTTP_X_CALLBACK_EVENT' => 'payment_status',
            ],
            $rawJson
        );

        $response->assertStatus(200);
        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->meta_capi_purchase_sent_at);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '128495039201948')
                && $request->data()['data'][0]['event_name'] === 'Purchase';
        });
    }

    public function test_test_event_method_calls_graph_api(): void
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'events_received' => 1,
                'fbtrace_id' => 'TRACE_TEST',
            ], 200),
        ]);

        $service = app(MetaCapiService::class);
        $result = $service->sendTestEvent('128495039201948', 'EAAG_TOKEN_123', 'TEST99881');

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['events_received']);

        Http::assertSent(function ($request) {
            $data = $request->data();

            return isset($data['test_event_code'])
                && $data['test_event_code'] === 'TEST99881'
                && $data['data'][0]['event_name'] === 'TestEvent';
        });
    }
}
