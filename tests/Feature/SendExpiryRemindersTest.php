<?php

namespace Tests\Feature;

use App\Mail\MembershipExpiringSoonMail;
use App\Models\MemberProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendExpiryRemindersTest extends TestCase
{
    use RefreshDatabase;

    private function memberExpiringIn(int $days): User
    {
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'               => $user->id,
            'membership_status'     => 'active',
            'membership_expires_at' => now()->addDays($days),
            'activated_at'          => now()->subMonths(6),
        ]);
        return $user;
    }

    private function inactiveMember(): User
    {
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'               => $user->id,
            'membership_status'     => 'suspended',
            'membership_expires_at' => now()->addDays(10),
            'activated_at'          => now()->subMonths(3),
        ]);
        return $user;
    }

    public function test_sends_email_to_member_expiring_within_30_days(): void
    {
        Mail::fake();
        $user = $this->memberExpiringIn(15);

        $this->artisan('membres:expiry-reminders')->assertSuccessful();

        Mail::assertQueued(MembershipExpiringSoonMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }

    public function test_sends_emails_to_all_members_expiring_within_30_days(): void
    {
        Mail::fake();
        $user1 = $this->memberExpiringIn(5);
        $user2 = $this->memberExpiringIn(29);
        $user3 = $this->memberExpiringIn(1);

        $this->artisan('membres:expiry-reminders')->assertSuccessful();

        Mail::assertQueuedCount(3);
    }

    public function test_does_not_send_email_to_member_expiring_after_30_days(): void
    {
        Mail::fake();
        $this->memberExpiringIn(31);

        $this->artisan('membres:expiry-reminders')->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_does_not_send_email_to_inactive_members(): void
    {
        Mail::fake();
        $this->inactiveMember();

        $this->artisan('membres:expiry-reminders')->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_does_not_send_email_to_already_expired_members(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'               => $user->id,
            'membership_status'     => 'active',
            'membership_expires_at' => now()->subDay(), // déjà expiré
            'activated_at'          => now()->subYear(),
        ]);

        $this->artisan('membres:expiry-reminders')->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_outputs_correct_member_count(): void
    {
        $this->memberExpiringIn(10);
        $this->memberExpiringIn(20);

        $this->artisan('membres:expiry-reminders')
            ->expectsOutput('Rappels mis en file pour 2 membre(s).')
            ->assertSuccessful();
    }

    public function test_outputs_zero_when_no_members_expiring(): void
    {
        $this->artisan('membres:expiry-reminders')
            ->expectsOutput('Rappels mis en file pour 0 membre(s).')
            ->assertSuccessful();
    }
}