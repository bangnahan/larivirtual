<?php

namespace Tests\Feature;

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
