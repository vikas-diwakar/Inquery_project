<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppSettingController extends Controller
{
    /**
     * Display WhatsApp integration settings form
     */
    public function index()
    {
        $company = auth()->user()->company;
        $defaultTemplate = $company->getDefaultWhatsAppTemplate();

        return view('settings.whatsapp', compact('company', 'defaultTemplate'));
    }

    /**
     * Update WhatsApp settings
     */
    public function update(Request $request)
    {
        $company = auth()->user()->company;

        $validated = $request->validate([
            'whatsapp_provider' => 'required|in:simulated,twilio,ultramsg,meta_cloud',
            'whatsapp_api_key' => 'nullable|string|max:500',
            'whatsapp_phone_number_id' => 'nullable|string|max:255',
            'whatsapp_instance_id' => 'nullable|string|max:255',
            'whatsapp_auto_send' => 'nullable|boolean',
            'whatsapp_welcome_template' => 'nullable|string|max:2000',
        ]);

        $company->update([
            'whatsapp_provider' => $validated['whatsapp_provider'],
            'whatsapp_api_key' => $validated['whatsapp_api_key'] ?? null,
            'whatsapp_phone_number_id' => $validated['whatsapp_phone_number_id'] ?? null,
            'whatsapp_instance_id' => $validated['whatsapp_instance_id'] ?? null,
            'whatsapp_auto_send' => $request->has('whatsapp_auto_send'),
            'whatsapp_welcome_template' => $validated['whatsapp_welcome_template'] ?? null,
        ]);

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
