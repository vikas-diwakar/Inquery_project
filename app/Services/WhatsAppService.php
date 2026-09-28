<?php

namespace App\Services;

use App\Models\Inquiry;
use App\Models\Brochure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Last API error message for diagnostics
     */
    protected string $lastError = '';

    /**
     * Send instant WhatsApp brochure & welcome message for an inquiry
     */
    public function sendInstantBrochure(Inquiry $inquiry, bool $force = false): array
    {
        $company = $inquiry->company;

        // Check if auto-send is enabled unless forced manually
        if (!$force && (!$company || !$company->whatsapp_auto_send)) {
            return [
                'success' => false,
                'message' => 'WhatsApp auto-send is disabled in company settings.',
            ];
        }

        // Determine Brochure URL
        $brochure = Brochure::where('project_id', $inquiry->project_id)->latest()->first();
        $brochureUrl = $brochure 
            ? $brochure->getDownloadUrl()
            : ($inquiry->project ? $inquiry->project->getInquiryFormUrl() : url('/'));

        // Assigned Executive Name
        $executiveName = $inquiry->assignedUser ? $inquiry->assignedUser->name : 'Sales Desk';

        // Compile Template
        $template = $company ? $company->getDefaultWhatsAppTemplate() : '';
        $message = str_replace(
            ['{customer_name}', '{project_name}', '{company_name}', '{brochure_url}', '{executive_name}'],
            [$inquiry->customer_name, $inquiry->project->name ?? 'Project', $company->name ?? 'PropDrip', $brochureUrl, $executiveName],
            $template
        );

        $templateParams = [
            $inquiry->customer_name ?: 'Valued Customer',
            $inquiry->project->name ?? 'Project',
            $company->name ?? 'PropDrip',
            $brochureUrl,
            $executiveName,
        ];

        return $this->sendCustomMessage($inquiry, $message, true, $templateParams);
    }

    /**
     * Send a custom or templated WhatsApp message to an inquiry
     */
    public function sendCustomMessage(Inquiry $inquiry, string $message, bool $force = true, array $templateParams = []): array
    {
        $company = $inquiry->company;

        // Check if auto-send is enabled unless forced manually
        if (!$force && (!$company || !$company->whatsapp_auto_send)) {
            return [
                'success' => false,
                'message' => 'WhatsApp messaging is disabled in company settings.',
            ];
        }

        // Determine WhatsApp provider:
        // 1. Explicit company setting if connected
        // 2. Explicit WHATSAPP_PROVIDER env variable
        // 3. Environment-based: in 'local' use UltraMsg for instant testing without template review; in 'production' use official Meta Cloud API
        $provider = $company->whatsapp_provider ?? null;
        if (empty($provider) || $provider === 'simulated') {
            if (!empty(env('WHATSAPP_PROVIDER'))) {
                $provider = env('WHATSAPP_PROVIDER');
            } elseif (app()->isLocal() || config('app.env') === 'local') {
                // LOCAL ENVIRONMENT: Prefer UltraMsg for zero-template, instant delivery
                if (!empty(env('ULTRAMSG_INSTANCE_ID')) || !empty(env('WHATSAPP_INSTANCE_ID')) || !empty(config('services.ultramsg.instance_id'))) {
                    $provider = 'ultramsg';
                } elseif (!empty(config('services.whatsapp.phone_number_id')) || !empty(env('WHATSAPP_PHONE_NUMBER_ID'))) {
                    $provider = 'meta_cloud';
                } else {
                    $provider = 'simulated';
                }
            } else {
                // PRODUCTION ENVIRONMENT: Always use official Meta WhatsApp Cloud API
                if (!empty(config('services.whatsapp.phone_number_id')) || !empty(env('WHATSAPP_PHONE_NUMBER_ID')) || !empty(config('services.social.whatsapp.phone_number_id'))) {
                    $provider = 'meta_cloud';
                } else {
                    $provider = 'simulated';
                }
            }
        }
        $success = false;
        $responseMsg = '';
        $this->lastError = '';

        try {
            switch ($provider) {
                case 'twilio':
                    $success = $this->sendViaTwilio($company, $inquiry->phone, $message);
                    $responseMsg = $success ? 'Sent via Twilio API' : ($this->lastError ?: 'Twilio API Error');
                    break;

                case 'ultramsg':
                    $success = $this->sendViaUltraMsg($company, $inquiry->phone, $message);
                    $responseMsg = $success ? 'Sent via UltraMsg API' : ($this->lastError ?: 'UltraMsg API Error');
                    break;

                case 'meta_cloud':
                    $success = $this->sendViaMetaCloud($company, $inquiry->phone, $message, $templateParams);
                    $responseMsg = $success ? 'Sent via Meta Cloud API' : ($this->lastError ?: 'Meta Cloud API Error');
                    break;

                case 'simulated':
                default:
                    // Simulated instant delivery for development / testing
                    Log::info("Simulated WhatsApp Delivery to {$inquiry->phone}: \n{$message}");
                    $success = true;
                    $responseMsg = 'Delivered instantly (Simulated Mode)';
                    break;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp Delivery Exception: ' . $e->getMessage());
            $success = false;
            $responseMsg = 'Delivery error: ' . $e->getMessage();
        }

        // Update inquiry WhatsApp tracking status if the inquiry is already persisted in DB
        if ($inquiry->exists) {
            $inquiry->update([
                'whatsapp_sent_at' => $success ? now() : $inquiry->whatsapp_sent_at,
                'whatsapp_status' => $success ? 'sent' : 'failed',
                'whatsapp_last_message' => $message,
            ]);
        }

        return [
            'success' => $success,
            'message' => $responseMsg,
            'whatsapp_message' => $message,
        ];
    }

    /**
     * Send message via Twilio API
     */
    protected function sendViaTwilio($company, string $phone, string $message): bool
    {
        if (empty($company->whatsapp_api_key) || empty($company->whatsapp_phone_number_id)) {
            $this->lastError = 'Missing Twilio Auth Token or Account SID';
            return false;
        }

        $accountSid = trim($company->whatsapp_phone_number_id);
        $authToken = trim($company->whatsapp_api_key);
        $fromNumber = $company->whatsapp_instance_id ?? 'whatsapp:+14155238886';
        $toNumber = 'whatsapp:+' . self::normalizePhoneNumber($phone);

        $response = Http::withBasicAuth($accountSid, $authToken)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                'From' => $fromNumber,
                'To' => $toNumber,
                'Body' => $message,
            ]);

        if ($response->successful()) {
            return true;
        }

        $this->lastError = 'Twilio Error: ' . ($response->json('message') ?? $response->body());
        Log::error($this->lastError);
        return false;
    }

    /**
     * Send message via UltraMsg API
     */
    protected function sendViaUltraMsg($company, string $phone, string $message): bool
    {
        $instanceId = trim($company->whatsapp_instance_id 
            ?: $company->whatsapp_waba_id
            ?: config('services.ultramsg.instance_id') 
            ?: env('ULTRAMSG_INSTANCE_ID') 
            ?: env('WHATSAPP_INSTANCE_ID', ''));

        $token = trim($company->whatsapp_api_key 
            ?: config('services.ultramsg.token') 
            ?: env('ULTRAMSG_TOKEN') 
            ?: env('WHATSAPP_API_KEY', ''));

        if (empty($token) || empty($instanceId)) {
            $this->lastError = 'Missing UltraMsg Token or Instance ID in company or centralized .env';
            Log::warning($this->lastError);
            return false;
        }

        $toNumber = self::normalizePhoneNumber($phone);

        $response = Http::post("https://api.ultramsg.com/{$instanceId}/messages/chat", [
            'token' => $token,
            'to' => $toNumber,
            'body' => $message,
        ]);

        if ($response->successful()) {
            return true;
        }

        $this->lastError = 'UltraMsg Error: ' . ($response->json('error') ?? $response->body());
        Log::error($this->lastError);
        return false;
    }

    /**
     * Send message via Meta WhatsApp Cloud API
     */
    protected function sendViaMetaCloud($company, string $phone, string $message, array $templateParams = []): bool
    {
        // 1. Check for Demo sandbox connection
        if (str_starts_with($company->whatsapp_phone_number_id ?? '', 'PHONE-')) {
            Log::info("Demo Meta WhatsApp Cloud Dispatch from {$company->whatsapp_connected_phone} to {$phone}: \n{$message}");
            return true;
        }

        // Only use company-specific credentials if the company has an actively connected custom account
        $useCompanyCustom = ($company->whatsapp_account_status === 'connected' && !empty($company->whatsapp_api_key) && !empty($company->whatsapp_phone_number_id));

        $token = $useCompanyCustom 
            ? trim($company->whatsapp_api_key)
            : trim(config('services.whatsapp.access_token')
                ?: config('services.meta.system_user_token') 
                ?: config('services.social.whatsapp.access_token') 
                ?: env('WHATSAPP_ACCESS_TOKEN', ''));

        $phoneId = $useCompanyCustom 
            ? trim($company->whatsapp_phone_number_id)
            : trim(config('services.whatsapp.phone_number_id')
                ?: config('services.social.whatsapp.phone_number_id') 
                ?: env('WHATSAPP_PHONE_NUMBER_ID', ''));

        // If company token is empty, fallback to centralized
        if (empty($token)) {
            $token = trim(config('services.whatsapp.access_token') ?: config('services.meta.system_user_token') ?: config('services.social.whatsapp.access_token') ?: env('WHATSAPP_ACCESS_TOKEN', ''));
        }
        if (empty($phoneId)) {
            $phoneId = trim(config('services.whatsapp.phone_number_id') ?: config('services.social.whatsapp.phone_number_id') ?: env('WHATSAPP_PHONE_NUMBER_ID', ''));
        }

        if (empty($token) || empty($phoneId)) {
            $this->lastError = 'Meta Cloud API Error: Missing API key or Phone Number ID in company or centralized config';
            Log::warning($this->lastError);
            return false;
        }

        $toNumber = self::normalizePhoneNumber($phone);

        // 2. If template parameters are provided, prioritize the approved Meta Template for cold inquiries
        if (!empty($templateParams)) {
            $templateName = env('WHATSAPP_TEMPLATE_NAME', config('services.whatsapp.template_name', 'property_inquiry_brochure'));
            $templateLang = env('WHATSAPP_TEMPLATE_LANG', config('services.whatsapp.template_lang', 'en_IN'));

            $templateResponse = Http::withToken($token)
                ->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $toNumber,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => ['code' => $templateLang],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => array_map(function ($val) {
                                    return ['type' => 'text', 'text' => (string) $val];
                                }, $templateParams),
                            ]
                        ]
                    ]
                ]);

            if ($templateResponse->successful()) {
                Log::info("Meta WhatsApp Cloud API template ({$templateName}) sent successfully to {$toNumber} using Phone ID {$phoneId}");
                return true;
            }

            $templateError = $templateResponse->json('error.message') ?? $templateResponse->body();
            Log::warning("Meta WhatsApp Template Dispatch Notice ({$templateResponse->status()}): {$templateError}. Attempting text fallback...");
        }

        // 3. Try sending freeform text message (works within 24-hr customer service window)
        $response = Http::withToken($token)
            ->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $toNumber,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

        if ($response->successful()) {
            Log::info("Meta WhatsApp Cloud API text message sent successfully to {$toNumber} using Phone ID {$phoneId}");
            return true;
        }

        $metaError = $response->json('error.message') ?? $response->body();
        $this->lastError = "Meta Cloud Error: {$metaError}";
        Log::error($this->lastError);

        return false;
    }

    /**
     * Normalize international phone numbers (e.g. strips + spaces, prepends 91 if 10-digit)
     */
    public static function normalizePhoneNumber(string $phone, string $defaultCountryCode = '91'): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        $clean = ltrim($clean, '0');

        // If 10 digits (standard mobile number without country prefix), prepend default country code
        if (strlen($clean) === 10) {
            return $defaultCountryCode . $clean;
        }

        return $clean;
    }
}
