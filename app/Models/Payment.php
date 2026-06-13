<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'payable_type',
        'payable_id',
        'subscription_plan_id',
        'method',
        'amount',
        'status',
        'transaction_reference',
        'screenshot_path',
        'cheque_number',
        'bank_name',
        'cheque_date',
        'notes',
        'validated_by',
        'validated_at',
        'receipt_path',
        'period_start',
        'period_end',
    ];

    protected $casts = [
        'amount'       => 'integer',
        'cheque_date'  => 'date',
        'validated_at' => 'datetime',
        'period_start' => 'date',
        'period_end'   => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isValidated(): bool
    {
        return $this->status === 'validated';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
