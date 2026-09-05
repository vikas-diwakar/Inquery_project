<?php

namespace App\Services;

use App\Models\Inquiry;
use App\Models\Brochure;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
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
            ? route('public.brochure.download', $brochure)
            : route('public.inquiry.form', $inquiry->project);

        // Assigned Executive Name
        $executiveName = $inquiry->assignedUser ? $inquiry->assignedUser->name : 'Sales Desk';

        // Compile Template
        $template = $company->getDefaultWhatsAppTemplate();
        $message = str_replace(
            ['{customer_name}', '{project_name}', '{company_name}', '{brochure_url}', '{executive_name}'],
            [$inquiry->customer_name, $inquiry->project->name ?? 'Project', $company->name ?? 'PropDrip', $brochureUrl, $executiveName],
            $template
        );

        $provider = $company->whatsapp_provider ?? 'simulated';
        $success = false;
        $responseMsg = '';

        try {
            switch ($provider) {
                case 'twilio':
                    $success = $this->sendViaTwilio($company, $inquiry->phone, $message);
                    $responseMsg = $success ? 'Sent via Twilio API' : 'Twilio API Error';
                    break;

                case 'ultramsg':
                    $success = $this->sendViaUltraMsg($company, $inquiry->phone, $message);
                    $responseMsg = $success ? 'Sent via UltraMsg API' : 'UltraMsg API Error';
                    break;

                case 'meta_cloud':
                    $success = $this->sendViaMetaCloud($company, $inquiry->phone, $message);
                    $responseMsg = $success ? 'Sent via Meta Cloud API' : 'Meta Cloud API Error';
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

        // Update inquiry WhatsApp tracking status
        $inquiry->update([
            'whatsapp_sent_at' => $success ? now() : $inquiry->whatsapp_sent_at,
            'whatsapp_status' => $success ? 'sent' : 'failed',
            'whatsapp_last_message' => $message,
        ]);

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
            return false;
        }

        // Twilio API format implementation
        $accountSid = $company->whatsapp_phone_number_id;
        $authToken = $company->whatsapp_api_key;
        $fromNumber = $company->whatsapp_instance_id ?? 'whatsapp:+14155238886';
        $toNumber = 'whatsapp:' . preg_replace('/[^0-9+]/', '', $phone);

        $response = Http::withBasicAuth($accountSid, $authToken)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                'From' => $fromNumber,
                'To' => $toNumber,
                'Body' => $message,
            ]);

        return $response->successful();
    }

    /**
     * Send message via UltraMsg API
     */
    protected function sendViaUltraMsg($company, string $phone, string $message): bool
    {
        if (empty($company->whatsapp_api_key) || empty($company->whatsapp_instance_id)) {
            return false;
        }

        $instanceId = $company->whatsapp_instance_id;
        $token = $company->whatsapp_api_key;
        $toNumber = preg_replace('/[^0-9]/', '', $phone);

        $response = Http::post("https://api.ultramsg.com/{$instanceId}/messages/chat", [
            'token' => $token,
            'to' => $toNumber,
            'body' => $message,
        ]);

        return $response->successful();
    }

    /**
     * Send message via Meta WhatsApp Cloud API
     */
    protected function sendViaMetaCloud($company, string $phone, string $message): bool
    {
        if (empty($company->whatsapp_api_key) || empty($company->whatsapp_phone_number_id)) {
            Log::warning('Meta Cloud API Error: Missing API key or Phone Number ID');
            return false;
        }

        $phoneId = trim($company->whatsapp_phone_number_id);
        $token = trim($company->whatsapp_api_key);
        $toNumber = preg_replace('/[^0-9]/', '', $phone);

        // 1. Try sending freeform text message
        $response = Http::withToken($token)
            ->post("https://graph.facebook.com/v18.0/{$phoneId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $toNumber,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

        if ($response->successful()) {
            Log::info("Meta WhatsApp Cloud API text message sent successfully to {$toNumber}");
            return true;
        }

        Log::error("Meta WhatsApp Cloud API Error ({$response->status()}): " . $response->body());

        // 2. If freeform text fails (Meta 24-hour window restriction), fallback to approved template
        $templateResponse = Http::withToken($token)
            ->post("https://graph.facebook.com/v18.0/{$phoneId}/messages", [
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

        Log::error("Meta WhatsApp Template Fallback Error ({$templateResponse->status()}): " . $templateResponse->body());

        return false;
    }
}
