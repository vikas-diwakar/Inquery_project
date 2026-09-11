<?php

namespace App\Services;

use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected ?Api $api = null;

    public function __construct()
    {
        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

        if (!empty($key) && !empty($secret)) {
            $this->api = new Api($key, $secret);
        }
    }

    /**
     * Check if Razorpay credentials are fully configured
     */
    public function isConfigured(): bool
    {
        return !empty(config('services.razorpay.key')) && 
               !empty(config('services.razorpay.secret')) && 
               $this->api !== null;
    }

    /**
     * Check if using Razorpay Test Mode keys
     */
    public function isTestMode(): bool
    {
        $key = $this->getKey();
        return !empty($key) && str_starts_with($key, 'rzp_test_');
    }

    /**
     * Create a Razorpay order
     *
     * @param float|int $amount In INR/Currency (e.g. 6000)
     * @param string $currency Defaults to INR
     * @param string|null $receipt Custom receipt identifier
     * @param array $notes Metadata key-values attached to order
     */
    public function createOrder($amount, string $currency = 'INR', ?string $receipt = null, array $notes = []): array
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Razorpay API keys (RAZORPAY_KEY and RAZORPAY_SECRET) are not configured in .env');
        }

        try {
            $orderData = [
                'receipt' => $receipt ?: 'order_' . time(),
                'amount' => (int) round($amount * 100), // Amount in integer paisa
                'currency' => strtoupper($currency),
                'payment_capture' => 1, // Auto capture payment upon authorization
            ];

            if (!empty($notes)) {
                $orderData['notes'] = $notes;
            }

            $order = $this->api->order->create($orderData);

            return [
                'id' => $order->id,
                'amount' => $order->amount,
                'currency' => $order->currency,
                'receipt' => $order->receipt,
                'status' => $order->status,
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage(), [
                'amount' => $amount,
                'currency' => $currency,
                'receipt' => $receipt,
            ]);
            throw $e;
        }
    }

    /**
     * Verify payment signature from checkout callback
     */
    public function verifyPayment($paymentId, $orderId, $signature): bool
    {
        if (!$this->isConfigured()) {
            Log::error('Razorpay verifyPayment called but credentials are not configured');
            return false;
        }

        try {
            $attributes = [
                'razorpay_payment_id' => $paymentId,
                'razorpay_order_id' => $orderId,
                'razorpay_signature' => $signature,
            ];

            $this->api->utility->verifyPaymentSignature($attributes);

            return true;
        } catch (\Exception $e) {
            Log::error('Razorpay payment verification failed: ' . $e->getMessage(), [
                'payment_id' => $paymentId,
                'order_id' => $orderId,
            ]);
            return false;
        }
    }

    /**
     * Verify webhook signature from Razorpay webhook events
     */
    public function verifyWebhookSignature(string $payload, string $signature, ?string $webhookSecret = null): bool
    {
        if (!$this->api) {
            return false;
        }

        $secret = $webhookSecret ?: config('services.razorpay.webhook_secret');
        if (empty($secret)) {
            Log::warning('Razorpay webhook secret not configured; cannot verify signature.');
            return false;
        }

        try {
            $this->api->utility->verifyWebhookSignature($payload, $signature, $secret);
            return true;
        } catch (\Exception $e) {
            Log::error('Razorpay webhook signature verification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch payment details by payment ID
     */
    public function getPayment($paymentId): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $payment = $this->api->payment->fetch($paymentId);

            return [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'method' => $payment->method,
                'email' => $payment->email,
                'contact' => $payment->contact,
                'order_id' => $payment->order_id,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch Razorpay payment: ' . $e->getMessage(), [
                'payment_id' => $paymentId,
            ]);
            return null;
        }
    }

    /**
     * Get Razorpay key for frontend checkout initialization
     */
    public function getKey(): ?string
    {
        return config('services.razorpay.key');
    }

    /**
     * Get Razorpay API instance directly if needed
     */
    public function getApi(): ?Api
    {
        return $this->api;
    }
}