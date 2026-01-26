<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CompanyRegistrationController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('company.register');
    }

    /**
     * Handle company registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|email|unique:companies,email',
            'company_phone' => 'nullable|string|max:20',
            'company_address' => 'nullable|string',
            'company_logo' => 'nullable|image|max:2048',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8|confirmed',
        ]);

        // Create company
        $company = Company::create([
            'name' => $validated['company_name'],
            'email' => $validated['company_email'],
            'phone' => $validated['company_phone'] ?? null,
            'address' => $validated['company_address'] ?? null,
            'is_active' => true,
            'subscription_status' => 'pending', // No automatic trial assignment
            'trial_used' => false,
        ]);

        // Handle logo upload
        if ($request->hasFile('company_logo')) {
            $logoPath = $request->file('company_logo')->store('logos', 'public');
            $company->logo = $logoPath;
            $company->save();
        }

        // Note: Trial subscription will be assigned on first login when user chooses a plan
        // No automatic trial assignment here

        // Create default Admin role
        $adminRole = Role::create([
            'company_id' => $company->id,
            'name' => 'Admin',
            'permissions' => ['*'], // Full access
        ]);

        // Create Manager role
        Role::create([
            'company_id' => $company->id,
            'name' => 'Manager',
            'permissions' => [
                'projects.view',
                'projects.create',
                'projects.edit',
                'inquiries.view',
                'inquiries.edit',
                'brochures.view',
                'brochures.create',
            ],
        ]);

        // Create Sales Executive role
        Role::create([
            'company_id' => $company->id,
            'name' => 'Sales Executive',
            'permissions' => [
                'inquiries.view',
                'inquiries.edit',
            ],
        ]);

        // Create admin user
        $user = User::create([
            'name' => $validated['admin_name'],
            'email' => $validated['admin_email'],
            'password' => Hash::make($validated['admin_password']),
            'company_id' => $company->id,
            'role_id' => $adminRole->id,
        ]);

        // Auto login
        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Company registered successfully!');
    }
}
