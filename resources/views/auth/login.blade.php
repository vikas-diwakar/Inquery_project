@extends('layouts.app')

@php
    $loginCompany = $currentTenant ?? \App\Models\Company::default();
    $companyName = $loginCompany->name ?? config('app.name', 'Real Estate CRM');
    $companyLogo = $loginCompany->logo ? asset('storage/' . $loginCompany->logo) : null;
@endphp

@section('title', 'Sign In - ' . $companyName)

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-6 px-4 sm:px-6">
    <div class="w-full max-w-4xl overflow-hidden rounded-3xl shadow-2xl bg-white border border-slate-200/80 grid grid-cols-1 lg:grid-cols-12 min-h-[520px]">
        
        <!-- Left Feature Showcase Banner -->
        <div class="hidden lg:flex lg:col-span-5 bg-mesh relative p-8 xl:p-10 flex-col justify-between text-white overflow-hidden">
            <!-- Background Decorative Glow Blobs -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-600/30 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-purple-600/30 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Brand -->
            <div class="relative z-10 flex items-center space-x-3">
                @if($companyLogo)
                    <img src="{{ $companyLogo }}" alt="{{ $companyName }}" 
                        style="max-height: 44px; max-width: 170px; width: auto; height: auto; object-fit: contain;" 
                        class="rounded-xl object-contain bg-white p-1.5 shadow-md flex-shrink-0">
                @else
                    <div class="h-10 w-10 rounded-xl bg-white text-indigo-700 font-black text-lg flex items-center justify-center shadow-md flex-shrink-0">
                        {{ strtoupper(substr($companyName, 0, 1)) }}
                    </div>
                @endif
                <div class="leading-tight">
                    <span class="text-base font-bold text-white block truncate max-w-[190px]">{{ $companyName }}</span>
                    <span class="text-[11px] font-medium text-indigo-200">Real Estate CRM Portal</span>
                </div>
            </div>

            <!-- Center Headline & Highlights -->
            <div class="relative z-10 my-auto py-6 space-y-5">
                <div class="space-y-2">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 backdrop-blur-md border border-white/15 text-indigo-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Agency Operations System</span>
                    </div>
                    <h1 class="text-2xl xl:text-3xl font-extrabold text-white leading-tight">
                        Property & Lead Automation
                    </h1>
                    <p class="text-xs xl:text-sm text-slate-300 leading-relaxed">
                        Manage properties, unit inventory, live inquiries, automated follow-ups, and WhatsApp drip sequences.
                    </p>
                </div>

                <div class="space-y-2.5 pt-1">
                    <div class="flex items-center space-x-3 text-xs font-medium text-slate-200 bg-white/10 backdrop-blur-md px-3.5 py-2.5 rounded-xl border border-white/10">
                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <span>Real-Time Lead Tracking & Routing</span>
                    </div>
                    <div class="flex items-center space-x-3 text-xs font-medium text-slate-200 bg-white/10 backdrop-blur-md px-3.5 py-2.5 rounded-xl border border-white/10">
                        <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <span>WhatsApp Automation & Instant Brochures</span>
                    </div>
                    <div class="flex items-center space-x-3 text-xs font-medium text-slate-200 bg-white/10 backdrop-blur-md px-3.5 py-2.5 rounded-xl border border-white/10">
                        <svg class="w-4 h-4 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Interactive Unit Stacking Charts</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Note -->
            <div class="relative z-10 pt-4 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-300">
                <span>Authorized Staff Access Only</span>
                <span class="text-emerald-400 font-semibold flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Online
                </span>
            </div>
        </div>

        <!-- Right Login Form Column -->
        <div class="col-span-1 lg:col-span-7 p-6 sm:p-10 xl:p-12 flex flex-col justify-center bg-white">
            <div class="max-w-md w-full mx-auto space-y-6">
                
                <!-- Mobile Only Logo Display -->
                <div class="lg:hidden flex items-center justify-center pb-2">
                    @if($companyLogo)
                        <img src="{{ $companyLogo }}" alt="{{ $companyName }}" 
                            style="max-height: 42px; max-width: 170px; width: auto; height: auto; object-fit: contain;" 
                            class="rounded-xl object-contain bg-white p-1 border border-slate-200/80 shadow-xs">
                    @else
                        <div class="h-10 px-4 rounded-xl bg-indigo-600 text-white font-black text-sm flex items-center justify-center shadow-xs">
                            {{ $companyName }}
                        </div>
                    @endif
                </div>

                <!-- Form Header -->
                <div class="space-y-1.5">
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Sign In
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Enter your email and password to access the portal.
                    </p>
                </div>

                <!-- Session Status / Alert Banners -->
                @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-xs font-medium text-emerald-800 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
                @endif

                @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs font-medium text-rose-800 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                @endif

                @if ($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs font-medium text-rose-800 space-y-1">
                    @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-rose-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $error }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login', [], false) }}" class="space-y-4">
                    @csrf

                    <!-- Email Field -->
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
                                placeholder="admin@example.com">
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700 transition">Forgot Password?</a>
                        </div>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                class="block w-full pl-10 pr-11 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                                placeholder="••••••••">
                            <button type="button" 
                                onclick="togglePasswordVisibility('password', this)"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none focus:text-indigo-600 transition-colors cursor-pointer"
                                aria-label="Toggle password visibility">
                                <svg class="h-5 w-5 eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="h-5 w-5 eye-off-icon hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center space-x-2.5 cursor-pointer">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-slate-300 rounded transition-colors">
                            <span class="text-xs font-medium text-slate-600">Remember me</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="w-full btn-gradient shadow-glow text-white font-bold py-3.5 px-4 rounded-xl text-sm transition-all duration-200 flex items-center justify-center space-x-2 hover:opacity-95 active:scale-[0.99]">
                            <span>Sign In to Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Security Note -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-center text-center text-xs text-slate-400 gap-1.5">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Secure end-to-end encrypted session</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    
    const eyeIcon = btn.querySelector('.eye-icon');
    const eyeOffIcon = btn.querySelector('.eye-off-icon');
    if (eyeIcon && eyeOffIcon) {
        if (isPassword) {
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
            btn.setAttribute('aria-label', 'Hide password');
        } else {
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
            btn.setAttribute('aria-label', 'Show password');
        }
    }
}
</script>
@endpush
