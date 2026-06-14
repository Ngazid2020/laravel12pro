<?php

namespace Tests\Feature\Security;

use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de protection des routes par authentification.
 * Vérifie que chaque route protégée redirige correctement les visiteurs non authentifiés.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // ── Routes membres exigent authentification ───────────────────────────

    #[\PHPUnit\Framework\Attributes\DataProvider('memberRouteProvider')]
    public function test_guest_is_redirected_to_login_on_member_routes(string $routeName): void
    {
        $this->get(route($routeName))
            ->assertRedirect(route('member.login'));
    }

    public static function memberRouteProvider(): array
    {
        return [
            'dashboard'      => ['membre.dashboard'],
            'profile'        => ['membre.profile'],
            'directory'      => ['membre.directory'],
            'trainings'      => ['membre.trainings'],
            'opportunities'  => ['membre.opportunities'],
            'events'         => ['membre.events'],
            'payments'       => ['membre.payments'],
            'recommendations'=> ['membre.recommendations'],
            'mentoring'      => ['membre.mentoring'],
            'progress'       => ['membre.progress'],
            'contacts'       => ['membre.contacts'],
            'network'        => ['membre.network'],
        ];
    }

    // ── Routes publiques accessibles sans auth ────────────────────────────

    public function test_home_page_is_accessible_for_guests(): void
    {
        $this->get(route('home'))->assertOk();
    }

    public function test_login_page_is_accessible_for_guests(): void
    {
        $this->get(route('member.login'))->assertOk();
    }

    public function test_forgot_password_page_is_accessible_for_guests(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    // ── Routes publiques redirigent les membres déjà connectés ───────────

    public function test_login_page_redirects_authenticated_member(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'           => $user->id,
            'membership_status' => 'active',
            'activated_at'      => now()->subMonths(2),
        ]);

        $this->actingAs($user)
            ->get(route('member.login'))
            ->assertRedirect();
    }

    // ── Session regeneration après connexion ──────────────────────────────

    public function test_logout_clears_session(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'           => $user->id,
            'membership_status' => 'active',
            'activated_at'      => now()->subMonths(2),
        ]);

        $this->actingAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    // ── Middleware EnsureActiveMember — réponses selon le statut ──────────

    public function test_active_member_can_access_member_dashboard(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'           => $user->id,
            'membership_status' => 'active',
            'activated_at'      => now()->subMonths(2),
        ]);

        $this->actingAs($user)
            ->get(route('membre.dashboard'))
            ->assertOk();
    }

    public function test_user_without_profile_gets_403(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('membre.dashboard'))
            ->assertForbidden();
    }
}