<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class MemberProfile extends Model
{
    use HasRecursiveRelationships;

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

    // adjacency-list utilise "parent_id" par défaut ; on le pointe vers mentor_id
    public function getParentKeyName(): string
    {
        return 'mentor_id';
    }

    // La clé locale pointée par les enfants est user_id (et non id)
    public function getLocalKeyName(): string
    {
        return 'user_id';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

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

    public function statusLabel(): string
    {
        return match ($this->membership_status) {
            'active'    => 'Actif',
            'candidate' => 'Candidat',
            'suspended' => 'Suspendu',
            'excluded'  => 'Exclu',
            'alumni'    => 'Alumni',
            default     => 'Inconnu',
        };
    }

    public function statusColor(): string
    {
        return match ($this->membership_status) {
            'active'    => 'badge-success',
            'candidate' => 'badge-info',
            'suspended' => 'badge-warning',
            'excluded'  => 'badge-error',
            'alumni'    => 'badge-ghost',
            default     => 'badge-ghost',
        };
    }
}
