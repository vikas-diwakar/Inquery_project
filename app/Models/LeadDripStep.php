<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeadDripStep extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'company_id',
        'project_id',
        'day_offset',
        'step_title',
        'channel',
        'message_template',
        'is_active',
    ];

    protected $casts = [
        'day_offset' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get company for this drip step
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get project for this drip step
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get logs associated with this step
     */
    public function logs(): HasMany
    {
        return $this->hasMany(InquiryDripLog::class);
    }
}
