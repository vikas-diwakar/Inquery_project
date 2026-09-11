@php
    $isSubdomain = !empty(request()->getHost()) && (
        str_ends_with(request()->getHost(), '.localhost') || 
        (!empty(config('app.domain')) && str_ends_with(request()->getHost(), '.' . ltrim(config('app.domain'), '.'))) ||
        (count(explode('.', request()->getHost())) >= 3 && !filter_var(request()->getHost(), FILTER_VALIDATE_IP))
    );
    // If we are on an unrecognized subdomain (or explicitly no tenant resolved), route back to root domain
    $homeUrl = ($isSubdomain && !isset($currentTenant)) ? \App\Models\Company::getRootUrl('/') : route('home');
    $aboutUrl = ($isSubdomain && !isset($currentTenant)) ? \App\Models\Company::getRootUrl('/about') : route('about');
    $contactUrl = ($isSubdomain && !isset($currentTenant)) ? \App\Models\Company::getRootUrl('/contact') : route('contact');
    $loginUrl = ($isSubdomain && !isset($currentTenant)) ? \App\Models\Company::getRootUrl('/login') : route('login');
    $registerUrl = ($isSubdomain && !isset($currentTenant)) ? \App\Models\Company::getRootUrl('/register') : route('company.register');
@endphp

<header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-xs transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            <!-- Official Brand Logo -->
            <a href="{{ $homeUrl }}" class="flex items-center space-x-3 group">
                <img src="{{ asset('images/propdrip-logo.png') }}" alt="PropDrip Logo" class="h-10 sm:h-12 w-auto rounded-xl object-contain group-hover:scale-105 transition-transform duration-200">
            </a>

            <!-- Center Navigation Links (Desktop) -->
            <nav class="hidden md:flex items-center space-x-2">
                <a href="{{ $homeUrl }}" class="{{ request()->routeIs('home') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-semibold' }} px-4 py-2 rounded-xl text-sm transition-all">
                    Home
                </a>
                <a href="{{ $aboutUrl }}" class="{{ request()->routeIs('about') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-semibold' }} px-4 py-2 rounded-xl text-sm transition-all">
                    About
                </a>
                <a href="{{ $contactUrl }}" class="{{ request()->routeIs('contact') ? 'bg-indigo-50 text-indigo-700 font-bold border border-indigo-100' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-semibold' }} px-4 py-2 rounded-xl text-sm transition-all">
                    Contact Us
                </a>
            </nav>

            <!-- Right Actions (Login & Register / Dashboard) -->
            <div class="hidden md:flex items-center space-x-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm transition-all shadow-md shadow-indigo-500/20">
                        Dashboard →
                    </a>
                @else
                    <a href="{{ $loginUrl }}" class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-700 hover:text-indigo-600 hover:bg-slate-100 transition-all">
                        Log In
                    </a>
                    <a href="{{ $registerUrl }}" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm transition-all shadow-md shadow-indigo-500/20">
                        Start Free Trial
                    </a>
                @endauth
            </div>

            <!-- Mobile Menu Toggle Button -->
            <div class="flex items-center md:hidden">
                <button type="button" onclick="document.getElementById('publicMobileMenu').classList.toggle('hidden')" class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer -->
    <div id="publicMobileMenu" class="hidden md:hidden border-t border-slate-200/80 bg-white px-4 pt-3 pb-6 space-y-2 shadow-lg">
        <a href="{{ $homeUrl }}" class="block px-3 py-2.5 rounded-xl text-base font-semibold {{ request()->routeIs('home') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
            Home
        </a>
        <a href="{{ $aboutUrl }}" class="block px-3 py-2.5 rounded-xl text-base font-semibold {{ request()->routeIs('about') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
            About
        </a>
        <a href="{{ $contactUrl }}" class="block px-3 py-2.5 rounded-xl text-base font-semibold {{ request()->routeIs('contact') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-100' }}">
            Contact Us
        </a>
        <div class="pt-3 border-t border-slate-200 flex flex-col space-y-2">
            @auth
                <a href="{{ route('dashboard') }}" class="w-full text-center px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md">
                    Dashboard →
                </a>
            @else
                <a href="{{ $loginUrl }}" class="w-full text-center px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-sm">
                    Log In
                </a>
                <a href="{{ $registerUrl }}" class="w-full text-center px-4 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm shadow-md">
                    Start Free Trial
                </a>
            @endauth
        </div>
    </div>
</header>
