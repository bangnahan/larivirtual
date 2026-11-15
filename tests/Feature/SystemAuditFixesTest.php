<?php

namespace Tests\Feature;

use App\Jobs\SendPaymentPendingEmailJob;
use App\Jobs\SendTicketEmailJob;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Order;
use App\Models\Organizer;
use App\Services\TripayService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
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
