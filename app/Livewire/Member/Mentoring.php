<?php

namespace App\Livewire\Member;

use App\Models\MentoringSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts.member')]
#[Title('Mentorat')]
class Mentoring extends Component
{
    use Toast;

    // Demande de session (vue mentoré)
    public bool   $showRequest    = false;
    public string $scheduledAt    = '';
    public string $requestNotes   = '';

    // Notes de session (vue mentor)
    public bool   $showNotes      = false;
    public ?int   $notesSessionId = null;
    public string $sessionNotes   = '';

    protected function rulesRequest(): array
    {
        return [
            'scheduledAt'  => 'required|date|after:now',
            'requestNotes' => 'nullable|string|max:500',
        ];
    }

    public function requestSession(): void
    {
        $this->validate($this->rulesRequest());

        $mentor = Auth::user()->profile?->mentor_id;
        if (!$mentor) {
            $this->error('Vous n\'avez pas de mentor assigné.');
            return;
        }

        MentoringSession::create([
            'mentor_id'    => $mentor,
            'mentee_id'    => Auth::id(),
            'scheduled_at' => $this->scheduledAt,
            'status'       => 'pending',
            'notes'        => $this->requestNotes ?: null,
        ]);

        $this->reset(['showRequest', 'scheduledAt', 'requestNotes']);
        $this->success('Demande de session envoyée à votre mentor.');
    }

    public function confirmSession(int $sessionId): void
    {
        MentoringSession::where('id', $sessionId)
            ->where('mentee_id', Auth::id())
            ->update(['confirmed_by_mentee' => true]);

        $this->success('Session confirmée.');
    }

    public function openNotes(int $sessionId): void
    {
        $session = MentoringSession::where('id', $sessionId)
            ->where('mentor_id', Auth::id())
            ->firstOrFail();

        $this->notesSessionId = $sessionId;
        $this->sessionNotes   = $session->notes ?? '';
        $this->showNotes      = true;
    }

    public function saveNotes(): void
    {
        $this->validate(['sessionNotes' => 'nullable|string|max:1000']);

        MentoringSession::where('id', $this->notesSessionId)
            ->where('mentor_id', Auth::id())
            ->update(['notes' => $this->sessionNotes ?: null, 'status' => 'confirmed']);

        $this->showNotes = false;
        $this->success('Notes enregistrées.');
    }

    public function render()
    {
        $user = Auth::user();

        // Sessions en tant que mentoré
        $menteeSessions = MentoringSession::where('mentee_id', $user->id)
            ->with('mentor')
            ->orderByDesc('scheduled_at')
            ->get();

        // Sessions en tant que mentor
        $mentorSessions = MentoringSession::where('mentor_id', $user->id)
            ->with('mentee')
            ->orderByDesc('scheduled_at')
            ->get();

        $hasMentor  = (bool) $user->profile?->mentor_id;
        $isMentor   = $user->profile?->membership_status === 'active'
                      && $mentorSessions->isNotEmpty();

        $statusLabels = [
            'pending'   => 'En attente',
            'confirmed' => 'Confirmée',
            'held'      => 'Tenue',
            'cancelled' => 'Annulée',
        ];

        $statusColors = [
            'pending'   => 'badge-warning',
            'confirmed' => 'badge-info',
            'held'      => 'badge-success',
            'cancelled' => 'badge-error',
        ];

        return view('livewire.member.mentoring', compact(
            'menteeSessions', 'mentorSessions', 'hasMentor', 'isMentor',
            'statusLabels', 'statusColors'
        ));
    }
}
