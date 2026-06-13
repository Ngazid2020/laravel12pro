<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Training extends Model
{
    protected $fillable = [
        'title',
        'description',
        'trainer_id',
        'prerequisites',
        'format',
        'capacity',
        'price_type',
        'price',
        'is_published',
    ];

    protected $casts = [
        'capacity'     => 'integer',
        'price'        => 'integer',
        'is_published' => 'boolean',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function isFree(): bool
    {
        return in_array($this->price_type, ['free', 'included']);
    }
}
