<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <x-seo-meta :title="View::yieldContent('title')" />

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @php
        $navCompany = $currentTenant ?? (auth()->check() ? auth()->user()->company : \App\Models\Company::default());
        $navLogoUrl = ($navCompany && $navCompany->logo) 
            ? asset('storage/' . $navCompany->logo) 
            : asset('images/propdrip-logo.png');
        $hasActiveSub = true;
        $logoRoute = route('dashboard');
    @endphp
    <link rel="preload" as="image" href="{{ $navLogoUrl }}" fetchpriority="high">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-mesh-light min-h-screen flex flex-col text-slate-800 font-sans antialiased">
    @auth
        <nav class="sticky top-0 z-40 glass-nav border-b border-slate-200/80 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Left Section: Logo & Nav Links -->
                    <div class="flex items-center space-x-4 lg:space-x-6 flex-shrink-0">
                        <div class="w-32 sm:w-36 h-9 flex items-center flex-shrink-0">
                            <a href="{{ $logoRoute }}" class="flex items-center h-full w-full">
                                <img src="{{ $navLogoUrl }}" 
                                    alt="{{ $navCompany->name ?? config('app.name') }}" 
                                    width="130" height="36"
                                    loading="eager" decoding="sync" fetchpriority="high"
                                    style="height: 36px; max-height: 36px; max-width: 130px; width: auto; object-fit: contain;" 
                                    class="rounded-lg object-contain flex-shrink-0">
                            </a>
                        </div>

                        <!-- Desktop Navigation -->
                        <div class="hidden md:flex items-center space-x-1">
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} px-3 py-2 rounded-lg text-sm transition-colors duration-150">
                                Dashboard
                            </a>
                            
                            @if(!session('selected_project_id'))
                                <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} px-3 py-2 rounded-lg text-sm transition-colors duration-150">
                                    Projects
                                </a>

                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} px-3 py-2 rounded-lg text-sm transition-colors duration-150">
                                        Users
                                    </a>
                                    <a href="{{ route('settings.company') }}" class="{{ request()->routeIs('settings.company*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} px-3 py-2 rounded-lg text-sm transition-colors duration-150 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <span>Company Settings</span>
                                    </a>
                                @endif
                            @endif

                            @if(session('selected_project_id'))
                                <a href="{{ route('inquiries.index') }}" class="{{ request()->routeIs('inquiries.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} px-3 py-2 rounded-lg text-sm transition-colors duration-150">
                                    Inquiries
                                </a>
                                <a href="{{ route('follow-ups.index') }}" class="{{ request()->routeIs('follow-ups.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} px-3 py-2 rounded-lg text-sm transition-colors duration-150">
                                    Follow-ups
                                </a>
                                <a href="{{ route('brochures.index') }}" class="{{ request()->routeIs('brochures.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} px-3 py-2 rounded-lg text-sm transition-colors duration-150">
                                    Brochures
                                </a>
                                <a href="{{ route('forms-qr.index') }}" class="{{ request()->routeIs('forms-qr.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} px-3 py-2 rounded-lg text-sm transition-colors duration-150">
                                    Form & QR
                                </a>
                                <a href="{{ route('settings.whatsapp') }}" class="{{ request()->routeIs('settings.whatsapp*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} px-3 py-2 rounded-lg text-sm transition-colors duration-150">
                                    WhatsApp API
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Right Section: Selected Project & User Profile -->
                    <div class="hidden sm:flex items-center space-x-3 flex-shrink-0">
                        @if(session('selected_project_id') && isset($selectedProject))
                            <div class="flex items-center bg-indigo-50/80 border border-indigo-100 text-indigo-800 text-xs font-semibold px-3 py-1.5 rounded-full">
                                <span class="h-2 w-2 rounded-full bg-indigo-600 mr-2"></span>
                                <span class="max-w-[120px] truncate">{{ $selectedProject->name }}</span>
                            </div>
                        @endif

                        <!-- User Profile Badge -->
                        <div class="flex items-center space-x-2 bg-slate-50 border border-slate-200/80 px-3 py-1.5 rounded-full text-xs font-medium text-slate-700">
                            <div class="h-6 w-6 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-[10px]">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <div class="flex flex-col text-left leading-tight">
                                <span class="font-bold text-slate-900 truncate max-w-[100px]">{{ auth()->user()->name }}</span>
                                <span class="text-[9px] text-slate-500 font-semibold">{{ auth()->user()->role->name ?? 'User' }}</span>
                            </div>
                        </div>

                        <!-- Logout Button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" title="Sign out" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="flex items-center sm:hidden">
                        <button type="button" onclick="document.getElementById('mobileMenu').classList.toggle('hidden')" class="p-2 rounded-md text-slate-500 hover:text-slate-700 hover:bg-slate-100">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Drawer -->
            <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 bg-white/95 px-4 pt-2 pb-4 space-y-1">
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">Dashboard</a>
                @if(!session('selected_project_id'))
                    <a href="{{ route('projects.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">Projects</a>
                @endif
                @if(session('selected_project_id'))
                    <a href="{{ route('inquiries.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">Inquiries</a>
                    <a href="{{ route('follow-ups.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">Follow-ups</a>
                    <a href="{{ route('brochures.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">Brochures</a>
                    <a href="{{ route('forms-qr.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">Form & QR</a>
                    <a href="{{ route('settings.whatsapp') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">WhatsApp API</a>
                @endif
                @if(auth()->user()->isAdmin())
                    @if(!session('selected_project_id'))
                        <a href="{{ route('users.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">Users</a>
                        <a href="{{ route('settings.company') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:bg-indigo-50 hover:text-indigo-600">Company Settings</a>
                    @endif
                @endif
                <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-sm font-medium text-slate-700">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">Logout</button>
                    </form>
                </div>
            </div>
        </nav>
    @else
        <!-- Guest Header -->
        <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center space-x-3">
                        @if($navCompany && $navCompany->logo)
                            <img src="{{ asset('storage/' . $navCompany->logo) }}" alt="{{ $navCompany->name }}" 
                                style="max-height: 40px; max-width: 180px; width: auto; height: auto; object-fit: contain;" 
                                class="rounded-lg shadow-xs bg-white p-1">
                        @else
                            <div class="h-10 px-3.5 rounded-xl bg-indigo-600 text-white font-extrabold text-sm flex items-center justify-center shadow-sm tracking-tight">
                                {{ $navCompany->name ?? config('app.name') }}
                            </div>
                        @endif
                        <div class="hidden sm:block leading-tight">
                            <span class="block text-sm font-bold text-slate-900">{{ $navCompany->name ?? config('app.name') }}</span>
                            <span class="text-[11px] font-semibold text-indigo-600">CRM & Property Portal</span>
                        </div>
                    </div>

                    <div>
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition-all shadow-sm">
                            <span>Sign In</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </header>
    @endauth

    <main class="flex-grow py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
            @if(session('success'))
                <div class="flex items-center p-4 bg-emerald-50/90 border border-emerald-200 text-emerald-800 rounded-xl shadow-sm backdrop-blur-md animate-fade-in" role="alert">
                    <svg class="w-5 h-5 mr-3 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium flex-grow">{{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center p-4 bg-rose-50/90 border border-rose-200 text-rose-800 rounded-xl shadow-sm backdrop-blur-md animate-fade-in" role="alert">
                    <svg class="w-5 h-5 mr-3 text-rose-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium flex-grow">{{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="p-4 bg-rose-50/90 border border-rose-200 text-rose-800 rounded-xl shadow-sm backdrop-blur-md animate-fade-in" role="alert">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span class="text-sm font-semibold">Please fix the following issues:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-1 pl-2 text-rose-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="mt-auto py-6 border-t border-slate-200/60 bg-white/50 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} PropDrip Portal. Built for Real Estate Builders & Agencies.</p>
    </footer>

    @include('components.confirmation-modal')

    <script>
    let confirmationCallback = null;

    function showConfirmationModal(title, message, callback, options = {}) {
        const modal = document.getElementById('confirmationModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalMessage = document.getElementById('modalMessage');
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmBtn = document.getElementById('confirmBtn');

        if (modalTitle) modalTitle.textContent = title;
        if (modalMessage) modalMessage.textContent = message;

        if (cancelBtn) {
            cancelBtn.textContent = options.cancelText || 'Cancel';
            cancelBtn.style.display = options.hideCancel ? 'none' : 'inline-flex';
        }
        if (confirmBtn) {
            confirmBtn.textContent = options.confirmText || 'Confirm';
            if (options.btnClass) {
                confirmBtn.className = options.btnClass;
            } else {
                confirmBtn.className = 'btn-danger';
            }
        }

        confirmationCallback = callback;

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeConfirmationModal() {
        const modal = document.getElementById('confirmationModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        confirmationCallback = null;
    }

    function hideConfirmationModal() {
        closeConfirmationModal();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const cancelBtn = document.getElementById('cancelBtn');
        const confirmBtn = document.getElementById('confirmBtn');
        const modal = document.getElementById('confirmationModal');
        
        if (cancelBtn) cancelBtn.addEventListener('click', hideConfirmationModal);
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                if (typeof confirmationCallback === 'function') confirmationCallback();
                hideConfirmationModal();
            });
        }
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) hideConfirmationModal();
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') hideConfirmationModal();
        });

        // Instant link prefetch on hover for silky smooth page transitions
        document.addEventListener('mouseover', function(e) {
            const link = e.target.closest('a');
            if (!link || !link.href || link.origin !== location.origin) return;
            if (link.href.includes('/logout') || link.href.includes('#') || link.hasAttribute('download')) return;
            if (link._prefetched) return;
            link._prefetched = true;

            const prefetch = document.createElement('link');
            prefetch.rel = 'prefetch';
            prefetch.href = link.href;
            document.head.appendChild(prefetch);
        }, { passive: true });
    });
    </script>
    @stack('scripts')
</body>
</html>
