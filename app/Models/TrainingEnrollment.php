<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TrainingEnrollment extends Model
{
    protected $fillable = [
        'training_session_id',
        'user_id',
        'status',
        'attended_at',
        'rating',
        'comment',
    ];

    protected $casts = [
        'attended_at' => 'datetime',
        'rating'      => 'integer',
    ];

    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pointEntries(): MorphMany
    {
        return $this->morphMany(PointEntry::class, 'pointable');
    }
}
