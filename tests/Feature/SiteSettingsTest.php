<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteSettingsPage;
use App\Models\SiteSetting;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function getAdminPath(): string
    {
        return '/'.ltrim(Filament::getPanel('admin')->getPath(), '/');
    }

    public function test_guest_is_redirected_when_accessing_site_settings_page(): void
    {
        $adminPath = $this->getAdminPath();
        $response = $this->get("{$adminPath}/site-settings");

        $response->assertRedirect("{$adminPath}/login");
    }

    public function test_superadmin_can_access_and_update_site_settings_via_livewire(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $this->actingAs($superadmin);

        Livewire::test(SiteSettingsPage::class)
            ->assertSuccessful()
            ->set('data.top_bar_enabled', true)
            ->set('data.top_bar_badge', 'UPDATE')
            ->set('data.top_bar_message', 'Pendaftaran Borobudur Virtual Marathon Dibuka!')
            ->set('data.top_bar_button_text', 'Daftar Di Sini')
            ->set('data.top_bar_button_url', '/events/borobudur/register')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('UPDATE', SiteSetting::get('top_bar_badge'));
        $this->assertEquals('Pendaftaran Borobudur Virtual Marathon Dibuka!', SiteSetting::get('top_bar_message'));
        $this->assertEquals('Daftar Di Sini', SiteSetting::get('top_bar_button_text'));

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('UPDATE');
        $response->assertSee('Pendaftaran Borobudur Virtual Marathon Dibuka!');
        $response->assertSee('Daftar Di Sini');
    }

    public function test_homepage_hides_top_bar_when_disabled(): void
    {
        SiteSetting::set('top_bar_enabled', '0');
        SiteSetting::set('top_bar_badge', 'FLASH_TEST_BADGE');

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertDontSee('FLASH_TEST_BADGE');
    }
}
