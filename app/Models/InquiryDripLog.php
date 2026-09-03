<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InquiryDripLog extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'company_id',
        'inquiry_id',
        'lead_drip_step_id',
        'scheduled_for',
        'status',
        'sent_at',
        'sent_message',
        'last_error',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'sent_at' => 'datetime',
    ];

    /**
     * Get company that owns this log
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get inquiry for this log
     */
    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    /**
     * Get drip step definition
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(LeadDripStep::class, 'lead_drip_step_id');
    }

    /**
     * Get badge CSS class for log status
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'sent' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'pending' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'failed' => 'bg-rose-50 text-rose-700 border-rose-200',
            'skipped' => 'bg-slate-100 text-slate-600 border-slate-200',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
