<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\MemberLogin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MemberLoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    }

    private function regularMember(string $password = 'Membre@2026!'): User
    {
        return User::factory()->create(['password' => Hash::make($password)]);
    }

    // ── Rendu ─────────────────────────────────────────────────────────────

    public function test_login_page_is_accessible_for_guests(): void
    {
        $this->get(route('member.login'))->assertOk();
    }

    public function test_login_page_redirects_authenticated_users(): void
    {
        $user = $this->regularMember();
        $this->actingAs($user)
            ->get(route('member.login'))
            ->assertRedirect();
    }

    // ── Validation ────────────────────────────────────────────────────────

    public function test_email_is_required(): void
    {
        Livewire::test(MemberLogin::class)
            ->set('email', '')
            ->set('password', 'SomePassword1!')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    public function test_email_must_be_valid_format(): void
    {
        Livewire::test(MemberLogin::class)
            ->set('email', 'not-an-email')
            ->set('password', 'SomePassword1!')
            ->call('login')
            ->assertHasErrors(['email']);
    }

    public function test_password_is_required(): void
    {
        Livewire::test(MemberLogin::class)
            ->set('email', 'test@example.km')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['password']);
    }

    public function test_password_must_be_at_least_8_characters(): void
    {
        Livewire::test(MemberLogin::class)
            ->set('email', 'test@example.km')
            ->set('password', 'short')
            ->call('login')
            ->assertHasErrors(['password']);
    }

    // ── Authentification ─────────────────────────────────────────────────

    public function test_valid_credentials_redirect_member_to_dashboard(): void
    {
        $password = 'Membre@2026!';
        $user     = $this->regularMember($password);

        Livewire::test(MemberLogin::class)
            ->set('email', $user->email)
            ->set('password', $password)
            ->call('login')
            ->assertRedirect(route('membre.dashboard'));
    }

    public function test_admin_credentials_redirect_to_admin_panel(): void
    {
        $password = 'Admin@2026!';
        $admin    = User::factory()->create(['password' => Hash::make($password)]);
        $admin->assignRole('admin');

        Livewire::test(MemberLogin::class)
            ->set('email', $admin->email)
            ->set('password', $password)
            ->call('login')
            ->assertRedirect('/admin');
    }

    public function test_wrong_password_adds_error_and_does_not_authenticate(): void
    {
        $user = $this->regularMember('Membre@2026!');

        Livewire::test(MemberLogin::class)
            ->set('email', $user->email)
            ->set('password', 'WrongPassword!')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_unknown_email_adds_error(): void
    {
        Livewire::test(MemberLogin::class)
            ->set('email', 'nobody@example.km')
            ->set('password', 'SomePassword1!')
            ->call('login')
            ->assertHasErrors(['email']);
    }
}