@extends('layouts.app')

@section('title', 'WhatsApp Business Integration - ' . config('app.name', 'PropDrip'))

@section('content')
<div class="max-w-5xl mx-auto py-6 space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-600">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.983.538 1.839.814 2.791.814 3.179 0 5.765-2.587 5.765-5.766.001-3.181-2.584-5.766-5.765-5.766zm9.969 5.766c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.241l-7.181 1.883 1.916-7.003c-.879-1.488-1.386-3.226-1.386-5.08 0-5.518 4.482-10 10-10 5.518 0 10 4.482 10 10z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">WhatsApp Business Integration</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Send instant automated brochures and greetings from your own official WhatsApp business number.</p>
                </div>
            </div>
        </div>

        @if($company->whatsapp_account_status === 'connected' || !empty($company->whatsapp_connected_phone))
            <div class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0 shadow-sm">
                <span class="relative flex h-2.5 w-2.5 mr-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                </span>
                <span>Active • Connected</span>
            </div>
        @else
            <div class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 shrink-0">
                <span class="h-2 w-2 rounded-full bg-amber-400 mr-2"></span>
                <span>Not Connected</span>
            </div>
        @endif
    </div>

    <!-- Multi-Tenant Architecture Overview Box -->
    <div class="bg-gradient-to-r from-slate-900 to-indigo-950 rounded-3xl p-6 sm:p-7 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 space-y-4">
            <div class="flex items-center justify-between">
                <span class="px-3 py-1 rounded-full text-[11px] font-mono font-bold tracking-wider uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    Official Meta WhatsApp Cloud API (BSP Option 2)
                </span>
                <span class="text-xs text-slate-300 font-medium">Tenant ID: #{{ $company->id }} ({{ $company->name }})</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                    <div class="text-emerald-400 text-xs font-bold uppercase tracking-wider mb-1">1. Your Own Number</div>
                    <p class="text-xs text-slate-300 leading-relaxed">Each customer connects their own business number (+91 90000 11111). You retain 100% number ownership.</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                    <div class="text-indigo-300 text-xs font-bold uppercase tracking-wider mb-1">2. Auto Dynamic Routing</div>
                    <p class="text-xs text-slate-300 leading-relaxed">When a buyer submits an inquiry for your projects, messages are dispatched automatically from your registered number.</p>
                </div>
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                    <div class="text-cyan-300 text-xs font-bold uppercase tracking-wider mb-1">3. Direct Meta Delivery</div>
                    <p class="text-xs text-slate-300 leading-relaxed">Official Meta Cloud API delivery with zero middleman per-seat fees or third-party markup.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Onboarding Connection State -->
    @if($company->whatsapp_account_status === 'connected' || !empty($company->whatsapp_connected_phone))
        <!-- STATE A: CONNECTED -->
        <div class="bg-white rounded-3xl shadow-sm border border-emerald-200/80 overflow-hidden">
            <div class="bg-emerald-50/60 p-6 sm:p-8 border-b border-emerald-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="h-14 w-14 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md shadow-emerald-500/20 shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center space-x-2">
                            <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">Connected to WhatsApp Business</h2>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">Verified</span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                            Outbound lead brochures and automated greeting messages are broadcasting from your verified number.
                        </p>
                    </div>
                </div>

                <!-- Disconnect Action -->
                <form action="{{ route('settings.whatsapp.disconnect') }}" method="POST" onsubmit="return confirm('Are you sure you want to disconnect this WhatsApp number? Inquiries will stop receiving automated WhatsApp messages.');">
                    @csrf
                    <button type="submit" class="px-4 py-2 text-xs font-bold text-rose-600 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-200 hover:border-rose-600 rounded-xl transition duration-150 shadow-sm flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Disconnect Number</span>
                    </button>
                </form>
            </div>

            <!-- Connection Details Grid -->
            <div class="p-6 sm:p-8 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 bg-white">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Connected Number</span>
                    <div class="text-base font-extrabold text-slate-900 mt-1 flex items-center space-x-1.5">
                        <span class="font-mono text-emerald-600">{{ $company->whatsapp_connected_phone ?: 'Registered Number' }}</span>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Phone Number ID</span>
                    <div class="text-sm font-bold text-slate-800 font-mono mt-1 truncate" title="{{ $company->whatsapp_phone_number_id }}">
                        {{ $company->whatsapp_phone_number_id ?: 'Auto-Assigned' }}
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">WABA Account ID</span>
                    <div class="text-sm font-bold text-slate-800 font-mono mt-1 truncate" title="{{ $company->whatsapp_waba_id }}">
                        {{ $company->whatsapp_waba_id ?: 'Connected' }}
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Connected Since</span>
                    <div class="text-sm font-bold text-slate-800 mt-1">
                        {{ $company->whatsapp_connected_at ? $company->whatsapp_connected_at->format('M d, Y') : 'Active' }}
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- STATE B: NOT CONNECTED (1-CLICK ONBOARDING) -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="p-6 sm:p-10 space-y-6">
                <div class="max-w-2xl space-y-2">
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Connect your WhatsApp Business Account</h2>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Connect your company's WhatsApp number in under 2 minutes. We use Meta's official Embedded Signup so you can verify your number via OTP without configuring servers or webhooks.
                    </p>
                </div>

                <!-- 3-Step Wizard Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start space-x-3">
                        <div class="h-7 w-7 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0">1</div>
                        <div>
                            <div class="text-xs font-bold text-slate-900">Click Connect</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Launches the secure Meta WhatsApp Onboarding pop-up dialog.</div>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start space-x-3">
                        <div class="h-7 w-7 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0">2</div>
                        <div>
                            <div class="text-xs font-bold text-slate-900">Select / Enter Number</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Select your Meta Business profile or enter a new dedicated business phone number.</div>
                        </div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-start space-x-3">
                        <div class="h-7 w-7 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0">3</div>
                        <div>
                            <div class="text-xs font-bold text-slate-900">Verify via SMS OTP</div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Meta sends a 6-digit OTP to verify ownership. That's it!</div>
                        </div>
                    </div>
                </div>

                <!-- Main Connect Button Section -->
                <div class="pt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                    <!-- Primary Meta Embedded Signup Button -->
                    <button type="button" id="meta-connect-btn" onclick="launchMetaEmbeddedSignup()" class="px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-2xl shadow-lg shadow-emerald-600/25 transition duration-150 flex items-center justify-center space-x-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.983.538 1.839.814 2.791.814 3.179 0 5.765-2.587 5.765-5.766.001-3.181-2.584-5.766-5.765-5.766zm9.969 5.766c0 5.518-4.482 10-10 10-1.748 0-3.385-.45-4.819-1.241l-7.181 1.883 1.916-7.003c-.879-1.488-1.386-3.226-1.386-5.08 0-5.518 4.482-10 10-10 5.518 0 10 4.482 10 10z"/>
                        </svg>
                        <span id="btn-text">Connect WhatsApp Business</span>
                        <svg id="btn-spinner" class="hidden animate-spin ml-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>

                    <!-- Instant Demo Sandbox Connect Form (For instant local testing) -->
                    <form action="{{ route('settings.whatsapp.demo-connect') }}" method="POST">
                        @csrf
                        <input type="hidden" name="demo_phone" value="+91 90000 {{ str_pad($company->id, 5, '0', STR_PAD_LEFT) }}">
                        <button type="submit" class="w-full sm:w-auto px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-2xl border border-slate-300 transition duration-150 flex items-center justify-center space-x-2" title="Connect simulated business number instantly without waiting for Meta live approval">
                            <span>🧪 Quick Demo Connect (+91 90000 {{ str_pad($company->id, 5, '0', STR_PAD_LEFT) }})</span>
                        </button>
                    </form>
                </div>

                <!-- Meta App configuration note -->
                @if(empty($metaAppId))
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start space-x-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="space-y-1">
                            <span class="font-bold">Meta Cloud API Production Credentials:</span>
                            <p class="text-amber-800">
                                To activate the live Facebook dialog for all tenants, set <code class="font-mono bg-amber-100 px-1 py-0.5 rounded">META_APP_ID</code> and <code class="font-mono bg-amber-100 px-1 py-0.5 rounded">META_WHATSAPP_CONFIG_ID</code> in your SaaS <code class="font-mono bg-amber-100 px-1 py-0.5 rounded">.env</code>. You can also use the <strong>Quick Demo Connect</strong> button above to test the multi-tenant routing immediately.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Template & Automation Configuration Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">Automated Instant Brochure & Greeting</h2>
                <p class="text-xs sm:text-sm text-slate-500">Configure what message is sent automatically when a prospective customer scans a QR code or submits a lead form.</p>
            </div>
            <div class="h-10 w-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
        </div>

        <form action="{{ route('settings.whatsapp.update') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Auto-Send Toggle Switch -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                <div class="space-y-0.5">
                    <label for="whatsapp_auto_send" class="text-sm font-bold text-slate-900 cursor-pointer">Auto-Send WhatsApp Brochure on Lead Capture</label>
                    <p class="text-xs text-slate-500">Instantly trigger the WhatsApp greeting & brochure download link as soon as an inquiry is captured.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="whatsapp_auto_send" id="whatsapp_auto_send" value="1" {{ old('whatsapp_auto_send', $company->whatsapp_auto_send) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
            </div>

            <!-- Template Editor -->
            <div class="space-y-2 pt-1">
                <div class="flex items-center justify-between">
                    <label for="whatsapp_welcome_template" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Welcome Message & Brochure Template</label>
                    <span class="text-xs text-indigo-600 font-semibold">Live Merge Tags</span>
                </div>
                <textarea name="whatsapp_welcome_template" id="whatsapp_welcome_template" rows="7" class="input-field font-mono text-xs leading-relaxed" placeholder="Write greeting template...">{{ old('whatsapp_welcome_template', $company->whatsapp_welcome_template ?? $defaultTemplate) }}</textarea>
                
                <!-- Merge Tag Pills -->
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="text-[11px] font-mono bg-slate-100 border border-slate-200 px-2 py-1 rounded text-slate-700 cursor-pointer hover:bg-slate-200" onclick="insertTag('{customer_name}')">{customer_name}</span>
                    <span class="text-[11px] font-mono bg-slate-100 border border-slate-200 px-2 py-1 rounded text-slate-700 cursor-pointer hover:bg-slate-200" onclick="insertTag('{project_name}')">{project_name}</span>
                    <span class="text-[11px] font-mono bg-slate-100 border border-slate-200 px-2 py-1 rounded text-slate-700 cursor-pointer hover:bg-slate-200" onclick="insertTag('{company_name}')">{company_name}</span>
                    <span class="text-[11px] font-mono bg-indigo-50 border border-indigo-200 px-2 py-1 rounded text-indigo-700 font-bold cursor-pointer hover:bg-indigo-100" onclick="insertTag('{brochure_url}')">{brochure_url}</span>
                    <span class="text-[11px] font-mono bg-slate-100 border border-slate-200 px-2 py-1 rounded text-slate-700 cursor-pointer hover:bg-slate-200" onclick="insertTag('{executive_name}')">{executive_name}</span>
                </div>
            </div>

            <!-- Hidden Provider Selection to Preserve Existing Status -->
            <input type="hidden" name="whatsapp_provider" value="{{ $company->whatsapp_provider ?? 'meta_cloud' }}">

            <!-- Save Action -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end">
                <button type="submit" class="btn-primary space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Save Template & Preferences</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Live Test Message Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-4">
        <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-900">Send Live Test WhatsApp Message</h3>
                <p class="text-xs text-slate-500">Test message formatting and instant brochure delivery link directly to your phone.</p>
            </div>
        </div>

        <form action="{{ route('settings.whatsapp.test') }}" method="POST" class="flex flex-col sm:flex-row gap-3 pt-2">
            @csrf
            <input type="text" name="test_phone" required class="input-field flex-1" placeholder="Enter recipient phone number with country code (e.g. +919876543210)" value="{{ old('test_phone', auth()->user()->phone ?? '') }}">
            <button type="submit" class="btn-secondary space-x-2 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Send Test WhatsApp</span>
            </button>
        </form>
    </div>

    <!-- Advanced / Manual Gateway Configuration (Collapsible) -->
    <details class="group bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden transition-all">
        <summary class="p-6 sm:p-8 cursor-pointer flex items-center justify-between select-none list-none">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900">Developer & Alternative Gateway Settings</h3>
                    <p class="text-xs text-slate-500">Configure custom API tokens, Twilio, UltraMsg, or Simulated mode manually.</p>
                </div>
            </div>
            <div class="text-slate-400 group-open:rotate-180 transition-transform duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </summary>

        <form action="{{ route('settings.whatsapp.update') }}" method="POST" class="p-6 sm:p-8 pt-0 space-y-6 border-t border-slate-100">
            @csrf
            @method('PUT')

            <div class="space-y-1.5 pt-4">
                <label for="provider_manual" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Gateway Provider</label>
                <select name="whatsapp_provider" id="provider_manual" class="input-field cursor-pointer font-semibold">
                    <option value="meta_cloud" {{ old('whatsapp_provider', $company->whatsapp_provider) === 'meta_cloud' ? 'selected' : '' }}>
                        🌐 Meta WhatsApp Cloud API (Recommended)
                    </option>
                    <option value="simulated" {{ old('whatsapp_provider', $company->whatsapp_provider) === 'simulated' ? 'selected' : '' }}>
                        🧪 Simulated Mode (Development Testing - No Live Delivery)
                    </option>
                    <option value="twilio" {{ old('whatsapp_provider', $company->whatsapp_provider) === 'twilio' ? 'selected' : '' }}>
                        📲 Twilio WhatsApp Gateway
                    </option>
                    <option value="ultramsg" {{ old('whatsapp_provider', $company->whatsapp_provider) === 'ultramsg' ? 'selected' : '' }}>
                        💬 UltraMsg Gateway
                    </option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">API Auth Token / Secret</label>
                    <input type="password" name="whatsapp_api_key" value="{{ old('whatsapp_api_key', $company->whatsapp_api_key) }}" class="input-field" placeholder="Bearer Token / Secret">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Phone Number ID</label>
                    <input type="text" name="whatsapp_phone_number_id" value="{{ old('whatsapp_phone_number_id', $company->whatsapp_phone_number_id) }}" class="input-field" placeholder="e.g. 10928374829">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">WABA Account ID</label>
                    <input type="text" name="whatsapp_waba_id" value="{{ old('whatsapp_waba_id', $company->whatsapp_waba_id) }}" class="input-field" placeholder="e.g. 9876543210123">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Display Business Phone</label>
                    <input type="text" name="whatsapp_connected_phone" value="{{ old('whatsapp_connected_phone', $company->whatsapp_connected_phone) }}" class="input-field" placeholder="e.g. +91 90000 11111">
                </div>
            </div>

            <input type="hidden" name="whatsapp_auto_send" value="{{ $company->whatsapp_auto_send ? '1' : '0' }}">
            <input type="hidden" name="whatsapp_welcome_template" value="{{ $company->whatsapp_welcome_template }}">

            <div class="pt-4 border-t border-slate-200 flex justify-end">
                <button type="submit" class="btn-secondary">Save Manual Credentials</button>
            </div>
        </form>
    </details>
</div>

<!-- Meta JavaScript SDK & Embedded Signup Handler -->
<script>
    const META_APP_ID = "{{ $metaAppId }}";
    const META_CONFIG_ID = "{{ $metaConfigId }}";
    const CALLBACK_URL = "{{ route('settings.whatsapp.embedded-callback') }}";
    const CSRF_TOKEN = "{{ csrf_token() }}";

    // Insert Merge Tag into template
    function insertTag(tag) {
        const textarea = document.getElementById('whatsapp_welcome_template');
        if (!textarea) return;
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + tag + text.substring(end);
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + tag.length;
    }

    // Initialize Facebook JS SDK if Meta App ID is provided
    window.fbAsyncInit = function() {
        if (!META_APP_ID) return;
        FB.init({
            appId      : META_APP_ID,
            cookie     : true,
            xfbml      : true,
            version    : 'v19.0'
        });
    };

    // Load SDK asynchronously
    (function(d, s, id){
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) {return;}
        js = d.createElement(s); js.id = id;
        js.src = "https://connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    // Listen for Meta Embedded Signup message events
    window.addEventListener('message', function(event) {
        if (event.origin !== "https://www.facebook.com" && event.origin !== "https://web.facebook.com") {
            return;
        }

        console.log('[Meta Embedded Signup] Raw postMessage from Facebook:', event.data);

        try {
            const data = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;
            if (data && data.type === 'WA_EMBEDDED_SIGNUP') {
                console.log('[Meta Embedded Signup] Event Detected:', data.event, data.data || {});
                if (data.event === 'FINISH') {
                    const { phone_number_id, waba_id } = data.data || {};
                    window.metaSessionData = { phone_number_id, waba_id };
                    console.log('✅ [Meta Embedded Signup] Onboarding Completed! Extracted IDs:', window.metaSessionData);
                } else if (data.event === 'CANCEL') {
                    console.warn('⚠️ [Meta Embedded Signup] User cancelled the setup flow:', data);
                } else if (data.event === 'ERROR') {
                    console.error('❌ [Meta Embedded Signup] Popup returned an error:', data);
                }
            }
        } catch (e) {
            console.debug('[Meta Embedded Signup] Non-JSON message ignored:', event.data);
        }
    });

    // Launch Meta Embedded Signup Pop-up
    function launchMetaEmbeddedSignup() {
        if (!META_APP_ID || !META_CONFIG_ID) {
            console.error('❌ Missing META_APP_ID or META_CONFIG_ID in .env');
            alert("Meta App ID or WhatsApp Config ID is not configured yet in .env.\n\nPlease set META_APP_ID and META_WHATSAPP_CONFIG_ID, or use the 'Quick Demo Connect' button to test the multi-tenant workflow immediately.");
            return;
        }

        const btnText = document.getElementById('btn-text');
        const spinner = document.getElementById('btn-spinner');
        btnText.innerText = "Connecting...";
        spinner.classList.remove('hidden');

        console.log('🚀 [Meta Embedded Signup] Opening FB.login popup with Config ID:', META_CONFIG_ID);

        FB.login(function(response) {
            console.log('📥 [Meta Embedded Signup] FB.login returned response:', response);

            if (response.authResponse) {
                const code = response.authResponse.code;
                const sessionInfo = window.metaSessionData || {};

                const payload = {
                    code: code,
                    waba_id: sessionInfo.waba_id || null,
                    phone_number_id: sessionInfo.phone_number_id || null,
                };

                console.log('📤 [Meta Embedded Signup] Forwarding code & session info to Laravel backend:', payload);

                // Post code and session info to SaaS backend
                fetch(CALLBACK_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => {
                    console.log('📥 [Meta Embedded Signup] Backend HTTP Status:', res.status);
                    return res.json();
                })
                .then(result => {
                    console.log('✅ [Meta Embedded Signup] Backend JSON Result:', result);
                    if (result.success) {
                        alert("🎉 WhatsApp Business Account successfully connected!");
                        window.location.reload();
                    } else {
                        console.error('❌ [Meta Embedded Signup] Backend Connection Failure:', result.message);
                        alert("Connection failed: " + (result.message || 'Unknown error'));
                        btnText.innerText = "Connect WhatsApp Business";
                        spinner.classList.add('hidden');
                    }
                })
                .catch(err => {
                    console.error('💥 [Meta Embedded Signup] Network / Server Error:', err);
                    alert("Error communicating with server: " + err.message);
                    btnText.innerText = "Connect WhatsApp Business";
                    spinner.classList.add('hidden');
                });
            } else {
                console.warn('⚠️ [Meta Embedded Signup] Login window closed or authorization not granted. Response status:', response.status);
                btnText.innerText = "Connect WhatsApp Business";
                spinner.classList.add('hidden');
            }
        }, {
            config_id: META_CONFIG_ID,
            response_type: 'code',
            override_default_response_type: true,
            extras: {
                feature: 'whatsapp_embedded_signup',
                version: 2,
                sessionInfoVersion: 2
            }
        });
    }
</script>
@endsection
