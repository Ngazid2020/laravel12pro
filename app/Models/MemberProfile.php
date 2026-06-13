<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MemberProfile extends Model
{
    protected $fillable = [
        'user_id',
        'mentor_id',
        'company_name',
        'project_name',
        'sector',
        'city',
        'bio',
        'skills_offered',
        'needs_expressed',
        'social_links',
        'referral_code',
        'membership_status',
        'membership_expires_at',
        'activated_at',
        'admin_notes',
    ];

    protected $casts = [
        'skills_offered'       => 'array',
        'needs_expressed'      => 'array',
        'social_links'         => 'array',
        'membership_expires_at'=> 'date',
        'activated_at'         => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    // Affiliés directs de ce profil (via leur mentor_id)
    public function directMentees(): HasMany
    {
        return $this->hasMany(MemberProfile::class, 'mentor_id', 'user_id');
    }

    public function isActive(): bool
    {
        return $this->membership_status === 'active'
            && ($this->membership_expires_at === null || $this->membership_expires_at->isFuture());
    }

    public function isSuspended(): bool
    {
        return $this->membership_status === 'suspended';
    }
}
