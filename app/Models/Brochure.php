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

    /**
     * Get the encrypted URL-safe key for this brochure
     */
    public function getEncryptedKey(): string
    {
        return \App\Services\UrlCryptService::encrypt($this->id);
    }

    /**
     * Get the public download URL for this brochure
     * and uses an encrypted ID to guarantee privacy and prevent ID enumeration.
     */
    public function getDownloadUrl(): string
    {
        $encryptedKey = $this->getEncryptedKey();
        $company = $this->relationLoaded('company') ? $this->company : $this->company()->first();
        if ($company && !empty($company->subdomain)) {
            return rtrim($company->workspace_url, '/') . '/brochure/' . $encryptedKey . '/download';
        }

        return route('public.brochure.download', ['brochure' => $encryptedKey]);
    }

    /**
     * Generate and save the brochure QR code SVG image file
     */
    public function generateQrCode(): string
    {
        $downloadUrl = $this->getDownloadUrl();
        $qrCodePath = 'qrcodes/brochure_' . $this->id . '.svg';

        $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($downloadUrl);

        \Illuminate\Support\Facades\Storage::disk('public')->put($qrCodePath, $svg);

        $this->qr_code = $downloadUrl;
        $this->save();

        return $qrCodePath;
    }
}
