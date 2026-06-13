<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(['super_admin', 'admin']);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}") ?: $this->name;
    }

    // Relations
    public function profile(): HasOne
    {
        return $this->hasOne(MemberProfile::class);
    }

    public function candidature(): HasOne
    {
        return $this->hasOne(CandidatureApplication::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function pointEntries(): HasMany
    {
        return $this->hasMany(PointEntry::class);
    }

    // Affiliés dont cet utilisateur est le mentor
    public function mentees(): HasMany
    {
        return $this->hasMany(MemberProfile::class, 'mentor_id');
    }

    // Sessions de mentorat en tant que mentor
    public function mentoringSessions(): HasMany
    {
        return $this->hasMany(MentoringSession::class, 'mentor_id');
    }

    // Sessions de mentorat en tant que mentoré
    public function menteeSessions(): HasMany
    {
        return $this->hasMany(MentoringSession::class, 'mentee_id');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class, 'requester_id');
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class, 'published_by');
    }

    public function trainingEnrollments(): HasMany
    {
        return $this->hasMany(TrainingEnrollment::class);
    }

    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function sentContactRequests(): HasMany
    {
        return $this->hasMany(ContactRequest::class, 'sender_id');
    }

    public function receivedContactRequests(): HasMany
    {
        return $this->hasMany(ContactRequest::class, 'receiver_id');
    }

    public function getTotalPointsAttribute(): int
    {
        return $this->pointEntries()->sum('points');
    }
}
