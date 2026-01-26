<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory, HasTenant;

    protected $fillable = [
        'company_id',
        'name',
        'location',
        'description',
        'start_date',
        'status',
        'logo',
        'inquiry_qr_code',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    /**
     * Get the company that owns this project
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all inquiries for this project
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    /**
     * Get all brochures for this project
     */
    public function brochures(): HasMany
    {
        return $this->hasMany(Brochure::class);
    }

    /**
     * Get all users assigned to this project
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_user')
            ->withTimestamps();
    }

    /**
     * Get the unique inquiry form URL for this project
     * This URL is unique per company and per project (company_id + project_id)
     */
    public function getInquiryFormUrl(): string
    {
        return route('public.inquiry.form', ['project' => $this->id]);
    }

    /**
     * Get a unique identifier for this QR code
     * Format: company_id-project_id
     */
    public function getQrCodeIdentifier(): string
    {
        return "{$this->company_id}-{$this->id}";
    }
}
