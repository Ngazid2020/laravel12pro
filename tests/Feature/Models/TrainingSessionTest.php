<?php

namespace Tests\Feature\Models;

use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingSessionTest extends TestCase
{
    use RefreshDatabase;

    private User $trainer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->trainer = User::factory()->create();
    }

    private function makeTraining(array $attrs = []): Training
    {
        return Training::create(array_merge([
            'title'      => 'Formation test',
            'trainer_id' => $this->trainer->id,
        ], $attrs));
    }

    private function makeSession(Training $training, array $attrs = []): TrainingSession
    {
        return TrainingSession::create(array_merge([
            'training_id' => $training->id,
            'starts_at'   => now()->addDays(3),
            'ends_at'     => now()->addDays(3)->addHours(3),
            'status'      => 'scheduled',
        ], $attrs));
    }

    // ── isFull ────────────────────────────────────────────────────────────

    public function test_isFull_returns_false_when_training_has_no_capacity(): void
    {
        $session = $this->makeSession($this->makeTraining(['capacity' => null]));

        $this->assertFalse($session->isFull());
    }

    public function test_isFull_returns_false_when_under_capacity(): void
    {
        $training = $this->makeTraining(['capacity' => 5]);
        $session  = $this->makeSession($training);
        $user     = User::factory()->create();

        TrainingEnrollment::create([
            'training_session_id' => $session->id,
            'user_id'             => $user->id,
            'status'              => 'enrolled',
        ]);

        $this->assertFalse($session->isFull());
    }

    public function test_isFull_returns_true_when_at_capacity(): void
    {
        $training = $this->makeTraining(['capacity' => 2]);
        $session  = $this->makeSession($training);

        foreach (User::factory()->count(2)->create() as $user) {
            TrainingEnrollment::create([
                'training_session_id' => $session->id,
                'user_id'             => $user->id,
                'status'              => 'enrolled',
            ]);
        }

        $this->assertTrue($session->isFull());
    }

    // ── spotsLeft ─────────────────────────────────────────────────────────

    public function test_spotsLeft_returns_null_when_no_capacity(): void
    {
        $session = $this->makeSession($this->makeTraining(['capacity' => null]));

        $this->assertNull($session->spotsLeft());
    }

    public function test_spotsLeft_returns_full_capacity_when_no_enrollments(): void
    {
        $session = $this->makeSession($this->makeTraining(['capacity' => 10]));

        $this->assertSame(10, $session->spotsLeft());
    }

    public function test_spotsLeft_decreases_with_each_enrollment(): void
    {
        $training = $this->makeTraining(['capacity' => 5]);
        $session  = $this->makeSession($training);

        foreach (User::factory()->count(3)->create() as $user) {
            TrainingEnrollment::create([
                'training_session_id' => $session->id,
                'user_id'             => $user->id,
                'status'              => 'enrolled',
            ]);
        }

        $this->assertSame(2, $session->spotsLeft());
    }

    public function test_spotsLeft_returns_zero_when_at_capacity(): void
    {
        $training = $this->makeTraining(['capacity' => 2]);
        $session  = $this->makeSession($training);

        foreach (User::factory()->count(2)->create() as $user) {
            TrainingEnrollment::create([
                'training_session_id' => $session->id,
                'user_id'             => $user->id,
                'status'              => 'enrolled',
            ]);
        }

        $this->assertSame(0, $session->spotsLeft());
    }

    // ── attendeesCount ────────────────────────────────────────────────────

    public function test_attendeesCount_counts_only_attended_enrollments(): void
    {
        $training = $this->makeTraining();
        $session  = $this->makeSession($training);
        [$u1, $u2, $u3] = User::factory()->count(3)->create()->all();

        TrainingEnrollment::create(['training_session_id' => $session->id, 'user_id' => $u1->id, 'status' => 'attended']);
        TrainingEnrollment::create(['training_session_id' => $session->id, 'user_id' => $u2->id, 'status' => 'attended']);
        TrainingEnrollment::create(['training_session_id' => $session->id, 'user_id' => $u3->id, 'status' => 'enrolled']);

        $this->assertSame(2, $session->attendeesCount());
    }
}