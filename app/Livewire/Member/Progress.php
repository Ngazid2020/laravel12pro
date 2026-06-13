<?php

namespace App\Livewire\Member;

use App\Models\Level;
use App\Models\PointEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.member')]
#[Title('Ma Progression')]
class Progress extends Component
{
    use WithPagination;

    public function render()
    {
        $user    = Auth::user();
        $profile = $user->profile;

        $totalPoints       = $user->total_points;
        $trainingsAttended = $user->trainingEnrollments()->where('status', 'attended')->count();
        $monthsActive      = $profile?->activated_at
            ? (int) $profile->activated_at->diffInMonths(now())
            : 0;

        // Tous les niveaux ordonnés
        $levels = Level::ordered()->with('rewards')->get();

        // Niveau actuel = le plus élevé déverrouillé
        $currentLevel = $levels->filter(fn($l) => $l->isUnlockedBy($user))->last();

        // Niveau suivant
        $nextLevel = $currentLevel
            ? $levels->firstWhere('order', '>', $currentLevel->order)
            : $levels->first();

        // Progression vers le niveau suivant (basée sur les points)
        $progressPercent = 0;
        if ($nextLevel) {
            $base  = $currentLevel?->min_points ?? 0;
            $range = $nextLevel->min_points - $base;
            $done  = $totalPoints - $base;
            $progressPercent = $range > 0 ? min(100, (int) ($done / $range * 100)) : 100;
        }

        // Historique des points (paginé)
        $pointHistory = PointEntry::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(15);

        $sourceLabels = [
            'candidature'       => 'Candidature acceptée',
            'training'          => 'Formation suivie',
            'event'             => 'Événement assisté',
            'mentoring'         => 'Session de mentorat',
            'referral'          => 'Parrainage',
            'manual'            => 'Crédit manuel',
            'recommendation'    => 'Recommandation aboutie',
        ];

        return view('livewire.member.progress', compact(
            'totalPoints', 'trainingsAttended', 'monthsActive',
            'levels', 'currentLevel', 'nextLevel', 'progressPercent',
            'pointHistory', 'sourceLabels'
        ));
    }
}
