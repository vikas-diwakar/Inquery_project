@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    
    <!-- Top Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Checkout Order</h1>
            <p class="mt-1 text-xs sm:text-sm text-slate-600">Review your subscription plan details and complete payment.</p>
        </div>
        <a href="{{ route('subscription.index') }}" class="text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
            ← Back to Plans
        </a>
    </div>

    <!-- Alert / Notification Box (Dynamic) -->
    <div id="checkout_alert" class="hidden border rounded-2xl p-4 flex items-start gap-3 transition-all duration-300">
        <svg class="w-5 h-5 shrink-0 mt-0.5 text-current" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="text-xs">
            <h4 class="font-bold text-sm">Payment Notice</h4>
            <p id="checkout_alert_message" class="mt-0.5"></p>
        </div>
    </div>

    @if(!$isConfigured)
        <!-- Setup Warning Box for Developers / Admins -->
        <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-4 sm:p-5 text-amber-900 shadow-sm">
            <div class="flex items-start gap-3.5">
                <div class="p-2 bg-amber-100 text-amber-800 rounded-xl shrink-0">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="text-xs space-y-1">
                    <h4 class="font-bold text-sm text-amber-950">Razorpay API Credentials Required</h4>
                    <p class="text-amber-800 leading-relaxed">
                        To accept real or simulated card, UPI, and net-banking payments, configure your 
                        <code class="px-1.5 py-0.5 bg-amber-100/80 rounded font-mono text-[11px] font-bold">RAZORPAY_KEY</code> and 
                        <code class="px-1.5 py-0.5 bg-amber-100/80 rounded font-mono text-[11px] font-bold">RAZORPAY_SECRET</code> in your 
                        <code class="px-1.5 py-0.5 bg-amber-100/80 rounded font-mono text-[11px] font-bold">.env</code> file.
                    </p>
                </div>
            </div>
        </div>
    @elseif($isTestMode)
        <!-- Test Mode Notice -->
        <div class="bg-indigo-50/70 border border-indigo-200/80 rounded-2xl p-3.5 text-indigo-950 flex items-center justify-between gap-3 text-xs">
            <div class="flex items-center space-x-2">
                <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="font-bold text-indigo-900">Razorpay Test Mode Active:</span>
                <span class="text-indigo-700">You can simulate real payments using Razorpay's test UPI or card numbers.</span>
            </div>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-indigo-200/60 text-indigo-800">Sandbox</span>
        </div>
    @endif

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
                    <span>Taxes & Platform Fees</span>
                    <span class="font-semibold text-emerald-600">Included (₹0.00 extra)</span>
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
                    <h4 class="font-bold text-indigo-900 text-sm">Official Razorpay Checkout</h4>
                    <p class="text-slate-600">Transactions are encrypted with 256-bit bank-grade SSL. Supports UPI (Google Pay, PhonePe, Paytm), Credit/Debit Cards, Net Banking, and Wallets.</p>
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

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
function showCheckoutAlert(message, type = 'error') {
    const alertBox = document.getElementById('checkout_alert');
    const alertMsg = document.getElementById('checkout_alert_message');
    if (!alertBox || !alertMsg) return;

    alertMsg.innerText = message;
    alertBox.classList.remove('hidden', 'bg-rose-50', 'border-rose-200', 'text-rose-900', 'bg-emerald-50', 'border-emerald-200', 'text-emerald-900');
    
    if (type === 'error') {
        alertBox.classList.add('bg-rose-50', 'border-rose-200', 'text-rose-900');
    } else {
        alertBox.classList.add('bg-emerald-50', 'border-emerald-200', 'text-emerald-900');
    }
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function initRazorpay() {
    const payBtn = document.getElementById('pay_button');
    const payBtnText = document.getElementById('pay_button_text');
    const paySpinner = document.getElementById('pay_spinner');
    const alertBox = document.getElementById('checkout_alert');

    if (alertBox) alertBox.classList.add('hidden');

    // Disable button & show loading state
    if (payBtn) payBtn.disabled = true;
    if (payBtnText) payBtnText.innerText = 'Creating Razorpay Order...';
    if (paySpinner) paySpinner.classList.remove('hidden');

    fetch('{{ route("subscription.create-order", $plan) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Payment order creation failed. Please check server logs.');
        }
        return data;
    })
    .then(data => {
        if (payBtnText) payBtnText.innerText = 'Opening Razorpay Checkout...';

        const options = {
            key: data.key || '{{ $razorpayKey ?? config("services.razorpay.key") }}',
            amount: data.amount,
            currency: data.currency || '{{ $plan->currency }}',
            name: '{{ config("app.name", "PropDrip") }}',
            description: '{{ $plan->name }} - {{ $plan->duration_months }} Month(s) SaaS Access',
            order_id: data.order_id,
            handler: function (response) {
                if (payBtnText) payBtnText.innerText = 'Verifying & Activating...';
                document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                document.getElementById('razorpay_signature').value = response.razorpay_signature;
                document.getElementById('payment_form').submit();
            },
            prefill: {
                name: '{{ auth()->user()->name }}',
                email: '{{ auth()->user()->email }}',
                contact: '{{ $company->phone ?? auth()->user()->phone ?? "" }}',
            },
            notes: {
                plan_id: '{{ $plan->id }}',
                plan_name: '{{ $plan->name }}',
                company_id: '{{ $company->id }}'
            },
            theme: {
                color: '#4F46E5', // Indigo
            },
            modal: {
                ondismiss: function() {
                    if (payBtn) payBtn.disabled = false;
                    if (payBtnText) payBtnText.innerText = 'Pay ₹{{ number_format($plan->price) }} via Razorpay →';
                    if (paySpinner) paySpinner.classList.add('hidden');
                }
            }
        };

        const rzp = new Razorpay(options);
        rzp.on('payment.failed', function (response) {
            console.error('Razorpay payment failed:', response.error);
            showCheckoutAlert('Payment Failed: ' + (response.error.description || response.error.reason || 'Transaction could not be completed.'));
            if (payBtn) payBtn.disabled = false;
            if (payBtnText) payBtnText.innerText = 'Pay ₹{{ number_format($plan->price) }} via Razorpay →';
            if (paySpinner) paySpinner.classList.add('hidden');
        });

        rzp.open();
    })
    .catch(error => {
        console.error('Error initializing Razorpay:', error);
        showCheckoutAlert(error.message || 'Something went wrong starting payment checkout. Please check configuration.');
        if (payBtn) payBtn.disabled = false;
        if (payBtnText) payBtnText.innerText = 'Pay ₹{{ number_format($plan->price) }} via Razorpay →';
        if (paySpinner) paySpinner.classList.add('hidden');
    });
}
</script>
@endsection