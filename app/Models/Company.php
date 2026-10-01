<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'logo',
        'is_active',
        'whatsapp_provider',
        'whatsapp_api_key',
        'whatsapp_phone_number_id',
        'whatsapp_waba_id',
        'whatsapp_connected_phone',
        'whatsapp_account_status',
        'whatsapp_connected_at',
        'whatsapp_instance_id',
        'whatsapp_auto_send',
        'whatsapp_welcome_template',
        'lead_allocation_method',
        'last_allocated_user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'whatsapp_auto_send' => 'boolean',
        'whatsapp_connected_at' => 'datetime',
    ];

    /**
     * Get default WhatsApp welcome template
     */
    public function getDefaultWhatsAppTemplate(): string
    {
        if (!empty($this->whatsapp_welcome_template)) {
            return $this->whatsapp_welcome_template;
        }

        return "Hello {customer_name}! 👋\n\nThank you for inquiring about *{project_name}* at {company_name}.\n\n📄 *Download Official Project Brochure:*\n{brochure_url}\n\nOur team representative *{executive_name}* will be in touch with you shortly!\n\nBest regards,\n*{company_name}*";
    }

    /**
     * Get all users for this company
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get all projects for this company
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get all inquiries for this company
     */
    public function inquiries(): HasMany
    {
        return $this->hasMany(Inquiry::class);
    }

    /**
     * Get all brochures for this company
     */
    public function brochures(): HasMany
    {
        return $this->hasMany(Brochure::class);
    }

    /**
     * Get all roles for this company
     */
    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    /**
     * Get the default/primary company instance for standalone CRM operation.
     */
    public static function default(): self
    {
        $company = static::first();
        if (!$company) {
            $company = static::create([
                'name' => config('app.name', 'Real Estate CRM'),
                'email' => 'admin@example.com',
                'is_active' => true,
            ]);
        }
        return $company;
    }

    /**
     * Regenerate all Form QR codes and Brochure QR codes for this company
     */
    public function regenerateAllQrCodes(): array
    {
        $this->refresh();

        $projectCount = 0;
        $brochureCount = 0;

        // Regenerate Form QR codes for all projects
        $projects = $this->projects()->get();
        foreach ($projects as $project) {
            $project->generateQrCode();
            $projectCount++;
        }

        // Regenerate Brochure QR codes for all brochures
        $brochures = $this->brochures()->get();
        foreach ($brochures as $brochure) {
            $brochure->generateQrCode();
            $brochureCount++;
        }

        return [
            'projects_count' => $projectCount,
            'brochures_count' => $brochureCount,
        ];
    }
}
