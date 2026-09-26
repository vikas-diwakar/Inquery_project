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

        return $this->sendCustomMessage($inquiry, $message, true);
    }

    /**
     * Send a custom or templated WhatsApp message to an inquiry
     */
    public function sendCustomMessage(Inquiry $inquiry, string $message, bool $force = true): array
    {
        $company = $inquiry->company;

        // Check if auto-send is enabled unless forced manually
        if (!$force && (!$company || !$company->whatsapp_auto_send)) {
            return [
                'success' => false,
                'message' => 'WhatsApp messaging is disabled in company settings.',
            ];
        }

        // Determine WhatsApp provider: use company setting if defined, otherwise fallback to centralized meta_cloud if configured
        $provider = $company->whatsapp_provider ?? null;
        if (empty($provider) || $provider === 'simulated') {
            if (!empty(config('services.social.whatsapp.phone_number_id')) || !empty(env('WHATSAPP_PHONE_NUMBER_ID'))) {
                $provider = 'meta_cloud';
            } else {
                $provider = 'simulated';
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
                    $success = $this->sendViaMetaCloud($company, $inquiry->phone, $message);
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
        if (empty($company->whatsapp_api_key) || empty($company->whatsapp_instance_id)) {
            $this->lastError = 'Missing UltraMsg Token or Instance ID';
            return false;
        }

        $instanceId = trim($company->whatsapp_instance_id);
        $token = trim($company->whatsapp_api_key);
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
    protected function sendViaMetaCloud($company, string $phone, string $message): bool
    {
        // 1. Check for Demo sandbox connection
        if (str_starts_with($company->whatsapp_phone_number_id ?? '', 'PHONE-')) {
            Log::info("Demo Meta WhatsApp Cloud Dispatch from {$company->whatsapp_connected_phone} to {$phone}: \n{$message}");
            return true;
        }

        $token = trim($company->whatsapp_api_key 
            ?: config('services.meta.system_user_token') 
            ?: config('services.social.whatsapp.access_token') 
            ?: env('WHATSAPP_ACCESS_TOKEN', ''));

        $phoneId = trim($company->whatsapp_phone_number_id 
            ?: config('services.social.whatsapp.phone_number_id') 
            ?: env('WHATSAPP_PHONE_NUMBER_ID', ''));

        if (empty($token) || empty($phoneId)) {
            $this->lastError = 'Meta Cloud API Error: Missing API key or Phone Number ID in company or centralized config';
            Log::warning($this->lastError);
            return false;
        }

        $toNumber = self::normalizePhoneNumber($phone);

        // 2. Try sending freeform text message (works within 24-hr customer service window)
        $response = Http::withToken($token)
            ->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $toNumber,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

        if ($response->successful()) {
            Log::info("Meta WhatsApp Cloud API text message sent successfully to {$toNumber}");
            return true;
        }

        $metaError = $response->json('error.message') ?? $response->body();
        Log::warning("Meta WhatsApp Cloud API Freeform Notice ({$response->status()}): {$metaError}. Attempting approved template fallback...");

        // 3. Fallback to pre-approved Meta Template (required if outside 24-hour window for business-initiated chats)
        $templateResponse = Http::withToken($token)
            ->post("https://graph.facebook.com/v19.0/{$phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $toNumber,
                'type' => 'template',
                'template' => [
                    'name' => 'hello_world',
                    'language' => ['code' => 'en_US']
                ]
            ]);

        if ($templateResponse->successful()) {
            Log::info("Meta WhatsApp sent via hello_world template fallback to {$toNumber}");
            return true;
        }

        $templateError = $templateResponse->json('error.message') ?? $templateResponse->body();
        $this->lastError = "Meta Cloud Error: {$templateError}";
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
