@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
    
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Subscription Plans</h1>
            <p class="mt-1 text-sm text-slate-600">Select or upgrade to a subscription plan tailored for your real estate business.</p>
        </div>
        <div>
            <a href="{{ route('subscription.show') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold rounded-xl text-sm shadow-sm transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                View Subscription History
            </a>
        </div>
    </div>

    <!-- Active Subscription Status Banner -->
    @if($currentSubscription)
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        @if($currentSubscription->isExpired()) bg-red-500/20 text-red-300 border border-red-500/30
                        @elseif($currentSubscription->isExpiringSoon()) bg-amber-500/20 text-amber-300 border border-amber-500/30
                        @else bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 @endif">
                        @if($currentSubscription->isExpired()) Expired
                        @elseif($currentSubscription->isExpiringSoon()) Expiring Soon
                        @else Active Plan @endif
                    </span>
                    <span class="text-xs text-slate-400 font-medium">Current Status</span>
                </div>

                <h3 class="text-2xl sm:text-3xl font-extrabold text-white">
                    {{ $currentSubscription->plan->name ?? 'Custom Plan' }}
                </h3>

                <p class="text-sm text-slate-300">
                    @if($currentSubscription->isTrial())
                        Free Trial Period • Valid until <span class="font-semibold text-white">{{ $currentSubscription->end_date ? $currentSubscription->end_date->format('M j, Y') : 'N/A' }}</span>
                    @else
                        Paid Subscription • Valid until <span class="font-semibold text-white">{{ $currentSubscription->end_date ? $currentSubscription->end_date->format('M j, Y') : 'N/A' }}</span>
                    @endif
                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                @if(auth()->user()->isAdmin())
                <form action="{{ route('subscription.renew') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold rounded-2xl shadow-lg shadow-indigo-600/30 transition text-sm text-center">
                        Renew Active Plan →
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Available Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
        @foreach($plans as $plan)
        <div class="bg-white border border-slate-200/80 rounded-3xl p-8 shadow-sm hover:shadow-xl hover:border-indigo-500/50 transition-all duration-300 flex flex-col justify-between relative group">
            
            @if($loop->first)
            <div class="absolute -top-3.5 right-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-[10px] font-extrabold tracking-wider uppercase px-3 py-1 rounded-full shadow-sm">
                MOST POPULAR
            </div>
            @endif

            <div>
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-bold text-slate-900">{{ $plan->name }}</h3>
                    <span class="p-2.5 bg-indigo-50 text-indigo-600 rounded-2xl font-semibold text-xs">
                        {{ $plan->duration_months }} Months
                    </span>
                </div>

                <div class="my-6">
                    <span class="text-4xl font-extrabold text-slate-900">₹{{ number_format($plan->price) }}</span>
                    <span class="text-slate-500 text-sm font-medium"> / {{ $plan->duration_months }} months</span>
                </div>

                <div class="border-t border-slate-100 pt-6">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Included Features</p>
                    <ul class="space-y-3.5 text-sm text-slate-700">
                        @if(!empty($plan->features))
                            @foreach($plan->features as $feature => $enabled)
                                @if($enabled)
                                <li class="flex items-center gap-3">
                                    <div class="h-5 w-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $feature)) }}</span>
                                </li>
                                @endif
                            @endforeach
                        @else
                            <li class="flex items-center gap-3">
                                <div class="h-5 w-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium">Full Property Inquiry & Unit Inventory Access</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="h-5 w-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="font-medium">WhatsApp API & Lead Drip Sequences</span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="mt-8 pt-4">
                @if(auth()->user()->isAdmin())
                <a href="{{ route('subscription.checkout', $plan) }}"
                   class="w-full flex items-center justify-center py-3.5 px-4 rounded-2xl font-bold text-white bg-slate-900 hover:bg-indigo-600 shadow-md transition-all duration-200">
                    Select {{ $plan->name }} →
                </a>
                @else
                <button disabled class="w-full py-3.5 px-4 rounded-2xl font-semibold text-slate-400 bg-slate-100 cursor-not-allowed text-center">
                    Admin Authorization Required
                </button>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Back to Dashboard Navigation -->
    <div class="pt-6 text-center">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-semibold text-indigo-600 hover:text-indigo-800 transition">
            ← Back to Dashboard
        </a>
    </div>
</div>
@endsection