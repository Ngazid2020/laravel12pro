<?php

namespace App\Livewire\Member;

use App\Models\PartnerCompany;
use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts.member')]
#[Title('Mes Recommandations')]
class Recommendations extends Component
{
    use Toast;

    public bool $showForm = false;

    // Formulaire
    public string $targetType      = 'company'; // company | member
    public ?int   $partnerCompanyId = null;
    public ?int   $targetUserId     = null;
    public string $needDescription  = '';

    protected function rules(): array
    {
        return [
            'targetType'       => 'required|in:company,member',
            'partnerCompanyId' => 'required_if:targetType,company|nullable|exists:partner_companies,id',
            'targetUserId'     => 'required_if:targetType,member|nullable|exists:users,id',
            'needDescription'  => 'required|string|min:20|max:1000',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        Recommendation::create([
            'requester_id'      => Auth::id(),
            'partner_company_id'=> $this->targetType === 'company' ? $this->partnerCompanyId : null,
            'target_user_id'    => $this->targetType === 'member'  ? $this->targetUserId     : null,
            'need_description'  => $this->needDescription,
            'status'            => 'pending',
        ]);

        $this->reset(['showForm', 'partnerCompanyId', 'targetUserId', 'needDescription']);
        $this->targetType = 'company';
        $this->success('Demande de recommandation envoyée. L\'équipe l\'examinera prochainement.');
    }

    public function render()
    {
        $recommendations = Recommendation::where('requester_id', Auth::id())
            ->with(['partnerCompany', 'targetUser', 'examiner'])
            ->latest()
            ->get();

        $companies = PartnerCompany::active()->orderBy('name')->get();

        $members = User::whereHas('profile', fn($q) => $q->where('membership_status', 'active'))
            ->where('id', '!=', Auth::id())
            ->orderBy('name')
            ->get(['id', 'name']);

        $statusLabels = [
            'pending'          => 'En attente',
            'examining'        => 'En cours d\'examen',
            'transmitted'      => 'Transmise',
            'meeting_obtained' => 'Rendez-vous obtenu',
            'deal_closed'      => 'Conclue',
            'refused'          => 'Refusée',
        ];

        $statusColors = [
            'pending'          => 'badge-ghost',
            'examining'        => 'badge-info',
            'transmitted'      => 'badge-primary',
            'meeting_obtained' => 'badge-warning',
            'deal_closed'      => 'badge-success',
            'refused'          => 'badge-error',
        ];

        return view('livewire.member.recommendations', compact(
            'recommendations', 'companies', 'members', 'statusLabels', 'statusColors'
        ));
    }
}
