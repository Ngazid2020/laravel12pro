<?php

namespace Tests\Feature\Models;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentScopeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    private function payment(string $status): Payment
    {
        return Payment::create([
            'user_id' => $this->user->id,
            'method'  => 'mvola',
            'amount'  => 15000,
            'status'  => $status,
        ]);
    }

    public function test_scopePending_returns_only_pending_payments(): void
    {
        $pending   = $this->payment('pending');
        $validated = $this->payment('validated');
        $rejected  = $this->payment('rejected');

        $result = Payment::pending()->pluck('id');

        $this->assertContains($pending->id, $result);
        $this->assertNotContains($validated->id, $result);
        $this->assertNotContains($rejected->id, $result);
    }

    public function test_scopePending_returns_empty_when_no_pending_payments(): void
    {
        $this->payment('validated');
        $this->payment('rejected');

        $this->assertCount(0, Payment::pending()->get());
    }

    public function test_scopePending_returns_all_pending_when_multiple(): void
    {
        $this->payment('pending');
        $this->payment('pending');
        $this->payment('validated');

        $this->assertCount(2, Payment::pending()->get());
    }
}