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
        SubscriptionPlan::ensureDefaultPlansExist();

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
            return response()->json(['success' => false, 'message' => 'Invalid or inactive plan selected.'], 400);
        }

        if (!$razorpay->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay API credentials (RAZORPAY_KEY and RAZORPAY_SECRET) are not configured in .env. Please configure your Razorpay keys.'
            ], 422);
        }

        try {
            $company = auth()->user()->company;
            $order = $razorpay->createOrder(
                $plan->price,
                $plan->currency ?? 'INR',
                'sub_' . $plan->id . '_' . $company->id . '_' . time(),
                [
                    'company_id' => (string) $company->id,
                    'company_name' => (string) $company->name,
                    'plan_id' => (string) $plan->id,
                    'plan_name' => (string) $plan->name,
                    'user_id' => (string) auth()->id(),
                    'user_email' => (string) auth()->user()->email,
                ]
            );

            return response()->json([
                'success' => true,
                'order_id' => $order['id'],
                'amount' => $order['amount'],
                'currency' => $order['currency'],
                'key' => $razorpay->getKey(),
                'is_test_mode' => $razorpay->isTestMode(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Razorpay order creation failed: ' . $e->getMessage(), [
                'plan_id' => $plan->id,
                'company_id' => auth()->user()->company_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to create payment order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show plan selection page (for first login or expired subscriptions)
     */
    public function choosePlan()
    {
        SubscriptionPlan::ensureDefaultPlansExist();

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
        SubscriptionPlan::ensureDefaultPlansExist();

        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $company = auth()->user()->company;
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Check if user can select this plan
        if ($plan->type === 'trial' && !$company->canUseTrial()) {
            return redirect()->back()->with('error', 'Trial plan is not available for your account.');
        }

        // Paid plans redirect to checkout
        if ($plan->type === 'paid') {
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
            $company->startTrial($plan->duration_months ?: 1);

            return redirect()->route('dashboard')
                ->with('success', 'Free trial activated successfully! You now have full access to all features.');
        }

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
    public function checkout(SubscriptionPlan $plan, RazorpayService $razorpay)
    {
        $this->authorize('manageSubscription', auth()->user()->company);

        if (!$plan->isPaid() || !$plan->is_active) {
            abort(404);
        }

        $company = auth()->user()->company;
        $isConfigured = $razorpay->isConfigured();
        $isTestMode = $razorpay->isTestMode();
        $razorpayKey = $razorpay->getKey();

        return view('subscription.checkout', compact('plan', 'company', 'isConfigured', 'isTestMode', 'razorpayKey'));
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
            \Illuminate\Support\Facades\Log::warning('Razorpay payment signature mismatch on purchase', [
                'company_id' => $company->id,
                'payment_id' => $request->razorpay_payment_id,
                'order_id' => $request->razorpay_order_id,
            ]);

            return redirect()->route('subscription.checkout', $plan)
                ->with('error', 'Payment signature verification failed. If money was debited from your account, please contact support with Payment ID: ' . $request->razorpay_payment_id);
        }

        // Get payment details from Razorpay API
        $paymentDetails = $razorpay->getPayment($request->razorpay_payment_id);

        if ($paymentDetails && isset($paymentDetails['status']) && !in_array($paymentDetails['status'], ['captured', 'authorized'])) {
            return redirect()->route('subscription.checkout', $plan)
                ->with('error', 'Payment was not captured (status: ' . $paymentDetails['status'] . '). Please try again.');
        }

        // Atomic update via DB transaction
        \Illuminate\Support\Facades\DB::transaction(function () use ($company, $plan, $request, $paymentDetails) {
            // Calculate dates (stack upon current active subscription end date if not expired)
            $currentSub = $company->activeSubscription();
            $startDate = ($currentSub && $currentSub->end_date && $currentSub->end_date->isFuture()) 
                ? $currentSub->end_date->copy() 
                : now();
            $endDate = $startDate->copy()->addMonths($plan->duration_months);

            // Create subscription record
            Subscription::create([
                'company_id' => $company->id,
                'subscription_plan_id' => $plan->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'active',
                'amount_paid' => $plan->price,
                'currency' => $plan->currency ?? 'INR',
                'payment_reference' => $request->razorpay_payment_id,
                'payment_details' => [
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_order_id' => $request->razorpay_order_id,
                    'razorpay_signature' => $request->razorpay_signature,
                    'payment_method' => $paymentDetails['method'] ?? 'online',
                    'email' => $paymentDetails['email'] ?? auth()->user()->email,
                    'contact' => $paymentDetails['contact'] ?? '',
                    'processed_at' => now()->toIso8601String(),
                ],
            ]);

            // Update company status
            $company->activateSubscription($endDate);
        });

        return redirect()->route('subscription.show')
            ->with('success', '🎉 Payment successful! Your ' . $plan->name . ' (' . $plan->duration_months . ' months) subscription is now active.');
    }

    /**
     * Renew existing subscription
     */
    public function renew(Request $request)
    {
        $this->authorize('manageSubscription', auth()->user()->company);

        $company = auth()->user()->company;
        $currentSubscription = $company->activeSubscription();

        if ($request->has('plan_id')) {
            $plan = SubscriptionPlan::findOrFail($request->plan_id);
        } elseif ($currentSubscription && $currentSubscription->plan) {
            $plan = $currentSubscription->plan;
        } else {
            return redirect()->route('subscription.index')
                ->with('error', 'Please choose a plan to continue.');
        }

        return redirect()->route('subscription.checkout', $plan);
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
