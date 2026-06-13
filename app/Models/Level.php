<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'min_points',
        'required_trainings',
        'required_months',
        'grants_mentor_status',
        'description',
        'badge_color',
        'order',
    ];

    protected $casts = [
        'min_points'          => 'integer',
        'required_trainings'  => 'integer',
        'required_months'     => 'integer',
        'grants_mentor_status'=> 'boolean',
        'order'               => 'integer',
    ];

    public function rewards(): HasMany
    {
        return $this->hasMany(LevelReward::class);
    }

    // Vérifie si un membre satisfait les conditions mixtes pour ce niveau
    public function isUnlockedBy(User $user): bool
    {
        $profile      = $user->profile;
        $totalPoints  = $user->total_points;
        $trainings    = $user->trainingEnrollments()->where('status', 'attended')->count();
        $months       = $profile?->activated_at
            ? (int) $profile->activated_at->diffInMonths(now())
            : 0;

        return $totalPoints >= $this->min_points
            && $trainings >= $this->required_trainings
            && $months >= $this->required_months;
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
