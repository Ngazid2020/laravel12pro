<?php

namespace Tests\Unit;

use App\Models\EventRegistration;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    public function test_isCheckedIn_returns_false_when_checked_in_at_is_null(): void
    {
        $registration = new EventRegistration(['checked_in_at' => null]);

        $this->assertFalse($registration->isCheckedIn());
    }

    public function test_isCheckedIn_returns_true_when_checked_in_at_is_set(): void
    {
        $registration = new EventRegistration(['checked_in_at' => now()]);

        $this->assertTrue($registration->isCheckedIn());
    }
}