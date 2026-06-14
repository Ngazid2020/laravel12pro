<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TrainingEnrollmentResource;
use App\Http\Resources\TrainingResource;
use App\Http\Resources\TrainingSessionResource;
use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Models\TrainingSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    /**
     * GET /api/v1/trainings
     */
    public function index(): JsonResponse
    {
        $trainings = Training::published()
            ->with(['trainer', 'sessions'])
            ->paginate(15);

        return response()->json(TrainingResource::collection($trainings)->response()->getData(true));
    }

    /**
     * GET /api/v1/trainings/{training}
     */
    public function show(Training $training): JsonResponse
    {
        abort_unless($training->is_published, 404);

        $training->load(['trainer', 'sessions']);

        return response()->json(new TrainingResource($training));
    }

    /**
     * GET /api/v1/trainings/{training}/sessions
     */
    public function sessions(Training $training): JsonResponse
    {
        abort_unless($training->is_published, 404);

        $sessions = $training->sessions()
            ->where('status', '!=', 'cancelled')
            ->orderBy('starts_at')
            ->with('training')
            ->get();

        return response()->json(TrainingSessionResource::collection($sessions));
    }

    /**
     * POST /api/v1/trainings/sessions/{session}/enroll
     */
    public function enroll(Request $request, TrainingSession $session): JsonResponse
    {
        $training = $session->training;

        if ($session->isFull()) {
            return response()->json(['message' => 'Cette session est complète.'], 422);
        }

        $exists = TrainingEnrollment::where('training_session_id', $session->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Vous êtes déjà inscrit à cette session.'], 422);
        }

        $enrollment = TrainingEnrollment::create([
            'training_session_id' => $session->id,
            'user_id'             => $request->user()->id,
            'status'              => 'enrolled',
        ]);

        return response()->json([
            'message'       => 'Inscription confirmée.',
            'enrollment_id' => $enrollment->id,
        ], 201);
    }

    /**
     * DELETE /api/v1/trainings/sessions/{session}/enroll
     */
    public function unenroll(Request $request, TrainingSession $session): JsonResponse
    {
        TrainingEnrollment::where('training_session_id', $session->id)
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->json(['message' => 'Désinscription effectuée.']);
    }

    /**
     * GET /api/v1/trainings/my-enrollments
     */
    public function myEnrollments(Request $request): JsonResponse
    {
        $enrollments = TrainingEnrollment::where('user_id', $request->user()->id)
            ->with(['trainingSession.training'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json(TrainingEnrollmentResource::collection($enrollments)->response()->getData(true));
    }

    /**
     * POST /api/v1/trainings/enrollments/{enrollment}/rate
     */
    public function rate(Request $request, TrainingEnrollment $enrollment): JsonResponse
    {
        abort_unless($enrollment->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $enrollment->update($validated);

        return response()->json(['message' => 'Note enregistrée.']);
    }
}