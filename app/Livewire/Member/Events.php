<?php

namespace App\Livewire\Member;

use App\Models\Event;
use App\Models\EventRegistration;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('layouts.member')]
#[Title('Événements')]
class Events extends Component
{
    use WithPagination, Toast;

    public string $filter = 'upcoming'; // upcoming | past

    public function register(int $eventId): void
    {
        $event = Event::findOrFail($eventId);

        if ($event->isFull()) {
            $this->warning('Cet événement est complet.');
            return;
        }

        $exists = EventRegistration::where('event_id', $eventId)
            ->where('user_id', auth()->id())
            ->exists();

        if ($exists) {
            $this->warning('Vous êtes déjà inscrit à cet événement.');
            return;
        }

        EventRegistration::create([
            'event_id' => $eventId,
            'user_id'  => auth()->id(),
        ]);

        $this->success('Inscription confirmée !');
    }

    public function unregister(int $eventId): void
    {
        EventRegistration::where('event_id', $eventId)
            ->where('user_id', auth()->id())
            ->delete();

        $this->success('Inscription annulée.');
    }

    public function render()
    {
        $query = Event::published()->with('organizer');

        if ($this->filter === 'upcoming') {
            $query->where('starts_at', '>', now())->orderBy('starts_at');
        } else {
            $query->where('starts_at', '<=', now())->orderByDesc('starts_at');
        }

        $events = $query->paginate(8);

        $myRegistrations = EventRegistration::where('user_id', auth()->id())
            ->pluck('event_id')
            ->toArray();

        $typeLabels = [
            'networking'  => 'Networking',
            'conference'  => 'Conférence',
            'masterclass' => 'Masterclass',
            'workshop'    => 'Atelier',
        ];

        return view('livewire.member.events', compact('events', 'myRegistrations', 'typeLabels'));
    }
}
