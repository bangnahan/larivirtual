<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventAddon;
use App\Models\EventCategory;
use App\Models\JerseySize;
use App\Models\Order;
use App\Models\OrderAddon;
use App\Models\Organizer;
use App\Models\SpxShippingRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventAddonTest extends TestCase
{
    use RefreshDatabase;

    protected Event $event;

    protected EventCategory $category10k;

    protected JerseySize $jerseyL;

    protected EventAddon $addonJersey;

    protected EventAddon $addonPin;

    protected EventAddon $addonBottle;

    protected function setUp(): void
    {
        parent::setUp();

        $organizer = Organizer::create([
            'name' => 'VIRA Organizer',
            'slug' => 'vira-org',
            'email' => 'admin@vira.org',
            'phone' => '081299998888',
            'commission_rate' => 0.00,
        ]);

        $this->event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Jakarta Night Virtual Run 2026',
            'slug' => 'jakarta-night-virtual-2026',
            'event_code' => 'JNVR26',
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

        $this->category10k = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => '10K Finisher',
            'distance_km' => 10.00,
            'normal_price' => 150000,
            'quota' => 200,
            'slots_taken' => 0,
            'bib_start_number' => 1001,
            'base_weight_grams' => 500,
            'is_active' => true,
        ]);

        $this->jerseyL = JerseySize::create([
            'event_id' => $this->event->id,
            'size_name' => 'L',
            'gender_type' => 'unisex',
            'stock' => 100,
            'is_unlimited' => true,
        ]);

        // Add-ons
        $this->addonJersey = EventAddon::create([
            'event_id' => $this->event->id,
            'name' => 'Finisher Jersey Eksklusif',
            'description' => 'Jersey finisher bahan quick-dry dengan logo reflektif.',
            'price' => 125000,
            'weight_grams' => 200,
            'is_unlimited' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->addonPin = EventAddon::create([
            'event_id' => $this->event->id,
            'name' => 'Enamel Pin 3D Collector',
            'description' => 'Pin logam kuningan 3D.',
            'price' => 45000,
            'weight_grams' => 50,
            'is_unlimited' => false,
            'stock' => 10,
            'allocated_stock' => 0,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $this->addonBottle = EventAddon::create([
            'event_id' => $this->event->id,
            'name' => 'Soft Flask 500ml',
            'description' => 'Botol minum lari lipat BPA-free.',
            'price' => 65000,
            'weight_grams' => 100,
            'is_unlimited' => true,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        SpxShippingRate::create([
            'origin_city' => 'KAB. TANGERANG',
            'destination_province' => 'DKI JAKARTA',
            'destination_city' => 'KOTA JAKARTA SELATAN',
            'destination_district' => 'KEBAYORAN BARU',
            'regular_rate_per_kg' => 10000,
            'regular_sla_days' => 2,
            'search_keyword' => 'kebayoran baru kota jakarta selatan',
        ]);
    }

    public function test_ticket_selection_page_displays_custom_addons_section(): void
    {
        $response = $this->get(route('public.register.show', $this->event->slug));

        $response->assertStatus(200);
        $response->assertSee('Add-on &amp; Merchandise Event', false);
        $response->assertSee('Finisher Jersey Eksklusif');
        $response->assertSee('Enamel Pin 3D Collector');
        $response->assertSee('Soft Flask 500ml');
        $response->assertSee('Rp 125.000');
        $response->assertSee('Rp 45.000');
        $response->assertSee('Rp 65.000');
    }

    public function test_participants_page_receives_and_displays_selected_addons(): void
    {
        $payload = [
            'tickets' => [
                $this->category10k->id => 2,
            ],
            'addons' => [
                $this->addonJersey->id => 1,
                $this->addonPin->id => 2,
            ],
        ];

        $response = $this->post(route('public.register.participants', $this->event->slug), $payload);

        $response->assertStatus(200);
        $response->assertSee('Add-on &amp; Merchandise Tambahan', false);
        $response->assertSee('Finisher Jersey Eksklusif');
        $response->assertSee('Enamel Pin 3D Collector');
        $response->assertSee('1x');
        $response->assertSee('2x');
        // Subtotal tiket = 300.000, subtotal addon = 125.000 + 90.000 = 215.000
        $response->assertSee('Rp 515.000'); // Total item subtotal
    }

    public function test_cannot_order_addon_exceeding_limited_stock(): void
    {
        $payload = [
            'tickets' => [
                $this->category10k->id => 1,
            ],
            'addons' => [
                $this->addonPin->id => 15, // Stock is only 10
            ],
        ];

        $response = $this->post(route('public.register.participants', $this->event->slug), $payload);

        $response->assertRedirect(route('public.register.show', $this->event->slug));
        $response->assertSessionHasErrors('addons');
    }

    public function test_checkout_successfully_creates_order_with_addons_and_accurate_grand_total(): void
    {
        $payload = [
            'buyer_name' => 'Budi Santoso',
            'buyer_email' => 'budi@example.com',
            'buyer_phone' => '081234567890',
            'payment_method' => 'QRIS',
            'waiver_accepted' => '1',
            'destination_city' => 'KOTA JAKARTA SELATAN',
            'destination_district' => 'KEBAYORAN BARU',
            'province' => 'DKI JAKARTA',
            'postal_code' => '12110',
            'address_detail' => 'Jl. Senopati No. 10',
            'participants' => [
                [
                    'category_id' => $this->category10k->id,
                    'jersey_size_id' => $this->jerseyL->id,
                    'bib_name' => 'BUDI RUN',
                    'full_name' => 'Budi Santoso',
                    'gender' => 'male',
                    'phone' => '081234567890',
                    'email' => 'budi@example.com',
                ],
            ],
            'addons' => [
                $this->addonJersey->id => 1, // 125.000, 200gr
                $this->addonBottle->id => 2, // 2 * 65.000 = 130.000, 200gr
            ],
        ];

        $response = $this->post(route('public.register.checkout', $this->event->slug), $payload);

        $order = Order::where('customer_email', 'budi@example.com')->first();
        $this->assertNotNull($order);

        // Ticket 1x 150.000
        // Addon Jersey 1x 125.000
        // Addon Bottle 2x 130.000
        // Total Addons = 255.000
        // Subtotal = 150.000 + 255.000 = 405.000
        // Platform fee: 0
        // Grand Total: 405.000 + 10.000 (ongkir) + 3.655 (biaya payment gateway QRIS: 750 + 0.7%) = 418.655
        $this->assertEquals(405000, (int) $order->subtotal);
        $this->assertEquals(255000, (int) $order->addons_total);
        $this->assertEquals(10000, (int) $order->shipping_cost);
        $this->assertEquals(0, (int) $order->platform_fee);
        $this->assertEquals(3655, (int) $order->payment_gateway_fee);
        $this->assertEquals(418655, (int) $order->grand_total);

        // Verify OrderAddon records
        $this->assertCount(2, $order->addons);
        $this->assertDatabaseHas('order_addons', [
            'order_id' => $order->id,
            'event_addon_id' => $this->addonJersey->id,
            'addon_name' => 'Finisher Jersey Eksklusif',
            'quantity' => 1,
            'unit_price' => 125000,
            'total_price' => 125000,
        ]);
        $this->assertDatabaseHas('order_addons', [
            'order_id' => $order->id,
            'event_addon_id' => $this->addonBottle->id,
            'addon_name' => 'Soft Flask 500ml',
            'quantity' => 2,
            'unit_price' => 65000,
            'total_price' => 130000,
        ]);

        // Verify invoice view displays addons
        $invoiceResponse = $this->get(route('public.order.show', $order->order_code));
        $invoiceResponse->assertStatus(200);
        $invoiceResponse->assertSee('Finisher Jersey Eksklusif');
        $invoiceResponse->assertSee('Soft Flask 500ml');
        $invoiceResponse->assertSee('Subtotal Add-on &amp; Merchandise', false);
        $invoiceResponse->assertSee('Rp 255.000');
    }
}
