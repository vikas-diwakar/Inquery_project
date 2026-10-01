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
                    Manage your agency branding, contact information, and lead management preferences.
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

            <!-- Section 1: Brand & Logo -->
            <div class="p-6 sm:p-8 space-y-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Brand Identity</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Your agency name and logo displayed across the dashboard, reports, and public forms.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-start">
                    <div class="space-y-2">
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Company / Agency Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                            value="{{ old('name', $company->name) }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all font-medium"
                            placeholder="e.g. Apex Realty Group">
                    </div>

                    <div class="space-y-3">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Company Logo
                        </label>
                        <div class="flex items-center gap-4">
                            @if($company->logo)
                                <div class="h-16 w-28 rounded-xl border border-slate-200 p-1.5 flex items-center justify-center bg-slate-50 overflow-hidden flex-shrink-0">
                                    <img src="{{ asset('storage/' . $company->logo) }}" alt="Logo" class="max-h-full max-w-full object-contain">
                                </div>
                            @else
                                <div class="h-16 w-28 rounded-xl border border-dashed border-slate-300 p-2 flex flex-col items-center justify-center bg-slate-50 text-slate-400 text-[10px] text-center flex-shrink-0">
                                    <span>No Logo</span>
                                </div>
                            @endif

                            <div class="space-y-1.5 flex-1">
                                <input type="file" name="logo" id="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                    class="block w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                                <p class="text-[11px] text-slate-400">PNG, JPG, WebP or SVG (Max: 2MB).</p>
                                @if($company->logo)
                                    <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 cursor-pointer pt-1">
                                        <input type="checkbox" name="remove_logo" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                                        <span>Remove current logo</span>
                                    </label>
                                @endif
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
            <div class="p-6 bg-slate-50 flex items-center justify-end gap-3 rounded-b-3xl">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
