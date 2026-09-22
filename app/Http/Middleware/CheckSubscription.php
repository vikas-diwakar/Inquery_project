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
            'subscription.required',
            'subscription.choose-plan',
            'subscription.activate-plan',
            'subscription.create-order',
            'subscription.index',
            'subscription.show',
            'subscription.checkout',
            'subscription.purchase',
            'subscription.renew',
            'subscription.cancel',
            'logout',
        ];

        $currentRouteName = $request->route()?->getName();

        if ($currentRouteName && in_array($currentRouteName, $subscriptionRoutes, true)) {
            // If non-admin tries to access admin-only subscription routes while company has no active subscription
            $adminOnlySubscriptionRoutes = [
                'subscription.choose-plan',
                'subscription.activate-plan',
                'subscription.create-order',
                'subscription.index',
                'subscription.show',
                'subscription.checkout',
                'subscription.purchase',
                'subscription.renew',
                'subscription.cancel',
            ];

            $isExplicitNonAdmin = $user->role && $user->role->name !== 'Admin';

            if ($isExplicitNonAdmin && in_array($currentRouteName, $adminOnlySubscriptionRoutes, true)) {
                return redirect()->route('subscription.required');
            }

            return $next($request);
        }

        // If company has active subscription, allow access
        if ($company->hasActiveSubscription()) {
            return $next($request);
        }

        // Return JSON 403 for API / AJAX requests when subscription is expired or inactive
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $company->isFirstLogin()
                    ? 'Subscription plan selection required.'
                    : 'Subscription expired. Please renew your subscription.',
                'subscription_required' => true
            ], 403);
        }

        // If company is on first login (no subscription ever) and user is not explicitly non-admin, redirect to plan selection
        $isExplicitNonAdmin = $user->role && $user->role->name !== 'Admin';
        if ($company->isFirstLogin() && !$isExplicitNonAdmin) {
            return redirect()->route('subscription.choose-plan');
        }

        return redirect()->route('subscription.required')
            ->with('error', 'Your subscription has expired. Please choose a subscription plan to continue.');
    }
}
