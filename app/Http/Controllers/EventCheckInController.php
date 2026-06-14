<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;

class EventCheckInController extends Controller
{
    public function __invoke(EventRegistration $registration)
    {
        // Vérifier que l'événement n'est pas trop ancien (72h après le début)
        if ($registration->event->starts_at->addHours(72)->isPast()) {
            return view('events.checkin-result', [
                'success'      => false,
                'message'      => 'Ce QR code a expiré (plus de 72h après l\'événement).',
                'registration' => $registration->load(['user', 'event']),
            ]);
        }

        $alreadyCheckedIn = $registration->isCheckedIn();

        if (!$alreadyCheckedIn) {
            $registration->checkIn();
        }

        return view('events.checkin-result', [
            'success'         => true,
            'alreadyCheckedIn'=> $alreadyCheckedIn,
            'registration'    => $registration->load(['user', 'event']),
        ]);
    }
}
