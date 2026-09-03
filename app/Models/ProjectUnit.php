<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectUnit extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'company_id',
        'project_id',
        'unit_number',
        'tower_name',
        'floor_number',
        'unit_type',
        'status',
        'price',
        'notes',
    ];

    protected $casts = [
        'floor_number' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Get company that owns this unit
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get project that owns this unit
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get badge CSS classes for unit status
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'available' => 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100',
            'on_hold' => 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100',
            'sold' => 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100',
            default => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    }
}
