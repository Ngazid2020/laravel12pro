<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class EventController extends Controller
{
    /**
     * GET /api/v1/events
     * Liste paginée, filtre upcoming|past.
     */
    public function index(Request $request): JsonResponse
    {
        $filter = $request->query('filter', 'upcoming');

        $query = Event::published()->with('organizer');

        if ($filter === 'upcoming') {
            $query->where('starts_at', '>', now())->orderBy('starts_at');
        } else {
            $query->where('starts_at', '<=', now())->orderByDesc('starts_at');
        }

        $events = $query->paginate(15);

        return response()->json(EventResource::collection($events)->response()->getData(true));
    }

    /**
     * GET /api/v1/events/{event}
     */
    public function show(Event $event): JsonResponse
    {
        abort_unless($event->is_published, 404);

        $event->load('organizer');

        return response()->json(new EventResource($event));
    }

    /**
     * POST /api/v1/events/{event}/register
     */
    public function register(Request $request, Event $event): JsonResponse
    {
        abort_unless($event->is_published, 404);

        if ($event->isFull()) {
            return response()->json(['message' => 'Cet événement est complet.'], 422);
        }

        $exists = EventRegistration::where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Vous êtes déjà inscrit à cet événement.'], 422);
        }

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'user_id'  => $request->user()->id,
        ]);

        return response()->json([
            'message'         => 'Inscription confirmée.',
            'registration_id' => $registration->id,
        ], 201);
    }

    /**
     * DELETE /api/v1/events/{event}/register
     */
    public function unregister(Request $request, Event $event): JsonResponse
    {
        EventRegistration::where('event_id', $event->id)
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['message' => 'Inscription annulée.']);
    }

    /**
     * GET /api/v1/events/registrations/{registration}/qr
     * Retourne l'URL signée pour le QR code de check-in.
     */
    public function qrCode(Request $request, EventRegistration $registration): JsonResponse
    {
        abort_unless($registration->user_id === $request->user()->id, 403);

        $signedUrl = URL::signedRoute('event.checkin', ['registration' => $registration->id]);

        return response()->json([
            'qr_url'      => $signedUrl,
            'event_title' => $registration->event->title,
            'checked_in'  => $registration->isCheckedIn(),
        ]);
    }
}