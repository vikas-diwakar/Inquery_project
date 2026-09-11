@extends('layouts.app')

@section('title', 'Workspace Not Found - PropDrip')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-100 relative overflow-hidden">
        <!-- Glow accents -->
        <div class="absolute -top-16 -right-16 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-40 h-40 bg-rose-500/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 space-y-4">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>

            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                Workspace Not Found
            </div>

            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                No company workspace found for <span class="text-indigo-600 font-mono">"{{ $subdomain }}"</span>
            </h1>

            <p class="text-sm text-slate-500 leading-relaxed">
                The workspace subdomain you are trying to access does not exist or may have been renamed. Please verify the URL or look up your organization.
            </p>
        </div>

        <div class="relative z-10 space-y-3 pt-2">
            <a href="{{ route('home') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold shadow-lg shadow-indigo-600/20 transition-all">
                Go to PropDrip Home
            </a>
            
            <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold transition-all">
                Find Your Organization
            </a>

            <div class="pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-400">
                    Looking to launch your own real estate portal?
                    <a href="{{ route('company.register') }}" class="text-indigo-600 font-medium hover:underline">Register Company</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
