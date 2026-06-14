<?php

namespace Tests\Feature\Security;

use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests d'escalade de privilèges.
 * Vérifie qu'un membre ne peut pas accéder aux zones et actions réservées aux admins.
 */
class PrivilegeEscalationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    private function memberUser(): User
    {
        /** @var User $user */
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'           => $user->id,
            'membership_status' => 'active',
            'activated_at'      => now()->subMonths(3),
        ]);
        return $user;
    }

    // ── Filament /admin ────────────────────────────────────────────────────

    public function test_regular_member_cannot_access_admin_panel(): void
    {
        $member = $this->memberUser();

        $this->actingAs($member)
            ->get('/admin')
            ->assertForbidden(); // canAccessPanel() retourne false → Filament abort 403
    }

    public function test_guest_cannot_access_admin_panel(): void
    {
        $this->get('/admin')
            ->assertRedirect();
    }

    public function test_admin_can_access_admin_panel(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['password' => Hash::make('Admin@2026!')]);
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }

    // ── Routes membres — escalade par modification de URL ─────────────────

    public function test_member_without_profile_is_blocked_by_middleware(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        // Pas de MemberProfile créé

        $this->actingAs($user)
            ->get(route('membre.dashboard'))
            ->assertForbidden();
    }

    public function test_excluded_member_is_blocked(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'           => $user->id,
            'membership_status' => 'excluded',
            'activated_at'      => now()->subYear(),
        ]);

        $this->actingAs($user)
            ->get(route('membre.dashboard'))
            ->assertForbidden();
    }

    public function test_suspended_member_is_redirected_to_payments_on_other_routes(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'           => $user->id,
            'membership_status' => 'suspended',
            'activated_at'      => now()->subMonths(6),
        ]);

        $this->actingAs($user)
            ->get(route('membre.dashboard'))
            ->assertRedirect(route('membre.payments'));
    }

    public function test_suspended_member_can_still_access_payments(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'           => $user->id,
            'membership_status' => 'suspended',
            'activated_at'      => now()->subMonths(6),
        ]);

        $this->actingAs($user)
            ->get(route('membre.payments'))
            ->assertOk();
    }

    // ── canAccessPanel() ──────────────────────────────────────────────────

    public function test_member_canAccessPanel_returns_false(): void
    {
        $member = $this->memberUser();

        $this->assertFalse($member->canAccessPanel(
            app(\Filament\Panel::class)
        ));
    }

    public function test_admin_canAccessPanel_returns_true(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertTrue($admin->canAccessPanel(
            app(\Filament\Panel::class)
        ));
    }
}