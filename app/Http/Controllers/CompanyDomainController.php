<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CompanyDomainController extends Controller
{
    /**
     * Show the company workspace and domain settings page
     */
    public function index()
    {
        $company = auth()->user()->company;

        if (!$company) {
            abort(404, 'Company not found.');
        }

        return view('settings.domain', compact('company'));
    }

    /**
     * Update the company workspace subdomain
     */
    public function update(Request $request)
    {
        $company = auth()->user()->company;

        if (!$company) {
            abort(404, 'Company not found.');
        }

        $subdomain = strtolower(trim($request->input('subdomain')));
        $request->merge(['subdomain' => $subdomain]);

        $request->validate([
            'subdomain' => [
                'required',
                'string',
                'alpha_dash',
                'min:3',
                'max:50',
                Rule::unique('companies', 'subdomain')->ignore($company->id),
                function ($attribute, $value, $fail) {
                    if (Company::isReservedSubdomain($value)) {
                        $fail('The workspace subdomain "' . $value . '" is reserved. Please choose another name.');
                    }
                },
            ],
        ]);

        $oldSubdomain = $company->subdomain;
        $company->update([
            'subdomain' => $subdomain,
        ]);

        // If the subdomain changed and we are currently on the old subdomain, redirect to the new workspace URL
        if ($oldSubdomain !== $subdomain) {
            $newUrl = $company->workspace_url . '/settings/domain';
            return redirect($newUrl)->with('success', "Workspace domain updated successfully to {$company->workspace_domain}!");
        }

        return redirect()->back()->with('success', 'Workspace domain settings saved successfully!');
    }

    /**
     * Check if a subdomain is available (AJAX API)
     */
    public function checkAvailability(Request $request)
    {
        $subdomain = strtolower(trim($request->input('subdomain', '')));
        $currentCompanyId = auth()->check() ? auth()->user()->company_id : null;

        if (empty($subdomain)) {
            return response()->json([
                'available' => false,
                'message' => 'Subdomain cannot be empty',
            ]);
        }

        if (strlen($subdomain) < 3 || strlen($subdomain) > 50) {
            return response()->json([
                'available' => false,
                'message' => 'Subdomain must be between 3 and 50 characters',
            ]);
        }

        if (!preg_match('/^[a-z0-9-]+$/', $subdomain)) {
            return response()->json([
                'available' => false,
                'message' => 'Subdomain may only contain letters, numbers, and hyphens',
            ]);
        }

        if (Company::isReservedSubdomain($subdomain)) {
            return response()->json([
                'available' => false,
                'message' => 'This subdomain is reserved. Please pick another name.',
            ]);
        }

        $query = Company::where('subdomain', $subdomain);
        if ($currentCompanyId) {
            $query->where('id', '!=', $currentCompanyId);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => !$exists,
            'subdomain' => $subdomain,
            'message' => $exists ? 'This subdomain is already taken.' : 'Available!',
        ]);
    }
}
