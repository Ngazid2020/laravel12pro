<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\TrainingSessionResource;
use App\Models\Announcement;
use App\Models\Event;
use App\Models\Level;
use App\Models\TrainingSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * GET /api/v1/dashboard
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user    = $request->user();
        $profile = $user->profile;

        // Points et niveau
        $totalPoints = $user->total_points;
        $currentLevel = Level::where('min_points', '<=', $totalPoints)
            ->orderByDesc('min_points')
            ->first();
        $nextLevel = Level::where('min_points', '>', $totalPoints)
            ->orderBy('min_points')
            ->first();

        // Prochains événements (3)
        $upcomingEvents = Event::published()
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->limit(3)
            ->with('organizer')
            ->get();

        // Prochaines sessions de formation inscrites
        $mySessions = TrainingSession::whereHas('enrollments', fn ($q) => $q->where('user_id', $user->id))
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->limit(3)
            ->with('training')
            ->get();

        // Annonces actives
        $announcements = Announcement::where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        })
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();

        return response()->json([
            'member' => [
                'full_name'          => $user->full_name,
                'membership_status'  => $profile?->membership_status,
                'status_label'       => $profile?->statusLabel(),
                'expires_at'         => $profile?->membership_expires_at?->toDateString(),
                'referral_code'      => $profile?->referral_code,
            ],
            'points' => [
                'total'           => $totalPoints,
                'current_level'   => $currentLevel ? [
                    'name'        => $currentLevel->name,
                    'badge_color' => $currentLevel->badge_color,
                ] : null,
                'next_level'      => $nextLevel ? [
                    'name'       => $nextLevel->name,
                    'min_points' => $nextLevel->min_points,
                    'remaining'  => $nextLevel->min_points - $totalPoints,
                ] : null,
            ],
            'upcoming_events'   => EventResource::collection($upcomingEvents),
            'my_sessions'       => TrainingSessionResource::collection($mySessions),
            'announcements'     => AnnouncementResource::collection($announcements),
        ]);
    }
}