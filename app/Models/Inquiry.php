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
    ];

    protected $casts = [
        'budget' => 'decimal:2',
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
}
