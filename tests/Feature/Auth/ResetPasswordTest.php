<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    private function userWithToken(): array
    {
        $user  = User::factory()->create();
        $token = Password::broker()->createToken($user);
        return [$user, $token];
    }

    // ── Rendu ─────────────────────────────────────────────────────────────

    public function test_reset_page_is_accessible_with_token(): void
    {
        [, $token] = $this->userWithToken();

        $this->get(route('password.reset', ['token' => $token]))
            ->assertOk();
    }

    public function test_token_is_set_on_mount(): void
    {
        [, $token] = $this->userWithToken();

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->assertSet('token', $token);
    }

    // ── Validation ────────────────────────────────────────────────────────

    public function test_password_minimum_length_is_8(): void
    {
        [$user, $token] = $this->userWithToken();

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'short')
            ->set('passwordConfirmation', 'short')
            ->call('save')
            ->assertHasErrors(['password']);
    }

    public function test_passwords_must_match(): void
    {
        [$user, $token] = $this->userWithToken();

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'NewPassword@123')
            ->set('passwordConfirmation', 'DifferentPassword@123')
            ->call('save')
            ->assertHasErrors(['password']);
    }

    public function test_email_is_required(): void
    {
        [, $token] = $this->userWithToken();

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', '')
            ->set('password', 'NewPassword@123')
            ->set('passwordConfirmation', 'NewPassword@123')
            ->call('save')
            ->assertHasErrors(['email']);
    }

    // ── Reset réussi ─────────────────────────────────────────────────────

    public function test_valid_reset_redirects_to_login(): void
    {
        [$user, $token] = $this->userWithToken();

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', 'NewPassword@123')
            ->set('passwordConfirmation', 'NewPassword@123')
            ->call('save')
            ->assertRedirect(route('member.login'));
    }

    public function test_valid_reset_actually_changes_the_password(): void
    {
        [$user, $token] = $this->userWithToken();
        $newPassword    = 'NewPassword@123';

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', $user->email)
            ->set('password', $newPassword)
            ->set('passwordConfirmation', $newPassword)
            ->call('save');

        $this->assertTrue(Hash::check($newPassword, $user->fresh()->password));
    }

    // ── Token invalide ────────────────────────────────────────────────────

    public function test_invalid_token_adds_email_error(): void
    {
        [$user] = $this->userWithToken();

        Livewire::test(ResetPassword::class, ['token' => 'invalid-token-xyz'])
            ->set('email', $user->email)
            ->set('password', 'NewPassword@123')
            ->set('passwordConfirmation', 'NewPassword@123')
            ->call('save')
            ->assertHasErrors(['email']);
    }

    public function test_wrong_email_for_valid_token_adds_error(): void
    {
        [, $token] = $this->userWithToken();

        Livewire::test(ResetPassword::class, ['token' => $token])
            ->set('email', 'wrong@example.km')
            ->set('password', 'NewPassword@123')
            ->set('passwordConfirmation', 'NewPassword@123')
            ->call('save')
            ->assertHasErrors(['email']);
    }
}