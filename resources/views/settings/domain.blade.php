@extends('layouts.app')

@section('title', 'Workspace & Domain Settings - PropDrip')

@section('content')
<div class="max-w-5xl mx-auto space-y-8 py-6">
    <!-- Breadcrumb & Header -->
    <div class="space-y-2">
        <div class="flex items-center space-x-2 text-xs text-slate-500 font-medium">
            <a href="{{ route('dashboard') }}" class="hover:text-indigo-600 transition-colors">Dashboard</a>
            <span>/</span>
            <span class="text-slate-400">Settings</span>
            <span>/</span>
            <span class="text-indigo-600 font-semibold">Workspace Domain</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="p-2.5 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 shadow-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </span>
                    Company Workspace Domain
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Manage your company's dedicated branded subdomain (like Keka) for your team and sales executives.
                </p>
            </div>

            <!-- Status Pill -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold self-start sm:self-auto">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Active Subdomain</span>
            </div>
        </div>
    </div>

    <!-- Session Alerts -->
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-xs font-medium text-emerald-800 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
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

    <!-- Card 1: Dedicated Workspace Banner -->
    <div class="rounded-3xl p-6 sm:p-8 bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 text-white shadow-2xl border border-slate-800 relative overflow-hidden">
        <!-- Ambient light blob -->
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
            <div class="lg:col-span-8 space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white/10 backdrop-blur-md border border-white/10 text-indigo-200">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Your Branded Team Portal
                </div>
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white">
                    {{ $company->name }} Workspace URL
                </h2>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-xl">
                    Share this dedicated link with your sales managers, brokers, and staff. They will see your company logo and sign directly into your workspace.
                </p>

                <!-- URL Bar Display -->
                <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                    <div class="flex-1 flex items-center bg-white/10 backdrop-blur-md border border-white/15 rounded-xl px-4 py-3 text-sm font-mono text-indigo-200 select-all overflow-x-auto">
                        <svg class="w-4 h-4 text-emerald-400 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span id="portal-url" class="truncate font-semibold text-white">{{ $company->workspace_url }}</span>
                    </div>

                    <button type="button" onclick="copyWorkspaceUrl()" id="copy-btn" class="px-5 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-indigo-600/30 active:scale-95 flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                        </svg>
                        <span id="copy-text">Copy Link</span>
                    </button>

                    <a href="{{ $company->workspace_url }}" target="_blank" rel="noopener noreferrer" class="px-4 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition-all flex items-center justify-center gap-1.5 border border-white/10 flex-shrink-0">
                        <span>Launch</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Workspace Snapshot Preview -->
            <div class="lg:col-span-4 bg-white/5 backdrop-blur-md rounded-2xl p-5 border border-white/10 text-center space-y-3">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" 
                        style="max-height: 48px; max-width: 180px; width: auto; height: auto; object-fit: contain;" 
                        class="mx-auto object-contain rounded-xl bg-white p-1.5 shadow-md">
                @else
                    <div class="h-12 w-12 rounded-xl bg-indigo-600 text-white font-black text-xl flex items-center justify-center mx-auto shadow-md">
                        {{ strtoupper(substr($company->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h3 class="text-sm font-bold text-white">{{ $company->name }}</h3>
                    <p class="text-xs text-indigo-200/80 font-mono">{{ $company->workspace_domain }}</p>
                </div>
                <div class="text-[11px] text-slate-400 bg-white/5 rounded-lg py-1.5 px-3">
                    Branded Login Portal Enabled
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Customize Subdomain Form -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-slate-200/80 space-y-6">
        <div class="pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Change Workspace Subdomain</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Update your company slug. Note: Previous links will need to be updated.
                </p>
            </div>
        </div>

        <form action="{{ route('settings.domain.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="max-w-xl space-y-2">
                <div class="flex items-center justify-between">
                    <label for="subdomain" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">
                        Workspace Subdomain <span class="text-rose-500">*</span>
                    </label>
                    <span id="subdomain-status" class="text-[11px] font-medium hidden"></span>
                </div>

                <div class="flex rounded-xl shadow-sm overflow-hidden border border-slate-200 focus-within:ring-2 focus-within:ring-indigo-500/20 focus-within:border-indigo-600 transition-all bg-slate-50">
                    <div class="inline-flex items-center pl-3.5 pr-1.5 text-slate-400 text-xs font-mono select-none">
                        https://
                    </div>
                    <input type="text" name="subdomain" id="subdomain" required value="{{ old('subdomain', $company->subdomain) }}" 
                        class="block w-full py-3 px-1 bg-transparent text-sm font-bold text-indigo-700 placeholder-slate-400 focus:outline-none" 
                        placeholder="your-company">
                    <div class="inline-flex items-center px-3 text-slate-500 text-xs font-mono bg-slate-100/80 border-l border-slate-200 select-none">
                        .{{ \App\Models\Company::getBaseHost(request()->getHost()) }}
                    </div>
                </div>

                <p class="text-xs text-slate-500 flex items-center gap-1.5 pt-1">
                    <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Live preview: <span id="subdomain-preview" class="font-mono text-indigo-600 font-bold">https://{{ $company->subdomain }}.{{ \App\Models\Company::getBaseHost(request()->getHost()) }}</span>
                </p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/20 transition-all">
                    Save Workspace Subdomain
                </button>
            </div>
        </form>
    </div>

    <!-- Card 3: How It Works & Help -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 space-y-2.5">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                1
            </div>
            <h3 class="text-sm font-bold text-slate-900">Direct Team Access</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                Your staff can bookmark <span class="font-semibold text-slate-700">{{ $company->workspace_domain }}</span> to immediately view your branded login screen.
            </p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 space-y-2.5">
            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">
                2
            </div>
            <h3 class="text-sm font-bold text-slate-900">Custom Branding</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                When visitors open your subdomain, your logo, company name, and builder credentials will automatically take center stage.
            </p>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 space-y-2.5">
            <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">
                3
            </div>
            <h3 class="text-sm font-bold text-slate-900">Organization Finder</h3>
            <p class="text-xs text-slate-500 leading-relaxed">
                If team members forget their link, they can type <span class="font-semibold text-slate-700">"{{ $company->name }}"</span> on the main login screen to be redirected instantly.
            </p>
        </div>
    </div>
</div>

<script>
function copyWorkspaceUrl() {
    const url = document.getElementById('portal-url').textContent.trim();
    navigator.clipboard.writeText(url).then(() => {
        const copyText = document.getElementById('copy-text');
        copyText.textContent = 'Copied! ✓';
        setTimeout(() => {
            copyText.textContent = 'Copy Link';
        }, 2000);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const subdomainInput = document.getElementById('subdomain');
    const previewSpan = document.getElementById('subdomain-preview');
    const statusSpan = document.getElementById('subdomain-status');
    const baseHost = "{{ \App\Models\Company::getBaseHost(request()->getHost()) }}";
    const currentSubdomain = "{{ $company->subdomain }}";

    let checkTimeout = null;

    subdomainInput.addEventListener('input', function() {
        let val = subdomainInput.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
        subdomainInput.value = val;

        const displaySlug = val || 'your-company';
        previewSpan.textContent = `https://${displaySlug}.${baseHost}`;

        if (val === currentSubdomain) {
            statusSpan.className = 'text-[11px] font-semibold text-slate-500 block';
            statusSpan.textContent = 'Current domain';
            return;
        }

        if (val.length < 3) {
            statusSpan.className = 'text-[11px] font-medium text-amber-600 block';
            statusSpan.textContent = 'Min 3 chars';
            return;
        }

        statusSpan.className = 'text-[11px] font-medium text-slate-400 block';
        statusSpan.textContent = 'Checking availability...';

        clearTimeout(checkTimeout);
        checkTimeout = setTimeout(() => {
            fetch("{{ route('api.subdomain.check') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ subdomain: val })
            })
            .then(res => res.json())
            .then(data => {
                if (data.available) {
                    statusSpan.className = 'text-[11px] font-semibold text-emerald-600 block';
                    statusSpan.innerHTML = '✓ Available';
                } else {
                    statusSpan.className = 'text-[11px] font-semibold text-rose-600 block';
                    statusSpan.innerHTML = `✗ ${data.message}`;
                }
            })
            .catch(() => {
                statusSpan.classList.add('hidden');
            });
        }, 350);
    });
});
</script>
@endsection
