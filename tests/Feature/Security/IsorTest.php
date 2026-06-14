<?php

namespace Tests\Feature\Security;

use App\Livewire\Member\Contacts;
use App\Livewire\Member\Events as EventsComponent;
use App\Livewire\Member\Trainings;
use App\Models\ContactRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\MemberProfile;
use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Tests IDOR (Insecure Direct Object Reference)
 * Vérifie qu'un membre ne peut pas manipuler les données d'un autre membre.
 */
class IsorTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────

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

    // ── Événements ────────────────────────────────────────────────────────

    public function test_member_cannot_unregister_another_member_from_event(): void
    {
        $memberA = $this->memberUser();
        $memberB = $this->memberUser();
        $event   = $this->makeEvent();

        // B s'inscrit à l'événement
        EventRegistration::create(['event_id' => $event->id, 'user_id' => $memberB->id]);

        // A n'est PAS inscrit — il tente quand même d'appeler unregister avec l'ID de l'événement
        // La requête filtre par user_id = A, donc ne supprime rien
        Livewire::actingAs($memberA)
            ->test(EventsComponent::class)
            ->call('unregister', $event->id)
            ->assertHasNoErrors();

        // L'inscription de B doit toujours exister
        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'user_id'  => $memberB->id,
        ]);
    }

    public function test_member_cannot_view_another_members_qr_code(): void
    {
        $memberA = $this->memberUser();
        $memberB = $this->memberUser();
        $event   = $this->makeEvent();

        $registrationB = EventRegistration::create([
            'event_id' => $event->id,
            'user_id'  => $memberB->id,
        ]);

        // A tente d'accéder au QR de la registration de B → modal ne s'ouvre pas
        Livewire::actingAs($memberA)
            ->test(EventsComponent::class)
            ->call('showQrCode', $registrationB->id)
            ->assertSet('showQr', false)
            ->assertSet('qrUrl', null);
    }

    // ── Formations ────────────────────────────────────────────────────────

    public function test_member_cannot_unenroll_another_member_from_training(): void
    {
        $memberA  = $this->memberUser();
        $memberB  = $this->memberUser();
        $training = $this->makeTraining();
        $session  = $this->makeSession($training);

        // B s'inscrit à la session
        TrainingEnrollment::create([
            'training_session_id' => $session->id,
            'user_id'             => $memberB->id,
            'status'              => 'enrolled',
        ]);

        // A tente de désinscrire B en passant l'ID de la session
        Livewire::actingAs($memberA)
            ->test(Trainings::class)
            ->call('unenroll', $session->id);

        // L'inscription de B doit toujours exister
        $this->assertDatabaseHas('training_enrollments', [
            'training_session_id' => $session->id,
            'user_id'             => $memberB->id,
        ]);
    }

    public function test_member_cannot_open_rating_for_another_members_enrollment(): void
    {
        $memberA  = $this->memberUser();
        $memberB  = $this->memberUser();
        $training = $this->makeTraining();
        $session  = $this->makeSession($training, [
            'starts_at' => now()->subDays(3),
            'ends_at'   => now()->subDays(3)->addHours(3),
            'status'    => 'completed',
        ]);

        // B a une inscription "attended"
        $enrollmentB = TrainingEnrollment::create([
            'training_session_id' => $session->id,
            'user_id'             => $memberB->id,
            'status'              => 'attended',
        ]);

        // A tente d'ouvrir la modal de notation de B → ne doit pas ouvrir la modal
        Livewire::actingAs($memberA)
            ->test(Trainings::class)
            ->call('openRating', $enrollmentB->id)
            ->assertSet('showRating', false);
    }

    public function test_member_cannot_submit_rating_for_another_members_enrollment(): void
    {
        $memberA  = $this->memberUser();
        $memberB  = $this->memberUser();
        $training = $this->makeTraining();
        $session  = $this->makeSession($training, [
            'starts_at' => now()->subDays(3),
            'status'    => 'completed',
        ]);

        $enrollmentB = TrainingEnrollment::create([
            'training_session_id' => $session->id,
            'user_id'             => $memberB->id,
            'status'              => 'attended',
        ]);

        // A force ratingEnrollmentId à l'ID de B, puis soumet
        Livewire::actingAs($memberA)
            ->test(Trainings::class)
            ->set('ratingEnrollmentId', $enrollmentB->id)
            ->set('rating', 1)
            ->set('ratingComment', 'Injection de note')
            ->call('submitRating');

        // La note de B ne doit pas changer
        $this->assertDatabaseMissing('training_enrollments', [
            'id'      => $enrollmentB->id,
            'rating'  => 1,
            'comment' => 'Injection de note',
        ]);
    }

    // ── Contacts ──────────────────────────────────────────────────────────

    public function test_member_cannot_accept_contact_request_sent_to_another_member(): void
    {
        $memberA = $this->memberUser();
        $memberB = $this->memberUser();
        $memberC = $this->memberUser();

        // B envoie une demande à C
        $request = ContactRequest::create([
            'sender_id'   => $memberB->id,
            'receiver_id' => $memberC->id,
            'status'      => 'pending',
        ]);

        // A tente d'accepter la demande destinée à C
        Livewire::actingAs($memberA)
            ->test(Contacts::class)
            ->call('accept', $request->id);

        // Le statut ne doit pas changer
        $this->assertDatabaseHas('contact_requests', [
            'id'     => $request->id,
            'status' => 'pending',
        ]);
    }

    public function test_member_cannot_decline_contact_request_sent_to_another_member(): void
    {
        $memberA = $this->memberUser();
        $memberB = $this->memberUser();
        $memberC = $this->memberUser();

        $request = ContactRequest::create([
            'sender_id'   => $memberB->id,
            'receiver_id' => $memberC->id,
            'status'      => 'pending',
        ]);

        Livewire::actingAs($memberA)
            ->test(Contacts::class)
            ->call('decline', $request->id);

        $this->assertDatabaseHas('contact_requests', [
            'id'     => $request->id,
            'status' => 'pending',
        ]);
    }
}
