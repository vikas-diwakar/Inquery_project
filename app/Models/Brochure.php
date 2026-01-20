<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Brochure extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'company_id',
        'project_id',
        'file_path',
        'file_name',
        'qr_code',
    ];

    /**
     * Get the company that owns this brochure
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the project for this brochure
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
