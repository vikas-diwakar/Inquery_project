@extends('layouts.app')

@section('title', 'Subscription Required')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl w-full">
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 rounded-2xl bg-red-100 items-center justify-center mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-slate-900">
                @if($company->isFirstLogin()) Choose Your Subscription Plan
                @else Subscription Required
                @endif
            </h2>
            <p class="mt-2 text-sm text-slate-600">
                @if($company->isFirstLogin()) Get started with your property inquiry management platform.
                @else Your subscription has expired. Please renew to continue.
                @endif
            </p>
        </div>

        @if($activeSubscription)
        <div class="card p-6 mb-6">
            <h3 class="text-lg font-semibold text-slate-900 mb-2">Previous Plan</h3>
            <div class="flex flex-wrap justify-between items-center gap-4 p-4 bg-slate-50 rounded-xl">
                <div>
                    <p class="font-medium text-slate-900">{{ $activeSubscription->plan->name }}</p>
                    <p class="text-sm text-slate-500">Expired: {{ $activeSubscription->end_date->format('M j, Y') }}</p>
                </div>
                <span class="badge bg-red-100 text-red-800">Expired</span>
            </div>
        </div>
        @endif

        <div class="card p-6 md:p-8">
            <div class="text-center mb-6">
                <h3 class="text-lg font-semibold text-slate-900">Available Plans</h3>
                <p class="text-sm text-slate-500 mt-1">Choose a plan to restore full access</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-{{ $company->canUseTrial() ? '3' : '2' }} gap-4 md:gap-6">
                @if($company->canUseTrial())
                <div class="card p-6 border-2 border-primary-500 bg-primary-50/50">
                    <div class="text-center">
                        <h4 class="text-lg font-semibold text-slate-900">Free Trial</h4>
                        <div class="mt-2"><span class="text-2xl font-bold text-primary-600">FREE</span><span class="text-slate-500 block text-sm">3 months</span></div>
                        <p class="text-sm text-slate-600 mt-2">Full access to all features</p>
                    </div>
                    <form action="{{ route('subscription.activate-plan') }}" method="POST" class="mt-4">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ \App\Models\SubscriptionPlan::where('type', 'trial')->first()->id ?? '' }}">
                        <button type="submit" class="btn-primary w-full">Start Free Trial</button>
                    </form>
                </div>
                @endif

                @php $plans = \App\Models\SubscriptionPlan::active()->paid()->get(); @endphp
                @foreach($plans as $plan)
                <div class="card p-6 {{ $loop->first && !$company->canUseTrial() ? 'border-2 border-primary-500 bg-primary-50/50' : '' }}">
                    <div class="text-center">
                        <h4 class="text-lg font-semibold text-slate-900">{{ $plan->name }}</h4>
                        <div class="mt-2"><span class="text-2xl font-bold text-primary-600">₹{{ number_format($plan->price) }}</span><span class="text-slate-500">/{{ $plan->duration_months }} months</span></div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('subscription.checkout', $plan) }}" class="btn-primary w-full text-center block">Choose Plan</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <p class="text-center mt-6">
            <a href="{{ route('logout') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">← Logout</a>
        </p>
    </div>
</div>
@endsection
