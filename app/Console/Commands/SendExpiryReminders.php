<?php

namespace App\Console\Commands;

use App\Mail\MembershipExpiringSoonMail;
use App\Models\MemberProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendExpiryReminders extends Command
{
    protected $signature = 'membres:expiry-reminders';
    protected $description = 'Envoie les rappels aux membres dont la cotisation expire dans 30 jours';

    public function handle(): int
    {
        $profiles = MemberProfile::where('membership_status', 'active')
            ->whereBetween('membership_expires_at', [now(), now()->addDays(30)])
            ->with('user')
            ->get();

        foreach ($profiles as $profile) {
            Mail::to($profile->user->email)
                ->queue(new MembershipExpiringSoonMail($profile));
        }

        $this->info("Rappels mis en file pour {$profiles->count()} membre(s).");

        return self::SUCCESS;
    }
}
