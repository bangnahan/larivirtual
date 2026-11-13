<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\JerseySize;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\Participant;
use App\Services\TripayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JerseyCustomizationTest extends TestCase
{
    use RefreshDatabase;

    protected Event $event;

    protected EventCategory $category5k;

    protected JerseySize $jerseyM;

    protected JerseySize $jersey4XL;

    protected function setUp(): void
    {
        parent::setUp();

        $organizer = Organizer::create([
            'name' => 'Victory Sports',
            'slug' => 'victory-sports',
            'email' => 'admin@victory.com',
            'phone' => '081234567890',
            'commission_rate' => 0.00,
        ]);

        $this->event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Victory Virtual Run 2026',
            'slug' => 'victory-virtual-2026',
            'event_code' => 'VVR26',
            'activity_type' => 'RUN',
            'submission_mode' => 'SINGLE',
            'race_location_name' => 'Di Mana Saja',
            'registration_open_date' => now()->subDays(2),
            'registration_close_date' => now()->addDays(20),
            'event_start_date' => now()->subDay(),
            'event_end_date' => now()->addDays(30),
            'race_start_date' => now()->subDay(),
            'race_end_date' => now()->addDays(30),
            'auto_assign_bib' => true,
            'status' => 'published',
        ]);

        $this->category5k = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => '5K Fun Run',
            'distance_km' => 5.0,
            'normal_price' => 150000,
            'quota' => 100,
            'slots_taken' => 0,
            'is_active' => true,
        ]);

        $this->jerseyM = JerseySize::create([
            'event_id' => $this->event->id,
            'size_name' => 'M',
            'chest_width_cm' => 50,
            'body_length_cm' => 70,
            'stock' => 50,
            'allocated_stock' => 0,
            'surcharge' => 0,
        ]);

        $this->jersey4XL = JerseySize::create([
            'event_id' => $this->event->id,
            'size_name' => '4XL',
            'chest_width_cm' => 64,
            'body_length_cm' => 84,
            'stock' => 20,
            'allocated_stock' => 0,
            'surcharge' => 10000,
        ]);
    }

    public function test_participants_form_displays_jersey_sleeve_and_size_surcharges(): void
    {
        $response = $this->withSession([
            "event_{$this->event->id}_tickets" => [
                $this->category5k->id => 1,
            ],
        ])->get(route('public.register.participants', $this->event->slug));

        $response->assertStatus(200);
        $response->assertSee('Kustomisasi Jersey Finisher');
        $response->assertSee('Model Lengan');
        $response->assertSee('Pendek');
        $response->assertSee('Singlet');
        $response->assertSee('Panjang');
        $response->assertSee('+Rp 20.000');
        $response->assertSee('Big Size +Rp 10.000');
    }

    public function test_checkout_calculates_sleeve_and_big_size_surcharges_accurately(): void
    {
        $mockTripay = $this->mock(TripayService::class);
        $mockTripay->shouldReceive('calculateFee')->andReturn(0);
        $mockTripay->shouldReceive('createTransaction')
            ->once()
            ->andReturn([
                'reference' => 'DEV-TRIPAY-SURCHARGE-1',
                'checkout_url' => 'https://tripay.co.id/checkout/DEV-TRIPAY-SURCHARGE-1',
                'payment_method' => 'QRIS',
                'pay_code' => '00020101021226',
                'qr_url' => 'https://tripay.co.id/qr/DEV-TRIPAY-SURCHARGE-1',
                'amount' => 180000,
                'status' => 'UNPAID',
            ]);

        // 1 participant: 5K ticket (150.000) + Long Sleeve (20.000) + 4XL size (10.000) = 180.000
        $payload = [
            'buyer_name' => 'Budi Runner',
            'buyer_email' => 'budi@example.com',
            'buyer_phone' => '08123456789',
            'payment_method' => 'QRIS',
            'waiver_accepted' => '1',
            'participants' => [
                [
                    'category_id' => $this->category5k->id,
                    'jersey_size_id' => $this->jersey4XL->id,
                    'jersey_type' => 'long',
                    'bib_name' => 'BUDI 4XL',
                    'gender' => 'male',
                    'phone' => '08123456789',
                    'email' => 'budi@example.com',
                ],
            ],
        ];

        $response = $this->post(route('public.register.checkout', $this->event->slug), $payload);

        $response->assertRedirect();

        $order = Order::where('customer_email', 'budi@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(30000, (float) $order->jersey_surcharges_total);
        $this->assertEquals(180000, (float) $order->subtotal); // 150.000 ticket + 30.000 jersey surcharge
        $this->assertEquals(180000, (float) $order->grand_total); // 180.000 without platform fee

        $participant = $order->participants()->first();
        $this->assertNotNull($participant);
        $this->assertEquals('long', $participant->jersey_type);
        $this->assertEquals(20000, (float) $participant->jersey_sleeve_surcharge);
        $this->assertEquals(10000, (float) $participant->jersey_size_surcharge);
        $this->assertEquals(30000, (float) $participant->jersey_surcharge);
        $this->assertEquals('Lengan Panjang', $participant->jersey_type_label);
        $this->assertEquals('Lengan Panjang - Size 4XL', $participant->jersey_full_spec);
    }

    public function test_tripay_order_items_include_jersey_surcharge_line_item(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-TEST-SURCHARGE',
            'customer_name' => 'Candra Pelari',
            'customer_email' => 'candra@example.com',
            'customer_phone' => '081298765432',
            'subtotal' => 180000,
            'jersey_surcharges_total' => 30000,
            'platform_fee' => 5000,
            'shipping_cost' => 0,
            'grand_total' => 185000,
            'status' => 'pending',
        ]);

        $tripayService = app(TripayService::class);
        $reflection = new \ReflectionClass($tripayService);
        $method = $reflection->getMethod('prepareOrderItems');
        $method->setAccessible(true);

        $items = $method->invoke($tripayService, $order);

        $surchargeItem = collect($items)->firstWhere('sku', 'SURCHARGE-JERSEY');
        $this->assertNotNull($surchargeItem);
        $this->assertEquals(30000, $surchargeItem['price']);
        $this->assertEquals(1, $surchargeItem['quantity']);
    }

    public function test_invoice_shows_jersey_full_spec_and_surcharge_breakdown(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-INV-SURCHARGE',
            'customer_name' => 'Dewi Pelari',
            'customer_email' => 'dewi@example.com',
            'customer_phone' => '081298765432',
            'subtotal' => 180000,
            'jersey_surcharges_total' => 30000,
            'platform_fee' => 5000,
            'shipping_cost' => 0,
            'grand_total' => 185000,
            'status' => 'pending',
        ]);

        Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category5k->id,
            'jersey_size_id' => $this->jersey4XL->id,
            'jersey_type' => 'long',
            'jersey_sleeve_surcharge' => 20000,
            'jersey_size_surcharge' => 10000,
            'jersey_surcharge' => 30000,
            'full_name' => 'Dewi Pelari',
            'bib_name' => 'DEWI 4XL',
            'gender' => 'female',
            'phone' => '081298765432',
            'email' => 'dewi@example.com',
            'waiver_accepted' => true,
        ]);

        $response = $this->get(route('public.order.show', $order->order_code));

        $response->assertStatus(200);
        $response->assertSee('Lengan Panjang - Size 4XL');
        $response->assertSee('+Rp 30.000');
        $response->assertSee('Upgrade Jersey (Lengan / Size):');
    }
}
