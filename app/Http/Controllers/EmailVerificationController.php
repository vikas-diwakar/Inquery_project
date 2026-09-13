<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Display email verification notice.
     */
    public function notice(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect()->route('dashboard')
            : view('auth.verify-email');
    }

    /**
     * Mark the authenticated/specified user's email address as verified.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        $loginUrl = ($user->company && $user->company->subdomain)
            ? ($user->company->workspace_url . '/login')
            : route('login');

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->to($loginUrl)->with('error', 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->to($loginUrl)->with('status', 'Email is already verified. Please sign in.');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->to($loginUrl)->with('status', 'Email verified successfully! You can now sign in.');
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            $loginUrl = ($user->company && $user->company->subdomain)
                ? ($user->company->workspace_url . '/login')
                : route('login');

            return redirect()->to($loginUrl)->with('status', 'Email is already verified. Please sign in.');
        }

        $user->sendEmailVerificationNotification();

        return redirect()->back()->with('status', 'A new verification link has been sent to your email address.');
    }
}
