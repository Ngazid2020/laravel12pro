<?php

namespace Tests\Unit;

use App\Models\MemberProfile;
use Tests\TestCase;

class MemberProfileTest extends TestCase
{
    // ── isActive ────────────────────────────────────────────────────────────

    public function test_isActive_returns_true_when_active_with_no_expiry(): void
    {
        $profile = new MemberProfile([
            'membership_status'    => 'active',
            'membership_expires_at'=> null,
        ]);

        $this->assertTrue($profile->isActive());
    }

    public function test_isActive_returns_true_when_active_with_future_expiry(): void
    {
        $profile = new MemberProfile([
            'membership_status'    => 'active',
            'membership_expires_at'=> now()->addMonth(),
        ]);

        $this->assertTrue($profile->isActive());
    }

    public function test_isActive_returns_false_when_active_but_expired(): void
    {
        $profile = new MemberProfile([
            'membership_status'    => 'active',
            'membership_expires_at'=> now()->subDay(),
        ]);

        $this->assertFalse($profile->isActive());
    }

    public function test_isActive_returns_false_when_status_is_suspended(): void
    {
        $profile = new MemberProfile([
            'membership_status'    => 'suspended',
            'membership_expires_at'=> null,
        ]);

        $this->assertFalse($profile->isActive());
    }

    public function test_isActive_returns_false_when_status_is_candidate(): void
    {
        $profile = new MemberProfile([
            'membership_status'    => 'candidate',
            'membership_expires_at'=> null,
        ]);

        $this->assertFalse($profile->isActive());
    }

    // ── isSuspended ─────────────────────────────────────────────────────────

    public function test_isSuspended_returns_true_when_suspended(): void
    {
        $profile = new MemberProfile(['membership_status' => 'suspended']);

        $this->assertTrue($profile->isSuspended());
    }

    public function test_isSuspended_returns_false_when_active(): void
    {
        $profile = new MemberProfile(['membership_status' => 'active']);

        $this->assertFalse($profile->isSuspended());
    }

    // ── statusLabel ──────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\DataProvider('statusLabelProvider')]
    public function test_statusLabel_returns_correct_label(string $status, string $expected): void
    {
        $profile = new MemberProfile(['membership_status' => $status]);

        $this->assertSame($expected, $profile->statusLabel());
    }

    public static function statusLabelProvider(): array
    {
        return [
            'active'    => ['active',    'Actif'],
            'candidate' => ['candidate', 'Candidat'],
            'suspended' => ['suspended', 'Suspendu'],
            'excluded'  => ['excluded',  'Exclu'],
            'alumni'    => ['alumni',    'Alumni'],
            'unknown'   => ['foo',       'Inconnu'],
        ];
    }

    // ── statusColor ──────────────────────────────────────────────────────────

    #[\PHPUnit\Framework\Attributes\DataProvider('statusColorProvider')]
    public function test_statusColor_returns_correct_badge(string $status, string $expected): void
    {
        $profile = new MemberProfile(['membership_status' => $status]);

        $this->assertSame($expected, $profile->statusColor());
    }

    public static function statusColorProvider(): array
    {
        return [
            'active'    => ['active',    'badge-success'],
            'candidate' => ['candidate', 'badge-info'],
            'suspended' => ['suspended', 'badge-warning'],
            'excluded'  => ['excluded',  'badge-error'],
            'alumni'    => ['alumni',    'badge-ghost'],
        ];
    }
}