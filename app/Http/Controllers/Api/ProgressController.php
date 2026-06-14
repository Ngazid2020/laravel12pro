<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LevelResource;
use App\Models\Level;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    /**
     * GET /api/v1/progress
     * Progression du membre : points, niveau, formations, historique.
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user    = $request->user();
        $profile = $user->profile;

        $totalPoints = $user->total_points;

        $levels = Level::orderBy('order')->with('rewards')->get();

        $currentLevel = $levels->filter(fn ($l) => $l->min_points <= $totalPoints)->last();
        $nextLevel    = $levels->first(fn ($l) => $l->min_points > $totalPoints);

        // Formations complétées
        $completedTrainings = $user->trainingEnrollments()
            ->where('status', 'attended')
            ->count();

        // Mois de membership
        $monthsActive = $profile?->activated_at
            ? (int) $profile->activated_at->diffInMonths(now())
            : 0;

        // Historique des points (15 dernières entrées)
        $pointHistory = $user->pointEntries()
            ->orderByDesc('created_at')
            ->limit(15)
            ->get()
            ->map(fn ($e) => [
                'source'      => $e->source,
                'points'      => $e->points,
                'description' => $e->description,
                'earned_at'   => $e->created_at?->toISOString(),
            ]);

        return response()->json([
            'total_points'        => $totalPoints,
            'completed_trainings' => $completedTrainings,
            'months_active'       => $monthsActive,
            'current_level'       => $currentLevel ? new LevelResource($currentLevel) : null,
            'next_level'          => $nextLevel ? new LevelResource($nextLevel) : null,
            'progress_to_next'    => $nextLevel ? [
                'remaining_points' => $nextLevel->min_points - $totalPoints,
                'percentage'       => $currentLevel
                    ? round(($totalPoints - $currentLevel->min_points) / ($nextLevel->min_points - $currentLevel->min_points) * 100)
                    : 0,
            ] : null,
            'all_levels'          => LevelResource::collection($levels),
            'point_history'       => $pointHistory,
        ]);
    }
}