<?php

namespace Tests\Feature;

use App\Filament\Pages\SystemUpdatePage;
use App\Models\Organizer;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SystemUpdatePageTest extends TestCase
{
    use RefreshDatabase;

    private function getAdminPath(): string
    {
        return '/'.ltrim(Filament::getPanel('admin')->getPath(), '/');
    }

    public function test_guest_is_redirected_when_accessing_system_update_page(): void
    {
        $adminPath = $this->getAdminPath();
        $response = $this->get("{$adminPath}/system-update");

        $response->assertRedirect("{$adminPath}/login");
    }

    public function test_organizer_cannot_access_system_update_page(): void
    {
        $organizer = Organizer::create([
            'name' => 'Test EO System',
            'slug' => 'test-eo-sys',
            'email' => 'sys@eo.com',
            'phone' => '08123456789',
            'is_verified' => true,
            'is_active' => true,
        ]);

        $eoUser = User::factory()->create([
            'role' => 'organizer_owner',
            'organizer_id' => $organizer->id,
            'is_active' => true,
        ]);

        $adminPath = $this->getAdminPath();
        $response = $this->actingAs($eoUser)->get("{$adminPath}/system-update");

        $response->assertRedirect("{$adminPath}/login");
    }

    public function test_regular_admin_is_forbidden_from_system_update_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $adminPath = $this->getAdminPath();
        $response = $this->actingAs($admin)->get("{$adminPath}/system-update");

        $response->assertForbidden();
    }

    public function test_superadmin_can_access_system_update_page(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $adminPath = $this->getAdminPath();
        $response = $this->actingAs($superadmin)->get("{$adminPath}/system-update");

        $response->assertOk();
        $response->assertSee('Pembaruan Sistem &amp; GitHub Sync', false);
        $response->assertSee('Branch Aktif');
        $response->assertSee('Cek Pembaruan GitHub');
        $response->assertSee('Tarik &amp; Perbarui Sistem', false);
    }

    public function test_superadmin_can_trigger_check_for_updates(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        Livewire::actingAs($superadmin)
            ->test(SystemUpdatePage::class)
            ->call('checkForUpdates')
            ->assertHasNoErrors()
            ->assertSet('statusMessage', 'Sistem sudah menggunakan versi terbaru (Up to date).');
    }
}
