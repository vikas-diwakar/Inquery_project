@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Choose Your Subscription Plan</h1>
        <p class="mt-2 text-gray-600">Select a plan that best fits your business needs</p>
    </div>

    @if($currentSubscription)
    <div class="mb-8 bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Current Subscription</h2>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-lg font-medium">{{ $currentSubscription->plan->name }}</p>
                <p class="text-sm text-gray-500">
                    @if($currentSubscription->isTrial())
                        Trial ends: {{ $currentSubscription->end_date->format('M j, Y') }}
                    @else
                        Renews: {{ $currentSubscription->end_date->format('M j, Y') }}
                    @endif
                </p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($currentSubscription->isExpired()) bg-red-100 text-red-800
                    @elseif($currentSubscription->isExpiringSoon()) bg-yellow-100 text-yellow-800
                    @else bg-green-100 text-green-800 @endif">
                    @if($currentSubscription->isExpired()) Expired
                    @elseif($currentSubscription->isExpiringSoon()) Expires Soon
                    @else Active @endif
                </span>
                @if($currentSubscription->isExpired())
                <p class="text-xs text-red-600 mt-1">Renew to continue service</p>
                @elseif($currentSubscription->isExpiringSoon())
                <p class="text-xs text-yellow-600 mt-1">{{ $currentSubscription->daysUntilExpiry() }} days left</p>
                @endif
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($plans as $plan)
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-8">
                <h3 class="text-2xl font-bold text-gray-900 text-center">{{ $plan->name }}</h3>
                <div class="mt-4 text-center">
                    <span class="text-4xl font-bold text-indigo-600">₹{{ number_format($plan->price) }}</span>
                    <span class="text-gray-500">/{{ $plan->duration_months }} months</span>
                </div>

                <ul class="mt-8 space-y-4">
                    @foreach($plan->features as $feature => $enabled)
                        @if($enabled)
                        <li class="flex items-center">
                            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-3 text-gray-700">{{ ucfirst(str_replace('_', ' ', $feature)) }}</span>
                        </li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <div class="px-6 py-4 bg-gray-50">
                <a href="{{ route('subscription.checkout', $plan) }}"
                   class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Select Plan
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-12 text-center">
        <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800">← Back to Dashboard</a>
    </div>
</div>
@endsection