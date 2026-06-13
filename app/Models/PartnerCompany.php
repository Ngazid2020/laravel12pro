<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PartnerCompany extends Model
{
    protected $fillable = [
        'name',
        'description',
        'sector',
        'logo',
        'website',
        'contact_name',
        'contact_email',
        'contact_phone',
        'is_active',
        'show_publicly',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'show_publicly' => 'boolean',
    ];

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    public function opportunities(): HasMany
    {
        return $this->hasMany(Opportunity::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePublic($query)
    {
        return $query->where('show_publicly', true)->where('is_active', true);
    }
}
