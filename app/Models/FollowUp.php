<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_id',
        'company_id',
        'follow_up_by',
        'type',
        'notes',
        'outcome',
        'scheduled_date',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the inquiry this follow-up is for
     */
    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }

    /**
     * Get the company this follow-up belongs to
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user who performed this follow-up
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'follow_up_by');
    }
}
