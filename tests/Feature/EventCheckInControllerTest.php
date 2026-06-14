<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EventCheckInControllerTest extends TestCase
{
    use RefreshDatabase;

    private function makeEvent(array $attrs = []): Event
    {
        $organizer = User::factory()->create();

        return Event::create(array_merge([
            'title'        => 'Networking Moroni',
            'organizer_id' => $organizer->id,
            'type'         => 'networking',
            'starts_at'    => now()->subHours(2),  // en cours
            'ends_at'      => now()->addHours(1),
            'is_published' => true,
        ], $attrs));
    }

    private function makeRegistration(Event $event, ?User $user = null): EventRegistration
    {
        $user ??= User::factory()->create();

        return EventRegistration::create([
            'event_id' => $event->id,
            'user_id'  => $user->id,
        ]);
    }

    private function signedUrl(EventRegistration $registration): string
    {
        return URL::signedRoute('event.checkin', ['registration' => $registration->id]);
    }

    // ── Check-in valide ───────────────────────────────────────────────────

    public function test_valid_checkin_marks_attendance_and_shows_success(): void
    {
        $event        = $this->makeEvent();
        $registration = $this->makeRegistration($event);

        $response = $this->get($this->signedUrl($registration));

        $response->assertOk();
        $response->assertViewIs('events.checkin-result');
        $response->assertViewHas('success', true);
        $response->assertViewHas('alreadyCheckedIn', false);

        $this->assertNotNull($registration->fresh()->checked_in_at);
    }

    // ── Idempotence ───────────────────────────────────────────────────────

    public function test_second_checkin_returns_success_without_changing_timestamp(): void
    {
        $event        = $this->makeEvent();
        $registration = $this->makeRegistration($event);

        $firstUrl = $this->signedUrl($registration);
        $this->get($firstUrl);
        $firstTimestamp = $registration->fresh()->checked_in_at;

        // Simule un second scan
        $response = $this->get($firstUrl);

        $response->assertOk();
        $response->assertViewHas('success', true);
        $response->assertViewHas('alreadyCheckedIn', true);

        // Le timestamp ne change pas au second passage
        $this->assertEquals($firstTimestamp, $registration->fresh()->checked_in_at);
    }

    // ── Expiration 72h ───────────────────────────────────────────────────

    public function test_expired_event_returns_failure_view(): void
    {
        $event = $this->makeEvent([
            'starts_at' => now()->subHours(73), // plus de 72h
            'ends_at'   => now()->subHours(70),
        ]);
        $registration = $this->makeRegistration($event);

        $response = $this->get($this->signedUrl($registration));

        $response->assertOk();
        $response->assertViewIs('events.checkin-result');
        $response->assertViewHas('success', false);

        // Aucun check-in enregistré
        $this->assertNull($registration->fresh()->checked_in_at);
    }

    // ── Signature invalide ────────────────────────────────────────────────

    public function test_tampered_url_returns_403(): void
    {
        $event        = $this->makeEvent();
        $registration = $this->makeRegistration($event);

        // URL signée valide, mais on modifie le paramètre après
        $url = $this->signedUrl($registration) . '&tampered=1';

        $response = $this->get($url);

        $response->assertForbidden();
    }
}