@extends('layouts.app')

@section('title', 'Company Profile & Settings')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 py-6">
    <!-- Breadcrumb & Header -->
    <div class="space-y-2">
        <div class="flex items-center space-x-2 text-xs text-slate-500 font-medium">
            <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-slate-400">Settings</span>
            <span>/</span>
            <span class="text-indigo-600 font-semibold">Company Profile</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="p-2.5 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </span>
                    Company & Agency Profile
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Manage your agency branding, logo identity, contact information, and lead management preferences.
                </p>
            </div>

            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Self-Hosted & Active</span>
            </div>
        </div>
    </div>

    <!-- Session Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-medium text-emerald-800 flex items-center gap-2.5 shadow-sm">
            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl text-xs font-medium text-rose-800 space-y-1 shadow-sm">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Profile Form Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <form action="{{ route('settings.company.update') }}" method="POST" enctype="multipart/form-data" class="divide-y divide-slate-100">
            @csrf
            @method('PUT')

            @php
                $hasStoredLogo = $company->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($company->logo);
                $storedLogoUrl = $hasStoredLogo ? asset('storage/' . $company->logo) : null;
            @endphp

            <!-- Section 1: Brand & Logo -->
            <div class="p-6 sm:p-8 space-y-8">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Brand Identity</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Your agency name and logo displayed across the dashboard, top navigation, reports, brochures, and public customer inquiry forms.</p>
                </div>

                <!-- Company Name Field -->
                <div class="max-w-xl space-y-2">
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Company / Agency Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required
                        value="{{ old('name', $company->name) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all font-medium"
                        placeholder="e.g. Apex Realty Group"
                        oninput="updateNavPreviewName(this.value)">
                    <p class="text-[11px] text-slate-400">Displayed in portal headers, brochures, emails, and WhatsApp greetings.</p>
                </div>

                <!-- Company Logo Management -->
                <div class="space-y-4 pt-2 border-t border-slate-100">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                                Company Logo
                            </label>
                            <p class="text-xs text-slate-500 mt-0.5">Upload a transparent PNG, SVG, or high-res JPG for your company branding.</p>
                        </div>
                        <div id="logo-badge-container">
                            @if($hasStoredLogo)
                                <span id="logo-status-badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Logo Active</span>
                                </span>
                            @else
                                <span id="logo-status-badge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                    <span>No Logo Set</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- 2-Column Responsive Layout: Left = Proper Preview + Navbar Demo; Right = Upload Dropzone & Controls -->
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        
                        <!-- Left Column: Proper Logo Preview Canvas & Navbar Simulation (7 cols) -->
                        <div class="lg:col-span-7 space-y-4">
                            <!-- Preview Canvas Card -->
                            <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-4 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Logo Showcase Preview
                                    </span>

                                    <!-- Canvas Background Switcher (Light / Checkerboard / Dark) -->
                                    <div class="flex items-center gap-1 bg-white p-1 rounded-lg border border-slate-200 text-[11px] font-medium text-slate-600">
                                        <span class="text-[10px] text-slate-400 px-1">Canvas:</span>
                                        <button type="button" onclick="setCanvasBg('light')" id="btn-bg-light" title="Light Background" class="px-2 py-0.5 rounded text-xs transition-all bg-indigo-50 text-indigo-700 font-semibold">Light</button>
                                        <button type="button" onclick="setCanvasBg('checker')" id="btn-bg-checker" title="Transparency Checkerboard" class="px-2 py-0.5 rounded text-xs transition-all hover:bg-slate-100 text-slate-600">Grid</button>
                                        <button type="button" onclick="setCanvasBg('dark')" id="btn-bg-dark" title="Dark Background" class="px-2 py-0.5 rounded text-xs transition-all hover:bg-slate-100 text-slate-600">Dark</button>
                                    </div>
                                </div>

                                <!-- The Main Logo Canvas Box -->
                                <div id="preview-canvas-box" class="h-44 sm:h-48 w-full rounded-xl border border-slate-200 bg-white flex items-center justify-center p-4 relative overflow-hidden transition-all shadow-inner">
                                    <!-- Stored or Newly Selected Image -->
                                    <img id="main-logo-preview" 
                                        src="{{ $storedLogoUrl ?? '' }}" 
                                        alt="Logo Preview" 
                                        style="max-height: 120px; max-width: 90%; width: auto; height: auto; object-fit: contain;"
                                        class="{{ $hasStoredLogo ? '' : 'hidden' }} transition-all duration-200 drop-shadow-xs">

                                    <!-- No Logo Empty State -->
                                    <div id="main-logo-empty" class="{{ $hasStoredLogo ? 'hidden' : 'flex' }} flex-col items-center justify-center text-center p-4">
                                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-500 mb-2">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-700">No Custom Logo Uploaded</span>
                                        <span class="text-[11px] text-slate-400 mt-0.5">Use the upload box on the right to add your agency logo</span>
                                    </div>

                                    <!-- Removal Overlay Indicator -->
                                    <div id="removal-overlay" class="hidden absolute inset-0 bg-rose-950/70 backdrop-blur-xs flex-col items-center justify-center text-white text-center p-4 transition-all">
                                        <svg class="w-7 h-7 text-rose-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span class="text-xs font-bold text-white">Logo Marked for Removal</span>
                                        <span class="text-[11px] text-rose-200 mt-0.5">Will be removed once you click "Save Changes"</span>
                                        <button type="button" onclick="cancelRemoval()" class="mt-2.5 px-3 py-1 bg-white hover:bg-rose-50 text-rose-700 rounded-lg text-xs font-bold shadow-xs transition-all">Undo Removal</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Live Top Navbar Context Preview -->
                            <div class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-3.5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Live Header Preview</span>
                                    <span class="text-[10px] text-slate-400">How it appears on top navigation bar</span>
                                </div>

                                <!-- Simulated Top Navigation Bar -->
                                <div class="bg-white border border-slate-200/90 rounded-xl px-4 py-2 flex items-center justify-between shadow-xs overflow-hidden">
                                    <div class="flex items-center space-x-3">
                                        <!-- Navbar Logo Container -->
                                        <div class="h-8 flex items-center justify-start flex-shrink-0">
                                            <img id="nav-preview-logo" 
                                                src="{{ $storedLogoUrl ?? '' }}" 
                                                alt="Nav Logo" 
                                                style="max-height: 32px; max-width: 130px; width: auto; height: auto; object-fit: contain;"
                                                class="{{ $hasStoredLogo ? '' : 'hidden' }} rounded">
                                            
                                            <div id="nav-preview-text" class="{{ $hasStoredLogo ? 'hidden' : 'flex' }} items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-600 text-white font-bold text-xs">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                <span id="nav-preview-name">{{ $company->name ?? 'PropDrip' }}</span>
                                            </div>
                                        </div>

                                        <!-- Mock Nav Items -->
                                        <div class="hidden sm:flex items-center space-x-2 text-[11px] font-medium text-slate-400 pl-2 border-l border-slate-100">
                                            <span class="text-indigo-600 font-semibold">Dashboard</span>
                                            <span>Projects</span>
                                            <span>Inquiries</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 border border-indigo-200 flex items-center justify-center text-[10px] font-bold text-indigo-700">A</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Always-Visible Upload Controls & Dropzone (5 cols) -->
                        <div class="lg:col-span-5 space-y-4">
                            
                            <!-- Interactive Upload Dropzone Box -->
                            <div id="logo-dropzone" 
                                onclick="document.getElementById('logo-file-input').click()"
                                class="border-2 border-dashed border-indigo-200 hover:border-indigo-500 bg-indigo-50/20 hover:bg-indigo-50/50 rounded-2xl p-5 text-center cursor-pointer transition-all flex flex-col items-center justify-center group relative">
                                
                                <div class="w-12 h-12 rounded-2xl bg-white border border-indigo-100 shadow-xs flex items-center justify-center text-indigo-600 group-hover:scale-105 transition-transform mb-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                </div>

                                <div class="space-y-1">
                                    <p class="text-xs font-bold text-slate-800">
                                        <span class="text-indigo-600 hover:underline">Click to upload logo</span> or drag & drop
                                    </p>
                                    <p class="text-[11px] text-slate-500">PNG, JPG, WebP, or SVG (Max 2MB)</p>
                                    <p class="text-[10px] text-slate-400">Recommended: Transparent background (PNG/SVG)</p>
                                </div>

                                <!-- Hidden Native File Input -->
                                <input type="file" 
                                    name="logo" 
                                    id="logo-file-input" 
                                    accept="image/png,image/jpeg,image/webp,image/svg+xml" 
                                    class="hidden" 
                                    onchange="handleLogoSelection(this)">
                            </div>

                            <!-- Dedicated Action Buttons (Always Visible) -->
                            <div class="flex flex-wrap items-center gap-2">
                                <!-- Browse Button -->
                                <button type="button" 
                                    onclick="document.getElementById('logo-file-input').click()" 
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm shadow-indigo-600/20 transition-all cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                    </svg>
                                    <span id="btn-browse-text">{{ $hasStoredLogo ? 'Upload / Change Logo' : 'Upload New Logo' }}</span>
                                </button>

                                <!-- Remove Logo Button (if company has stored logo) -->
                                @if($hasStoredLogo)
                                    <button type="button" 
                                        id="btn-remove-logo"
                                        onclick="toggleRemoveLogo()" 
                                        class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold transition-all cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        <span>Remove</span>
                                    </button>
                                    <!-- Hidden Checkbox for backend remove_logo flag -->
                                    <input type="checkbox" name="remove_logo" id="remove_logo_input" value="1" class="hidden">
                                @endif
                            </div>

                            <!-- Selected File Banner (Appears when a new file is chosen) -->
                            <div id="selected-file-card" class="hidden rounded-xl border border-emerald-200 bg-emerald-50/80 p-3.5 text-xs text-emerald-900 space-y-2">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex items-center gap-2 overflow-hidden">
                                        <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 font-bold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        <div class="truncate">
                                            <p id="selected-file-name" class="font-bold truncate text-emerald-900">logo.png</p>
                                            <p id="selected-file-size" class="text-[10px] text-emerald-700">120 KB • Image ready</p>
                                        </div>
                                    </div>
                                    <button type="button" onclick="cancelSelectedLogo()" title="Cancel selection" class="text-emerald-700 hover:text-emerald-900 font-bold text-base p-1 leading-none shrink-0">&times;</button>
                                </div>
                                <div class="pt-1 border-t border-emerald-200/60 flex items-center justify-between text-[11px]">
                                    <span class="text-emerald-700">Preview active in canvas</span>
                                    <span class="font-semibold text-emerald-800">Click "Save Changes" below to apply</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Contact Details -->
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Contact & Location</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Official contact information shown on brochures, emails, and inquiry landing pages.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Support / Inquiries Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" required
                            value="{{ old('email', $company->email) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all font-medium"
                            placeholder="sales@yourdomain.com">
                    </div>

                    <div class="space-y-2">
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Official Phone Number
                        </label>
                        <input type="text" name="phone" id="phone"
                            value="{{ old('phone', $company->phone) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all font-medium"
                            placeholder="+1 (555) 123-4567">
                    </div>

                    <div class="sm:col-span-2 space-y-2">
                        <label for="address" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Office / Agency Address
                        </label>
                        <textarea name="address" id="address" rows="2"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all font-medium"
                            placeholder="123 Real Estate Blvd, Suite 400, New York, NY">{{ old('address', $company->address) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3: CRM Operations & Lead Allocation -->
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Lead Allocation & Automation</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Control how new incoming leads and inquiries are distributed to your team.</p>
                </div>

                <div class="space-y-4">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Lead Distribution Strategy
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-start p-4 rounded-2xl border cursor-pointer transition-all {{ old('lead_allocation_method', $company->lead_allocation_method ?? 'manual') === 'manual' ? 'border-indigo-500 bg-indigo-50/30 ring-1 ring-indigo-500' : 'border-slate-200 hover:border-slate-300' }}">
                            <input type="radio" name="lead_allocation_method" value="manual" class="mt-1 text-indigo-600 focus:ring-indigo-500" {{ old('lead_allocation_method', $company->lead_allocation_method ?? 'manual') === 'manual' ? 'checked' : '' }}>
                            <div class="ml-3">
                                <span class="block text-sm font-bold text-slate-800">Manual Assignment</span>
                                <span class="block text-xs text-slate-500 mt-0.5">New inquiries arrive in the unassigned pool. Admins or managers manually assign them to sales agents.</span>
                            </div>
                        </label>

                        <label class="relative flex items-start p-4 rounded-2xl border cursor-pointer transition-all {{ old('lead_allocation_method', $company->lead_allocation_method) === 'round_robin' ? 'border-indigo-500 bg-indigo-50/30 ring-1 ring-indigo-500' : 'border-slate-200 hover:border-slate-300' }}">
                            <input type="radio" name="lead_allocation_method" value="round_robin" class="mt-1 text-indigo-600 focus:ring-indigo-500" {{ old('lead_allocation_method', $company->lead_allocation_method) === 'round_robin' ? 'checked' : '' }}>
                            <div class="ml-3">
                                <span class="block text-sm font-bold text-slate-800">Auto Round-Robin</span>
                                <span class="block text-xs text-slate-500 mt-0.5">Automatically assigns new leads equally in sequential order among all active sales agents.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="space-y-2 pt-2">
                    <label for="whatsapp_welcome_template" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Default WhatsApp Welcome Template
                    </label>
                    <textarea name="whatsapp_welcome_template" id="whatsapp_welcome_template" rows="4"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all"
                        placeholder="Hello {customer_name}! Thank you for inquiring about {project_name}.">{{ old('whatsapp_welcome_template', $company->getDefaultWhatsAppTemplate()) }}</textarea>
                    <p class="text-[11px] text-slate-500">
                        Available variables: <code class="text-indigo-600">{customer_name}</code>, <code class="text-indigo-600">{project_name}</code>, <code class="text-indigo-600">{company_name}</code>, <code class="text-indigo-600">{brochure_url}</code>, <code class="text-indigo-600">{executive_name}</code>
                    </p>
                </div>
            </div>

            <!-- Footer Save Action -->
            <div class="p-4 sm:p-6 bg-slate-50 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 rounded-b-3xl">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .transparency-grid {
        background-color: #f8fafc !important;
        background-image: 
            linear-gradient(45deg, #e2e8f0 25%, transparent 25%), 
            linear-gradient(-45deg, #e2e8f0 25%, transparent 25%), 
            linear-gradient(45deg, transparent 75%, #e2e8f0 75%), 
            linear-gradient(-45deg, transparent 75%, #e2e8f0 75%) !important;
        background-size: 16px 16px !important;
        background-position: 0 0, 0 8px, 8px -8px, -8px 0px !important;
    }
</style>

<script>
    const originalLogoSrc = @json($storedLogoUrl ?? '');
    const hasOriginalLogo = Boolean(originalLogoSrc);

    // Setup Drag and Drop onto upload dropzone
    document.addEventListener('DOMContentLoaded', () => {
        const dropzone = document.getElementById('logo-dropzone');
        const fileInput = document.getElementById('logo-file-input');

        if (dropzone && fileInput) {
            ['dragenter', 'dragover'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('border-indigo-600', 'bg-indigo-50/70', 'scale-[1.01]');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropzone.addEventListener(eventName, (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('border-indigo-600', 'bg-indigo-50/70', 'scale-[1.01]');
                }, false);
            });

            dropzone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                if (dt && dt.files && dt.files.length) {
                    fileInput.files = dt.files;
                    handleLogoSelection(fileInput);
                }
            }, false);
        }
    });

    // Handle File Selection (from input or dropzone)
    function handleLogoSelection(input) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];

        // Validate max size 2MB (2048 KB)
        if (file.size > 2 * 1024 * 1024) {
            alert('The selected logo file exceeds the 2MB size limit. Please choose a smaller image.');
            input.value = '';
            return;
        }

        const mainPreview = document.getElementById('main-logo-preview');
        const mainEmpty = document.getElementById('main-logo-empty');
        const removalOverlay = document.getElementById('removal-overlay');
        const navLogo = document.getElementById('nav-preview-logo');
        const navText = document.getElementById('nav-preview-text');
        const fileCard = document.getElementById('selected-file-card');
        const fileNameEl = document.getElementById('selected-file-name');
        const fileSizeEl = document.getElementById('selected-file-size');
        const btnBrowseText = document.getElementById('btn-browse-text');
        const removeCheckbox = document.getElementById('remove_logo_input');
        const badgeContainer = document.getElementById('logo-badge-container');

        // Reset removal checkbox if active
        if (removeCheckbox) {
            removeCheckbox.checked = false;
        }
        if (removalOverlay) {
            removalOverlay.classList.add('hidden');
            removalOverlay.classList.remove('flex');
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const dataUrl = e.target.result;

            // Update Main Showcase Canvas
            if (mainPreview) {
                mainPreview.src = dataUrl;
                mainPreview.classList.remove('hidden', 'opacity-30');
            }
            if (mainEmpty) {
                mainEmpty.classList.add('hidden');
                mainEmpty.classList.remove('flex');
            }

            // Update Real-Time Navbar Preview
            if (navLogo) {
                navLogo.src = dataUrl;
                navLogo.classList.remove('hidden');
            }
            if (navText) {
                navText.classList.add('hidden');
                navText.classList.remove('flex');
            }
        };
        reader.readAsDataURL(file);

        // Update Selected File Card Details
        if (fileCard && fileNameEl && fileSizeEl) {
            fileNameEl.textContent = file.name;
            const sizeInKb = (file.size / 1024).toFixed(0);
            fileSizeEl.textContent = sizeInKb + ' KB • Preview ready to save';
            fileCard.classList.remove('hidden');
        }

        if (btnBrowseText) {
            btnBrowseText.textContent = 'Choose Different Image';
        }

        // Update status badge
        if (badgeContainer) {
            badgeContainer.innerHTML = `
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>New Logo Preview (Unsaved)</span>
                </span>
            `;
        }
    }

    // Cancel / Revert Selected Logo
    function cancelSelectedLogo() {
        const input = document.getElementById('logo-file-input');
        const mainPreview = document.getElementById('main-logo-preview');
        const mainEmpty = document.getElementById('main-logo-empty');
        const navLogo = document.getElementById('nav-preview-logo');
        const navText = document.getElementById('nav-preview-text');
        const fileCard = document.getElementById('selected-file-card');
        const btnBrowseText = document.getElementById('btn-browse-text');
        const badgeContainer = document.getElementById('logo-badge-container');

        if (input) input.value = '';

        if (fileCard) {
            fileCard.classList.add('hidden');
        }

        if (hasOriginalLogo) {
            // Restore Original Logo
            if (mainPreview) {
                mainPreview.src = originalLogoSrc;
                mainPreview.classList.remove('hidden', 'opacity-30');
            }
            if (mainEmpty) {
                mainEmpty.classList.add('hidden');
                mainEmpty.classList.remove('flex');
            }
            if (navLogo) {
                navLogo.src = originalLogoSrc;
                navLogo.classList.remove('hidden');
            }
            if (navText) {
                navText.classList.add('hidden');
                navText.classList.remove('flex');
            }
            if (btnBrowseText) {
                btnBrowseText.textContent = 'Upload / Change Logo';
            }
            if (badgeContainer) {
                badgeContainer.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Logo Active</span>
                    </span>
                `;
            }
        } else {
            // Revert to Empty State
            if (mainPreview) {
                mainPreview.src = '';
                mainPreview.classList.add('hidden');
            }
            if (mainEmpty) {
                mainEmpty.classList.remove('hidden');
                mainEmpty.classList.add('flex');
            }
            if (navLogo) {
                navLogo.src = '';
                navLogo.classList.add('hidden');
            }
            if (navText) {
                navText.classList.remove('hidden');
                navText.classList.add('flex');
            }
            if (btnBrowseText) {
                btnBrowseText.textContent = 'Upload New Logo';
            }
            if (badgeContainer) {
                badgeContainer.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600 border border-slate-200">
                        <span>No Logo Set</span>
                    </span>
                `;
            }
        }
    }

    // Toggle Remove Logo
    function toggleRemoveLogo() {
        const checkbox = document.getElementById('remove_logo_input');
        if (!checkbox) return;

        checkbox.checked = !checkbox.checked;

        const mainPreview = document.getElementById('main-logo-preview');
        const removalOverlay = document.getElementById('removal-overlay');
        const badgeContainer = document.getElementById('logo-badge-container');
        const input = document.getElementById('logo-file-input');
        const fileCard = document.getElementById('selected-file-card');

        // Cancel any pending un-saved file selection first
        if (input) input.value = '';
        if (fileCard) fileCard.classList.add('hidden');

        if (checkbox.checked) {
            if (removalOverlay) {
                removalOverlay.classList.remove('hidden');
                removalOverlay.classList.add('flex');
            }
            if (mainPreview) {
                mainPreview.classList.add('opacity-30');
            }
            if (badgeContainer) {
                badgeContainer.innerHTML = `
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Marked for Removal</span>
                    </span>
                `;
            }
        } else {
            cancelRemoval();
        }
    }

    // Cancel Removal
    function cancelRemoval() {
        const checkbox = document.getElementById('remove_logo_input');
        const removalOverlay = document.getElementById('removal-overlay');
        const mainPreview = document.getElementById('main-logo-preview');
        const badgeContainer = document.getElementById('logo-badge-container');

        if (checkbox) checkbox.checked = false;
        if (removalOverlay) {
            removalOverlay.classList.add('hidden');
            removalOverlay.classList.remove('flex');
        }
        if (mainPreview) {
            mainPreview.classList.remove('opacity-30');
        }
        if (badgeContainer) {
            badgeContainer.innerHTML = `
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Logo Active</span>
                </span>
            `;
        }
    }

    // Canvas Background Switcher
    function setCanvasBg(mode) {
        const canvas = document.getElementById('preview-canvas-box');
        const btnLight = document.getElementById('btn-bg-light');
        const btnChecker = document.getElementById('btn-bg-checker');
        const btnDark = document.getElementById('btn-bg-dark');

        if (!canvas) return;

        // Reset classes
        canvas.classList.remove('bg-white', 'transparency-grid', 'bg-slate-900');
        [btnLight, btnChecker, btnDark].forEach(btn => {
            if (btn) {
                btn.className = 'px-2 py-0.5 rounded text-xs transition-all hover:bg-slate-100 text-slate-600';
            }
        });

        if (mode === 'checker') {
            canvas.classList.add('transparency-grid');
            if (btnChecker) btnChecker.className = 'px-2 py-0.5 rounded text-xs transition-all bg-indigo-50 text-indigo-700 font-semibold';
        } else if (mode === 'dark') {
            canvas.classList.add('bg-slate-900');
            if (btnDark) btnDark.className = 'px-2 py-0.5 rounded text-xs transition-all bg-indigo-50 text-indigo-700 font-semibold';
        } else {
            canvas.classList.add('bg-white');
            if (btnLight) btnLight.className = 'px-2 py-0.5 rounded text-xs transition-all bg-indigo-50 text-indigo-700 font-semibold';
        }
    }

    // Live update company name in mock navigation preview
    function updateNavPreviewName(val) {
        const navName = document.getElementById('nav-preview-name');
        if (navName) {
            navName.textContent = val.trim() || 'Your Agency';
        }
    }
</script>
@endsection