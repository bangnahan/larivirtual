<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\JerseySize;
use App\Models\Order;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\PaymentLog;
use App\Services\OrderManagementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Event $event;

    protected EventCategory $category10k;

    protected JerseySize $jerseyL;

    protected OrderManagementService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $organizer = Organizer::create([
            'name' => 'VR Management',
            'slug' => 'vr-mgmt',
            'email' => 'admin@vrmgmt.com',
            'phone' => '08123456789',
        ]);

        $this->event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Bali Sunset Virtual Marathon 2026',
            'slug' => 'bali-sunset-2026',
            'event_code' => 'BSVM26',
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
            'name' => '10K Challenge',
            'distance_km' => 10.0,
            'normal_price' => 200000,
            'quota' => 100,
            'slots_taken' => 2,
            'bib_start_number' => 1001,
            'is_active' => true,
        ]);

        $this->jerseyL = JerseySize::create([
            'event_id' => $this->event->id,
            'size_name' => 'L',
            'chest_width_cm' => 52,
            'body_length_cm' => 72,
            'stock' => 50,
            'allocated_stock' => 2,
            'surcharge' => 0,
        ]);

        $this->service = app(OrderManagementService::class);
    }

    public function test_admin_can_update_status_to_paid_which_auto_assigns_bib_and_logs_audit(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-MANUAL-001',
            'customer_name' => 'Gita Runner',
            'customer_email' => 'gita@example.com',
            'customer_phone' => '081234567890',
            'subtotal' => 400000,
            'grand_total' => 405000,
            'status' => 'pending',
        ]);

        $p1 = Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category10k->id,
            'jersey_size_id' => $this->jerseyL->id,
            'full_name' => 'Gita Runner',
            'bib_name' => 'GITA',
            'gender' => 'female',
            'phone' => '081234567890',
            'email' => 'gita@example.com',
            'waiver_accepted' => true,
        ]);

        $p2 = Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category10k->id,
            'jersey_size_id' => $this->jerseyL->id,
            'full_name' => 'Hadi Runner',
            'bib_name' => 'HADI',
            'gender' => 'male',
            'phone' => '081234567891',
            'email' => 'hadi@example.com',
            'waiver_accepted' => true,
        ]);

        $this->assertNull($p1->bib_number);
        $this->assertNull($p2->bib_number);

        // Update ke PAID
        $updatedOrder = $this->service->updateStatus(
            order: $order,
            newStatus: 'paid',
            reason: 'Bukti transfer BCA diverifikasi manual oleh Finance',
            adminName: 'Super Admin Indra',
            sendTicketEmail: false
        );

        $this->assertEquals('paid', $updatedOrder->status);
        $this->assertNotNull($updatedOrder->paid_at);

        $p1->refresh();
        $p2->refresh();
        $this->assertNotEmpty($p1->bib_number);
        $this->assertNotEmpty($p2->bib_number);
        $this->assertNotEquals($p1->bib_number, $p2->bib_number);

        // Audit Log verification
        $log = PaymentLog::where('order_id', $order->id)->latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals('manual_admin', $log->gateway);
        $this->assertEquals('paid', $log->status);
        $this->assertEquals('Super Admin Indra', $log->payload['changed_by']);
        $this->assertEquals('Bukti transfer BCA diverifikasi manual oleh Finance', $log->payload['reason']);
        $this->assertEquals('pending', $log->payload['old_status']);
    }

    public function test_admin_can_cancel_order_which_releases_category_and_jersey_quota(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-CANCEL-001',
            'customer_name' => 'Indra Runner',
            'customer_email' => 'indra@example.com',
            'customer_phone' => '081299991111',
            'subtotal' => 200000,
            'grand_total' => 205000,
            'status' => 'pending',
        ]);

        Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category10k->id,
            'jersey_size_id' => $this->jerseyL->id,
            'full_name' => 'Indra Runner',
            'bib_name' => 'INDRA',
            'gender' => 'male',
            'phone' => '081299991111',
            'email' => 'indra@example.com',
            'waiver_accepted' => true,
        ]);

        $this->assertEquals(2, $this->category10k->fresh()->slots_taken);
        $this->assertEquals(2, $this->jerseyL->fresh()->allocated_stock);

        // Cancel order
        $this->service->updateStatus(
            order: $order,
            newStatus: 'cancelled',
            reason: 'Peserta membatalkan pendaftaran karena bentrok jadwal',
            adminName: 'Admin Maya'
        );

        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertEquals(1, $this->category10k->fresh()->slots_taken);
        $this->assertEquals(1, $this->jerseyL->fresh()->allocated_stock);

        $log = PaymentLog::where('order_id', $order->id)->latest()->first();
        $this->assertNotNull($log);
        $this->assertEquals('cancelled', $log->status);
        $this->assertEquals('Admin Maya', $log->payload['changed_by']);
    }

    public function test_admin_can_refund_order_which_releases_quota_and_logs_audit(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-REFUND-001',
            'customer_name' => 'Joko Runner',
            'customer_email' => 'joko@example.com',
            'customer_phone' => '081288882222',
            'subtotal' => 200000,
            'grand_total' => 205000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category10k->id,
            'jersey_size_id' => $this->jerseyL->id,
            'full_name' => 'Joko Runner',
            'bib_name' => 'JOKO',
            'gender' => 'male',
            'phone' => '081288882222',
            'email' => 'joko@example.com',
            'waiver_accepted' => true,
        ]);

        $this->service->updateStatus(
            order: $order,
            newStatus: 'refunded',
            reason: 'Pengembalian dana 100% via transfer bank telah diproses finance',
            adminName: 'Finance Staff'
        );

        $this->assertEquals('refunded', $order->fresh()->status);
        $this->assertEquals(1, $this->category10k->fresh()->slots_taken);
        $this->assertEquals(1, $this->jerseyL->fresh()->allocated_stock);
    }

    public function test_re_activating_cancelled_order_to_paid_re_allocates_slots(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-REOPEN-001',
            'customer_name' => 'Kiki Runner',
            'customer_email' => 'kiki@example.com',
            'customer_phone' => '081277773333',
            'subtotal' => 200000,
            'grand_total' => 205000,
            'status' => 'cancelled',
        ]);

        Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category10k->id,
            'jersey_size_id' => $this->jerseyL->id,
            'full_name' => 'Kiki Runner',
            'bib_name' => 'KIKI',
            'gender' => 'female',
            'phone' => '081277773333',
            'email' => 'kiki@example.com',
            'waiver_accepted' => true,
        ]);

        $this->assertEquals(2, $this->category10k->fresh()->slots_taken);

        // Re-aktifkan ke PAID
        $this->service->updateStatus(
            order: $order,
            newStatus: 'paid',
            reason: 'Peserta jadi mengikuti lomba dan telah melunasi',
            adminName: 'Admin Utama',
            sendTicketEmail: false
        );

        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertEquals(3, $this->category10k->fresh()->slots_taken);
        $this->assertEquals(3, $this->jerseyL->fresh()->allocated_stock);
    }

    public function test_invalid_status_throws_exception(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'JLTX-ERR-001',
            'customer_name' => 'Error Test',
            'customer_email' => 'err@example.com',
            'customer_phone' => '081200000000',
            'subtotal' => 200000,
            'grand_total' => 205000,
            'status' => 'pending',
        ]);

        $this->expectException(InvalidArgumentException::class);

        $this->service->updateStatus(
            order: $order,
            newStatus: 'invalid_status_xyz'
        );
    }
}
