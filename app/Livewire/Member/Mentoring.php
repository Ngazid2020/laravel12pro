<?php

namespace App\Livewire\Member;

use App\Models\MemberProfile;
use App\Models\MentoringSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts.member')]
#[Title('Mentorat')]
class Mentoring extends Component
{
    use Toast;

    public string $activeTab = 'sessions';

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

        $menteeSessions = MentoringSession::where('mentee_id', $user->id)
            ->with('mentor')
            ->orderByDesc('scheduled_at')
            ->get();

        $mentorSessions = MentoringSession::where('mentor_id', $user->id)
            ->with('mentee')
            ->orderByDesc('scheduled_at')
            ->get();

        $hasMentor = (bool) $user->profile?->mentor_id;
        $isMentor  = $user->profile?->membership_status === 'active'
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

        // ── Organigramme (calculé uniquement quand l'onglet est actif) ──
        $treeJson  = null;
        $activeTab = $this->activeTab;

        if ($activeTab === 'organigramme' && $user->profile) {
            $profile     = $user->profile->loadMissing('user');
            $descendants = $profile->descendants()->with('user')->get();
            $tree        = $this->buildOrgData($profile, $descendants, $user->id, 0);

            // Si le membre a un mentor, on le place à la racine
            if ($profile->mentor_id) {
                $mp = MemberProfile::where('user_id', $profile->mentor_id)->with('user')->first();
                if ($mp) {
                    $mu       = $mp->user;
                    $initials = strtoupper(substr($mu?->first_name ?? '?', 0, 1) . substr($mu?->last_name ?? '', 0, 1));
                    $color    = '#7c3aed';
                    $tree     = [
                        'name'     => trim(($mu?->first_name ?? '') . ' ' . ($mu?->last_name ?? '')),
                        'role'     => $mp->sector ?? $mp->company_name ?? $mp->project_name ?? 'Mentor',
                        'color'    => $color,
                        'imageUrl' => $mu?->avatar ? Storage::url($mu->avatar) : $this->initialsAvatar($initials, $color),
                        'isSelf'   => false,
                        'isMentor' => true,
                        'children' => [$tree],
                    ];
                }
            }

            $treeJson = $tree;
        }

        return view('livewire.member.mentoring', compact(
            'activeTab', 'menteeSessions', 'mentorSessions', 'hasMentor', 'isMentor',
            'statusLabels', 'statusColors', 'treeJson'
        ));
    }

    // ── Helpers organigramme ─────────────────────────────────────────────────

    private function buildOrgData(MemberProfile $root, Collection $all, int $currentUserId, int $depth): array
    {
        $user     = $root->user;
        $isSelf   = $root->user_id === $currentUserId;
        $initials = strtoupper(substr($user?->first_name ?? '?', 0, 1) . substr($user?->last_name ?? '', 0, 1));
        $color    = $isSelf ? '#6366f1' : $this->depthColor($depth);
        $name     = trim(($user?->first_name ?? '') . ' ' . ($user?->last_name ?? ''));
        $role     = $root->sector ?? $root->company_name ?? $root->project_name
                    ?? $this->statusLabelFor($root->membership_status);

        $node = [
            'name'     => $name ?: '?',
            'role'     => $role ?: '—',
            'color'    => $color,
            'imageUrl' => $user?->avatar ? Storage::url($user->avatar) : $this->initialsAvatar($initials, $color),
            'isSelf'   => $isSelf,
            'isMentor' => false,
        ];

        $children = $all->filter(fn ($n) => $n->mentor_id == $root->user_id)->values();
        if ($children->isNotEmpty()) {
            $node['children'] = $children
                ->map(fn ($child) => $this->buildOrgData($child, $all, $currentUserId, $depth + 1))
                ->all();
        }

        return $node;
    }

    private function depthColor(int $depth): string
    {
        return ['#2dd4bf', '#fb923c', '#f472b6', '#38bdf8', '#a78bfa'][$depth % 5];
    }

    private function initialsAvatar(string $initials, string $color): string
    {
        $safe = htmlspecialchars($initials, ENT_XML1);
        $svg  = '<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100">'
              . '<circle cx="50" cy="50" r="50" fill="' . $color . '"/>'
              . '<text x="50" y="50" dy=".38em" text-anchor="middle" fill="white" '
              . 'font-size="38" font-family="sans-serif" font-weight="bold">' . $safe . '</text>'
              . '</svg>';
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    private function statusLabelFor(string $status): string
    {
        return match ($status) {
            'active'    => 'Actif',
            'candidate' => 'Candidat',
            'suspended' => 'Suspendu',
            'excluded'  => 'Exclu',
            'alumni'    => 'Alumni',
            default     => $status,
        };
    }
}
