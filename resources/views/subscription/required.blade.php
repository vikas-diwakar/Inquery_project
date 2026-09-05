@extends('layouts.app')

@section('title', 'Subscription Required')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full space-y-8">
        
        <!-- Top Lock Hero Banner -->
        <div class="text-center max-w-2xl mx-auto space-y-4">
            <div class="inline-flex w-20 h-20 rounded-3xl bg-red-500/10 border border-red-500/20 items-center justify-center shadow-inner">
                <svg class="w-10 h-10 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                @if($company->isFirstLogin()) Choose Your Subscription Plan
                @else Subscription Expired
                @endif
            </h1>
            
            <p class="text-base text-slate-600">
                @if($company->isFirstLogin())
                    To access dashboard features, please choose a plan to get started.
                @else
                    Your property management subscription has ended. Renew or select a plan to regain full access to your workspace.
                @endif
            </p>
        </div>

        @if(!auth()->user()->isAdmin())
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-amber-800 text-sm flex items-start gap-3.5 max-w-2xl mx-auto shadow-sm">
            <svg class="w-6 h-6 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <div>
                <h4 class="font-bold text-amber-900">Administrator Authorization Required</h4>
                <p class="mt-1 text-amber-700">Only company administrators can make payments or change subscription plans. Please ask your workspace administrator to renew the plan.</p>
            </div>
        </div>
        @endif

        @if($activeSubscription)
        <!-- Expired Plan Notice Bar -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 shadow-sm flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-bold text-lg">
                    {{ strtoupper(substr($activeSubscription->plan->name ?? 'P', 0, 1)) }}
                </div>
                <div>
                    <h4 class="text-base font-bold text-slate-900">{{ $activeSubscription->plan->name ?? 'Previous Subscription' }}</h4>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Expired on: <span class="font-semibold text-slate-700">{{ $activeSubscription->end_date ? $activeSubscription->end_date->format('M d, Y') : 'N/A' }}</span>
                    </p>
                </div>
            </div>
            <span class="inline-flex items-center px-3.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                ● Expired
            </span>
        </div>
        @endif

        <!-- Available Plans Comparison Grid -->
        <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-10 shadow-lg space-y-8">
            <div class="text-center max-w-xl mx-auto">
                <h3 class="text-xl font-bold text-slate-900">Select a Plan to Continue</h3>
                <p class="text-xs text-slate-500 mt-1">Instant activation upon plan confirmation or payment</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-{{ $company->canUseTrial() ? '3' : '2' }} gap-6 items-stretch">
                
                @if($company->canUseTrial())
                <!-- Free Trial Option -->
                <div class="flex flex-col justify-between border-2 border-indigo-600 bg-indigo-50/40 rounded-2xl p-6 relative shadow-md">
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-indigo-600 text-white text-[10px] font-extrabold uppercase px-3 py-0.5 rounded-full">
                        FREE TRIAL
                    </span>
                    <div>
                        <h4 class="text-lg font-bold text-slate-900 text-center">3 Months Free</h4>
                        <div class="my-4 text-center">
                            <span class="text-3xl font-extrabold text-indigo-600">₹0</span>
                            <span class="text-xs text-slate-500 block">3 months full trial</span>
                        </div>
                        <p class="text-xs text-slate-600 text-center mb-4">Try out all SaaS tools with zero upfront payment.</p>
                    </div>

                    @if(auth()->user()->isAdmin())
                    <form action="{{ route('subscription.activate-plan') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ \App\Models\SubscriptionPlan::where('type', 'trial')->first()->id ?? '' }}">
                        <button type="submit" class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow transition text-sm">
                            Start Free Trial
                        </button>
                    </form>
                    @else
                    <button disabled class="w-full py-2.5 px-4 bg-slate-200 text-slate-400 font-semibold rounded-xl text-sm cursor-not-allowed">
                        Admin Only
                    </button>
                    @endif
                </div>
                @endif

                @php $paidPlans = \App\Models\SubscriptionPlan::active()->paid()->get(); @endphp
                @foreach($paidPlans as $plan)
                <!-- Paid Plan Option -->
                <div class="flex flex-col justify-between border border-slate-200 hover:border-indigo-500 bg-white rounded-2xl p-6 relative shadow-sm hover:shadow-md transition">
                    @if($loop->first && !$company->canUseTrial())
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] font-extrabold uppercase px-3 py-0.5 rounded-full">
                        BEST VALUE
                    </span>
                    @endif

                    <div>
                        <h4 class="text-lg font-bold text-slate-900 text-center">{{ $plan->name }}</h4>
                        <div class="my-4 text-center">
                            <span class="text-3xl font-extrabold text-slate-900">₹{{ number_format($plan->price) }}</span>
                            <span class="text-xs text-slate-500 block">for {{ $plan->duration_months }} months</span>
                        </div>
                        <p class="text-xs text-slate-600 text-center mb-4">Complete access to inquiries, WhatsApp API & reports.</p>
                    </div>

                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('subscription.checkout', $plan) }}" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-indigo-600 text-white font-bold rounded-xl text-center shadow transition text-sm block">
                        Choose {{ $plan->name }}
                    </a>
                    @else
                    <button disabled class="w-full py-2.5 px-4 bg-slate-200 text-slate-400 font-semibold rounded-xl text-sm cursor-not-allowed">
                        Admin Only
                    </button>
                    @endif
                </div>
                @endforeach

            </div>
        </div>

        <!-- Footer Actions -->
        <div class="text-center pt-2">
            <form method="POST" action="{{ route('logout') }}" class="inline-block">
                @csrf
                <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-slate-800 transition flex items-center gap-1.5 mx-auto">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Sign out of account
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
