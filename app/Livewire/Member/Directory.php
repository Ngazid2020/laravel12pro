<?php

namespace App\Livewire\Member;

use App\Models\MemberProfile;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.member')]
#[Title('Annuaire')]
class Directory extends Component
{
    use WithPagination;

    public string $search  = '';
    public string $sector  = '';
    public string $city    = '';
    public string $skill   = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedSector(): void { $this->resetPage(); }
    public function updatedCity(): void   { $this->resetPage(); }

    public function render()
    {
        $query = MemberProfile::with('user')
            ->where('membership_status', 'active')
            ->where('user_id', '!=', auth()->id());

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

        return view('livewire.member.directory', compact('members', 'sectors'));
    }
}
