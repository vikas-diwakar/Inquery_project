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

            // Has active subscription - proceed to dashboard
            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
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
