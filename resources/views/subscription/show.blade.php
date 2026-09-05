@extends('layouts.app')

@section('title', 'Subscription Details')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Subscription Details</h1>
            <p class="mt-1 text-sm text-slate-600">Overview of active subscription plan, billing details, and payment history.</p>
        </div>
        <div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('subscription.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl shadow-md shadow-indigo-600/20 text-sm transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Upgrade or Renew Plan
            </a>
            @endif
        </div>
    </div>

    <!-- Active Subscription Banner Card -->
    @if($activeSubscription)
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-sm space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-5">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Current Subscription</span>
                <h2 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $activeSubscription->plan->name ?? 'Active Plan' }}</h2>
            </div>
            
            <div>
                <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold
                    @if($activeSubscription->isExpired()) bg-red-100 text-red-700 border border-red-200
                    @elseif($activeSubscription->isExpiringSoon()) bg-amber-100 text-amber-700 border border-amber-200
                    @else bg-emerald-100 text-emerald-700 border border-emerald-200 @endif">
                    ● @if($activeSubscription->isExpired()) Expired
                    @elseif($activeSubscription->isExpiringSoon()) Expiring Soon
                    @else Active @endif
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-sm">
            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                <span class="text-xs font-semibold text-slate-500 block">Plan Type</span>
                <span class="text-base font-bold text-slate-900 mt-1 block">
                    {{ $activeSubscription->isTrial() ? 'Free Trial' : 'Paid Plan' }}
                </span>
            </div>
            
            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                <span class="text-xs font-semibold text-slate-500 block">Expiration / Renewal Date</span>
                <span class="text-base font-bold text-slate-900 mt-1 block">
                    {{ $activeSubscription->end_date ? $activeSubscription->end_date->format('M j, Y') : 'N/A' }}
                </span>
            </div>

            <div class="bg-slate-50 border border-slate-100 rounded-2xl p-4">
                <span class="text-xs font-semibold text-slate-500 block">Time Remaining</span>
                <span class="text-base font-bold text-indigo-600 mt-1 block">
                    @if($activeSubscription->isExpired())
                        Expired
                    @else
                        {{ max(0, $activeSubscription->daysUntilExpiry()) }} Days Left
                    @endif
                </span>
            </div>
        </div>

        @if($activeSubscription->isExpired())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-sm text-red-800 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <span>Your subscription has expired. Renew to restore uninterrupted access to all features.</span>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('subscription.index') }}" class="px-4 py-2 bg-red-600 text-white font-bold rounded-xl text-xs shadow hover:bg-red-700 transition">
                Renew Now →
            </a>
            @endif
        </div>
        @elseif($activeSubscription->isExpiringSoon())
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-sm text-amber-800 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Your subscription expires in {{ $activeSubscription->daysUntilExpiry() }} days.</span>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('subscription.index') }}" class="px-4 py-2 bg-amber-600 text-white font-bold rounded-xl text-xs shadow hover:bg-amber-700 transition">
                Extend Plan →
            </a>
            @endif
        </div>
        @endif
    </div>
    @endif

    <!-- Subscription History Table -->
    <div class="bg-white border border-slate-200/80 rounded-3xl overflow-hidden shadow-sm">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900">Subscription & Payment History</h3>
            <p class="text-xs text-slate-500 mt-0.5">Historical record of all plan activations and Razorpay payment transactions.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <th class="py-3.5 px-6">Plan Name</th>
                        <th class="py-3.5 px-6">Validity Period</th>
                        <th class="py-3.5 px-6">Amount Paid</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Payment Reference</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($subscriptions as $subscription)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="py-4 px-6 font-bold text-slate-900">
                            {{ $subscription->plan->name ?? 'Plan #' . $subscription->subscription_plan_id }}
                        </td>
                        <td class="py-4 px-6 text-slate-600">
                            {{ $subscription->start_date ? $subscription->start_date->format('M j, Y') : '-' }} — {{ $subscription->end_date ? $subscription->end_date->format('M j, Y') : '-' }}
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-900">
                            @if($subscription->amount_paid && $subscription->amount_paid > 0)
                                ₹{{ number_format($subscription->amount_paid) }}
                            @else
                                <span class="text-emerald-600 font-bold">Free</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                @if($subscription->status === 'active') bg-emerald-100 text-emerald-800
                                @elseif($subscription->status === 'trial') bg-indigo-100 text-indigo-800
                                @elseif($subscription->status === 'expired') bg-red-100 text-red-800
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 font-mono">
                            {{ $subscription->payment_reference ?? 'N/A' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 px-6 text-center text-slate-400 text-sm">
                            No subscription history available.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dashboard Link -->
    <div class="text-center pt-2">
        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
            ← Back to Dashboard
        </a>
    </div>
</div>
@endsection