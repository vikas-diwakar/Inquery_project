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
        $hasNavLogo = $navCompany && $navCompany->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($navCompany->logo);
        $navLogoUrl = $hasNavLogo 
            ? asset('storage/' . $navCompany->logo) 
            : asset('images/propdrip-logo.png');
        $hasActiveSub = true;
        $logoRoute = route('dashboard');

        // Project Scope vs Global Scope determination
        $navSelectedProjectId = session('selected_project_id');
        // Only show project-specific inner menus if a project is selected AND we are not on global listing/admin routes
        $isProjectScope = $navSelectedProjectId 
            && !request()->routeIs('projects.index') 
            && !request()->routeIs('projects.create') 
            && !request()->routeIs('users.*') 
            && !request()->routeIs('settings.company*');

        $navActiveProject = null;
        if ($navSelectedProjectId && auth()->check()) {
            $navActiveProject = $project ?? $selectedProject ?? \App\Models\Project::where('id', $navSelectedProjectId)
                ->where('company_id', auth()->user()->company_id)
                ->first();
        }
    @endphp
    @if($hasNavLogo)
        <link rel="preload" as="image" href="{{ $navLogoUrl }}" fetchpriority="high">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-mesh-light min-h-screen flex flex-col text-slate-800 font-sans antialiased">
    @auth
        <nav class="sticky top-0 z-40 glass-nav border-b border-slate-200/80 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Left Section: Logo & Nav Links -->
                    <div class="flex items-center space-x-4 lg:space-x-6 flex-shrink-0">
                        <div class="h-9 flex items-center flex-shrink-0">
                            <a href="{{ $logoRoute }}" class="flex items-center h-full">
                                @if($hasNavLogo)
                                    <img src="{{ $navLogoUrl }}" 
                                        alt="{{ $navCompany->name ?? config('app.name') }}" 
                                        width="130" height="36"
                                        loading="eager" decoding="sync" fetchpriority="high"
                                        style="height: 36px; max-height: 36px; max-width: 150px; width: auto; object-fit: contain;" 
                                        class="rounded-lg object-contain flex-shrink-0">
                                @else
                                    <div class="h-9 px-3 rounded-xl bg-indigo-600 text-white font-extrabold text-sm flex items-center justify-center shadow-xs tracking-tight">
                                        {{ $navCompany->name ?? config('app.name') }}
                                    </div>
                                @endif
                            </a>
                        </div>

                        <!-- Desktop Navigation -->
                        <div class="hidden md:flex items-center space-x-1">
                            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} px-3 py-2 rounded-lg text-sm transition-colors duration-150">
                                Dashboard
                            </a>
                            
                            @if(!$isProjectScope)
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
                            @else
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
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('social-media.index') }}" class="{{ (request()->routeIs('social-media.*') || request()->routeIs('integrations.*')) ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium' }} px-3 py-2 rounded-lg text-sm transition-colors duration-150 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                        </svg>
                                        <span>Social Media</span>
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Right Section: Selected Project & User Profile (Desktop/Tablet) -->
                    <div class="hidden md:flex items-center space-x-3 flex-shrink-0">
                        @if($navActiveProject && $isProjectScope)
                            <div class="flex items-center bg-indigo-50/90 border border-indigo-200 text-indigo-900 text-xs font-semibold px-3 py-1.5 rounded-full shadow-2xs">
                                <span class="h-2 w-2 rounded-full bg-indigo-600 mr-2 shrink-0 animate-pulse"></span>
                                <span class="max-w-[120px] truncate font-bold" title="{{ $navActiveProject->name }}">{{ $navActiveProject->name }}</span>
                                <a href="{{ route('projects.index') }}" class="ml-2 text-[10px] text-indigo-600 hover:text-indigo-900 underline font-extrabold uppercase tracking-wider" title="Switch project">Switch</a>
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
                            <button type="submit" title="Sign out" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                            </button>
                        </form>
                    </div>

                    <!-- Mobile Menu Button (Visible on screens < md) -->
                    <div class="flex items-center md:hidden space-x-2">
                        @if($isProjectScope && $navActiveProject)
                            <div class="flex items-center bg-indigo-50 border border-indigo-100 text-indigo-700 text-[11px] font-bold px-2 py-1 rounded-lg max-w-[120px] truncate">
                                <span class="h-1.5 w-1.5 rounded-full bg-indigo-600 mr-1.5 shrink-0"></span>
                                <span class="truncate">{{ $navActiveProject->name }}</span>
                            </div>
                        @endif

                        <button type="button" 
                            id="mobileMenuToggle"
                            onclick="toggleMobileMenu()" 
                            class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                            aria-label="Toggle Navigation Menu">
                            <!-- Hamburger Icon -->
                            <svg id="hamburgerIcon" class="h-6 w-6 block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <!-- Close Icon -->
                            <svg id="closeIcon" class="h-6 w-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Enhanced Mobile Navigation Drawer -->
            <div id="mobileMenu" class="hidden md:hidden border-t border-slate-200 bg-white shadow-xl transition-all">
                <!-- User Profile Header in Mobile Drawer -->
                <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="h-9 w-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex flex-col">
                            <span class="font-bold text-slate-900 text-sm leading-tight">{{ auth()->user()->name }}</span>
                            <span class="text-xs text-indigo-600 font-semibold">{{ auth()->user()->role->name ?? 'User' }}</span>
                        </div>
                    </div>
                    @if($isProjectScope && $navActiveProject)
                        <a href="{{ route('projects.index') }}" class="text-[11px] font-semibold text-slate-600 hover:text-indigo-600 px-2 py-1 rounded border border-slate-200 bg-white shadow-2xs">
                            Switch Project
                        </a>
                    @endif
                </div>

                <!-- Navigation Links List -->
                <div class="px-3 py-3 space-y-1">
                    <a href="{{ route('dashboard') }}" 
                        class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>

                    @if(!$isProjectScope)
                        <a href="{{ route('projects.index') }}" 
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('projects.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>Projects</span>
                        </a>

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('users.index') }}" 
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <span>Users</span>
                            </a>
                            <a href="{{ route('settings.company') }}" 
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('settings.company*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Company Settings</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('inquiries.index') }}" 
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('inquiries.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Inquiries</span>
                        </a>
                        <a href="{{ route('follow-ups.index') }}" 
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('follow-ups.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span>Follow-ups</span>
                        </a>
                        <a href="{{ route('brochures.index') }}" 
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('brochures.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>Brochures</span>
                        </a>
                        <a href="{{ route('forms-qr.index') }}" 
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('forms-qr.*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            <span>Form & QR</span>
                        </a>
                        <a href="{{ route('settings.whatsapp') }}" 
                            class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ request()->routeIs('settings.whatsapp*') ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>WhatsApp API</span>
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('social-media.index') }}" 
                                class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ (request()->routeIs('social-media.*') || request()->routeIs('integrations.*')) ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}">
                                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                                </svg>
                                <span>Social Media</span>
                            </a>
                        @endif
                    @endif
                </div>

                <!-- Drawer Logout Action -->
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl text-sm font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Sign Out of Portal</span>
                        </button>
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

    <main class="flex-grow py-4 sm:py-6 lg:py-8">
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
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        const hamburger = document.getElementById('hamburgerIcon');
        const close = document.getElementById('closeIcon');
        if (!menu) return;

        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            if (hamburger) hamburger.classList.add('hidden');
            if (close) close.classList.remove('hidden');
        } else {
            menu.classList.add('hidden');
            if (hamburger) hamburger.classList.remove('hidden');
            if (close) close.classList.add('hidden');
        }
    }

    let confirmationCallback = null;

    // ─── Icon / colour presets ────────────────────────────────────────────────
    const _modalPresets = {
        danger:  { wrap: 'bg-rose-50',   icon: 'text-rose-600',   id: 'modalIconDanger'  },
        info:    { wrap: 'bg-blue-50',    icon: 'text-blue-600',   id: 'modalIconInfo'    },
        success: { wrap: 'bg-emerald-50', icon: 'text-emerald-600',id: 'modalIconSuccess' },
        warning: { wrap: 'bg-amber-50',   icon: 'text-amber-600',  id: 'modalIconWarning' },
    };

    function _applyModalType(type) {
        const preset = _modalPresets[type] || _modalPresets.danger;
        const wrap   = document.getElementById('modalIconWrap');
        if (wrap) {
            wrap.className = 'h-11 w-11 rounded-2xl flex items-center justify-center shrink-0 ' + preset.wrap;
            ['modalIconDanger','modalIconInfo','modalIconSuccess','modalIconWarning'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.className = 'w-5 h-5' + (id === preset.id ? ' ' + preset.icon : ' hidden');
                }
            });
        }
    }

    function _openModal() {
        const modal = document.getElementById('confirmationModal');
        const inner = document.getElementById('confirmationModalInner');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            if (inner) { inner.classList.remove('scale-95','opacity-0'); inner.classList.add('scale-100','opacity-100'); }
        });
    }

    function _closeModal() {
        const modal = document.getElementById('confirmationModal');
        const inner = document.getElementById('confirmationModalInner');
        if (!modal) return;
        if (inner) { inner.classList.remove('scale-100','opacity-100'); inner.classList.add('scale-95','opacity-0'); }
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 150);
        confirmationCallback = null;
    }

    /**
     * showConfirmationModal(title, message, callback, options)
     * options: { confirmText, cancelText, hideCancel, btnClass, type: 'danger'|'info'|'success'|'warning' }
     */
    function showConfirmationModal(title, message, callback, options = {}) {
        const modalTitle  = document.getElementById('modalTitle');
        const modalMsg    = document.getElementById('modalMessage');
        const cancelBtn   = document.getElementById('cancelBtn');
        const confirmBtn  = document.getElementById('confirmBtn');

        if (modalTitle) modalTitle.textContent = title;
        if (modalMsg)   modalMsg.textContent   = message;

        _applyModalType(options.type || 'danger');

        if (cancelBtn) {
            cancelBtn.textContent = options.cancelText || 'Cancel';
            cancelBtn.style.display = options.hideCancel ? 'none' : '';
        }
        if (confirmBtn) {
            confirmBtn.textContent = options.confirmText || 'Confirm';
            confirmBtn.className = options.btnClass ||
                'px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-colors shadow-sm';
        }

        confirmationCallback = callback;
        _openModal();
    }

    /**
     * showAlert(title, message, options)
     * options: { type: 'info'|'success'|'warning'|'danger', okText }
     * Replaces native alert() with a polished modal.
     */
    function showAlert(title, message, options = {}) {
        const type = options.type || 'info';
        const confirmBtn = document.getElementById('confirmBtn');
        if (confirmBtn) {
            const colours = {
                danger:  'px-4 py-2 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl transition-colors shadow-sm',
                info:    'px-4 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors shadow-sm',
                success: 'px-4 py-2 text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl transition-colors shadow-sm',
                warning: 'px-4 py-2 text-sm font-bold text-white bg-amber-500 hover:bg-amber-600 rounded-xl transition-colors shadow-sm',
            };
            confirmBtn.className = colours[type] || colours.info;
        }
        showConfirmationModal(title, message, null, {
            type,
            confirmText: options.okText || 'OK',
            hideCancel: true,
        });
    }

    function closeConfirmationModal() { _closeModal(); }
    function hideConfirmationModal()  { _closeModal(); }

    document.addEventListener('DOMContentLoaded', function() {
        const cancelBtn  = document.getElementById('cancelBtn');
        const confirmBtn = document.getElementById('confirmBtn');
        const modal      = document.getElementById('confirmationModal');

        if (cancelBtn)  cancelBtn.addEventListener('click', _closeModal);
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                if (typeof confirmationCallback === 'function') confirmationCallback();
                _closeModal();
            });
        }
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) _closeModal();
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') _closeModal();
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

        // Global toggle password visibility helper
        window.togglePasswordVisibility = function(inputId, btn) {
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
        };
    });
    </script>
    @stack('scripts')
</body>
</html>
