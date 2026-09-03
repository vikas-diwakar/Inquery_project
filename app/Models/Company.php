<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'logo',
        'is_active',
        'subscription_status',
        'trial_ends_at',
        'subscription_ends_at',
        'trial_used',
        'whatsapp_provider',
        'whatsapp_api_key',
        'whatsapp_phone_number_id',
        'whatsapp_instance_id',
        'whatsapp_auto_send',
        'whatsapp_welcome_template',
        'lead_allocation_method',
        'last_allocated_user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'trial_used' => 'boolean',
        'whatsapp_auto_send' => 'boolean',
    ];

    /**
     * Get default WhatsApp welcome template
     */
    public function getDefaultWhatsAppTemplate(): string
    {
        if (!empty($this->whatsapp_welcome_template)) {
            return $this->whatsapp_welcome_template;
        }

        return "Hello {customer_name}! 👋\n\nThank you for inquiring about *{project_name}* at {company_name}.\n\n📄 *Download Official Project Brochure:*\n{brochure_url}\n\nOur team representative *{executive_name}* will be in touch with you shortly!\n\nBest regards,\n*{company_name}*";
    }

    /**
     * Get all users for this company
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all projects for this company
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get all inquiries for this company
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    /**
     * Get all brochures for this company
     */
    public function brochures(): HasMany
    {
        return $this->hasMany(Brochure::class);
    }

    /**
     * Get all roles for this company
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Get all subscriptions for this company
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Get the active subscription for this company
     */
    public function activeSubscription()
    {
        return $this->subscriptions()->active()->latest()->first();
    }

    /**
     * Check if company has active subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_status === 'active' || $this->subscription_status === 'trial';
    }

    /**
     * Check if company is on trial
     */
    public function onTrial(): bool
    {
        return $this->subscription_status === 'trial' &&
               $this->trial_ends_at &&
               $this->trial_ends_at->isFuture();
    }

    /**
     * Check if company subscription is expired
     */
    public function subscriptionExpired(): bool
    {
        return $this->subscription_status === 'expired' ||
               ($this->subscription_ends_at && $this->subscription_ends_at->isPast());
    }

    /**
     * Check if trial is expiring soon (within 7 days)
     */
    public function trialExpiringSoon(): bool
    {
        return $this->onTrial() &&
               $this->trial_ends_at->diffInDays(now()) <= 7;
    }

    /**
     * Check if subscription is expiring soon (within 7 days)
     */
    public function subscriptionExpiringSoon(): bool
    {
        return $this->hasActiveSubscription() &&
               $this->subscription_ends_at &&
               $this->subscription_ends_at->diffInDays(now()) <= 7;
    }

    /**
     * Check if company can use free trial
     */
    public function canUseTrial(): bool
    {
        // Can use trial if never used it before
        if (!$this->trial_used) {
            return true;
        }

        // Can use trial again if the last trial subscription has expired
        $lastTrial = $this->subscriptions()->where('status', 'trial')->latest()->first();
        if ($lastTrial && $lastTrial->isExpired()) {
            return true;
        }

        return false;
    }

    /**
     * Check if this is the first login (no subscriptions ever)
     */
    public function isFirstLogin(): bool
    {
        return $this->subscriptions()->count() === 0;
    }

    /**
     * Start trial period for the company
     */
    public function startTrial(): void
    {
        // Only set trial_used to true if this is the first time using trial
        if (!$this->trial_used) {
            $this->trial_used = true;
        }

        $this->update([
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addMonths(3),
        ]);
    }

    /**
     * Activate paid subscription
     */
    public function activateSubscription($endDate): void
    {
        $this->update([
            'subscription_status' => 'active',
            'subscription_ends_at' => $endDate,
        ]);
    }

    /**
     * Expire subscription
     */
    public function expireSubscription(): void
    {
        $this->update([
            'subscription_status' => 'expired',
        ]);
    }
}
