@extends('layouts.app')

@section('title', 'Sign In - PropDrip')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center py-6">
    <div class="w-full max-w-5xl overflow-hidden rounded-3xl shadow-2xl bg-white border border-slate-200/80 grid grid-cols-1 lg:grid-cols-12 min-h-[580px]">
        
        <!-- Left Feature Showcase Banner (Visible on LG screens) -->
        <div class="hidden lg:flex lg:col-span-6 bg-mesh relative p-12 flex-col justify-between text-white overflow-hidden">
            <!-- Background Decorative Glow Blobs -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-600/30 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-purple-600/30 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Brand -->
            <div class="relative z-10 flex items-center space-x-3">
                <img src="{{ asset('images/propdrip-logo.png') }}" alt="PropDrip Logo" class="h-10 w-auto rounded-xl object-contain bg-white/90 p-1 shadow-lg">
            </div>

            <!-- Center Headline & Highlights -->
            <div class="relative z-10 my-auto py-8 space-y-6">
                <h1 class="text-3xl font-extrabold text-white leading-tight">
                    Smart Property Inquiry & Lead Management
                </h1>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Empower your real estate sales team with automated QR inquiry forms, brochure distribution, smart follow-up schedules, and lead webhooks.
                </p>

                <!-- Feature Pills -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-center space-x-3 text-xs font-medium text-slate-200 bg-white/10 backdrop-blur-md px-3.5 py-2.5 rounded-xl border border-white/10">
                        <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Project-Specific Dynamic QR Inquiry Forms</span>
                    </div>
                    <div class="flex items-center space-x-3 text-xs font-medium text-slate-200 bg-white/10 backdrop-blur-md px-3.5 py-2.5 rounded-xl border border-white/10">
                        <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Automated Follow-Up Reminders & Schedules</span>
                    </div>
                    <div class="flex items-center space-x-3 text-xs font-medium text-slate-200 bg-white/10 backdrop-blur-md px-3.5 py-2.5 rounded-xl border border-white/10">
                        <svg class="w-4 h-4 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Embeddable Website Widgets & Webhook API</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Stats Bar -->
            <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                <span>Multi-Tenant Builder Platform</span>
                <span class="text-indigo-400 font-semibold">v2.5 Active</span>
            </div>
        </div>

        <!-- Right Login Form Column -->
        <div class="col-span-1 lg:col-span-6 p-8 sm:p-12 flex flex-col justify-center bg-white">
            <div class="max-w-md w-full mx-auto space-y-6">
                <!-- Form Header -->
                <div class="space-y-2">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Welcome Back
                    </h2>
                    <p class="text-sm text-slate-500">
                        Sign in to access your builder portal dashboard
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
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-xs font-medium text-red-800 space-y-2">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>

                    @if(session('unverified_email'))
                    <form action="{{ route('verification.resend') }}" method="POST" class="pt-1">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                        <button type="submit" class="w-full py-2 px-3 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg text-xs transition text-center">
                            Resend Email Verification Link →
                        </button>
                    </form>
                    @endif
                </div>
                @endif

                @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-xs font-medium text-red-800 space-y-1">
                    @foreach ($errors->all() as $error)
                        <p>• {{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <!-- Login Form -->
                <form class="space-y-5" action="{{ route('login') }}" method="POST">
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
                                placeholder="builder@company.com">
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
                                class="block w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all" 
                                placeholder="••••••••">
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
                        <button type="submit" class="w-full btn-gradient shadow-glow text-white font-semibold py-3 px-4 rounded-xl text-sm transition-all duration-200 flex items-center justify-center space-x-2">
                            <span>Sign In to Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Footer Link to Register Company -->
                <div class="pt-4 border-t border-slate-100 text-center">
                    <p class="text-xs text-slate-500">
                        Don't have a company workspace yet?
                    </p>
                    <a href="{{ route('company.register') }}" class="inline-flex items-center space-x-1 text-sm font-bold text-indigo-600 hover:text-indigo-700 mt-1">
                        <span>Register your company</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

