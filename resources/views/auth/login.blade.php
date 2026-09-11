@extends('layouts.app')

@section('title', isset($currentTenant) ? 'Sign In - ' . $currentTenant->name . ' Workspace' : 'Sign In - PropDrip')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-6 px-4 sm:px-6">
    <div class="w-full max-w-5xl overflow-hidden rounded-3xl shadow-2xl bg-white border border-slate-200/80 grid grid-cols-1 lg:grid-cols-12 min-h-[560px]">
        
        <!-- Left Feature Showcase Banner (Visible on LG screens) -->
        <div class="hidden lg:flex lg:col-span-6 bg-mesh relative p-10 xl:p-12 flex-col justify-between text-white overflow-hidden">
            <!-- Background Decorative Glow Blobs -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-600/30 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-purple-600/30 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Brand -->
            <div class="relative z-10 flex items-center space-x-3">
                @if(isset($currentTenant))
                    @if($currentTenant->logo)
                        <img src="{{ asset('storage/' . $currentTenant->logo) }}" alt="{{ $currentTenant->name }}" 
                            style="max-height: 44px; max-width: 180px; width: auto; height: auto; object-fit: contain;" 
                            class="rounded-xl object-contain bg-white p-1.5 shadow-md flex-shrink-0">
                    @else
                        <div class="h-11 w-11 rounded-xl bg-white text-indigo-700 font-black text-xl flex items-center justify-center shadow-md flex-shrink-0">
                            {{ strtoupper(substr($currentTenant->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="leading-tight">
                        <span class="text-base font-bold text-white block truncate max-w-[200px]">{{ $currentTenant->name }}</span>
                        <span class="text-[11px] font-medium text-indigo-200">Official Builder Workspace</span>
                    </div>
                @else
                    <img src="{{ asset('images/propdrip-logo.png') }}" alt="PropDrip Logo" 
                        style="max-height: 44px; max-width: 180px; width: auto; height: auto; object-fit: contain;" 
                        class="rounded-xl object-contain bg-white/90 p-1.5 shadow-md">
                @endif
            </div>

            <!-- Center Headline & Highlights -->
            <div class="relative z-10 my-auto py-8 space-y-6">
                @if(isset($currentTenant))
                    <div class="space-y-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 backdrop-blur-md border border-white/15 text-indigo-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <span>Dedicated Company Portal</span>
                        </div>
                        <h1 class="text-3xl font-extrabold text-white leading-tight">
                            {{ $currentTenant->name }}
                        </h1>
                        <p class="text-sm text-slate-300 leading-relaxed">
                            Sign in to access real-time inquiries, unit stacking charts, brochure distribution, and lead drip automation.
                        </p>
                    </div>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-center space-x-3 text-xs font-medium text-slate-200 bg-white/10 backdrop-blur-md px-3.5 py-2.5 rounded-xl border border-white/10">
                            <svg class="w-4 h-4 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Dedicated & Isolated Workspace Access</span>
                        </div>
                        <div class="flex items-center space-x-3 text-xs font-medium text-slate-200 bg-white/10 backdrop-blur-md px-3.5 py-2.5 rounded-xl border border-white/10">
                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span>Live Lead Distribution & Instant WhatsApp</span>
                        </div>
                        <div class="flex items-center space-x-3 text-xs font-medium text-slate-200 bg-white/10 backdrop-blur-md px-3.5 py-2.5 rounded-xl border border-white/10">
                            <svg class="w-4 h-4 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span>Unit Availability Map & Stacking Charts</span>
                        </div>
                    </div>
                @else
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
                @endif
            </div>

            <!-- Bottom Stats Bar -->
            <div class="relative z-10 pt-6 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                @if(isset($currentTenant))
                    <span class="font-mono text-indigo-200 truncate max-w-[200px]">{{ $currentTenant->workspace_domain }}</span>
                    <span class="text-emerald-400 font-semibold flex items-center gap-1.5 flex-shrink-0">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Verified Workspace
                    </span>
                @else
                    <span>Multi-Tenant Builder Platform</span>
                    <span class="text-indigo-400 font-semibold">v2.5 Active</span>
                @endif
            </div>
        </div>

        <!-- Right Login Form Column -->
        <div class="col-span-1 lg:col-span-6 p-6 sm:p-10 xl:p-12 flex flex-col justify-center bg-white">
            <div class="max-w-md w-full mx-auto space-y-6">
                
                <!-- Mobile Only Company Logo Display (Clean standard size) -->
                @if(isset($currentTenant))
                    <div class="lg:hidden flex items-center justify-center pb-2">
                        @if($currentTenant->logo)
                            <img src="{{ asset('storage/' . $currentTenant->logo) }}" alt="{{ $currentTenant->name }}" 
                                style="max-height: 42px; max-width: 180px; width: auto; height: auto; object-fit: contain;" 
                                class="rounded-xl object-contain bg-white p-1 border border-slate-200/80 shadow-xs">
                        @else
                            <div class="h-10 px-4 rounded-xl bg-indigo-600 text-white font-black text-sm flex items-center justify-center shadow-xs">
                                {{ $currentTenant->name }}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Form Header -->
                <div class="space-y-1.5">
                    @if(isset($currentTenant))
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100 mb-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                            <span>{{ $currentTenant->name }} Workspace</span>
                        </div>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                            Sign In to Portal
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500">
                            Enter your workspace credentials to access your dashboard.
                        </p>
                    @else
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                            Welcome Back
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500">
                            Sign in to access your builder portal dashboard
                        </p>
                    @endif
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
                    <div class="space-y-1">
                        <p>{{ session('error') }}</p>
                        @if (session('unverified_email'))
                        <form method="POST" action="{{ route('verification.resend') }}" class="pt-1">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('unverified_email') }}">
                            <button type="submit" class="text-rose-700 underline font-semibold hover:text-rose-900 text-xs">
                                Resend verification email &rarr;
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endif

                @if (session('workspace_error'))
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs font-medium text-amber-800 flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span>{{ session('workspace_error') }}</span>
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
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
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
                                placeholder="name@company.com">
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
                        <button type="submit" class="w-full btn-gradient shadow-glow text-white font-bold py-3.5 px-4 rounded-xl text-sm transition-all duration-200 flex items-center justify-center space-x-2 hover:opacity-95 active:scale-[0.99]">
                            <span>Sign In to Dashboard</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Switch Workspace / Find Workspace Section -->
                @if(isset($currentTenant))
                    <div class="pt-4 border-t border-slate-100 text-center">
                        <p class="text-xs text-slate-500">
                            Not a member of {{ $currentTenant->name }}?
                            <a href="{{ route('home') }}" class="text-indigo-600 font-bold hover:underline ml-1">
                                Go to Main Portal &rarr;
                            </a>
                        </p>
                    </div>
                @else
                    <!-- Keka-style "Find Your Organization" Card -->
                    <div class="pt-4 border-t border-slate-100 space-y-3">
                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 space-y-3">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs">
                                    🏢
                                </div>
                                <h3 class="text-xs font-bold text-slate-900">Find your Company Workspace</h3>
                            </div>
                            <p class="text-[11px] text-slate-500">
                                Enter your company name or subdomain to go to your dedicated portal.
                            </p>
                            <form action="{{ route('workspace.find') }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="workspace" required placeholder="e.g. apex-infra" 
                                    class="flex-1 py-2 px-3 text-xs bg-white border border-slate-200 rounded-lg text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <button type="submit" class="px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold transition-all">
                                    Go &rarr;
                                </button>
                            </form>
                        </div>

                        <!-- Footer Link to Register Company -->
                        <div class="text-center pt-1">
                            <p class="text-xs text-slate-500">
                                Don't have a company workspace yet?
                                <a href="{{ route('company.register') }}" class="text-indigo-600 font-bold hover:underline ml-1">
                                    Register company
                                </a>
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
