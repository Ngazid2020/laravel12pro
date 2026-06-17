<?php

namespace App\Livewire\Member;

use App\Models\ContactRequest;
use App\Models\User;
use App\Notifications\ContactRequestReceived;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts.member')]
#[Title('Mise en relation')]
class Contacts extends Component
{
    use Toast;

    public bool   $showForm  = false;
    public ?int   $receiverId = null;
    public string $message   = '';

    public function send(): void
    {
        $this->validate([
            'receiverId' => 'required|exists:users,id|different:'.Auth::id(),
            'message'    => 'nullable|string|max:500',
        ]);

        $exists = ContactRequest::where('sender_id', Auth::id())
            ->where('receiver_id', $this->receiverId)
            ->exists();

        if ($exists) {
            $this->warning('Vous avez déjà envoyé une demande à ce membre.');
            return;
        }

        $cr = ContactRequest::create([
            'sender_id'   => Auth::id(),
            'receiver_id' => $this->receiverId,
            'message'     => $this->message ?: null,
            'status'      => 'pending',
        ]);

        // Notifie le destinataire (in-app + email)
        $cr->setRelation('sender', Auth::user());
        User::find($this->receiverId)?->notify(new ContactRequestReceived($cr));

        $this->reset(['showForm', 'receiverId', 'message']);
        $this->success('Demande de mise en relation envoyée.');
    }

    public function accept(int $requestId): void
    {
        ContactRequest::where('id', $requestId)
            ->where('receiver_id', Auth::id())
            ->update(['status' => 'accepted']);

        $this->success('Mise en relation acceptée.');
    }

    public function decline(int $requestId): void
    {
        ContactRequest::where('id', $requestId)
            ->where('receiver_id', Auth::id())
            ->update(['status' => 'declined']);

        $this->info('Demande refusée.');
    }

    public function cancel(int $requestId): void
    {
        ContactRequest::where('id', $requestId)
            ->where('sender_id', Auth::id())
            ->where('status', 'pending')
            ->delete();

        $this->info('Demande annulée.');
    }

    public function render()
    {
        // Demandes reçues en attente
        $received = ContactRequest::where('receiver_id', Auth::id())
            ->with('sender.profile')
            ->latest()
            ->get();

        // Demandes envoyées
        $sent = ContactRequest::where('sender_id', Auth::id())
            ->with('receiver.profile')
            ->latest()
            ->get();

        // Membres actifs (pour le formulaire d'envoi)
        $members = User::whereHas('profile', fn($q) => $q->where('membership_status', 'active'))
            ->where('id', '!=', Auth::id())
            ->whereNotIn('id', $sent->pluck('receiver_id'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $statusLabels = [
            'pending'  => 'En attente',
            'accepted' => 'Acceptée',
            'declined' => 'Refusée',
        ];

        $statusColors = [
            'pending'  => 'badge-warning',
            'accepted' => 'badge-success',
            'declined' => 'badge-ghost',
        ];

        return view('livewire.member.contacts', compact(
            'received', 'sent', 'members', 'statusLabels', 'statusColors'
        ));
    }
}
