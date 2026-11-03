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

class MedaliOnlyCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected Event $event;

    protected EventCategory $categoryMedalOnly;

    protected EventCategory $categoryFullPackage;

    protected EventCategory $categoryJerseyOnly;

    protected EventCategory $categoryEpassOnly;

    protected JerseySize $jerseyM;

    protected function setUp(): void
    {
        parent::setUp();

        $organizer = Organizer::create([
            'name' => 'Budaya Run Indonesia',
            'slug' => 'budayarun-official',
            'email' => 'admin@budayarun.com',
            'phone' => '081916444458',
            'commission_rate' => 0.00,
        ]);

        $this->event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'BHINNEKA TUNGGAL IKA Virtual Run 42.195 KM',
            'slug' => 'bhinneka',
            'event_code' => 'BTI42K',
            'activity_type' => 'RUN',
            'submission_mode' => 'CUMULATIVE',
            'race_location_name' => 'Di Mana Saja',
            'registration_open_date' => now()->subDays(2),
            'registration_close_date' => now()->addDays(20),
            'event_start_date' => now()->subDay(),
            'event_end_date' => now()->addDays(60),
            'race_start_date' => now()->subDay(),
            'race_end_date' => now()->addDays(60),
            'auto_assign_bib' => true,
            'status' => 'published',
        ]);

        $this->categoryMedalOnly = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => 'Paket Medali Only (42.195 KM)',
            'distance_km' => 42.195,
            'normal_price' => 125000,
            'quota' => 200,
            'slots_taken' => 0,
            'includes_jersey' => false,
            'includes_medal' => true,
            'is_active' => true,
        ]);

        $this->categoryFullPackage = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => 'Paket Lengkap (Medali + Jersey 42.195 KM)',
            'distance_km' => 42.195,
            'normal_price' => 225000,
            'quota' => 300,
            'slots_taken' => 0,
            'includes_jersey' => true,
            'includes_medal' => true,
            'is_active' => true,
        ]);

        $this->categoryJerseyOnly = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => 'Paket Jersey Only (42.195 KM)',
            'distance_km' => 42.195,
            'normal_price' => 150000,
            'quota' => 150,
            'slots_taken' => 0,
            'includes_jersey' => true,
            'includes_medal' => false,
            'is_active' => true,
        ]);

        $this->categoryEpassOnly = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => 'Paket E-Pass Digital (42.195 KM)',
            'distance_km' => 42.195,
            'normal_price' => 50000,
            'quota' => 500,
            'slots_taken' => 0,
            'includes_jersey' => false,
            'includes_medal' => false,
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
    }

    public function test_register_page_inclusions_display_jersey_only_and_medal_only_accurately(): void
    {
        $response = $this->get(route('public.register.show', $this->event->slug));

        $response->assertStatus(200);

        // Paket Medali Only has badge and does not have Jersey Dryfit in its section
        $response->assertSee('🏅 Medali Only');
        $response->assertSee('👕 Jersey Only');
        $response->assertSee('📱 E-Pass Digital');

        // Check HTML contains appropriate conditional inclusions
        $content = $response->getContent();

        // Find the sections for Medali Only and Jersey Only
        $this->assertStringContainsString('Paket Medali Only (42.195 KM)', $content);
        $this->assertStringContainsString('Paket Jersey Only (42.195 KM)', $content);

        // Category Medali Only section
        $medalOnlySection = substr($content, strpos($content, 'Paket Medali Only (42.195 KM)'), 2000);
        $this->assertStringContainsString('Medali Zinc Alloy Import', $medalOnlySection);
        $this->assertStringNotContainsString('Jersey Dryfit', $medalOnlySection);

        // Category Jersey Only section
        $jerseyOnlySection = substr($content, strpos($content, 'Paket Jersey Only (42.195 KM)'), 2000);
        $this->assertStringContainsString('Jersey Dryfit', $jerseyOnlySection);
        $this->assertStringNotContainsString('Medali Zinc Alloy Import', $jerseyOnlySection);
    }

    public function test_participant_form_displays_medali_only_notice_and_hides_jersey_selectors(): void
    {
        $response = $this->withSession([
            "event_{$this->event->id}_tickets" => [
                $this->categoryMedalOnly->id => 1,
            ],
        ])->get(route('public.register.participants', $this->event->slug));

        $response->assertStatus(200);
        $response->assertSee('Paket Medali Only (Tanpa Jersey)');
        $response->assertSee('Anda tidak perlu memilih ukuran baju');
    }

    public function test_participant_form_displays_jersey_only_notice_and_shows_jersey_selectors(): void
    {
        $response = $this->withSession([
            "event_{$this->event->id}_tickets" => [
                $this->categoryJerseyOnly->id => 1,
            ],
        ])->get(route('public.register.participants', $this->event->slug));

        $response->assertStatus(200);
        $response->assertSee('Jersey Only');
        $response->assertSee('Paket Jersey Only (Tanpa Medali Fisik)');
        $response->assertSee('Medali fisik tidak disertakan');
        $response->assertSee('Kustomisasi Jersey Finisher');
    }

    public function test_participant_form_displays_epass_digital_only_notice(): void
    {
        $response = $this->withSession([
            "event_{$this->event->id}_tickets" => [
                $this->categoryEpassOnly->id => 1,
            ],
        ])->get(route('public.register.participants', $this->event->slug));

        $response->assertStatus(200);
        $response->assertSee('E-Pass Digital');
        $response->assertSee('Paket E-Pass (Digital Finisher Only)');
    }

    public function test_participant_registration_succeeds_without_jersey_size_for_medali_only(): void
    {
        $mockTripay = $this->mock(TripayService::class);
        $mockTripay->shouldReceive('createTransaction')
            ->once()
            ->andReturn([
                'reference' => 'DEV-MEDAL-001',
                'checkout_url' => 'https://tripay.co.id/checkout/DEV-MEDAL-001',
                'payment_method' => 'BCAVA',
                'pay_code' => '88880001234',
                'qr_url' => null,
                'amount' => 125000,
                'status' => 'UNPAID',
            ]);

        $payload = [
            'buyer_name' => 'Andi Medal Runner',
            'buyer_email' => 'andi@example.com',
            'buyer_phone' => '081299887766',
            'payment_method' => 'BCAVA',
            'waiver_accepted' => '1',
            'participants' => [
                [
                    'category_id' => $this->categoryMedalOnly->id,
                    'bib_name' => 'ANDI MEDAL',
                    'gender' => 'male',
                    'email' => 'andi@example.com',
                    'phone' => '081299887766',
                    'jersey_type' => 'short',
                    'jersey_size_id' => null,
                ],
            ],
        ];

        $response = $this->post(route('public.register.checkout', $this->event->slug), $payload);

        $response->assertRedirect();

        $participant = Participant::where('email', 'andi@example.com')->first();
        $this->assertNotNull($participant);
        $this->assertNull($participant->jersey_size_id);
        $this->assertEquals(0, (float) $participant->jersey_surcharge);
        $this->assertEquals('Tanpa Jersey', $participant->jersey_type_label);
        $this->assertEquals('Tanpa Jersey (Medali Only)', $participant->jersey_full_spec);
        $this->assertEquals('Medali Only', $participant->racepack_summary);

        // Check stock of jerseyM was not touched
        $this->jerseyM->refresh();
        $this->assertEquals(0, $this->jerseyM->allocated_stock);

        // Check order total amount
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals(125000, (float) $order->grand_total);
        $this->assertEquals(0, (float) $order->jersey_surcharges_total);
    }

    public function test_jersey_only_registration_succeeds_with_jersey_and_allocates_stock(): void
    {
        $mockTripay = $this->mock(TripayService::class);
        $mockTripay->shouldReceive('createTransaction')
            ->once()
            ->andReturn([
                'reference' => 'DEV-JERSEY-001',
                'checkout_url' => 'https://tripay.co.id/checkout/DEV-JERSEY-001',
                'payment_method' => 'BCAVA',
                'pay_code' => '88880005678',
                'qr_url' => null,
                'amount' => 170000,
                'status' => 'UNPAID',
            ]);

        $payload = [
            'buyer_name' => 'Citra Jersey Runner',
            'buyer_email' => 'citra@example.com',
            'buyer_phone' => '081299883344',
            'payment_method' => 'BCAVA',
            'waiver_accepted' => '1',
            'participants' => [
                [
                    'category_id' => $this->categoryJerseyOnly->id,
                    'bib_name' => 'CITRA JERSEY',
                    'gender' => 'female',
                    'email' => 'citra@example.com',
                    'phone' => '081299883344',
                    'jersey_type' => 'long', // Long sleeve (+Rp 20.000)
                    'jersey_size_id' => $this->jerseyM->id,
                ],
            ],
        ];

        $response = $this->post(route('public.register.checkout', $this->event->slug), $payload);

        $response->assertRedirect();

        $participant = Participant::where('email', 'citra@example.com')->first();
        $this->assertNotNull($participant);
        $this->assertEquals($this->jerseyM->id, $participant->jersey_size_id);
        $this->assertEquals(20000, (float) $participant->jersey_surcharge);
        $this->assertEquals('Lengan Panjang', $participant->jersey_type_label);
        $this->assertEquals('Lengan Panjang - Size M', $participant->jersey_full_spec);
        $this->assertEquals('Jersey Only (M)', $participant->racepack_summary);

        // Check stock was allocated for jersey
        $this->jerseyM->refresh();
        $this->assertEquals(1, $this->jerseyM->allocated_stock);

        // Check order total: 150.000 category + 20.000 long sleeve = 170.000
        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals(170000, (float) $order->grand_total);
        $this->assertEquals(20000, (float) $order->jersey_surcharges_total);
    }

    public function test_full_package_category_still_requires_jersey_size(): void
    {
        $payload = [
            'buyer_name' => 'Doni Full Package',
            'buyer_email' => 'doni@example.com',
            'buyer_phone' => '081299881122',
            'payment_method' => 'BCAVA',
            'waiver_accepted' => '1',
            'participants' => [
                [
                    'category_id' => $this->categoryFullPackage->id,
                    'bib_name' => 'DONI FULL',
                    'gender' => 'male',
                    'email' => 'doni@example.com',
                    'phone' => '081299881122',
                    'jersey_type' => 'short',
                    'jersey_size_id' => null, // missing jersey size for package with jersey
                ],
            ],
        ];

        $response = $this->post(route('public.register.checkout', $this->event->slug), $payload);

        $response->assertSessionHasErrors(['participants.0.jersey_size_id']);
    }
}
