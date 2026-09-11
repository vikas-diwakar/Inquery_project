<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RazorpayWebhookController extends Controller
{
    /**
     * Handle incoming Razorpay Webhook Events
     */
    public function handle(Request $request, RazorpayService $razorpay)
    {
        $signature = $request->header('X-Razorpay-Signature');
        $rawPayload = $request->getContent();

        if (empty($signature) || empty($rawPayload)) {
            Log::warning('Razorpay webhook received with missing signature or empty payload');
            return response()->json(['status' => 'ignored', 'message' => 'Missing signature or payload'], 400);
        }

        // Verify webhook signature if webhook secret is configured
        $webhookSecret = config('services.razorpay.webhook_secret');
        if (!empty($webhookSecret)) {
            $isValid = $razorpay->verifyWebhookSignature($rawPayload, $signature, $webhookSecret);
            if (!$isValid) {
                Log::error('Razorpay webhook signature mismatch');
                return response()->json(['status' => 'error', 'message' => 'Invalid webhook signature'], 401);
            }
        }

        $event = json_decode($rawPayload, true);
        $eventType = $event['event'] ?? 'unknown';

        Log::info('Razorpay webhook event received: ' . $eventType, [
            'event_id' => $event['id'] ?? null,
            'event_type' => $eventType,
        ]);

        // Process successful payment events
        if (in_array($eventType, ['payment.captured', 'order.paid'])) {
            $payment = $event['payload']['payment']['entity'] ?? null;
            if (!$payment) {
                return response()->json(['status' => 'ignored', 'message' => 'No payment entity in payload']);
            }

            $paymentId = $payment['id'];
            $orderId = $payment['order_id'] ?? null;
            $notes = $payment['notes'] ?? [];

            // Prevent duplicate processing
            $existing = Subscription::where('payment_reference', $paymentId)->first();
            if ($existing) {
                Log::info('Razorpay payment ' . $paymentId . ' already processed into subscription #' . $existing->id);
                return response()->json(['status' => 'success', 'message' => 'Already processed']);
            }

            $companyId = $notes['company_id'] ?? null;
            $planId = $notes['plan_id'] ?? null;

            if (!$companyId || !$planId) {
                Log::warning('Razorpay webhook missing company_id or plan_id in notes', ['payment_id' => $paymentId, 'notes' => $notes]);
                return response()->json(['status' => 'ignored', 'message' => 'Missing metadata notes']);
            }

            $company = Company::find($companyId);
            $plan = SubscriptionPlan::find($planId);

            if (!$company || !$plan) {
                Log::error('Razorpay webhook referenced non-existent company or plan', [
                    'company_id' => $companyId,
                    'plan_id' => $planId,
                ]);
                return response()->json(['status' => 'error', 'message' => 'Company or plan not found'], 404);
            }

            DB::transaction(function () use ($company, $plan, $payment, $paymentId, $orderId, $signature) {
                $currentSub = $company->activeSubscription();
                $startDate = ($currentSub && $currentSub->end_date && $currentSub->end_date->isFuture()) 
                    ? $currentSub->end_date->copy() 
                    : now();
                $endDate = $startDate->copy()->addMonths($plan->duration_months);

                Subscription::create([
                    'company_id' => $company->id,
                    'subscription_plan_id' => $plan->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => 'active',
                    'amount_paid' => isset($payment['amount']) ? ($payment['amount'] / 100) : $plan->price,
                    'currency' => $payment['currency'] ?? 'INR',
                    'payment_reference' => $paymentId,
                    'payment_details' => [
                        'razorpay_payment_id' => $paymentId,
                        'razorpay_order_id' => $orderId,
                        'razorpay_signature' => $signature,
                        'payment_method' => $payment['method'] ?? 'webhook',
                        'email' => $payment['email'] ?? '',
                        'contact' => $payment['contact'] ?? '',
                        'processed_via' => 'webhook',
                        'processed_at' => now()->toIso8601String(),
                    ],
                ]);

                $company->activateSubscription($endDate);

                Log::info("Activated subscription for company {$company->name} via Razorpay Webhook.");
            });

            return response()->json(['status' => 'success', 'message' => 'Subscription activated']);
        }

        return response()->json(['status' => 'ignored', 'message' => 'Unhandled event type']);
    }
}
