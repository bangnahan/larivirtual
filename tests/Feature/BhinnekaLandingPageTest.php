<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Organizer;
use Database\Seeders\BhinnekaEventSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BhinnekaLandingPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(BhinnekaEventSeeder::class);
    }

    public function test_bhinneka_landing_page_renders_successfully(): void
    {
        $response = $this->get('/bhinneka');

        $response->assertStatus(200);
        $response->assertSee('BHINNEKA TUNGGAL IKA');
        $response->assertSee('42.195 KM');
        $response->assertSee('6281916444458');
        $response->assertSee('events/bhinneka/register');
        $response->assertSee('1014082418078174');
        $response->assertSee('2512919952549398');
        $response->assertSee('Jersey Lengan Pendek');
        $response->assertSee('Jersey Lengan Panjang');
        $response->assertSee('Jersey Singlet Tanpa Kerah');
        $response->assertSee('Privacy Policy');
        $response->assertSee('Term &amp; Condition', false);
        $response->assertSee('Contact Us');
        $response->assertSee('showPrivacyModal');
        $response->assertSee('showTermsModal');
        $response->assertSee('showContactModal');
    }

    public function test_homepage_serves_larivirtual_hub_with_featured_bhinneka_event(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Run Anywhere. Race Together.', false);
        $response->assertSee('BHINNEKA TUNGGAL IKA');
        $response->assertSee('Cek E-BIB');
        $response->assertSee('Submit Hasil Lari');
    }

    public function test_featured_event_can_be_selected_and_rendered_on_homepage(): void
    {
        $organizer = Organizer::first();

        $newEvent = Event::create([
            'organizer_id' => $organizer->id,
            'title' => 'Borobudur Heritage Virtual Marathon',
            'slug' => 'borobudur-heritage-vm',
            'organizer_name' => 'Borobudur Marathon Club',
            'status' => 'published',
            'event_code' => 'BHVM26',
            'activity_type' => 'running',
            'submission_mode' => 'multiple',
            'race_location_name' => 'Magelang, Jawa Tengah',
            'is_featured' => true,
            'event_start_date' => now()->addDays(5),
            'event_end_date' => now()->addDays(30),
            'registration_open_date' => now()->subDays(5),
            'registration_close_date' => now()->addDays(20),
        ]);

        $bhinneka = Event::where('slug', 'bhinneka')->first();
        $this->assertFalse((bool) $bhinneka->fresh()->is_featured);
        $this->assertTrue((bool) $newEvent->fresh()->is_featured);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Borobudur Heritage Virtual Marathon');
        $response->assertSee('Borobudur Marathon Club');
        $response->assertSee('Event Virtual Run Lainnya');
        $response->assertSee('BHINNEKA TUNGGAL IKA');
    }

    public function test_bhinneka_registration_wizard_page_is_accessible(): void
    {
        $response = $this->get('/events/bhinneka/register');

        $response->assertStatus(200);
        $response->assertSee('BHINNEKA TUNGGAL IKA');
    }

    public function test_privacy_policy_page_renders_successfully(): void
    {
        $response = $this->get('/privacy-policy');

        $response->assertStatus(200);
        $response->assertSee('Kebijakan Privasi (Privacy Policy)');
        $response->assertSee('Budaya Run Indonesia');
        $response->assertSee('0819-1644-4458');
    }

    public function test_terms_and_conditions_page_renders_successfully(): void
    {
        $response = $this->get('/terms-and-conditions');

        $response->assertStatus(200);
        $response->assertSee('Syarat &amp; Ketentuan (Terms &amp; Conditions)', false);
        $response->assertSee('42.195 KM');
        $response->assertSee('non-refundable');
    }

    public function test_contact_page_renders_successfully(): void
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
        $response->assertSee('Hubungi Kami (Contact Us)');
        $response->assertSee('admin@budayarun.com');
        $response->assertSee('6281916444458');
    }
}
