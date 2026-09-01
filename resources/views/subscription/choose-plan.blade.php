@extends('layouts.app')

@section('title', 'Choose Your Subscription Plan')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full space-y-8">
        <div class="text-center">
            <h2 class="text-2xl md:text-3xl font-bold text-slate-900">
                Welcome to {{ config('app.name') }}!
            </h2>
            <p class="mt-2 text-sm text-slate-600">
                Choose a subscription plan to get started with your property inquiry management platform.
            </p>
        </div>

        <form action="{{ route('subscription.activate-plan') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-{{ $showTrial ? '3' : '2' }} gap-6">
                @if($showTrial)
                <!-- Free Trial Plan -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden border-2 border-primary-500 relative">
                    <div class="absolute top-0 right-0 bg-primary-500 text-white px-3 py-1 text-xs font-medium rounded-bl-lg">
                        RECOMMENDED
                    </div>
                    <div class="px-6 py-8">
                        <h3 class="text-2xl font-bold text-slate-900 text-center">Free Trial</h3>
                        <div class="mt-4 text-center">
                            <span class="text-4xl font-bold text-primary-600">FREE</span>
                            <span class="text-slate-500 block">3 months</span>
                        </div>

                        <ul class="mt-8 space-y-4">
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-3 text-slate-700">Full access to all features</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-3 text-slate-700">Create unlimited projects</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-3 text-slate-700">Manage inquiries & QR codes</span>
                            </li>
                            <li class="flex items-center">
                                <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-3 text-slate-700">No payment required</span>
                            </li>
                        </ul>
                    </div>

                    <div class="px-6 py-4 bg-slate-50">
                        <button type="submit" name="plan_id" value="{{ $plans->where('type', 'trial')->first()->id ?? '' }}"
                                class="w-full bg-primary-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Start Free Trial
                        </button>
                    </div>
                </div>
                @endif

                @foreach($plans->where('type', 'paid') as $plan)
                <!-- Paid Plans -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden {{ !$showTrial && $loop->first ? 'border-2 border-primary-500 relative' : '' }}">
                    @if(!$showTrial && $loop->first)
                    <div class="absolute top-0 right-0 bg-primary-500 text-white px-3 py-1 text-xs font-medium rounded-bl-lg">
                        POPULAR
                    </div>
                    @endif

                    <div class="px-6 py-8">
                        <h3 class="text-2xl font-bold text-slate-900 text-center">{{ $plan->name }}</h3>
                        <div class="mt-4 text-center">
                            <span class="text-4xl font-bold text-primary-600">₹{{ number_format($plan->price) }}</span>
                            <span class="text-slate-500">/{{ $plan->duration_months }} months</span>
                        </div>

                        <ul class="mt-8 space-y-4">
                            @foreach($plan->features as $feature => $enabled)
                                @if($enabled)
                                <li class="flex items-center">
                                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-3 text-slate-700">{{ ucfirst(str_replace('_', ' ', $feature)) }}</span>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>

                    <div class="px-6 py-4 bg-slate-50">
                        <button type="submit" name="plan_id" value="{{ $plan->id }}"
                                class="w-full bg-primary-600 border border-transparent rounded-md shadow-sm py-2 px-4 text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            Choose {{ $plan->name }}
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </form>

        @if($showTrial)
        <div class="text-center text-sm text-slate-500">
            <p>You can upgrade to a paid plan at any time during or after your trial period.</p>
        </div>
        @endif
    </div>
</div>
@endsection