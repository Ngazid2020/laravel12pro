<?php

namespace App\Livewire\Member;

use App\Models\Training;
use App\Models\TrainingEnrollment;
use App\Models\TrainingSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('layouts.member')]
#[Title('Formations')]
class Trainings extends Component
{
    use WithPagination, Toast;

    public string $tab        = 'catalog';
    public string $search     = '';
    public string $format     = '';
    public string $priceType  = '';

    // Rating modal
    public bool   $showRating        = false;
    public ?int   $ratingEnrollmentId = null;
    public int    $rating            = 5;
    public string $ratingComment     = '';

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void  { $this->resetPage(); }
    public function updatedFormat(): void  { $this->resetPage(); }
    public function updatedPriceType(): void { $this->resetPage(); }

    public function enroll(int $sessionId): void
    {
        $session = TrainingSession::with('training')->findOrFail($sessionId);

        if ($session->status !== 'scheduled') {
            $this->warning('Cette session n\'est plus disponible à l\'inscription.');
            return;
        }

        if ($session->isFull()) {
            $this->warning('Cette session est complète.');
            return;
        }

        $exists = TrainingEnrollment::where('training_session_id', $sessionId)
            ->where('user_id', Auth::id())
            ->exists();

        if ($exists) {
            $this->warning('Vous êtes déjà inscrit à cette session.');
            return;
        }

        TrainingEnrollment::create([
            'training_session_id' => $sessionId,
            'user_id'             => Auth::id(),
            'status'              => 'enrolled',
        ]);

        $this->success('Inscription confirmée pour « '.$session->training->title.' » !');
    }

    public function unenroll(int $sessionId): void
    {
        $session = TrainingSession::findOrFail($sessionId);

        if ($session->starts_at->isPast()) {
            $this->error('Impossible d\'annuler une session déjà passée.');
            return;
        }

        TrainingEnrollment::where('training_session_id', $sessionId)
            ->where('user_id', Auth::id())
            ->delete();

        $this->success('Inscription annulée.');
    }

    public function openRating(int $enrollmentId): void
    {
        $enrollment = TrainingEnrollment::where('id', $enrollmentId)
            ->where('user_id', Auth::id())
            ->first();

        if (! $enrollment) {
            return;
        }

        $this->ratingEnrollmentId = $enrollmentId;
        $this->rating             = 5;
        $this->ratingComment      = '';
        $this->showRating         = true;
    }

    public function submitRating(): void
    {
        $this->validate([
            'rating'        => 'required|integer|min:1|max:5',
            'ratingComment' => 'nullable|string|max:500',
        ]);

        TrainingEnrollment::where('id', $this->ratingEnrollmentId)
            ->where('user_id', Auth::id())
            ->update([
                'rating'  => $this->rating,
                'comment' => $this->ratingComment ?: null,
            ]);

        $this->showRating = false;
        $this->success('Merci pour votre évaluation !');
    }

    public function render()
    {
        // --- Catalogue ---
        $trainingsQuery = Training::published()
            ->with(['trainer', 'sessions' => fn($q) => $q
                ->where('starts_at', '>', now())
                ->where('status', 'scheduled')
                ->withCount('enrollments')
                ->orderBy('starts_at')
            ]);

        if ($this->search) {
            $trainingsQuery->where('title', 'like', "%{$this->search}%");
        }
        if ($this->format) {
            $trainingsQuery->where('format', $this->format);
        }
        if ($this->priceType) {
            $trainingsQuery->where('price_type', $this->priceType);
        }

        $trainings = $trainingsQuery->paginate(8);

        // Sessions dans lesquelles l'utilisateur est inscrit (IDs)
        $mySessionIds = TrainingEnrollment::where('user_id', Auth::id())
            ->pluck('training_session_id')
            ->toArray();

        // --- Mes formations ---
        $myEnrollments = TrainingEnrollment::where('user_id', Auth::id())
            ->with(['trainingSession.training', 'trainingSession'])
            ->get()
            ->sortByDesc(fn($e) => $e->trainingSession->starts_at);

        [$upcoming, $past] = $myEnrollments->partition(
            fn($e) => $e->trainingSession->starts_at->isFuture()
        );

        $formatLabels = [
            'in_person' => 'Présentiel',
            'online'    => 'En ligne',
            'hybrid'    => 'Hybride',
        ];

        $priceLabels = [
            'free'     => 'Gratuite',
            'included' => 'Incluse',
            'premium'  => 'Premium',
        ];

        $statusLabels = [
            'enrolled' => 'Inscrit',
            'attended' => 'Suivi',
            'absent'   => 'Absent',
        ];

        return view('livewire.member.trainings', compact(
            'trainings', 'mySessionIds', 'upcoming', 'past',
            'formatLabels', 'priceLabels', 'statusLabels'
        ));
    }
}
