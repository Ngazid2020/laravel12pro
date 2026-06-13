<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TrainingSession extends Model
{
    protected $fillable = [
        'training_id',
        'starts_at',
        'ends_at',
        'location',
        'meeting_link',
        'status',
        'materials',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'materials' => 'array',
    ];

    public function training(): BelongsTo
    {
        return $this->belongsTo(Training::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(TrainingEnrollment::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable');
    }

    public function attendeesCount(): int
    {
        return $this->enrollments()->where('status', 'attended')->count();
    }

    public function isFull(): bool
    {
        $capacity = $this->training->capacity ?? null;
        if ($capacity === null) {
            return false;
        }
        return $this->enrollments()->count() >= $capacity;
    }

    public function spotsLeft(): ?int
    {
        $capacity = $this->training->capacity ?? null;
        if ($capacity === null) {
            return null;
        }
        return max(0, $capacity - $this->enrollments()->count());
    }
}
