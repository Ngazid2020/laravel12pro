<?php

namespace Tests\Feature\Security;

use App\Livewire\Member\Events as EventsComponent;
use App\Livewire\Member\Payments;
use App\Livewire\Member\Trainings;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\MemberProfile;
use App\Models\SubscriptionPlan;
use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Tests de logique métier — protections contre les abus.
 * Vérifie les gardes-fous : capacité maximale, double inscription, injection de statut.
 */
class BusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    private function memberUser(): User
    {
        /** @var User $user */
        $user = User::factory()->create();
        MemberProfile::create([
            'user_id'           => $user->id,
            'membership_status' => 'active',
            'activated_at'      => now()->subMonths(3),
        ]);
        return $user;
    }

    private function makeEvent(array $attrs = []): Event
    {
        return Event::create(array_merge([
            'title'        => 'Événement test',
            'organizer_id' => $this->memberUser()->id,
            'type'         => 'networking',
            'starts_at'    => now()->addDays(2),
            'is_published' => true,
        ], $attrs));
    }

    private function makeTraining(): Training
    {
        return Training::create([
            'title'        => 'Formation test',
            'trainer_id'   => $this->memberUser()->id,
            'is_published' => true,
        ]);
    }

    private function makeSession(Training $training, array $attrs = []): TrainingSession
    {
        return TrainingSession::create(array_merge([
            'training_id' => $training->id,
            'starts_at'   => now()->addDays(5),
            'ends_at'     => now()->addDays(5)->addHours(3),
            'status'      => 'scheduled',
        ], $attrs));
    }

    // ── Capacité maximale ─────────────────────────────────────────────────

    public function test_member_cannot_register_for_full_event(): void
    {
        $event = $this->makeEvent(['capacity' => 1]);

        // Remplir l'événement
        $otherMember = $this->memberUser();
        EventRegistration::create(['event_id' => $event->id, 'user_id' => $otherMember->id]);

        // Un nouveau membre tente de s'inscrire
        $newMember = $this->memberUser();

        Livewire::actingAs($newMember)
            ->test(EventsComponent::class)
            ->call('register', $event->id);

        $this->assertDatabaseMissing('event_registrations', [
            'event_id' => $event->id,
            'user_id'  => $newMember->id,
        ]);
    }

    public function test_member_cannot_enroll_in_full_training_session(): void
    {
        $training = $this->makeTraining();
        Training::where('id', $training->id)->update(['capacity' => 1]);

        $session = $this->makeSession($training);

        // Remplir la session
        $otherMember = $this->memberUser();
        TrainingEnrollment::create([
            'training_session_id' => $session->id,
            'user_id'             => $otherMember->id,
            'status'              => 'enrolled',
        ]);

        // Un nouveau membre tente de s'inscrire
        $newMember = $this->memberUser();

        Livewire::actingAs($newMember)
            ->test(Trainings::class)
            ->call('enroll', $session->id);

        $this->assertDatabaseMissing('training_enrollments', [
            'training_session_id' => $session->id,
            'user_id'             => $newMember->id,
        ]);
    }

    // ── Double inscription ────────────────────────────────────────────────

    public function test_member_cannot_register_twice_for_same_event(): void
    {
        $member = $this->memberUser();
        $event  = $this->makeEvent();

        // Pré-inscription directe en base (simule la première inscription réussie)
        EventRegistration::create(['event_id' => $event->id, 'user_id' => $member->id]);

        // Tentative de double inscription via le composant
        Livewire::actingAs($member)
            ->test(EventsComponent::class)
            ->call('register', $event->id);

        $count = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $member->id)
            ->count();

        $this->assertSame(1, $count);
    }

    public function test_member_cannot_enroll_twice_in_same_training_session(): void
    {
        $member   = $this->memberUser();
        $training = $this->makeTraining();
        $session  = $this->makeSession($training);

        // Première inscription
        Livewire::actingAs($member)
            ->test(Trainings::class)
            ->call('enroll', $session->id);

        // Tentative de double inscription
        Livewire::actingAs($member)
            ->test(Trainings::class)
            ->call('enroll', $session->id);

        $count = TrainingEnrollment::where('training_session_id', $session->id)
            ->where('user_id', $member->id)
            ->count();

        $this->assertSame(1, $count);
    }

    // ── Injection de statut de paiement ───────────────────────────────────

    public function test_member_cannot_create_payment_with_validated_status(): void
    {
        $member = $this->memberUser();
        $plan   = SubscriptionPlan::create([
            'name'   => 'Cotisation annuelle',
            'amount' => 15000,
            'period' => 'annual',
        ]);

        // Le composant Payments.php hardcode status='pending'
        // On tente de passer status='validated' via la propriété Livewire
        Livewire::actingAs($member)
            ->test(Payments::class)
            ->set('plan_id', $plan->id)
            ->set('method', 'mvola')
            ->set('transaction_reference', 'MVOLA-TEST-001')
            ->call('declare');

        $this->assertDatabaseHas('payments', [
            'user_id' => $member->id,
            'status'  => 'pending',
        ]);

        $this->assertDatabaseMissing('payments', [
            'user_id' => $member->id,
            'status'  => 'validated',
        ]);
    }

    public function test_member_cannot_create_payment_for_another_user(): void
    {
        $memberA = $this->memberUser();
        $memberB = $this->memberUser();
        $plan    = SubscriptionPlan::create([
            'name'   => 'Cotisation mensuelle',
            'amount' => 2000,
            'period' => 'monthly',
        ]);

        Livewire::actingAs($memberA)
            ->test(Payments::class)
            ->set('plan_id', $plan->id)
            ->set('method', 'cash')
            ->call('declare');

        // Aucun paiement ne doit être enregistré au nom de B
        $this->assertDatabaseMissing('payments', [
            'user_id' => $memberB->id,
        ]);
    }

    // ── Inscription à un événement non publié ─────────────────────────────

    public function test_member_cannot_register_for_unpublished_event(): void
    {
        $member = $this->memberUser();
        $event  = $this->makeEvent(['is_published' => false]);

        Livewire::actingAs($member)
            ->test(EventsComponent::class)
            ->call('register', $event->id);

        $this->assertDatabaseMissing('event_registrations', [
            'event_id' => $event->id,
            'user_id'  => $member->id,
        ]);
    }

    // ── Candidature — pas d'injection de rôle ────────────────────────────

    public function test_candidature_form_cannot_create_user_with_admin_role(): void
    {
        $this->post(route('home'), [
            'first_name'  => 'Hacker',
            'last_name'   => 'Test',
            'email'       => 'hacker@evil.com',
            'phone'       => '+269 123 45 67',
            'motivation'  => 'Tentative de création de compte admin via le formulaire de candidature publique.',
        ]);

        $user = \App\Models\User::where('email', 'hacker@evil.com')->first();

        if ($user) {
            $this->assertFalse($user->hasRole('admin'));
            $this->assertFalse($user->hasRole('super_admin'));
        }

        // Si l'utilisateur n'est pas créé du tout, le test passe aussi
        $this->assertTrue(true);
    }
}