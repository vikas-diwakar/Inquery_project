<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $user = Auth::user();

            // Tenant scope check: If accessing from a company workspace subdomain, ensure user belongs to this company!
            if (app()->bound('currentTenant') && ($currentTenant = app('currentTenant'))) {
                if ((int) $user->company_id !== (int) $currentTenant->id) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')
                        ->withInput($request->only('email'))
                        ->with('error', "Your account is not a member of the {$currentTenant->name} workspace.");
                }
            }

            // Prevent login for unverified emails
            if (!$user->hasVerifiedEmail()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withInput($request->only('email'))
                    ->with('error', 'Your email address is not verified. Please verify your email before signing in.')
                    ->with('unverified_email', $user->email);
            }

            $request->session()->regenerate();

            $company = $user->company;

            // Check subscription status and redirect accordingly
            if ($company && $company->isFirstLogin()) {
                // First login - redirect to plan selection
                return redirect()->route('subscription.choose-plan');
            } elseif ($company && !$company->hasActiveSubscription()) {
                // No active subscription - redirect to subscription required
                return redirect()->route('subscription.required');
            }

            // If user logged in from root domain and has a dedicated subdomain, redirect to their workspace dashboard
            if (!app()->bound('currentTenant') && $company && $company->subdomain) {
                return redirect()->to($company->workspace_url . '/dashboard');
            }

            // Has active subscription - proceed to dashboard
            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    /**
     * Find company workspace and redirect to its login page
     */
    public function findWorkspace(Request $request)
    {
        $request->validate([
            'workspace' => 'required|string|max:100',
        ]);

        $query = strtolower(trim($request->input('workspace')));
        // Remove .propdrip.com or .localhost if entered by user
        $slug = preg_replace('/(\.localhost|\.propdrip\.com|\.propdrip\.test)$/i', '', $query);
        $slug = \Illuminate\Support\Str::slug($slug);

        $company = \App\Models\Company::where('subdomain', $slug)
            ->orWhere('name', 'like', "%{$query}%")
            ->where('is_active', true)
            ->first();

        if ($company && $company->subdomain) {
            return redirect()->to($company->workspace_url . '/login');
        }

        return redirect()->back()
            ->withInput()
            ->with('workspace_error', "No active workspace found for '{$query}'. Please check the spelling or ask your administrator.");
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
