<?php

namespace Tests\Feature;

use App\Jobs\SendPaymentPendingEmailJob;
use App\Jobs\SendTicketEmailJob;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Order;
use App\Models\Organizer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_payment_gateway_fee_is_charged_accurately_to_buyer(): void
    {
        // 1. Checkout dengan QRIS (fee: flat 750 + 0.7%)
        // Subtotal: 100.000
        // Fee QRIS: 750 + (100.000 * 0.7%) = 750 + 700 = 1.450
        // Grand Total: 101.450
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
        $this->assertEquals(1450, (int) $orderQris->payment_gateway_fee);
        $this->assertEquals(101450, (int) $orderQris->grand_total);

        // 2. Checkout dengan BRIVA (fee: flat 3.000 + 0%)
        // Subtotal: 100.000
        // Fee BRIVA: 3.000
        // Grand Total: 103.000
        $payloadVa = [
            'buyer_name' => 'Buyer VA',
            'buyer_email' => 'va@runner.com',
            'buyer_phone' => '081234567891',
            'payment_method' => 'BRIVA',
            'waiver_accepted' => '1',
            'participants' => [
                [
                    'category_id' => $this->category->id,
                    'bib_name' => 'VA RUNNER',
                    'gender' => 'female',
                ],
            ],
        ];

        $resVa = $this->post(route('public.register.checkout', $this->event->slug), $payloadVa);
        $resVa->assertRedirect();

        $orderVa = Order::where('customer_email', 'va@runner.com')->firstOrFail();
        $this->assertEquals(100000, (int) $orderVa->subtotal);
        $this->assertEquals(3000, (int) $orderVa->payment_gateway_fee);
        $this->assertEquals(103000, (int) $orderVa->grand_total);
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
}
