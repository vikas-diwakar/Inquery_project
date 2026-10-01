<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    /**
     * Show the company/business settings edit form.
     */
    public function edit()
    {
        $company = auth()->user()->company ?? Company::default();

        return view('settings.company', compact('company'));
    }

    /**
     * Update the company/business settings.
     */
    public function update(Request $request)
    {
        $company = auth()->user()->company ?? Company::default();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'logo' => 'nullable|image|max:2048',
            'lead_allocation_method' => 'nullable|in:manual,round_robin',
            'whatsapp_welcome_template' => 'nullable|string|max:1000',
        ]);

        $company->name = $validated['name'];
        $company->email = $validated['email'];
        $company->phone = $validated['phone'] ?? null;
        $company->address = $validated['address'] ?? null;

        if (isset($validated['lead_allocation_method'])) {
            $company->lead_allocation_method = $validated['lead_allocation_method'];
        }

        if (isset($validated['whatsapp_welcome_template'])) {
            $company->whatsapp_welcome_template = $validated['whatsapp_welcome_template'];
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $company->logo = $request->file('logo')->store('logos', 'public');
        } elseif ($request->boolean('remove_logo')) {
            if ($company->logo && Storage::disk('public')->exists($company->logo)) {
                Storage::disk('public')->delete($company->logo);
            }
            $company->logo = null;
        }

        $company->save();

        return redirect()->route('settings.company')
            ->with('success', 'Company details and logo updated successfully.');
    }
}