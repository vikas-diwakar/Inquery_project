@extends('layouts.app')

@section('title', 'Choose Your Subscription Plan')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-10">
    
    <!-- Top Header -->
    <div class="text-center max-w-3xl mx-auto space-y-4">
        <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200/80 shadow-sm">
            <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Welcome to {{ config('app.name') }}
        </span>
        
        <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
            Choose the Perfect Plan for Your Business
        </h1>
        
        <p class="text-base sm:text-lg text-slate-600 font-medium">
            Unlock AI lead intent scoring, auto round-robin allocation, WhatsApp automation, and dynamic QR forms.
        </p>
    </div>

    <!-- Admin Warning if non-admin user -->
    @if(!auth()->user()->isAdmin())
    <div class="max-w-2xl mx-auto bg-amber-50 border border-amber-200 rounded-2xl p-4 text-amber-900 text-sm flex items-start gap-3 shadow-sm">
        <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        <div>
            <span class="font-bold text-amber-950">Administrator Access Required:</span> Only company Administrators can activate subscription plans. Please inform your workspace administrator.
        </div>
    </div>
    @endif

    <!-- Pricing Grid Form -->
    <form action="{{ route('subscription.activate-plan') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-{{ $showTrial ? '3' : '2' }} gap-8 items-stretch max-w-6xl mx-auto">
            
            @if($showTrial)
            <!-- Free Trial Plan Card -->
            <div class="flex flex-col justify-between bg-white border-2 border-indigo-600 rounded-3xl p-8 shadow-xl hover:shadow-2xl relative transition-all duration-300 transform hover:-translate-y-1">
                <div class="absolute -top-4 right-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-[11px] font-extrabold tracking-wider uppercase px-3.5 py-1 rounded-full shadow-md">
                    RECOMMENDED FOR STARTERS
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-extrabold text-slate-900">Free Trial</h2>
                        <span class="p-2.5 bg-indigo-50 text-indigo-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Get started risk-free with zero commitment.</p>

                    <div class="my-6">
                        <span class="text-4xl sm:text-5xl font-black text-indigo-600">FREE</span>
                        <span class="text-slate-600 font-bold text-sm block mt-1">3 Months Full SaaS Access</span>
                    </div>

                    <div class="border-t border-slate-100 pt-6 space-y-4">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Features Included</p>
                        <ul class="space-y-3.5 text-sm text-slate-800 font-medium">
                            <li class="flex items-center gap-3">
                                <div class="h-5 w-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-slate-900 font-semibold">Full access to all SaaS features</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="h-5 w-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-slate-900 font-semibold">🔥 AI Lead Intent Score (0–100)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="h-5 w-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-slate-900 font-semibold">⚡ Round-Robin Lead Allocation</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="h-5 w-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-slate-900 font-semibold">Unlimited Projects & Units</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="h-5 w-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-slate-900 font-semibold">No credit card required</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 pt-4">
                    @if(auth()->user()->isAdmin())
                    <button type="submit" name="plan_id" value="{{ $plans->where('type', 'trial')->first()->id ?? '' }}"
                            class="w-full py-4 px-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-extrabold rounded-2xl shadow-lg shadow-indigo-600/30 transition-all duration-200 text-center text-sm">
                        Start 3-Month Free Trial →
                    </button>
                    @else
                    <button type="button" disabled class="w-full py-4 px-4 bg-slate-100 text-slate-400 font-bold rounded-2xl cursor-not-allowed text-sm">
                        Admin Authorization Required
                    </button>
                    @endif
                </div>
            </div>
            @endif

            @foreach($plans->where('type', 'paid') as $plan)
            <!-- Paid Plan Card -->
            <div class="flex flex-col justify-between bg-white border border-slate-200/90 rounded-3xl p-8 shadow-lg hover:shadow-2xl hover:border-indigo-500/50 relative transition-all duration-300 transform hover:-translate-y-1">
                @if(!$showTrial && $loop->first)
                <div class="absolute -top-4 right-6 bg-amber-500 text-slate-950 text-[11px] font-extrabold tracking-wider uppercase px-3.5 py-1 rounded-full shadow-md">
                    POPULAR CHOICE
                </div>
                @endif

                <div>
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-extrabold text-slate-900">{{ $plan->name }}</h2>
                        <span class="p-2.5 bg-purple-50 text-purple-600 rounded-2xl">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </span>
                    </div>
                    <p class="text-xs font-semibold text-slate-500 mt-1">Full power for growing real estate teams.</p>

                    <div class="my-6">
                        <span class="text-4xl sm:text-5xl font-black text-slate-900">₹{{ number_format($plan->price) }}</span>
                        <span class="text-slate-500 font-semibold text-sm"> / {{ $plan->duration_months }} Months</span>
                    </div>

                    <div class="border-t border-slate-100 pt-6 space-y-4">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Features Included</p>
                        <ul class="space-y-3.5 text-sm text-slate-800 font-medium">
                            <li class="flex items-center gap-3">
                                <div class="h-5 w-5 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-slate-900 font-semibold">🔥 AI Lead Quality Score (0–100)</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="h-5 w-5 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-slate-900 font-semibold">⚡ Auto Round-Robin Allocation</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="h-5 w-5 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-slate-900 font-semibold">WhatsApp Drips & Automation</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="h-5 w-5 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-slate-900 font-semibold">Dynamic QR Forms & Analytics</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <div class="h-5 w-5 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <span class="text-slate-900 font-semibold">Priority Support & API Access</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="mt-8 pt-4">
                    @if(auth()->user()->isAdmin())
                    <button type="submit" name="plan_id" value="{{ $plan->id }}"
                            class="w-full py-4 px-4 bg-slate-900 hover:bg-indigo-600 text-white font-extrabold rounded-2xl shadow-md transition-all duration-200 text-center text-sm">
                        Select {{ $plan->name }} →
                    </button>
                    @else
                    <button type="button" disabled class="w-full py-4 px-4 bg-slate-100 text-slate-400 font-bold rounded-2xl cursor-not-allowed text-sm">
                        Admin Authorization Required
                    </button>
                    @endif
                </div>
            </div>
            @endforeach

        </div>
    </form>

    <!-- Footer Support Line -->
    <div class="text-center text-xs font-semibold text-slate-500 pt-4">
        Need assistance with custom enterprise billing or multi-company plans? Contact our support team.
    </div>
</div>
@endsection