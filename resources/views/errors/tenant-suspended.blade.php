@extends('layouts.app')

@section('title', 'Workspace Inactive - PropDrip')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-100 relative overflow-hidden">
        <div class="relative z-10 space-y-4">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shadow-inner">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                Workspace Suspended
            </div>

            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                {{ $tenant->name }} is currently inactive
            </h1>

            <p class="text-sm text-slate-500 leading-relaxed">
                This workspace has been suspended or deactivated. Please reach out to your company administrator or support for assistance.
            </p>
        </div>

        <div class="relative z-10 pt-4 border-t border-slate-100">
            <a href="{{ \App\Models\Company::getRootUrl('/') }}" class="w-full inline-flex items-center justify-center px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold transition-all">
                Return to PropDrip Home
            </a>
        </div>
    </div>
</div>
@endsection
