<?php

namespace Tests\Feature\Models;

use App\Models\Level;
use App\Models\MemberProfile;
use App\Models\PointEntry;
use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LevelTest extends TestCase
{
    use RefreshDatabase;

    private Level $level;
    private User  $trainer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->trainer = User::factory()->create();

        $this->level = Level::create([
            'name'                => 'Entrepreneur Confirmé',
            'slug'                => 'entrepreneur-confirme',
            'min_points'          => 100,
            'required_trainings'  => 2,
            'required_months'     => 3,
            'order'               => 1,
        ]);
    }

    private function memberWithProfile(int $monthsAgo = 4): User
    {
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'           => $user->id,
            'membership_status' => 'active',
            'activated_at'      => now()->subMonths($monthsAgo),
        ]);
        return $user;
    }

    private function givePoints(User $user, int $points): void
    {
        PointEntry::create([
            'user_id'     => $user->id,
            'source'      => 'manual',
            'points'      => $points,
            'description' => 'Test credit',
        ]);
    }

    private function giveAttendedTrainings(User $user, int $count): void
    {
        $training = Training::create([
            'title'      => 'Formation test',
            'trainer_id' => $this->trainer->id,
        ]);

        for ($i = 0; $i < $count; $i++) {
            $session = TrainingSession::create([
                'training_id' => $training->id,
                'starts_at'   => now()->subDays($i + 1),
                'ends_at'     => now()->subDays($i + 1)->addHours(2),
                'status'      => 'completed', // valide : scheduled|ongoing|completed|cancelled
            ]);
            TrainingEnrollment::create([
                'training_session_id' => $session->id,
                'user_id'             => $user->id,
                'status'              => 'attended',
            ]);
        }
    }

    public function test_isUnlockedBy_returns_true_when_all_criteria_are_met(): void
    {
        $user = $this->memberWithProfile(monthsAgo: 4);
        $this->givePoints($user, 150);
        $this->giveAttendedTrainings($user, 3);

        $this->assertTrue($this->level->isUnlockedBy($user));
    }

    public function test_isUnlockedBy_returns_false_when_insufficient_points(): void
    {
        $user = $this->memberWithProfile(monthsAgo: 4);
        $this->givePoints($user, 50); // less than 100
        $this->giveAttendedTrainings($user, 3);

        $this->assertFalse($this->level->isUnlockedBy($user));
    }

    public function test_isUnlockedBy_returns_false_when_insufficient_trainings(): void
    {
        $user = $this->memberWithProfile(monthsAgo: 4);
        $this->givePoints($user, 150);
        $this->giveAttendedTrainings($user, 1); // less than 2

        $this->assertFalse($this->level->isUnlockedBy($user));
    }

    public function test_isUnlockedBy_returns_false_when_insufficient_months(): void
    {
        $user = $this->memberWithProfile(monthsAgo: 1); // less than 3
        $this->givePoints($user, 150);
        $this->giveAttendedTrainings($user, 3);

        $this->assertFalse($this->level->isUnlockedBy($user));
    }

    public function test_isUnlockedBy_returns_false_when_user_has_no_profile(): void
    {
        $user = User::factory()->create();
        $this->givePoints($user, 150);

        $this->assertFalse($this->level->isUnlockedBy($user));
    }

    public function test_scopeOrdered_returns_levels_in_order(): void
    {
        Level::create(['name' => 'Niveau A', 'slug' => 'a', 'min_points' => 0,  'required_trainings' => 0, 'required_months' => 0, 'order' => 3]);
        Level::create(['name' => 'Niveau B', 'slug' => 'b', 'min_points' => 50, 'required_trainings' => 1, 'required_months' => 1, 'order' => 2]);

        $orders = Level::ordered()->pluck('order')->toArray();

        $this->assertSame($orders, collect($orders)->sort()->values()->toArray());
    }
}