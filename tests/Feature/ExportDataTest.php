<?php

namespace Tests\Feature;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\ParticipantResource;
use App\Filament\Widgets\JerseyProductionRecapWidget;
use App\Models\Event;
use App\Models\EventAddon;
use App\Models\EventCategory;
use App\Models\JerseySize;
use App\Models\Order;
use App\Models\OrderAddon;
use App\Models\OrderShippingAddress;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ExportDataTest extends TestCase
{
    use RefreshDatabase;

    protected Event $event;

    protected EventCategory $category10k;

    protected JerseySize $jerseyM;

    protected JerseySize $jerseyL;

    protected EventAddon $addonFlask;

    protected function setUp(): void
    {
        parent::setUp();

        $organizer = Organizer::create([
            'name' => 'Virun Sport Organizer',
            'slug' => 'virun-sport-org',
            'email' => 'admin@virun.org',
            'phone' => '081299998888',
        ]);

        $this->event = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Virun Summer Challenge 2026',
            'slug' => 'virun-summer-challenge-2026',
            'event_code' => 'VSC26',
            'activity_type' => 'RUN',
            'submission_mode' => 'SINGLE',
            'race_location_name' => 'Virtual Run Nasional',
            'registration_open_date' => now()->subDays(5),
            'registration_close_date' => now()->addDays(20),
            'event_start_date' => now()->addDays(25),
            'event_end_date' => now()->addDays(30),
            'race_start_date' => now()->addDays(25),
            'race_end_date' => now()->addDays(30),
            'status' => 'published',
        ]);

        $this->category10k = EventCategory::create([
            'event_id' => $this->event->id,
            'name' => '10K Virtual Run',
            'distance_km' => 10.00,
            'normal_price' => 150000,
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

        $this->jerseyL = JerseySize::create([
            'event_id' => $this->event->id,
            'size_name' => 'L',
            'gender_type' => 'unisex',
            'chest_width_cm' => 53,
            'body_length_cm' => 72,
            'is_unlimited' => true,
        ]);

        $this->addonFlask = EventAddon::create([
            'event_id' => $this->event->id,
            'name' => 'Soft Flask 500ml',
            'price' => 85000,
            'weight_grams' => 120,
            'is_unlimited' => true,
            'is_active' => true,
        ]);
    }

    public function test_spx_manifest_exports_single_row_for_multi_ticket_orders(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'VR-SPX-001',
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'subtotal' => 300000,
            'addons_total' => 85000,
            'shipping_cost' => 15000,
            'grand_total' => 400000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        OrderShippingAddress::create([
            'order_id' => $order->id,
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081234567890',
            'province' => 'DKI Jakarta',
            'destination_city' => 'Jakarta Selatan',
            'destination_district' => 'Kebayoran Baru',
            'postal_code' => '12150',
            'address_detail' => 'Jl. Senopati No. 12',
            'total_weight_grams' => 620,
            'courier_name' => 'SPX Express',
            'service_type' => 'Standard',
            'shipping_cost' => 15000,
            'shipping_status' => 'processing',
            'tracking_number' => 'SPXID0987654321',
        ]);

        // Dua peserta dalam 1 transaksi
        Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category10k->id,
            'jersey_size_id' => $this->jerseyM->id,
            'bib_number' => '1001',
            'bib_name' => 'BUDI S',
            'full_name' => 'Budi Santoso',
            'gender' => 'male',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'qr_token' => 'QR-BUDI-1',
        ]);

        Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category10k->id,
            'jersey_size_id' => $this->jerseyL->id,
            'bib_number' => '1002',
            'bib_name' => 'SITI S',
            'full_name' => 'Siti Rahma',
            'gender' => 'female',
            'email' => 'siti@example.com',
            'phone' => '081234567891',
            'qr_token' => 'QR-SITI-2',
        ]);

        // Add-on
        OrderAddon::create([
            'order_id' => $order->id,
            'event_addon_id' => $this->addonFlask->id,
            'addon_name' => 'Soft Flask 500ml',
            'quantity' => 1,
            'unit_price' => 85000,
            'total_price' => 85000,
        ]);

        // Write row into buffer
        $stream = fopen('php://memory', 'r+');
        OrderResource::writeSpxCsvRow($stream, $order);
        rewind($stream);
        $csvLine = fgetcsv($stream);
        fclose($stream);

        $this->assertIsArray($csvLine);
        // Header: NO_ORDER, NAMA_PENERIMA, NO_HP, PROVINSI, KOTA, KEC, KODEPOS, ALAMAT, BERAT, JML_TIKET, JML_ADDON, RINCIAN, EKSPEDISI, LAYANAN, STATUS, RESI, TOTAL, TGL_LUNAS
        $this->assertEquals('VR-SPX-001', $csvLine[0]);
        $this->assertEquals('Budi Santoso', $csvLine[1]);
        $this->assertEquals('081234567890', $csvLine[2]);
        $this->assertEquals('DKI Jakarta', $csvLine[3]);
        $this->assertEquals('Jakarta Selatan', $csvLine[4]);
        $this->assertEquals('Kebayoran Baru', $csvLine[5]);
        $this->assertEquals('12150', $csvLine[6]);
        $this->assertEquals('Jl. Senopati No. 12', $csvLine[7]);
        $this->assertEquals('620', $csvLine[8]);
        $this->assertEquals('2', $csvLine[9]); // 2 tiket
        $this->assertEquals('1', $csvLine[10]); // 1 addon
        $this->assertStringContainsString('2x Race Pack', $csvLine[11]);
        $this->assertStringContainsString('10K Virtual Run(M)', $csvLine[11]);
        $this->assertStringContainsString('10K Virtual Run(L)', $csvLine[11]);
        $this->assertStringContainsString('Soft Flask 500ml', $csvLine[11]);
        $this->assertEquals('SPX Express', $csvLine[12]);
        $this->assertEquals('SPXID0987654321', $csvLine[15]);
    }

    public function test_participant_bib_csv_contains_no_timing_chips_or_shipping_addresses(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'VR-BIB-001',
            'customer_name' => 'Runner Test',
            'customer_email' => 'runner@example.com',
            'customer_phone' => '081211112222',
            'subtotal' => 150000,
            'grand_total' => 150000,
            'status' => 'paid',
        ]);

        $participant = Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category10k->id,
            'jersey_size_id' => $this->jerseyM->id,
            'bib_number' => '1099',
            'bib_name' => 'FAST RUNNER',
            'full_name' => 'Ahmad Pelari',
            'gender' => 'male',
            'birth_date' => '1995-04-12',
            'email' => 'ahmad@example.com',
            'phone' => '081233334444',
            'qr_token' => 'QR-AHMAD-99',
        ]);

        $stream = fopen('php://memory', 'r+');
        ParticipantResource::writeBibCsvRow($stream, $participant);
        rewind($stream);
        $csvLine = fgetcsv($stream);
        fclose($stream);

        $this->assertIsArray($csvLine);
        // Header: BIB_NUMBER, NAMA_BIB, NAMA_LENGKAP, KATEGORI, JARAK_KM, GENDER, TGL_LAHIR, UKURAN_JERSEY, NO_HP, EMAIL, KODE_ORDER, STATUS_BAYAR
        $this->assertEquals('1099', $csvLine[0]);
        $this->assertEquals('FAST RUNNER', $csvLine[1]);
        $this->assertEquals('Ahmad Pelari', $csvLine[2]);
        $this->assertEquals('10K Virtual Run', $csvLine[3]);
        $this->assertEquals('10.00', $csvLine[4]);
        $this->assertEquals('male', $csvLine[5]);
        $this->assertEquals('1995-04-12', $csvLine[6]);
        $this->assertEquals('M', $csvLine[7]);
        $this->assertEquals('081233334444', $csvLine[8]);
        $this->assertEquals('ahmad@example.com', $csvLine[9]);
        $this->assertEquals('VR-BIB-001', $csvLine[10]);
        $this->assertEquals('paid', $csvLine[11]);
        $this->assertCount(12, $csvLine);
    }

    public function test_production_recap_widget_calculates_merchandise_add_ons(): void
    {
        $order = Order::create([
            'event_id' => $this->event->id,
            'order_code' => 'VR-PO-001',
            'customer_name' => 'Buyer PO',
            'customer_email' => 'buyer@po.com',
            'customer_phone' => '081255556666',
            'subtotal' => 150000,
            'addons_total' => 170000,
            'grand_total' => 320000,
            'status' => 'paid',
        ]);

        Participant::create([
            'order_id' => $order->id,
            'event_category_id' => $this->category10k->id,
            'jersey_size_id' => $this->jerseyM->id,
            'bib_number' => '2001',
            'bib_name' => 'PO RUNNER',
            'full_name' => 'Pelari PO',
            'gender' => 'male',
            'email' => 'runner@po.com',
            'phone' => '081255556666',
            'qr_token' => 'QR-PO-1',
        ]);

        OrderAddon::create([
            'order_id' => $order->id,
            'event_addon_id' => $this->addonFlask->id,
            'addon_name' => 'Soft Flask 500ml',
            'quantity' => 2,
            'unit_price' => 85000,
            'total_price' => 170000,
        ]);

        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $component = Livewire::actingAs($superadmin)
            ->test(JerseyProductionRecapWidget::class);

        $recap = $component->get('recapData');
        $this->assertNotEmpty($recap['sizes']);
        $this->assertNotEmpty($recap['addons']);

        $flaskRecap = collect($recap['addons'])->firstWhere('id', $this->addonFlask->id);
        $this->assertNotNull($flaskRecap);
        $this->assertEquals('Soft Flask 500ml', $flaskRecap['name']);
        $this->assertEquals(2, $flaskRecap['paid_qty']);
        $this->assertEquals(2, $flaskRecap['total_qty']);

        ob_start();
        $response = $component->instance()->exportCsv();
        $response->sendContent();
        $output = ob_get_clean();

        $this->assertStringContainsString('REKAP KEBUTUHAN PRODUKSI JERSEY & MERCHANDISE - VIRUN', $output);
        $this->assertStringContainsString('REKAP KEBUTUHAN PRODUKSI ADD-ON & MERCHANDISE EVENT', $output);
        $this->assertStringContainsString('Soft Flask 500ml', $output);
    }
}
