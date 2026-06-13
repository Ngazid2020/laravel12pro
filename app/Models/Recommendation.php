<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recommendation extends Model
{
    protected $fillable = [
        'requester_id',
        'partner_company_id',
        'target_user_id',
        'need_description',
        'status',
        'examined_by',
        'examined_at',
        'transmitted_at',
        'outcome_notes',
        'estimated_value',
    ];

    protected $casts = [
        'examined_at'    => 'datetime',
        'transmitted_at' => 'datetime',
        'estimated_value'=> 'integer',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function partnerCompany(): BelongsTo
    {
        return $this->belongsTo(PartnerCompany::class);
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'examined_by');
    }

    public function isClosed(): bool
    {
        return $this->status === 'deal_closed';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
