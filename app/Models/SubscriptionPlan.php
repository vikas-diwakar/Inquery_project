<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'duration_months',
        'price',
        'currency',
        'features',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get all subscriptions for this plan
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Check if this is a trial plan
     */
    public function isTrial(): bool
    {
        return $this->type === 'trial';
    }

    /**
     * Check if this is a paid plan
     */
    public function isPaid(): bool
    {
        return $this->type === 'paid';
    }

    /**
     * Get active plans only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get trial plans
     */
    public function scopeTrial($query)
    {
        return $query->where('type', 'trial');
    }

    /**
     * Get paid plans
     */
    public function scopePaid($query)
    {
        return $query->where('type', 'paid');
    }
}
