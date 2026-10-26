<?php

namespace Tests\Feature;

use App\Filament\Widgets\EventStatsOverview;
use App\Models\Organizer;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentPanelAccessTest extends TestCase
{
    use RefreshDatabase;

    private function getAdminPath(): string
    {
        return '/'.ltrim(Filament::getPanel('admin')->getPath(), '/');
    }

    public function test_guest_is_redirected_to_login_when_visiting_admin(): void
    {
        $adminPath = $this->getAdminPath();
        $response = $this->get($adminPath);

        $response->assertRedirect("{$adminPath}/login");
    }

    public function test_superadmin_can_access_admin_panel(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superadmin)->get($this->getAdminPath());

        $response->assertOk();
    }

    public function test_organizer_user_can_access_organizer_panel(): void
    {
        $organizer = Organizer::create([
            'name' => 'Test EO',
            'slug' => 'test-eo',
            'email' => 'test@eo.com',
            'phone' => '08123456789',
            'is_verified' => true,
            'is_active' => true,
        ]);
        $eoUser = User::factory()->create([
            'role' => 'organizer_owner',
            'organizer_id' => $organizer->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($eoUser)->get('/organizer');

        $response->assertOk();
    }

    public function test_organizer_user_visiting_admin_panel_is_redirected_to_admin_login_without_403_trap(): void
    {
        $organizer = Organizer::create([
            'name' => 'Test EO 2',
            'slug' => 'test-eo-2',
            'email' => 'test2@eo.com',
            'phone' => '08123456780',
            'is_verified' => true,
            'is_active' => true,
        ]);
        $eoUser = User::factory()->create([
            'role' => 'organizer_owner',
            'organizer_id' => $organizer->id,
            'is_active' => true,
        ]);

        $adminPath = $this->getAdminPath();
        $response = $this->actingAs($eoUser)->get($adminPath);

        // Harus diarahkan ke login admin, bukan error 403
        $response->assertRedirect("{$adminPath}/login");
        $this->assertGuest();
    }

    public function test_internal_admin_can_access_admin_panel(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->get($this->getAdminPath());

        $response->assertOk();
    }

    public function test_admin_can_access_all_virtual_run_management_resources(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $adminPath = $this->getAdminPath();

        $this->actingAs($admin)->get("{$adminPath}/events")->assertOk();
        $this->actingAs($admin)->get("{$adminPath}/activity-submissions")->assertOk();
        $this->actingAs($admin)->get("{$adminPath}/participants")->assertOk();
        $this->actingAs($admin)->get("{$adminPath}/orders")->assertOk();
        $this->actingAs($admin)->get("{$adminPath}/organizers")->assertNotFound();
        $this->actingAs($admin)->get("{$adminPath}/organizer-payouts")->assertNotFound();
    }

    public function test_admin_dashboard_renders_navigation_groups_and_widgets(): void
    {
        $superadmin = User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superadmin)->get($this->getAdminPath());

        $response->assertOk();
        $response->assertSee('Virtual Run &amp; Event', false);
        $response->assertSee('Pendaftaran &amp; Peserta', false);
        $response->assertDontSee('Sistem &amp; Pengaturan', false);
        $response->assertSee('Validasi Aktivitas Lari');
        $response->assertSee('Data Peserta &amp; e-BIB', false);

        Livewire::actingAs($superadmin)
            ->test(EventStatsOverview::class)
            ->assertSee('Total Pelari Terdaftar')
            ->assertSee('Total Pendapatan Tiket')
            ->assertSee('Validasi Aktivitas Lari');
    }

    public function test_user_can_login_with_username_or_email(): void
    {
        $user = User::factory()->create([
            'username' => 'bangnahan',
            'email' => 'bangnahan@test.com',
            'password' => bcrypt('@Pekanbaru12'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $this->assertTrue(auth()->attempt(['username' => 'bangnahan', 'password' => '@Pekanbaru12']));
        auth()->logout();

        $this->assertTrue(auth()->attempt(['email' => 'bangnahan@test.com', 'password' => '@Pekanbaru12']));
        auth()->logout();
    }
}
