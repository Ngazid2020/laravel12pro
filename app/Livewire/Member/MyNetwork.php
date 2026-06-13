<?php

namespace App\Livewire\Member;

use App\Models\MemberProfile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.member')]
#[Title('Mon Réseau')]
class MyNetwork extends Component
{
    public function render()
    {
        $user = auth()->user();
        $profile = $user->profile;

        // Direct mentees (affiliés directs)
        $directMentees = collect();
        if ($profile) {
            $directMentees = MemberProfile::where('mentor_id', $user->id)
                ->with('user')
                ->get();
        }

        // My mentor
        $mentor = null;
        if ($profile?->mentor_id) {
            $mentor = \App\Models\User::with('profile')->find($profile->mentor_id);
        }

        // Points earned via affiliations (first-level only, anti-pyramid)
        $referralPoints = $user->pointEntries()
            ->where('source', 'referral')
            ->sum('points');

        // Full subtree count via adjacency list
        $subtreeCount = 0;
        if ($profile) {
            try {
                $subtreeCount = MemberProfile::where('user_id', $user->id)
                    ->with('descendants')
                    ->first()
                    ?->descendants
                    ->count() ?? 0;
            } catch (\Throwable) {
                $subtreeCount = $directMentees->count();
            }
        }

        return view('livewire.member.my-network', compact(
            'profile', 'directMentees', 'mentor', 'referralPoints', 'subtreeCount'
        ));
    }
}
