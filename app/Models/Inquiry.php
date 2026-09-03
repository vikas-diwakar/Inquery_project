<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inquiry extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'company_id',
        'project_id',
        'project_unit_id',
        'customer_name',
        'phone',
        'email',
        'budget',
        'flat_type',
        'message',
        'description',
        'selected_unit_option_id',
        'status',
        'assigned_to',
        'next_follow_up_date',
        'last_follow_up_date',
        'follow_up_notes',
        'source',
        'type',
        'whatsapp_sent_at',
        'whatsapp_status',
        'whatsapp_last_message',
        'lead_score',
        'lead_grade',
        'score_breakdown',
        'allocated_at',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'next_follow_up_date' => 'datetime',
        'last_follow_up_date' => 'datetime',
        'whatsapp_sent_at' => 'datetime',
        'allocated_at' => 'datetime',
        'lead_score' => 'integer',
        'score_breakdown' => 'array',
    ];

    /**
     * Get badge CSS classes and icon for lead intent grade
     */
    public function getGradeBadgeAttribute(): array
    {
        return match ($this->lead_grade) {
            'hot' => [
                'label' => '🔥 HOT',
                'class' => 'bg-rose-50 text-rose-700 border-rose-200 shadow-2xs font-extrabold',
            ],
            'warm' => [
                'label' => '☀️ WARM',
                'class' => 'bg-amber-50 text-amber-700 border-amber-200 font-bold',
            ],
            'cold' => [
                'label' => '❄️ COLD',
                'class' => 'bg-slate-100 text-slate-600 border-slate-200 font-medium',
            ],
            default => [
                'label' => '❄️ COLD',
                'class' => 'bg-slate-100 text-slate-600 border-slate-200',
            ],
        };
    }

    /**
     * Check if WhatsApp brochure was sent
     */
    public function hasWhatsAppBeenSent(): bool
    {
        return !is_null($this->whatsapp_sent_at) && $this->whatsapp_status === 'sent';
    }

    /**
     * Get the company that owns this inquiry
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the project for this inquiry
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user assigned to this inquiry
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the selected unit option for this inquiry
     */
    public function selectedUnitOption(): BelongsTo
    {
        return $this->belongsTo(ProjectUnitOption::class, 'selected_unit_option_id');
    }

    /**
     * Get the assigned specific physical unit for this inquiry
     */
    public function projectUnit(): BelongsTo
    {
        return $this->belongsTo(ProjectUnit::class, 'project_unit_id');
    }

    /**
     * Get scheduled & sent lead drip logs for this inquiry
     */
    public function dripLogs(): HasMany
    {
        return $this->hasMany(InquiryDripLog::class)->orderBy('scheduled_for');
    }

    /**
     * Inquiry status change history
     */
    public function statusHistories()
    {
        return $this->hasMany(InquiryStatusHistory::class);
    }

    /**
     * Get all follow-ups for this inquiry
     */
    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    /**
     * Scope to get inquiries with overdue follow-ups
     */
    public function scopeOverdueFollowUps($query)
    {
        return $query->whereNotNull('next_follow_up_date')
            ->where('next_follow_up_date', '<', now())
            ->whereNotIn('status', ['booked', 'rejected']);
    }

    /**
     * Scope to get inquiries with upcoming follow-ups (within 7 days)
     */
    public function scopeUpcomingFollowUps($query)
    {
        return $query->whereNotNull('next_follow_up_date')
            ->whereBetween('next_follow_up_date', [now(), now()->addDays(7)])
            ->whereNotIn('status', ['booked', 'rejected']);
    }

    /**
     * Scope to get inquiries that need follow-up (overdue or upcoming today)
     */
    public function scopeNeedsFollowUp($query)
    {
        return $query->whereNotNull('next_follow_up_date')
            ->where('next_follow_up_date', '<=', now()->endOfDay())
            ->whereNotIn('status', ['booked', 'rejected']);
    }
}
