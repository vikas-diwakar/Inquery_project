@extends('layouts.app')

@section('title', 'Checkout - ' . $plan->name)

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function initRazorpay() {
    const payBtn = document.getElementById('pay_button');
    const payBtnText = document.getElementById('pay_button_text');
    const paySpinner = document.getElementById('pay_spinner');

    // Disable button & show loading state
    if (payBtn) payBtn.disabled = true;
    if (payBtnText) payBtnText.innerText = 'Initializing Payment...';
    if (paySpinner) paySpinner.classList.remove('hidden');

    const options = {
        key: '{{ config("services.razorpay.key") }}',
        amount: {{ $plan->price * 100 }}, // Amount in paisa
        currency: '{{ $plan->currency }}',
        name: '{{ config("app.name") }}',
        description: '{{ $plan->name }} - {{ $plan->duration_months }} Months Subscription',
        order_id: '', 
        handler: function (response) {
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
            color: '#4F46E5', // Indigo
        },
        modal: {
            ondismiss: function() {
                if (payBtn) payBtn.disabled = false;
                if (payBtnText) payBtnText.innerText = 'Pay ₹{{ number_format($plan->price) }} via Razorpay';
                if (paySpinner) paySpinner.classList.add('hidden');
            }
        }
    };

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
            const rzp = new Razorpay(options);
            rzp.open();
        } else {
            alert(data.message || 'Failed to create payment order. Please verify API configuration.');
            if (payBtn) payBtn.disabled = false;
            if (payBtnText) payBtnText.innerText = 'Pay ₹{{ number_format($plan->price) }} via Razorpay';
            if (paySpinner) paySpinner.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Error initializing Razorpay:', error);
        alert('Something went wrong starting payment checkout. Please try again.');
        if (payBtn) payBtn.disabled = false;
        if (payBtnText) payBtnText.innerText = 'Pay ₹{{ number_format($plan->price) }} via Razorpay';
        if (paySpinner) paySpinner.classList.add('hidden');
    });
}
</script>
@endpush

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-8">
    
    <!-- Top Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Checkout Order</h1>
            <p class="mt-1 text-sm text-slate-600">Review your subscription plan details and complete payment.</p>
        </div>
        <a href="{{ route('subscription.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
            ← Back to Plans
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white border border-slate-200/80 rounded-3xl shadow-xl overflow-hidden">
        
        <!-- Plan Header Banner -->
        <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 p-6 sm:p-8 text-white flex flex-wrap items-center justify-between gap-4">
            <div>
                <span class="px-3 py-1 rounded-full text-[11px] font-extrabold uppercase bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                    Selected Plan
                </span>
                <h2 class="text-2xl font-bold text-white mt-2">{{ $plan->name }}</h2>
                <p class="text-xs text-slate-300 mt-0.5">{{ $plan->duration_months }} Months SaaS Subscription Access</p>
            </div>
            <div class="text-right">
                <span class="text-3xl font-extrabold text-white">₹{{ number_format($plan->price) }}</span>
                <span class="text-xs text-slate-400 block mt-0.5">INR (All Inclusive)</span>
            </div>
        </div>

        <!-- Breakdown -->
        <div class="p-6 sm:p-8 space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Order Summary</h3>

            <div class="space-y-3.5 text-sm">
                <div class="flex justify-between items-center text-slate-600">
                    <span>Plan Duration</span>
                    <span class="font-semibold text-slate-900">{{ $plan->duration_months }} Months</span>
                </div>
                <div class="flex justify-between items-center text-slate-600">
                    <span>Company / Account</span>
                    <span class="font-semibold text-slate-900">{{ $company->name }}</span>
                </div>
                <div class="flex justify-between items-center text-slate-600">
                    <span>Billing Contact</span>
                    <span class="font-semibold text-slate-900">{{ auth()->user()->email }}</span>
                </div>
                <div class="flex justify-between items-center text-slate-600">
                    <span>Subtotal</span>
                    <span class="font-semibold text-slate-900">₹{{ number_format($plan->price, 2) }}</span>
                </div>
                <div class="flex justify-between items-center text-slate-600">
                    <span>Taxes & Fees</span>
                    <span class="font-semibold text-slate-900">₹0.00</span>
                </div>

                <div class="border-t border-slate-200 pt-4 flex justify-between items-center">
                    <span class="text-base font-bold text-slate-900">Total Payable</span>
                    <span class="text-2xl font-extrabold text-indigo-600">₹{{ number_format($plan->price, 2) }}</span>
                </div>
            </div>

            <!-- Razorpay Security Box -->
            <div class="bg-indigo-50/60 border border-indigo-100 rounded-2xl p-4 flex items-start gap-3.5">
                <div class="p-2 bg-indigo-600 text-white rounded-xl flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="text-xs text-indigo-950 space-y-0.5">
                    <h4 class="font-bold text-indigo-900 text-sm">Secure Payment Gateway</h4>
                    <p>Transactions are encrypted and safely processed via Razorpay. We support UPI, Cards, Net Banking, and Wallets.</p>
                </div>
            </div>

            <!-- Payment Form -->
            <form id="payment_form" action="{{ route('subscription.purchase', $plan) }}" method="POST" class="pt-2">
                @csrf
                <input type="hidden" id="razorpay_payment_id" name="razorpay_payment_id">
                <input type="hidden" id="razorpay_order_id" name="razorpay_order_id">
                <input type="hidden" id="razorpay_signature" name="razorpay_signature">

                <div class="flex items-center justify-end">
                    @if(auth()->user()->isAdmin())
                    <button type="button" id="pay_button" onclick="initRazorpay()"
                            class="w-full sm:w-auto px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-lg shadow-indigo-600/30 transition-all duration-200 flex items-center justify-center gap-3">
                        <svg id="pay_spinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="pay_button_text">Pay ₹{{ number_format($plan->price) }} via Razorpay →</span>
                    </button>
                    @else
                    <button type="button" disabled class="w-full px-6 py-3.5 bg-slate-100 text-slate-400 font-semibold rounded-2xl cursor-not-allowed">
                        Admin Action Required
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection