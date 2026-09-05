@extends('layouts.app')

@section('title', 'Forgot Password - PropDrip')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-6">
    <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-3xl p-8 sm:p-10 shadow-2xl space-y-6">
        
        <!-- Header -->
        <div class="text-center space-y-2">
            <div class="h-12 w-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center mx-auto text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Reset Password</h1>
            <p class="text-xs text-slate-500">
                Forgot your password? Enter your registered email address and we'll send you a password reset link.
            </p>
        </div>

        <!-- Status Alerts -->
        @if (session('status'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-medium text-emerald-800 flex items-start gap-2.5">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
        @endif

        @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-xs font-medium text-red-800 space-y-1">
            @foreach ($errors->all() as $error)
                <p>• {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <!-- Form -->
        <form class="space-y-5" action="{{ route('password.email') }}" method="POST">
            @csrf

            <div class="space-y-1.5">
                <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Email Address</label>
                <div class="relative rounded-xl shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                        </svg>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                        value="{{ old('email') }}"
                        class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                        placeholder="yourname@company.com">
                </div>
            </div>

            <button type="submit" class="w-full btn-gradient shadow-glow text-white font-bold py-3 px-4 rounded-xl text-sm transition-all duration-200">
                Email Password Reset Link →
            </button>
        </form>

        <!-- Back to Login -->
        <div class="text-center pt-2 border-t border-slate-100">
            <a href="{{ route('login') }}" class="text-xs font-bold text-slate-600 hover:text-indigo-600 transition">
                ← Back to Sign In
            </a>
        </div>
    </div>
</div>
@endsection
