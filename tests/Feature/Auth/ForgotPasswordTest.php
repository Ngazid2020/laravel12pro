<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\ForgotPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    // ── Rendu ─────────────────────────────────────────────────────────────

    public function test_forgot_password_page_is_accessible(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    // ── Validation ────────────────────────────────────────────────────────

    public function test_email_is_required(): void
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', '')
            ->call('send')
            ->assertHasErrors(['email']);
    }

    public function test_email_must_be_valid_format(): void
    {
        Livewire::test(ForgotPassword::class)
            ->set('email', 'not-an-email')
            ->call('send')
            ->assertHasErrors(['email']);
    }

    // ── Envoi du lien ────────────────────────────────────────────────────

    public function test_sent_flag_becomes_true_after_valid_submission(): void
    {
        User::factory()->create(['email' => 'fatouma@example.km']);

        Livewire::test(ForgotPassword::class)
            ->set('email', 'fatouma@example.km')
            ->call('send')
            ->assertSet('sent', true);
    }

    public function test_sent_flag_becomes_true_even_for_unknown_email(): void
    {
        // Anti-énumération : même comportement si l'email n'existe pas
        Livewire::test(ForgotPassword::class)
            ->set('email', 'nobody@example.km')
            ->call('send')
            ->assertSet('sent', true);
    }

    public function test_no_validation_errors_after_valid_submission(): void
    {
        User::factory()->create(['email' => 'hamid@example.km']);

        Livewire::test(ForgotPassword::class)
            ->set('email', 'hamid@example.km')
            ->call('send')
            ->assertHasNoErrors();
    }
}