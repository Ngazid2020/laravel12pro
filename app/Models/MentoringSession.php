<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class MentoringSession extends Model
{
    protected $fillable = [
        'mentor_id',
        'mentee_id',
        'scheduled_at',
        'held_at',
        'status',
        'notes',
        'confirmed_by_mentee',
    ];

    protected $casts = [
        'scheduled_at'       => 'datetime',
        'held_at'            => 'datetime',
        'confirmed_by_mentee'=> 'boolean',
    ];

    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function mentee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    public function pointEntries(): MorphMany
    {
        return $this->morphMany(PointEntry::class, 'pointable');
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed' && $this->confirmed_by_mentee;
    }
}
