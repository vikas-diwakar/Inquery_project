<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppSettingController extends Controller
{
    /**
     * Display WhatsApp integration settings & Embedded Signup dashboard
     */
    public function index()
    {
        $company = auth()->user()->company;
        $defaultTemplate = $company->getDefaultWhatsAppTemplate();
        $metaAppId = config('services.meta.app_id');
        $metaConfigId = config('services.meta.config_id');

        return view('settings.whatsapp', compact('company', 'defaultTemplate', 'metaAppId', 'metaConfigId'));
    }

    /**
     * Handle Meta WhatsApp Embedded Signup OAuth Callback / PostMessage
     */
    public function handleEmbeddedCallback(Request $request)
    {
        $company = auth()->user()->company;

        Log::info("=== Meta WhatsApp Embedded Signup Callback Initiated ===", [
            'company_id' => $company->id,
            'company_name' => $company->name,
            'request_payload' => $request->all(),
        ]);

        $request->validate([
            'code' => 'nullable|string',
            'access_token' => 'nullable|string',
            'waba_id' => 'nullable|string',
            'phone_number_id' => 'nullable|string',
        ]);

        $appId = config('services.meta.app_id');
        $appSecret = config('services.meta.app_secret');
        $accessToken = $request->access_token;
        $wabaId = $request->waba_id;
        $phoneNumberId = $request->phone_number_id;
        $displayPhone = null;

        try {
            // 1. If authorization code is provided, exchange it for access token
            if ($request->filled('code') && $appId && $appSecret) {
                Log::info("Meta Token Exchange: Exchanging code for Access Token with Graph API v19.0...", [
                    'app_id' => $appId,
                    'code_preview' => substr($request->code, 0, 10) . '...',
                ]);

                $tokenResponse = Http::asJson()->get('https://graph.facebook.com/v19.0/oauth/access_token', [
                    'client_id' => $appId,
                    'client_secret' => $appSecret,
                    'code' => $request->code,
                ]);

                Log::info("Meta Token Exchange Response", [
                    'status' => $tokenResponse->status(),
                    'body' => $tokenResponse->json() ?? $tokenResponse->body(),
                ]);

                if ($tokenResponse->successful()) {
                    $tokenData = $tokenResponse->json();
                    $accessToken = $tokenData['access_token'] ?? $accessToken;
                } else {
                    Log::warning('Meta Code Exchange Warning: ' . $tokenResponse->body());
                }
            }

            // Fallback to configured System User Token if user token exchange did not yield one
            if (empty($accessToken) && config('services.meta.system_user_token')) {
                Log::info("Using fallback META_SYSTEM_USER_TOKEN from config.");
                $accessToken = config('services.meta.system_user_token');
            }

            // 2. Fetch phone number details and WABA info if token is present
            if (!empty($accessToken)) {
                // If phone_number_id is available, fetch its display name and formatted phone number
                if ($phoneNumberId) {
                    Log::info("Fetching Phone Number details from Meta Graph API for Phone ID: {$phoneNumberId}");
                    $phoneResp = Http::withToken($accessToken)
                        ->get("https://graph.facebook.com/v19.0/{$phoneNumberId}", [
                            'fields' => 'id,display_phone_number,verified_name,code_verification_status,quality_rating',
                        ]);

                    Log::info("Meta Phone Details Response", [
                        'status' => $phoneResp->status(),
                        'data' => $phoneResp->json(),
                    ]);

                    if ($phoneResp->successful()) {
                        $phoneData = $phoneResp->json();
                        $displayPhone = $phoneData['display_phone_number'] ?? null;
                    }
                }

                // If waba_id is available but no phone_number_id was sent, fetch first phone number from WABA
                if ($wabaId && empty($phoneNumberId)) {
                    Log::info("Fetching phone numbers from WABA ID: {$wabaId}");
                    $wabaPhoneResp = Http::withToken($accessToken)
                        ->get("https://graph.facebook.com/v19.0/{$wabaId}/phone_numbers");

                    Log::info("Meta WABA Phone Numbers Response", [
                        'status' => $wabaPhoneResp->status(),
                        'data' => $wabaPhoneResp->json(),
                    ]);

                    if ($wabaPhoneResp->successful()) {
                        $phoneList = $wabaPhoneResp->json('data') ?? [];
                        if (!empty($phoneList)) {
                            $phoneNumberId = $phoneList[0]['id'] ?? $phoneNumberId;
                            $displayPhone = $phoneList[0]['display_phone_number'] ?? $displayPhone;
                        }
                    }

                    // Auto-subscribe SaaS App to WABA webhooks
                    $subResp = Http::withToken($accessToken)
                        ->post("https://graph.facebook.com/v19.0/{$wabaId}/subscribed_apps");

                    Log::info("Meta WABA Subscribed Apps Response", [
                        'status' => $subResp->status(),
                        'body' => $subResp->json() ?? $subResp->body(),
                    ]);
                }
            }

            // 3. Fallback display phone placeholder if not retrieved from Graph API
            if (empty($displayPhone)) {
                $displayPhone = $company->phone ?: 'WhatsApp Business Number';
            }

            // 4. Persist to company record
            $updatePayload = [
                'whatsapp_provider' => 'meta_cloud',
                'whatsapp_api_key' => $accessToken ?: $company->whatsapp_api_key,
                'whatsapp_waba_id' => $wabaId ?: $company->whatsapp_waba_id,
                'whatsapp_phone_number_id' => $phoneNumberId ?: $company->whatsapp_phone_number_id,
                'whatsapp_connected_phone' => $displayPhone,
                'whatsapp_account_status' => 'connected',
                'whatsapp_connected_at' => now(),
            ];

            $company->update($updatePayload);

            Log::info("=== Meta WhatsApp Connected Successfully for Company ===", [
                'company_id' => $company->id,
                'phone' => $displayPhone,
                'waba_id' => $wabaId,
                'phone_number_id' => $phoneNumberId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp Business Account successfully connected to ' . $company->name . '!',
                'phone' => $displayPhone,
                'waba_id' => $wabaId,
                'phone_number_id' => $phoneNumberId,
            ]);

        } catch (\Exception $e) {
            Log::error('Meta Embedded Signup Exception: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete connection: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Quick Demo Connect (Simulated 1-Click for Instant Testing without Meta App Review)
     */
    public function quickDemoConnect(Request $request)
    {
        $company = auth()->user()->company;

        $phone = $request->input('demo_phone', '+91 90000 ' . str_pad($company->id, 5, '1', STR_PAD_LEFT));
        $wabaId = 'WABA-' . rand(10000000, 99999999);
        $phoneId = 'PHONE-' . rand(10000000, 99999999);

        $company->update([
            'whatsapp_provider' => 'meta_cloud',
            'whatsapp_waba_id' => $wabaId,
            'whatsapp_phone_number_id' => $phoneId,
            'whatsapp_connected_phone' => $phone,
            'whatsapp_account_status' => 'connected',
            'whatsapp_connected_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', "WhatsApp Business Account connected successfully with number {$phone}!");
    }

    /**
     * Disconnect / Unlink WhatsApp Business Account
     */
    public function disconnect(Request $request)
    {
        $company = auth()->user()->company;

        $company->update([
            'whatsapp_account_status' => 'disconnected',
            'whatsapp_connected_phone' => null,
            'whatsapp_waba_id' => null,
            'whatsapp_phone_number_id' => null,
            'whatsapp_api_key' => null,
            'whatsapp_provider' => 'simulated',
        ]);

        return redirect()->back()
            ->with('success', 'WhatsApp Business Account has been disconnected.');
    }

    /**
     * Update WhatsApp settings (Template & Manual Gateway configurations)
     */
    public function update(Request $request)
    {
        $company = auth()->user()->company;

        $validated = $request->validate([
            'whatsapp_provider' => 'required|in:simulated,twilio,ultramsg,meta_cloud',
            'whatsapp_api_key' => 'nullable|string|max:500',
            'whatsapp_phone_number_id' => 'nullable|string|max:255',
            'whatsapp_waba_id' => 'nullable|string|max:255',
            'whatsapp_connected_phone' => 'nullable|string|max:255',
            'whatsapp_instance_id' => 'nullable|string|max:255',
            'whatsapp_auto_send' => 'nullable|boolean',
            'whatsapp_welcome_template' => 'nullable|string|max:2000',
        ]);

        $updateData = [
            'whatsapp_provider' => $validated['whatsapp_provider'],
            'whatsapp_api_key' => $validated['whatsapp_api_key'] ?? $company->whatsapp_api_key,
            'whatsapp_phone_number_id' => $validated['whatsapp_phone_number_id'] ?? $company->whatsapp_phone_number_id,
            'whatsapp_waba_id' => $validated['whatsapp_waba_id'] ?? $company->whatsapp_waba_id,
            'whatsapp_connected_phone' => $validated['whatsapp_connected_phone'] ?? $company->whatsapp_connected_phone,
            'whatsapp_instance_id' => $validated['whatsapp_instance_id'] ?? null,
            'whatsapp_auto_send' => $request->has('whatsapp_auto_send'),
            'whatsapp_welcome_template' => $validated['whatsapp_welcome_template'] ?? null,
        ];

        if ($validated['whatsapp_provider'] === 'meta_cloud' && !empty($updateData['whatsapp_phone_number_id'])) {
            $updateData['whatsapp_account_status'] = 'connected';
            $updateData['whatsapp_connected_at'] = $company->whatsapp_connected_at ?? now();
        }

        $company->update($updateData);

        return redirect()->back()
            ->with('success', 'WhatsApp Integration settings updated successfully!');
    }

    /**
     * Send test WhatsApp message
     */
    public function testSend(Request $request, WhatsAppService $whatsAppService)
    {
        $request->validate([
            'test_phone' => 'required|string|max:20',
        ]);

        $company = auth()->user()->company;

        // Dummy test inquiry object representation
        $testInquiry = new \App\Models\Inquiry([
            'customer_name' => 'John (Test Lead)',
            'phone' => $request->test_phone,
            'company_id' => $company->id,
            'project_id' => session('selected_project_id'),
        ]);

        $result = $whatsAppService->sendInstantBrochure($testInquiry, true);

        if ($result['success']) {
            return redirect()->back()
                ->with('success', 'Test WhatsApp Message Dispatched: ' . $result['message']);
        }

        return redirect()->back()
            ->with('error', 'WhatsApp Test Failed: ' . $result['message']);
    }
}
