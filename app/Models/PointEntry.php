<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PointEntry extends Model
{
    // Journal immuable : pas de updated_at
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'source',
        'points',
        'pointable_type',
        'pointable_id',
        'description',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'points'     => 'integer',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pointable(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
