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
                <span>Active &bull; Connected</span>
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
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <span class="px-3 py-1 rounded-full text-[11px] font-mono font-bold tracking-wider uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 w-fit">
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
        <!-- STATE B: NOT CONNECTED — Manual Credentials Form -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center space-x-3">
                <div class="h-10 w-10 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Connect WhatsApp Business</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Enter your Meta WhatsApp Cloud API credentials to activate messaging.</p>
                </div>
            </div>

            <form action="{{ route('settings.whatsapp.update') }}" method="POST" class="p-6 sm:p-8 space-y-6">
                @csrf
                @method('PUT')

                <!-- Info Note -->
                <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200/80 text-xs text-blue-900 flex items-start space-x-3">
                    <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    <span><strong>Where to find these?</strong> Log in to <strong>developers.facebook.com</strong> &rarr; your App &rarr; WhatsApp &rarr; API Setup. Copy the <em>Phone Number ID</em>, <em>WhatsApp Business Account ID (WABA ID)</em>, and generate a <em>Permanent System User Access Token</em>.</span>
                </div>

                <!-- Gateway Provider -->
                <div class="space-y-1.5">
                    <label for="provider_select" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Gateway Provider</label>
                    <select name="whatsapp_provider" id="provider_select" class="input-field cursor-pointer font-semibold">
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

                <!-- Credentials Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">API Access Token <span class="text-rose-500">*</span></label>
                        <input type="password" name="whatsapp_api_key" value="{{ old('whatsapp_api_key', $company->whatsapp_api_key) }}" class="input-field" placeholder="EAAxxxxxxxxxxxxxxxx..." autocomplete="off">
                        <p class="text-[11px] text-slate-400">Permanent System User Access Token from Meta App Dashboard.</p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Phone Number ID <span class="text-rose-500">*</span></label>
                        <input type="text" name="whatsapp_phone_number_id" value="{{ old('whatsapp_phone_number_id', $company->whatsapp_phone_number_id) }}" class="input-field" placeholder="e.g. 1321289067731598">
                        <p class="text-[11px] text-slate-400">Found under WhatsApp &rarr; API Setup in your Meta App.</p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">WABA Account ID <span class="text-rose-500">*</span></label>
                        <input type="text" name="whatsapp_waba_id" value="{{ old('whatsapp_waba_id', $company->whatsapp_waba_id) }}" class="input-field" placeholder="e.g. 2556681498088580">
                        <p class="text-[11px] text-slate-400">WhatsApp Business Account ID from Meta Business Manager.</p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Display Business Phone <span class="text-rose-500">*</span></label>
                        <input type="text" name="whatsapp_connected_phone" value="{{ old('whatsapp_connected_phone', $company->whatsapp_connected_phone) }}" class="input-field" placeholder="e.g. +91 84600 41855">
                        <p class="text-[11px] text-slate-400">The actual registered WhatsApp phone number (for display).</p>
                    </div>
                </div>

                <!-- Hidden fields to preserve other settings -->
                <input type="hidden" name="whatsapp_auto_send" value="{{ $company->whatsapp_auto_send ? '1' : '0' }}">
                <input type="hidden" name="whatsapp_welcome_template" value="{{ $company->whatsapp_welcome_template }}">

                <div class="pt-2 border-t border-slate-200 flex items-center justify-end">
                    <button type="submit" class="btn-primary space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        <span>Save &amp; Connect WhatsApp</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Template & Automation Configuration Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-6 sm:p-8 border-b border-slate-100 flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">Automated Instant Brochure &amp; Greeting</h2>
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
                    <p class="text-xs text-slate-500">Instantly trigger the WhatsApp greeting &amp; brochure download link as soon as an inquiry is captured.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="whatsapp_auto_send" id="whatsapp_auto_send" value="1" {{ old('whatsapp_auto_send', $company->whatsapp_auto_send) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
            </div>

            <!-- Template Editor -->
            <div class="space-y-2 pt-1">
                <div class="flex items-center justify-between">
                    <label for="whatsapp_welcome_template" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Welcome Message &amp; Brochure Template</label>
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
                    <span>Save Template &amp; Preferences</span>
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
</div>

<script>
    // Insert Merge Tag into template textarea
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
</script>
@endsection
