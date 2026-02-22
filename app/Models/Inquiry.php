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
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'next_follow_up_date' => 'datetime',
        'last_follow_up_date' => 'datetime',
    ];

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
