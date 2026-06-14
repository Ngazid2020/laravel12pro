<?php

namespace Tests\Unit;

use App\Models\Payment;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    public function test_isPending_returns_true_when_status_is_pending(): void
    {
        $payment = new Payment(['status' => 'pending']);

        $this->assertTrue($payment->isPending());
    }

    public function test_isPending_returns_false_when_status_is_validated(): void
    {
        $payment = new Payment(['status' => 'validated']);

        $this->assertFalse($payment->isPending());
    }

    public function test_isPending_returns_false_when_status_is_rejected(): void
    {
        $payment = new Payment(['status' => 'rejected']);

        $this->assertFalse($payment->isPending());
    }

    public function test_isValidated_returns_true_when_status_is_validated(): void
    {
        $payment = new Payment(['status' => 'validated']);

        $this->assertTrue($payment->isValidated());
    }

    public function test_isValidated_returns_false_when_status_is_pending(): void
    {
        $payment = new Payment(['status' => 'pending']);

        $this->assertFalse($payment->isValidated());
    }

    public function test_isValidated_returns_false_when_status_is_rejected(): void
    {
        $payment = new Payment(['status' => 'rejected']);

        $this->assertFalse($payment->isValidated());
    }
}