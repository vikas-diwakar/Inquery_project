<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        $company = $user->company;

        if (!$company) {
            return $next($request);
        }

        // Allow access to subscription-related routes
        $subscriptionRoutes = [
            'subscription.choose-plan',
            'subscription.activate-plan',
            'subscription.required',
            'subscription.index',
            'subscription.show',
            'subscription.checkout',
            'subscription.purchase',
            'subscription.renew',
            'subscription.cancel',
            'logout',
        ];

        if (in_array($request->route()?->getName(), $subscriptionRoutes)) {
            return $next($request);
        }

        // If company has active subscription, allow access
        if ($company->hasActiveSubscription()) {
            return $next($request);
        }

        // If company is on first login (no subscription ever), redirect to plan selection
        if ($company->isFirstLogin()) {
            return redirect()->route('subscription.choose-plan');
        }

        // No active subscription and not first login - redirect to subscription required
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Subscription expired. Please renew your subscription.',
                'subscription_required' => true
            ], 403);
        }

        return redirect()->route('subscription.required')
            ->with('error', 'Your subscription has expired. Please choose a subscription plan to continue.');
    }
}
