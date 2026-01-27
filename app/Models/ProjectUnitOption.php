<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectUnitOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'option_name',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * Get the project that owns this unit option
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get all inquiries that selected this unit option
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class, 'selected_unit_option_id');
    }
}
