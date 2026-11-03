<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Organizer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BhinnekaEventSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = Organizer::first() ?? Organizer::create([
            'slug' => 'budayarun-official',
            'name' => 'Budaya Run Indonesia',
            'email' => 'admin@budayarun.com',
            'phone' => '081916444458',
            'bank_name' => 'BCA',
            'bank_account_number' => '8830123456',
            'bank_account_holder' => 'Budaya Run',
            'commission_rate' => 0,
            'is_verified' => true,
            'is_active' => true,
        ]);

        $event = Event::updateOrCreate([
            'slug' => 'bhinneka',
        ], [
            'organizer_id' => $organizer->id,
            'event_code' => 'BTI42K',
            'title' => 'BHINNEKA TUNGGAL IKA Virtual Run 42.195 KM',
            'short_description' => 'Mari kita rayakan persatuan Indonesia melalui event Bhinneka Tunggal Ika Virtual Run 42.195 KM. Berbeda-beda tetap bersatu adalah jati diri kita. Lari bisa dicicil atau sekali submit.',
            'full_description' => 'Jadilah bagian tak terpisahkan dari perayaan persatuan bangsa melalui event Bhinneka Tunggal Ika Virtual Run. Berbeda beda tetap tetap bersatu adalah jati diri kita. Lari sejauh 42,195KM dapat di lakukan dalam 1 waktu atau cicil di lakukan beberapa kali dalam rentang bulan September s.d. Desember 2026. Tidak ada batasan minimal waktu tempuh, jarak juga dapat di tempuh dengan berjalan kaki untuk pemula tidak harus berlari.',
            'banner_path' => 'events/banners/bhinneka-banner.jpg',
            'race_location_name' => 'Di Mana Saja (Virtual Run Seluruh Indonesia & Luar Negeri)',
            'race_location_address' => 'Jalan Raya, Taman, Jogging Track, atau Treadmill Pilihan Peserta',
            'race_location_map_url' => 'https://maps.google.com',
            'event_start_date' => Carbon::create(2026, 9, 21, 0, 0, 0),
            'event_end_date' => Carbon::create(2026, 12, 31, 23, 59, 59),
            'registration_open_date' => Carbon::create(2026, 9, 20, 0, 0, 0),
            'registration_close_date' => Carbon::create(2026, 11, 10, 23, 59, 59),
            'race_start_date' => Carbon::create(2026, 9, 21, 0, 0, 0),
            'race_end_date' => Carbon::create(2026, 12, 31, 23, 59, 59),
            'racepack_delivery_start_date' => Carbon::create(2026, 10, 25, 0, 0, 0),
            'racepack_delivery_end_date' => Carbon::create(2026, 10, 29, 23, 59, 59),
            'racepack_delivery_schedule' => 'Mulai 25 Oktober 2026 serentak via SPX Express',
            'racepack_delivery_notes' => 'Pengiriman Race Pack (Medali dan Jersey) dilakukan secara serentak via SPX Express setelah masa produksi selesai.',
            'submission_mode' => 'CUMULATIVE',
            'activity_type' => 'RUN',
            'status' => 'published',
            'auto_assign_bib' => true,
            'meta_pixel_id' => '1014082418078174',
            'waiver_content' => 'Dengan ini saya menyatakan bahwa saya dalam kondisi fisik yang sehat dan siap menyelesaikan lari virtual Bhinneka Tunggal Ika 42.195 KM ini secara jujur dan berdaya.',
        ]);

        $this->command?->info("Event Bhinneka berhasil disiapkan: {$event->title} (ID: {$event->id})");
    }
}
