<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubscriptionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Show subscription required page (when subscription expired)
     */
    public function required()
    {
        $company = auth()->user()->company;
        $activeSubscription = $company->activeSubscription();

        return view('subscription.required', compact('company', 'activeSubscription'));
    }

    /**
     * Create Razorpay order for payment
     */
    public function createOrder(SubscriptionPlan $plan, RazorpayService $razorpay)
    {
        $this->authorize('manageSubscription', auth()->user()->company);

        if (!$plan->isPaid() || !$plan->is_active) {
            return response()->json(['success' => false, 'message' => 'Invalid plan'], 400);
        }

        try {
            $order = $razorpay->createOrder($plan->price, $plan->currency, 'sub_' . $plan->id . '_' . time());

            return response()->json([
                'success' => true,
                'order_id' => $order['id'],
                'amount' => $order['amount'],
                'currency' => $order['currency'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order'
            ], 500);
        }
    }

    /**
     * Show plan selection page (for first login or expired subscriptions)
     */
    public function choosePlan()
    {
        $company = auth()->user()->company;

        // If company already has active subscription, redirect to dashboard
        if ($company->hasActiveSubscription()) {
            return redirect()->route('dashboard');
        }

        $plans = SubscriptionPlan::active()->get();

        // Filter plans based on whether trial is available
        if ($company->isFirstLogin() && $company->canUseTrial()) {
            // Show all plans including trial for first-time users
            $showTrial = true;
        } else {
            // Hide trial plan for existing users
            $plans = $plans->where('type', '!=', 'trial');
            $showTrial = false;
        }

        return view('subscription.choose-plan', compact('plans', 'showTrial', 'company'));
    }

    /**
     * Activate selected plan
     */
    public function activatePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $company = auth()->user()->company;
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if user can select this plan
        if ($plan->type === 'trial' && !$company->canUseTrial()) {
            return redirect()->back()->with('error', 'Trial plan is not available for your account.');
        }

        if ($plan->type === 'paid' && $company->isFirstLogin() && $company->canUseTrial()) {
            // First-time user choosing paid plan - process payment
            return redirect()->route('subscription.checkout', $plan);
        }

        // Activate trial plan immediately
        if ($plan->type === 'trial') {
            // Create trial subscription record
            Subscription::create([
                'company_id' => $company->id,
                'subscription_plan_id' => $plan->id,
                'start_date' => now(),
                'end_date' => now()->addMonths($plan->duration_months),
                'status' => 'trial',
            ]);

            // Update company with trial status
            $company->startTrial();

            return redirect()->route('dashboard')
                ->with('success', 'Free trial activated successfully! You now have full access to all features.');
        }

        // This shouldn't happen, but handle it
        return redirect()->back()->with('error', 'Invalid plan selection.');
    }

    /**
     * Display subscription plans and current status
     */
    public function index()
    {
        $this->authorize('manageSubscription', auth()->user()->company);

        $company = auth()->user()->company;
        $plans = SubscriptionPlan::active()->paid()->get();
        $currentSubscription = $company->activeSubscription();

        return view('subscription.index', compact('company', 'plans', 'currentSubscription'));
    }

    /**
     * Show subscription details
     */
    public function show()
    {
        $this->authorize('manageSubscription', auth()->user()->company);

        $company = auth()->user()->company;
        $subscriptions = $company->subscriptions()->with('plan')->latest()->get();
        $activeSubscription = $company->activeSubscription();

        return view('subscription.show', compact('company', 'subscriptions', 'activeSubscription'));
    }

    /**
     * Show checkout page for a plan
     */
    public function checkout(SubscriptionPlan $plan)
    {
        $this->authorize('manageSubscription', auth()->user()->company);

        if (!$plan->isPaid() || !$plan->is_active) {
            abort(404);
        }

        $company = auth()->user()->company;

        return view('subscription.checkout', compact('plan', 'company'));
    }

    /**
     * Process subscription purchase
     */
    public function purchase(Request $request, SubscriptionPlan $plan, RazorpayService $razorpay)
    {
        $this->authorize('manageSubscription', auth()->user()->company);

        $request->validate([
            'razorpay_payment_id' => 'required|string',
            'razorpay_order_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $company = auth()->user()->company;

        // Verify payment signature
        $isValid = $razorpay->verifyPayment(
            $request->razorpay_payment_id,
            $request->razorpay_order_id,
            $request->razorpay_signature
        );

        if (!$isValid) {
            return redirect()->back()->with('error', 'Payment verification failed. Please contact support if money was debited.');
        }

        // Get payment details
        $paymentDetails = $razorpay->getPayment($request->razorpay_payment_id);

        if (!$paymentDetails || $paymentDetails['status'] !== 'captured') {
            return redirect()->back()->with('error', 'Payment was not successful. Please try again.');
        }

        // Calculate end date
        $startDate = now();
        $endDate = $startDate->copy()->addMonths($plan->duration_months);

        // Create subscription record
        $subscription = Subscription::create([
            'company_id' => $company->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'active',
            'amount_paid' => $plan->price,
            'currency' => $plan->currency,
            'payment_reference' => $request->razorpay_payment_id,
            'payment_details' => [
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_signature' => $request->razorpay_signature,
                'payment_method' => $paymentDetails['method'],
                'email' => $paymentDetails['email'],
                'contact' => $paymentDetails['contact'],
                'processed_at' => now(),
            ],
        ]);

        // Update company status
        $company->activateSubscription($endDate);

        return redirect()->route('subscription.show')
            ->with('success', 'Subscription activated successfully! Welcome to ' . config('app.name') . '.');
    }

    /**
     * Renew existing subscription
     */
    public function renew(Request $request)
    {
        $this->authorize('manageSubscription', auth()->user()->company);

        $company = auth()->user()->company;
        $currentSubscription = $company->activeSubscription();

        if (!$currentSubscription) {
            return redirect()->route('subscription.index')
                ->with('error', 'No active subscription found to renew.');
        }

        $plan = $currentSubscription->plan;

        // In a real application, process payment here

        // Extend subscription
        $newEndDate = $currentSubscription->end_date->copy()->addMonths($plan->duration_months);

        // Create new subscription record for renewal
        Subscription::create([
            'company_id' => $company->id,
            'subscription_plan_id' => $plan->id,
            'start_date' => $currentSubscription->end_date,
            'end_date' => $newEndDate,
            'status' => 'active',
            'amount_paid' => $plan->price,
            'currency' => $plan->currency,
            'payment_reference' => $request->payment_reference ?? 'renewal',
            'payment_details' => [
                'type' => 'renewal',
                'previous_end_date' => $currentSubscription->end_date,
                'processed_at' => now(),
            ],
        ]);

        // Update company status
        $company->activateSubscription($newEndDate);

        return redirect()->route('subscription.show')
            ->with('success', 'Subscription renewed successfully!');
    }

    /**
     * Cancel subscription
     */
    public function cancel(Request $request)
    {
        $this->authorize('manageSubscription', auth()->user()->company);

        $company = auth()->user()->company;
        $activeSubscription = $company->activeSubscription();

        if ($activeSubscription) {
            $activeSubscription->update([
                'status' => 'cancelled',
                'auto_renew' => false,
            ]);

            // If this was the last active subscription, expire the company
            if (!$company->hasActiveSubscription()) {
                $company->expireSubscription();
            }
        }

        return redirect()->route('subscription.show')
            ->with('success', 'Subscription cancelled successfully.');
    }
}
