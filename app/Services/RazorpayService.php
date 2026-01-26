<?php

namespace App\Services;

use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class RazorpayService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    /**
     * Create a Razorpay order
     */
    public function createOrder($amount, $currency = 'INR', $receipt = null)
    {
        try {
            $orderData = [
                'receipt' => $receipt ?: 'order_' . time(),
                'amount' => $amount * 100, // Amount in paisa
                'currency' => $currency,
                'payment_capture' => 1, // Auto capture
            ];

            $order = $this->api->order->create($orderData);

            return [
                'id' => $order->id,
                'amount' => $order->amount,
                'currency' => $order->currency,
                'receipt' => $order->receipt,
                'status' => $order->status,
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify payment signature
     */
    public function verifyPayment($paymentId, $orderId, $signature)
    {
        try {
            $attributes = [
                'razorpay_payment_id' => $paymentId,
                'razorpay_order_id' => $orderId,
                'razorpay_signature' => $signature,
            ];

            $this->api->utility->verifyPaymentSignature($attributes);

            return true;
        } catch (\Exception $e) {
            Log::error('Razorpay payment verification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch payment details
     */
    public function getPayment($paymentId)
    {
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
            Log::error('Failed to fetch Razorpay payment: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get Razorpay key for frontend
     */
    public function getKey()
    {
        return config('services.razorpay.key');
    }
}