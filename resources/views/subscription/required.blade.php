@extends('layouts.app')

@section('title', 'Subscription Required')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full space-y-8">
        <div class="text-center">
            <div class="mx-auto h-16 w-16 text-red-500">
                <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                @if($company->isFirstLogin())
                    Choose Your Subscription Plan
                @else
                    Subscription Required
                @endif
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                @if($company->isFirstLogin())
                    Get started with your property inquiry management platform.
                @else
                    Your subscription has expired. Please renew to continue using the platform.
                @endif
            </p>
        </div>

        @if($activeSubscription)
        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Previous Plan</h3>
                <div class="bg-gray-50 rounded-md p-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $activeSubscription->plan->name }}</p>
                            <p class="text-sm text-gray-500">
                                Expired: {{ $activeSubscription->end_date->format('M j, Y') }}
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Expired
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
            <div class="text-center mb-6">
                <h3 class="text-lg font-medium text-gray-900">Available Plans</h3>
                <p class="text-sm text-gray-500">Choose a plan to restore full access</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-{{ $company->canUseTrial() ? '3' : '2' }} gap-6">
                @if($company->canUseTrial())
                <!-- Free Trial Plan -->
                <div class="border-2 border-indigo-500 bg-indigo-50 rounded-lg p-6">
                    <div class="text-center">
                        <h4 class="text-lg font-medium text-gray-900">Free Trial</h4>
                        <div class="mt-2">
                            <span class="text-2xl font-bold text-indigo-600">FREE</span>
                            <span class="text-gray-500 block">3 months</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">Full access to all features</p>
                    </div>

                    <div class="mt-4">
                        <form action="{{ route('subscription.activate-plan') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ \App\Models\SubscriptionPlan::where('type', 'trial')->first()->id ?? '' }}">
                            <button type="submit"
                               class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Start Free Trial
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                @php
                    $plans = \App\Models\SubscriptionPlan::active()->paid()->get();
                @endphp

                @foreach($plans as $plan)
                <div class="border border-gray-200 rounded-lg p-6 {{ $loop->first ? 'border-indigo-500 bg-indigo-50' : '' }}">
                    <div class="text-center">
                        <h4 class="text-lg font-medium text-gray-900">{{ $plan->name }}</h4>
                        <div class="mt-2">
                            <span class="text-2xl font-bold text-indigo-600">₹{{ number_format($plan->price) }}</span>
                            <span class="text-gray-500">/{{ $plan->duration_months }} months</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('subscription.checkout', $plan) }}"
                           class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Choose Plan
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('logout') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                ← Logout
            </a>
        </div>
    </div>
</div>
@endsection