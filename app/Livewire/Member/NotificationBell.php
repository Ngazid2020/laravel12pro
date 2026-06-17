<?php

namespace App\Livewire\Member;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    public function markAllRead(): void
    {
        Auth::user()->unreadNotifications()->update(['read_at' => now()]);
    }

    public function render()
    {
        $notifications = Auth::user()->notifications()->latest()->limit(10)->get();
        $unreadCount   = Auth::user()->unreadNotifications()->count();

        return view('livewire.member.notification-bell', compact('notifications', 'unreadCount'));
    }
}
