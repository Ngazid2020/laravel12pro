<?php

namespace App\Livewire\Member;

use App\Models\MemberProfile;
use App\Models\MentoringRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('layouts.member')]
#[Title('Annuaire')]
class Directory extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public string $sector = '';
    public string $city   = '';
    public string $skill  = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedSector(): void { $this->resetPage(); }
    public function updatedCity(): void   { $this->resetPage(); }

    public function requestMentor(int $userId): void
    {
        $user = Auth::user();

        if ($user->profile?->mentor_id) {
            $this->error('Vous avez déjà un mentor assigné.');
            return;
        }

        $already = MentoringRequest::where('requester_id', $user->id)
            ->where('mentor_id', $userId)
            ->exists();

        if ($already) {
            $this->warning('Vous avez déjà envoyé une demande à ce membre.');
            return;
        }

        MentoringRequest::create([
            'requester_id' => $user->id,
            'mentor_id'    => $userId,
            'status'       => 'pending',
        ]);

        $this->success('Demande de mentorat envoyée. L\'administration la traitera prochainement.');
    }

    public function render()
    {
        $userId = Auth::id();

        $query = MemberProfile::with('user')
            ->where('membership_status', 'active')
            ->where('user_id', '!=', $userId);

        if ($this->search) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$this->search}%"));
        }

        if ($this->sector) {
            $query->where('sector', 'like', "%{$this->sector}%");
        }

        if ($this->city) {
            $query->where('city', 'like', "%{$this->city}%");
        }

        if ($this->skill) {
            $query->whereJsonContains('skills_offered', $this->skill);
        }

        $members = $query->latest()->paginate(12);

        $sectors = MemberProfile::where('membership_status', 'active')
            ->whereNotNull('sector')
            ->distinct()
            ->pluck('sector')
            ->sort()
            ->values();

        $hasMentor = (bool) Auth::user()->profile?->mentor_id;

        // Mentor_ids déjà demandés (pour désactiver le bouton sur leurs cartes)
        $pendingMentorRequestIds = $hasMentor ? [] :
            MentoringRequest::where('requester_id', $userId)
                ->where('status', 'pending')
                ->pluck('mentor_id')
                ->toArray();

        return view('livewire.member.directory', compact(
            'members', 'sectors', 'hasMentor', 'pendingMentorRequestIds'
        ));
    }
}
