<?php

namespace Database\Seeders;

use App\Models\ActivitySubmission;
use App\Models\Event;
use App\Models\EventAddon;
use App\Models\EventCategory;
use App\Models\JerseySize;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderShippingAddress;
use App\Models\Organizer;
use App\Models\Participant;
use App\Models\SpxShippingRate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VirtualRunDemoSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = Organizer::first() ?? Organizer::create([
            'slug' => 'vira-official',
            'name' => 'VIRA Virtual Running Indonesia',
            'email' => 'hello@vira.id',
            'phone' => '081298765432',
            'bank_name' => 'BCA',
            'bank_account_number' => '8830123456',
            'bank_account_holder' => 'PT Vira Olahraga Digital',
            'commission_rate' => 0,
            'is_verified' => true,
            'is_active' => true,
        ]);

        $event = Event::updateOrCreate([
            'slug' => 'vira-ramadan-2026',
        ], [
            'organizer_id' => $organizer->id,
            'event_code' => 'VIRA26',
            'title' => 'VIRA Ramadan 21K Virtual Run 2026',
            'short_description' => 'Tantangan lari virtual nasional. Lari di mana saja, lacak progres km Anda, dan raih Race Pack & Medali fisik eksklusif dikirim via SPX Express.',
            'full_description' => 'VIRA Ramadan Virtual Run 2026 mengajak seluruh pelari di Indonesia untuk tetap aktif. Selesaikan akumulasi jarak lari Anda kapan saja sebelum periode berakhir, submit link Strava atau Google Drive Anda, dan dapatkan E-Sertifikat resmi serta medali finisher fisik langsung ke depan pintu Anda.',
            'race_location_name' => 'Di Mana Saja (Virtual Run se-Indonesia)',
            'race_location_address' => 'Lintasan / Jalan / Treadmill Pilihan Pelari',
            'event_start_date' => now()->subDays(5),
            'registration_open_date' => now()->subDays(10),
            'registration_close_date' => now()->addDays(20),
            'race_start_date' => now()->subDays(5),
            'race_end_date' => now()->addDays(25),
            'activity_type' => 'RUN',
            'submission_mode' => 'CUMULATIVE',
            'waiver_content' => 'Saya menyatakan sehat jasmani dan siap menyelesaikan lari virtual ini secara jujur dan sportivitas tinggi.',
            'auto_assign_bib' => true,
            'status' => 'published',
        ]);

        $cat10k = EventCategory::updateOrCreate([
            'event_id' => $event->id,
            'name' => '10K Virtual Challenge',
        ], [
            'distance_km' => 10.00,
            'normal_price' => 175000,
            'early_bird_price' => 150000,
            'early_bird_end_date' => now()->addDays(10),
            'quota' => 1000,
            'slots_taken' => 2,
            'bib_prefix' => null, // Pure numeric BIB
            'bib_start_number' => 1001,
            'reserved_bib_numbers' => [],
            'cut_off_time_minutes' => null,
            'min_age' => 10,
            'base_weight_grams' => 500,
            'is_active' => true,
        ]);

        $cat21k = EventCategory::updateOrCreate([
            'event_id' => $event->id,
            'name' => '21K Half Marathon Virtual',
        ], [
            'distance_km' => 21.09,
            'normal_price' => 225000,
            'early_bird_price' => 195000,
            'early_bird_end_date' => now()->addDays(10),
            'quota' => 500,
            'slots_taken' => 0,
            'bib_prefix' => null,
            'bib_start_number' => 1001,
            'reserved_bib_numbers' => [],
            'cut_off_time_minutes' => null,
            'min_age' => 12,
            'base_weight_grams' => 500,
            'is_active' => true,
        ]);

        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $sizeModels = [];
        foreach ($sizes as $s) {
            $sizeModels[$s] = JerseySize::updateOrCreate([
                'event_id' => $event->id,
                'size_name' => $s,
                'gender_type' => 'unisex',
            ], [
                'stock' => 200,
            ]);
        }

        // Add-ons & Merchandise Event
        $addonJersey = EventAddon::updateOrCreate([
            'event_id' => $event->id,
            'name' => 'Jersey Finisher Eksklusif',
        ], [
            'description' => 'Jersey finisher edisi terbatas dengan material dry-fit breathable dan aksen reflektif 3M.',
            'price' => 125000,
            'weight_grams' => 180,
            'is_unlimited' => true,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $addonPin = EventAddon::updateOrCreate([
            'event_id' => $event->id,
            'name' => 'Enamel Pin Finisher 3D Metal',
        ], [
            'description' => 'Pin enamel logam 3D premium berlapis emas antik dengan pengait ganda eksklusif.',
            'price' => 45000,
            'weight_grams' => 50,
            'is_unlimited' => true,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $addonBottle = EventAddon::updateOrCreate([
            'event_id' => $event->id,
            'name' => 'Soft Flask Botol Minum 500ml',
        ], [
            'description' => 'Botol minum lipat BPA-free 500ml khusus pelari dengan gigitan silikon anti-bocor.',
            'price' => 65000,
            'weight_grams' => 90,
            'is_unlimited' => true,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $spxRate = SpxShippingRate::where('destination_district', 'KUTA')->first();
        $shippingFee = $spxRate ? (int) $spxRate->regular_rate_per_kg : 30000;
        $sla = $spxRate ? (int) $spxRate->regular_sla_days : 3;

        // Sample Order 1 (Aditya Pratama - BIB 1001)
        $order1 = Order::updateOrCreate([
            'order_code' => 'ORD-VIRA-DEMO1',
        ], [
            'event_id' => $event->id,
            'customer_name' => 'Aditya Pratama',
            'customer_email' => 'aditya@example.com',
            'customer_phone' => '081234567890',
            'subtotal' => 150000,
            'shipping_cost' => $shippingFee,
            'platform_fee' => 0,
            'payment_gateway_fee' => 0,
            'grand_total' => 150000 + $shippingFee,
            'status' => 'paid',
            'tripay_reference' => 'DEV-T1001',
            'tripay_payment_method' => 'QRIS',
            'paid_at' => now()->subDays(3),
        ]);

        OrderShippingAddress::updateOrCreate([
            'order_id' => $order1->id,
        ], [
            'recipient_name' => 'Aditya Pratama',
            'recipient_phone' => '081234567890',
            'province' => 'BALI',
            'destination_city' => 'KAB. BADUNG',
            'destination_district' => 'KUTA',
            'postal_code' => '80361',
            'address_detail' => 'Jl. Sunset Road No. 88, Seminyak',
            'total_weight_grams' => 500,
            'courier_name' => 'SPX',
            'service_type' => 'Regular',
            'shipping_cost' => $shippingFee,
            'estimated_sla_days' => $sla,
            'tracking_number' => 'SPXID00123456789',
            'shipping_status' => 'shipped',
        ]);

        $p1 = Participant::updateOrCreate([
            'bib_number' => '1001',
        ], [
            'order_id' => $order1->id,
            'event_category_id' => $cat10k->id,
            'jersey_size_id' => $sizeModels['L']->id,
            'full_name' => 'Aditya Pratama',
            'bib_name' => 'ADITYA',
            'email' => 'aditya@example.com',
            'phone' => '081234567890',
            'gender' => 'male',
            'birth_date' => '1995-05-15',
            'qr_token' => (string) Str::uuid(),
            'waiver_accepted' => true,
            'waiver_accepted_at' => now()->subDays(3),
            'total_distance_km' => 6.00,
            'total_duration_seconds' => 2160, // 36 mins
            'finisher_status' => 'in_progress',
        ]);

        OrderItem::updateOrCreate([
            'order_id' => $order1->id,
            'event_category_id' => $cat10k->id,
        ], [
            'quantity' => 1,
            'unit_price' => 150000,
            'total_price' => 150000,
        ]);

        // Prior submission for Aditya (6.0 km)
        ActivitySubmission::updateOrCreate([
            'participant_id' => $p1->id,
            'proof_url' => 'https://strava.app.link/aditya-run-part1',
        ], [
            'activity_date' => now()->subDays(2)->format('Y-m-d'),
            'distance_km' => 6.00,
            'duration_seconds' => 2160,
            'notes' => 'Lari sore santai keliling komplek',
            'validation_status' => 'approved',
        ]);

        // Sample Order 2 (Siti Rahmawati - BIB 1002 - Finisher)
        $order2 = Order::updateOrCreate([
            'order_code' => 'ORD-VIRA-DEMO2',
        ], [
            'event_id' => $event->id,
            'customer_name' => 'Siti Rahmawati',
            'customer_email' => 'siti@example.com',
            'customer_phone' => '081298765432',
            'subtotal' => 150000,
            'shipping_cost' => $shippingFee,
            'platform_fee' => 0,
            'payment_gateway_fee' => 0,
            'grand_total' => 150000 + $shippingFee,
            'status' => 'paid',
            'tripay_reference' => 'DEV-T1002',
            'tripay_payment_method' => 'QRIS',
            'paid_at' => now()->subDays(4),
        ]);

        OrderShippingAddress::updateOrCreate([
            'order_id' => $order2->id,
        ], [
            'recipient_name' => 'Siti Rahmawati',
            'recipient_phone' => '081298765432',
            'province' => 'BALI',
            'destination_city' => 'KAB. BADUNG',
            'destination_district' => 'KUTA',
            'postal_code' => '80361',
            'address_detail' => 'Jl. Raya Legian No. 12',
            'total_weight_grams' => 500,
            'courier_name' => 'SPX',
            'service_type' => 'Regular',
            'shipping_cost' => $shippingFee,
            'estimated_sla_days' => $sla,
            'tracking_number' => 'SPXID00987654321',
            'shipping_status' => 'delivered',
        ]);

        $p2 = Participant::updateOrCreate([
            'bib_number' => '1002',
        ], [
            'order_id' => $order2->id,
            'event_category_id' => $cat10k->id,
            'jersey_size_id' => $sizeModels['M']->id,
            'full_name' => 'Siti Rahmawati',
            'bib_name' => 'SITI R',
            'email' => 'siti@example.com',
            'phone' => '081298765432',
            'gender' => 'female',
            'birth_date' => '1998-08-20',
            'qr_token' => (string) Str::uuid(),
            'waiver_accepted' => true,
            'waiver_accepted_at' => now()->subDays(4),
            'total_distance_km' => 10.00,
            'total_duration_seconds' => 3600, // 60 mins
            'finisher_status' => 'finisher',
            'finished_at' => now()->subDay(),
        ]);

        OrderItem::updateOrCreate([
            'order_id' => $order2->id,
            'event_category_id' => $cat10k->id,
        ], [
            'quantity' => 1,
            'unit_price' => 150000,
            'total_price' => 150000,
        ]);

        ActivitySubmission::updateOrCreate([
            'participant_id' => $p2->id,
            'proof_url' => 'https://strava.app.link/siti-run-finish',
        ], [
            'activity_date' => now()->subDay()->format('Y-m-d'),
            'distance_km' => 10.00,
            'duration_seconds' => 3600,
            'notes' => '10K Finisher!',
            'validation_status' => 'approved',
        ]);
    }
}
