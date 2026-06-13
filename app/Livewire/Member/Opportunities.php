<?php

namespace App\Livewire\Member;

use App\Models\Opportunity;
use App\Models\OpportunityApplication;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('layouts.member')]
#[Title('Opportunités')]
class Opportunities extends Component
{
    use WithPagination, Toast;

    public string $search = '';
    public string $type   = '';
    public string $sector = '';

    public function apply(int $opportunityId): void
    {
        $exists = OpportunityApplication::where('opportunity_id', $opportunityId)
            ->where('user_id', auth()->id())
            ->exists();

        if ($exists) {
            $this->warning('Vous avez déjà postulé à cette opportunité.');
            return;
        }

        OpportunityApplication::create([
            'opportunity_id' => $opportunityId,
            'user_id'        => auth()->id(),
            'status'         => 'pending',
        ]);

        $this->success('Candidature envoyée avec succès !');
    }

    public function render()
    {
        $query = Opportunity::active()->with('partnerCompany');

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }
        if ($this->type) {
            $query->where('type', $this->type);
        }
        if ($this->sector) {
            $query->where('sector', 'like', "%{$this->sector}%");
        }

        $opportunities = $query->latest()->paginate(10);

        $myApplications = OpportunityApplication::where('user_id', auth()->id())
            ->pluck('opportunity_id')
            ->toArray();

        $typeLabels = [
            'tender'     => "Appel d'offres",
            'mission'    => 'Mission',
            'internship' => 'Stage',
            'funding'    => 'Financement',
            'contest'    => 'Concours',
        ];

        return view('livewire.member.opportunities', compact('opportunities', 'myApplications', 'typeLabels'));
    }
}
