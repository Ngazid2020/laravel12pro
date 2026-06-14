<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContactRequestResource;
use App\Http\Resources\UserResource;
use App\Models\ContactRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * GET /api/v1/contacts
     * Mes contacts acceptés.
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $contacts = User::where(fn ($q) => $q
            ->whereHas('sentContactRequests', fn ($r) => $r
                ->where('receiver_id', $userId)
                ->where('status', 'accepted')
            )
            ->orWhereHas('receivedContactRequests', fn ($r) => $r
                ->where('sender_id', $userId)
                ->where('status', 'accepted')
            )
        )
            ->with('profile')
            ->paginate(20);

        return response()->json(UserResource::collection($contacts)->response()->getData(true));
    }

    /**
     * GET /api/v1/contacts/requests
     * Demandes de contact reçues en attente.
     */
    public function requests(Request $request): JsonResponse
    {
        $pending = ContactRequest::where('receiver_id', $request->user()->id)
            ->where('status', 'pending')
            ->with(['sender.profile'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json(ContactRequestResource::collection($pending));
    }

    /**
     * POST /api/v1/contacts/requests/{user}
     * Envoyer une demande de contact.
     */
    public function sendRequest(Request $request, User $user): JsonResponse
    {
        $senderId = $request->user()->id;

        if ($user->id === $senderId) {
            return response()->json(['message' => 'Vous ne pouvez pas vous contacter vous-même.'], 422);
        }

        $exists = ContactRequest::where(fn ($q) => $q
            ->where('sender_id', $senderId)->where('receiver_id', $user->id)
        )->orWhere(fn ($q) => $q
            ->where('sender_id', $user->id)->where('receiver_id', $senderId)
        )->exists();

        if ($exists) {
            return response()->json(['message' => 'Une demande existe déjà entre vous et ce membre.'], 422);
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        ContactRequest::create([
            'sender_id'   => $senderId,
            'receiver_id' => $user->id,
            'message'     => $validated['message'] ?? null,
            'status'      => 'pending',
        ]);

        return response()->json(['message' => 'Demande de contact envoyée.'], 201);
    }

    /**
     * POST /api/v1/contacts/requests/{request}/accept
     */
    public function accept(Request $request, ContactRequest $contactRequest): JsonResponse
    {
        abort_unless($contactRequest->receiver_id === $request->user()->id, 403);

        $contactRequest->update(['status' => 'accepted']);

        return response()->json(['message' => 'Demande acceptée.']);
    }

    /**
     * POST /api/v1/contacts/requests/{request}/decline
     */
    public function decline(Request $request, ContactRequest $contactRequest): JsonResponse
    {
        abort_unless($contactRequest->receiver_id === $request->user()->id, 403);

        $contactRequest->update(['status' => 'declined']);

        return response()->json(['message' => 'Demande refusée.']);
    }
}