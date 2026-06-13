<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevelReward extends Model
{
    protected $fillable = [
        'level_id',
        'type',
        'description',
        'value',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }
}
