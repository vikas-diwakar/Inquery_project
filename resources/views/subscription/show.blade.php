@extends('layouts.app')

@section('title', 'Subscription Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Subscription Details</h1>
                <p class="mt-2 text-gray-600">Manage your subscription and billing</p>
            </div>
            <a href="{{ route('subscription.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Upgrade/Renew
            </a>
        </div>
    </div>

    @if($activeSubscription)
    <div class="bg-white shadow rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Current Subscription</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900">{{ $activeSubscription->plan->name }}</h3>
                <p class="text-sm text-gray-500 mt-1">
                    @if($activeSubscription->isTrial())
                        Trial Period
                    @else
                        Paid Subscription
                    @endif
                </p>
            </div>
            <div class="text-right">
                <div class="flex items-center justify-end space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($activeSubscription->isExpired()) bg-red-100 text-red-800
                        @elseif($activeSubscription->isExpiringSoon()) bg-yellow-100 text-yellow-800
                        @else bg-green-100 text-green-800 @endif">
                        @if($activeSubscription->isExpired()) Expired
                        @elseif($activeSubscription->isExpiringSoon()) Expires Soon
                        @else Active @endif
                    </span>
                </div>
                <p class="text-sm text-gray-500 mt-2">
                    @if($activeSubscription->isTrial())
                        Ends: {{ $activeSubscription->end_date->format('M j, Y') }}
                        @if($activeSubscription->isExpiringSoon())
                        <span class="text-yellow-600 font-medium">({{ $activeSubscription->daysUntilExpiry() }} days left)</span>
                        @endif
                    @else
                        Renews: {{ $activeSubscription->end_date->format('M j, Y') }}
                        @if($activeSubscription->isExpiringSoon())
                        <span class="text-yellow-600 font-medium">({{ $activeSubscription->daysUntilExpiry() }} days left)</span>
                        @endif
                    @endif
                </p>
            </div>
        </div>

        @if($activeSubscription->isExpired())
        <div class="mt-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Subscription Expired</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>Your subscription has expired. Renew now to restore full access to your account.</p>
                    </div>
                </div>
            </div>
        </div>
        @elseif($activeSubscription->isExpiringSoon())
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.732 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Subscription Expiring Soon</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Your subscription will expire in {{ $activeSubscription->daysUntilExpiry() }} days. Renew now to avoid service interruption.</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Subscription History</h2>
        </div>

        <div class="divide-y divide-gray-200">
            @forelse($subscriptions as $subscription)
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-900">{{ $subscription->plan->name }}</h3>
                        <p class="text-sm text-gray-500">
                            {{ $subscription->start_date->format('M j, Y') }} - {{ $subscription->end_date->format('M j, Y') }}
                        </p>
                        @if($subscription->amount_paid)
                        <p class="text-sm text-gray-500">₹{{ number_format($subscription->amount_paid) }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($subscription->status === 'active') bg-green-100 text-green-800
                            @elseif($subscription->status === 'trial') bg-blue-100 text-blue-800
                            @elseif($subscription->status === 'expired') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($subscription->status) }}
                        </span>
                        @if($subscription->payment_reference)
                        <p class="text-xs text-gray-500 mt-1">{{ $subscription->payment_reference }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-gray-500">
                <p>No subscription history found.</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="mt-8 text-center">
        <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800">← Back to Dashboard</a>
    </div>
</div>
@endsection