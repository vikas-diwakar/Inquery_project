<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'subscription_plan_id',
        'start_date',
        'end_date',
        'status',
        'amount_paid',
        'currency',
        'payment_reference',
        'payment_details',
        'auto_renew',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'amount_paid' => 'decimal:2',
        'payment_details' => 'array',
        'auto_renew' => 'boolean',
    ];

    /**
     * Get the company that owns this subscription
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the subscription plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Check if subscription is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' || $this->status === 'trial';
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->end_date->isPast();
    }

    /**
     * Check if subscription is trial
     */
    public function isTrial(): bool
    {
        return $this->status === 'trial';
    }

    /**
     * Get days until expiry
     */
    public function daysUntilExpiry(): int
    {
        return Carbon::now()->diffInDays($this->end_date, false);
    }

    /**
     * Check if subscription is about to expire (within 7 days)
     */
    public function isExpiringSoon(): bool
    {
        return $this->daysUntilExpiry() <= 7 && $this->daysUntilExpiry() > 0;
    }

    /**
     * Scope for active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'trial']);
    }

    /**
     * Scope for expired subscriptions
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Scope for trial subscriptions
     */
    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }

    /**
     * Scope for subscriptions expiring soon
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('end_date', '>=', Carbon::now())
                    ->where('end_date', '<=', Carbon::now()->addDays(7));
    }
}
