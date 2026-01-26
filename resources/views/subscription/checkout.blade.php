@extends('layouts.app')

@section('title', 'Checkout - ' . $plan->name)

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function initRazorpay() {
    const options = {
        key: '{{ config("services.razorpay.key") }}',
        amount: {{ $plan->price * 100 }}, // Amount in paisa
        currency: '{{ $plan->currency }}',
        name: '{{ config("app.name") }}',
        description: '{{ $plan->name }} - {{ $plan->duration_months }} months subscription',
        image: '{{ asset("images/logo.png") }}', // Add your logo path
        order_id: '', // Will be set by AJAX
        handler: function (response) {
            // Handle successful payment
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('payment_form').submit();
        },
        prefill: {
            name: '{{ auth()->user()->name }}',
            email: '{{ auth()->user()->email }}',
            contact: '{{ $company->phone ?? "" }}',
        },
        theme: {
            color: '#4F46E5', // Indigo color
        },
        modal: {
            ondismiss: function() {
                console.log('Payment modal dismissed');
            }
        }
    };

    const rzp = new Razorpay(options);

    // Create order first
    fetch('{{ route("subscription.create-order", $plan) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            options.order_id = data.order_id;
            rzp.open();
        } else {
            alert('Failed to create order. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Something went wrong. Please try again.');
    });
}
</script>
@endpush

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Complete Your Purchase</h1>
        <p class="mt-2 text-gray-600">Review your subscription details and complete payment</p>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>

            <div class="border-b border-gray-200 pb-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $plan->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $plan->duration_months }} months subscription</p>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">₹{{ number_format($plan->price) }}</span>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="text-gray-900">₹{{ number_format($plan->price) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tax</span>
                    <span class="text-gray-900">₹0.00</span>
                </div>
                <div class="border-t border-gray-200 pt-4 flex justify-between">
                    <span class="text-lg font-medium text-gray-900">Total</span>
                    <span class="text-2xl font-bold text-gray-900">₹{{ number_format($plan->price) }}</span>
                </div>
            </div>
        </div>

        <div class="px-6 py-6 bg-gray-50">
            <form id="payment_form" action="{{ route('subscription.purchase', $plan) }}" method="POST">
                @csrf

                <!-- Hidden fields for Razorpay response -->
                <input type="hidden" id="razorpay_payment_id" name="razorpay_payment_id">
                <input type="hidden" id="razorpay_order_id" name="razorpay_order_id">
                <input type="hidden" id="razorpay_signature" name="razorpay_signature">

                <div class="space-y-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">Secure Payment</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>Your payment is processed securely through Razorpay. We accept all major credit cards, debit cards, UPI, and net banking.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('subscription.index') }}" class="text-indigo-600 hover:text-indigo-800">← Back to Plans</a>
                        <button type="button" onclick="initRazorpay()"
                                class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Pay ₹{{ number_format($plan->price) }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection