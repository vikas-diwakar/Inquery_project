@extends('layouts.app')

@section('title', 'WhatsApp Integration Settings - PropDrip')

@section('content')
<div class="max-w-4xl mx-auto py-4 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">WhatsApp Business API Settings</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Configure automated instant brochure delivery to prospective buyers upon QR code scan or lead submission.</p>
        </div>
        <div class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
            <span class="h-2 w-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
            <span>WhatsApp Automation Enabled</span>
        </div>
    </div>

    <!-- Main Settings Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <!-- Banner -->
        <div class="bg-slate-900 p-6 sm:p-8 text-white flex items-center justify-between relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-emerald-500/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="space-y-1 relative z-10">
                <h2 class="text-lg sm:text-xl font-extrabold">Instant Brochure Delivery Setup</h2>
                <p class="text-xs sm:text-sm text-slate-300">Choose your WhatsApp provider and customize the automated greeting template sent to buyers.</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-emerald-600/30 border border-emerald-400/30 flex items-center justify-center text-emerald-300 relative z-10 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
        </div>

        <form action="{{ route('settings.whatsapp.update') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Auto-Send Toggle Switch -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                <div class="space-y-0.5">
                    <label for="whatsapp_auto_send" class="text-sm font-bold text-slate-900 cursor-pointer">Auto-Send WhatsApp Brochure on Lead Capture</label>
                    <p class="text-xs text-slate-500">Automatically trigger WhatsApp message as soon as a customer submits the inquiry form.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="whatsapp_auto_send" id="whatsapp_auto_send" value="1" {{ old('whatsapp_auto_send', $company->whatsapp_auto_send) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                </label>
            </div>

            <!-- Provider Selection -->
            <div class="space-y-1.5">
                <label for="whatsapp_provider" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">WhatsApp Service Gateway <span class="text-rose-500">*</span></label>
                <select name="whatsapp_provider" id="whatsapp_provider" class="input-field cursor-pointer font-semibold">
                    <option value="simulated" {{ old('whatsapp_provider', $company->whatsapp_provider) === 'simulated' ? 'selected' : '' }}>
                        🧪 Simulated Mode (Development & Local Testing - No API Key Required)
                    </option>
                    <option value="twilio" {{ old('whatsapp_provider', $company->whatsapp_provider) === 'twilio' ? 'selected' : '' }}>
                        📲 Twilio WhatsApp Business API
                    </option>
                    <option value="ultramsg" {{ old('whatsapp_provider', $company->whatsapp_provider) === 'ultramsg' ? 'selected' : '' }}>
                        💬 UltraMsg Gateway API
                    </option>
                    <option value="meta_cloud" {{ old('whatsapp_provider', $company->whatsapp_provider) === 'meta_cloud' ? 'selected' : '' }}>
                        🌐 Meta WhatsApp Cloud API (Official)
                    </option>
                </select>
            </div>

            <!-- Credentials Block -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div class="space-y-1.5">
                    <label for="whatsapp_api_key" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">API Auth Token / Secret Key</label>
                    <input type="password" name="whatsapp_api_key" id="whatsapp_api_key" value="{{ old('whatsapp_api_key', $company->whatsapp_api_key) }}" class="input-field" placeholder="Enter API Key / Token">
                </div>
                <div class="space-y-1.5">
                    <label for="whatsapp_phone_number_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Phone Number ID / Account SID</label>
                    <input type="text" name="whatsapp_phone_number_id" id="whatsapp_phone_number_id" value="{{ old('whatsapp_phone_number_id', $company->whatsapp_phone_number_id) }}" class="input-field" placeholder="e.g. 10928374829">
                </div>
                <div class="space-y-1.5 sm:col-span-2">
                    <label for="whatsapp_instance_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Instance ID / Sender Number (Optional)</label>
                    <input type="text" name="whatsapp_instance_id" id="whatsapp_instance_id" value="{{ old('whatsapp_instance_id', $company->whatsapp_instance_id) }}" class="input-field" placeholder="e.g. instance12345 or whatsapp:+14155238886">
                </div>
            </div>

            <!-- Template Editor -->
            <div class="space-y-2 pt-2">
                <div class="flex items-center justify-between">
                    <label for="whatsapp_welcome_template" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Welcome Message & Brochure Template</label>
                    <span class="text-xs text-indigo-600 font-semibold">Available Merge Tags below</span>
                </div>
                <textarea name="whatsapp_welcome_template" id="whatsapp_welcome_template" rows="6" class="input-field font-mono text-xs leading-relaxed" placeholder="Custom message template...">{{ old('whatsapp_welcome_template', $company->whatsapp_welcome_template ?? $defaultTemplate) }}</textarea>
                
                <!-- Dynamic Tag Pills -->
                <div class="flex flex-wrap gap-2 pt-1">
                    <span class="text-[11px] font-mono bg-slate-100 border border-slate-200 px-2 py-1 rounded text-slate-700">{customer_name}</span>
                    <span class="text-[11px] font-mono bg-slate-100 border border-slate-200 px-2 py-1 rounded text-slate-700">{project_name}</span>
                    <span class="text-[11px] font-mono bg-slate-100 border border-slate-200 px-2 py-1 rounded text-slate-700">{company_name}</span>
                    <span class="text-[11px] font-mono bg-indigo-50 border border-indigo-200 px-2 py-1 rounded text-indigo-700 font-bold">{brochure_url}</span>
                    <span class="text-[11px] font-mono bg-slate-100 border border-slate-200 px-2 py-1 rounded text-slate-700">{executive_name}</span>
                </div>
            </div>

            <!-- Submit Action -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end">
                <button type="submit" class="btn-primary space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Save WhatsApp Settings</span>
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
                <p class="text-xs text-slate-500">Test your WhatsApp template and instant brochure delivery link.</p>
            </div>
        </div>

        <form action="{{ route('settings.whatsapp.test') }}" method="POST" class="flex flex-col sm:flex-row gap-3 pt-2">
            @csrf
            <input type="text" name="test_phone" required class="input-field flex-1" placeholder="Enter recipient phone number (e.g. +1234567890)" value="{{ old('test_phone', auth()->user()->phone ?? '') }}">
            <button type="submit" class="btn-secondary space-x-2 shrink-0">
                <span>Send Test WhatsApp</span>
            </button>
        </form>
    </div>
</div>
@endsection
