<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MentoringSessionResource;
use App\Http\Resources\UserResource;
use App\Models\MentoringSession;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MentoringController extends Controller
{
    /**
     * GET /api/v1/mentoring/sessions
     * Mes sessions de mentorat (mentor ou mentoré).
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $sessions = MentoringSession::where('mentor_id', $userId)
            ->orWhere('mentee_id', $userId)
            ->with(['mentor.profile', 'mentee.profile'])
            ->orderByDesc('scheduled_at')
            ->paginate(15);

        return response()->json(MentoringSessionResource::collection($sessions)->response()->getData(true));
    }

    /**
     * POST /api/v1/mentoring/sessions
     * Demander une session de mentorat.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mentor_id'    => ['required', 'exists:users,id'],
            'scheduled_at' => ['required', 'date', 'after:now'],
            'notes'        => ['nullable', 'string', 'max:1000'],
        ]);

        $mentor = User::findOrFail($validated['mentor_id']);

        // Vérifier que le mentor est bien un mentor/admin
        $mentorProfile = $mentor->profile;
        $isMentor = $mentor->hasRole(['admin', 'super_admin'])
            || ($mentorProfile && $mentorProfile->membership_status === 'active');

        if (! $isMentor) {
            return response()->json(['message' => 'Cet utilisateur n\'est pas disponible comme mentor.'], 422);
        }

        $session = MentoringSession::create([
            'mentor_id'    => $validated['mentor_id'],
            'mentee_id'    => $request->user()->id,
            'scheduled_at' => $validated['scheduled_at'],
            'notes'        => $validated['notes'] ?? null,
            'status'       => 'scheduled',
        ]);

        return response()->json([
            'message' => 'Session de mentorat planifiée.',
            'session' => new MentoringSessionResource($session->load(['mentor', 'mentee'])),
        ], 201);
    }

    /**
     * POST /api/v1/mentoring/sessions/{session}/confirm
     * Le mentoré confirme que la session a bien eu lieu.
     */
    public function confirm(Request $request, MentoringSession $session): JsonResponse
    {
        abort_unless($session->mentee_id === $request->user()->id, 403);

        $session->update(['confirmed_by_mentee' => true]);

        return response()->json(['message' => 'Session confirmée.']);
    }

    /**
     * GET /api/v1/mentoring/mentors
     * Liste des membres qui peuvent être mentors.
     */
    public function mentors(): JsonResponse
    {
        $mentors = User::whereHas('profile', fn ($q) => $q->where('membership_status', 'active'))
            ->with('profile')
            ->get();

        return response()->json(UserResource::collection($mentors));
    }
}