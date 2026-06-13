<?php

namespace App\Livewire\Member;

use App\Models\Announcement;
use App\Models\Event;
use App\Models\Opportunity;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.member')]
#[Title('Tableau de bord')]
class Dashboard extends Component
{
    public function render()
    {
        $user    = auth()->user();
        $profile = $user->profile;

        $totalPoints     = $user->total_points;
        $trainingsCount  = $user->trainingEnrollments()->where('status', 'attended')->count();
        $menteesCount    = $profile?->directMentees()->count() ?? 0;

        $announcements = Announcement::visible()
            ->forAudience($profile?->membership_status ?? 'all')
            ->latest('published_at')
            ->take(5)
            ->get();

        $upcomingEvents = Event::published()
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->take(3)
            ->get();

        $latestOpportunities = Opportunity::active()
            ->latest()
            ->take(4)
            ->get();

        return view('livewire.member.dashboard', compact(
            'profile',
            'totalPoints',
            'trainingsCount',
            'menteesCount',
            'announcements',
            'upcomingEvents',
            'latestOpportunities',
        ));
    }
}
