@extends('layouts.app')

@section('title', 'Verify Email - PropDrip')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-6">
    <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-3xl p-8 sm:p-10 shadow-2xl space-y-6">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="h-14 w-14 rounded-3xl bg-indigo-50 border border-indigo-100 flex items-center justify-center mx-auto text-indigo-600">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Verify Email Address</h1>
            <p class="text-xs text-slate-500 leading-relaxed">
                Thanks for registering! Before getting started, please check your inbox and click the verification link we just emailed to you.
            </p>
        </div>

        @if (session('status'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-medium text-emerald-800 flex items-start gap-2.5">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <input type="hidden" name="email" value="{{ auth()->user()->email ?? old('email') }}">
            <button type="submit" class="w-full btn-gradient shadow-glow text-white font-bold py-3 px-4 rounded-xl text-sm transition-all duration-200">
                Resend Verification Email →
            </button>
        </form>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-semibold">
            <a href="{{ route('login') }}" class="text-slate-500 hover:text-slate-800 transition">
                ← Back to Login
            </a>

            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-red-600 hover:text-red-700 transition">
                    Log out
                </button>
            </form>
            @endauth
        </div>
    </div>
</div>
@endsection
