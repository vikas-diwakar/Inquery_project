@extends('layouts.app')

@section('title', 'Social Media Leads - ' . $project->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Project Selector & Scope Banner ("Every Project Wise") -->
    <div class="mb-6 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/90 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="h-11 w-11 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 flex items-center justify-center text-white font-extrabold shadow-sm shadow-indigo-500/20 shrink-0">
                {{ strtoupper(substr($project->name, 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Current Project Scope</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        ● Isolated Social Media Key
                    </span>
                </div>
                <h2 class="text-xl font-extrabold text-slate-900 leading-tight flex items-center gap-2">
                    <span>{{ $project->name }}</span>
                    @if($project->location)
                        <span class="text-xs font-semibold text-slate-500 font-normal">({{ $project->location }})</span>
                    @endif
                </h2>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <label for="projectSelector" class="text-xs font-bold text-slate-600 uppercase tracking-wider shrink-0 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                <span>Switch Project:</span>
            </label>
            <select id="projectSelector" onchange="window.location.href = this.value" 
                class="w-full sm:w-auto min-w-[220px] rounded-xl border-slate-300 bg-slate-50 text-slate-900 text-sm font-bold py-2 px-3 focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs cursor-pointer">
                @foreach($companyProjects as $p)
                    <option value="{{ route('social-media.project', $p) }}" {{ $p->id === $project->id ? 'selected' : '' }}>
                        {{ $p->name }} {{ $p->location ? '('.$p->location.')' : '' }} {{ $p->id === $project->id ? '✓ (Active)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Social Media Leads & Webhooks</h1>
            <p class="mt-2 text-sm text-slate-600">Connect Social Media platforms (Facebook Ads, Instagram Ads, Zapier, Make) or embed widgets to capture inquiries directly into <span class="font-bold text-slate-800">{{ $project->name }}</span>.</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-wrap items-center gap-3">
            <button type="button" 
                onclick="openTestLeadModal()" 
                class="inline-flex items-center px-4 py-2.5 rounded-lg text-sm font-bold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 transition-all shadow-xs">
                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                <span>🧪 Test Facebook / IG Lead</span>
            </button>
            <button type="button" 
                onclick="showConfirmationModal('Regenerate Lead Token', 'Warning: Regenerating the token will break all current external forms, social media setups and webhooks using the old token for {{ addslashes($project->name) }}. Are you sure you want to proceed?', function() { document.getElementById('regenerate-token-form-{{ $project->id }}').submit(); })" 
                class="inline-flex items-center px-4 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-red-600 hover:text-red-700 hover:bg-red-50 bg-white transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3m0 0l3 3m-3-3v8"/></svg>
                Regenerate Token
            </button>
            <form id="regenerate-token-form-{{ $project->id }}" action="{{ route('projects.regenerate-token', $project) }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>

    <!-- Social Media Token Info Box -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-xl overflow-hidden mb-8">
        <div class="px-6 py-8 sm:px-8 text-white relative">
            <div class="relative z-10">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white mb-3 backdrop-blur-sm border border-white/15">
                    Social Media & Webhook Token &bull; {{ $project->name }}
                </span>
                <h3 class="text-xl font-bold">Project Social Media Key</h3>
                <p class="mt-2 text-sm text-indigo-100 max-w-xl">Use this key to authenticate external Social Media requests, automations, and webhooks. Keep it secure—anyone with this token can submit leads directly to <span class="font-bold text-white underline">{{ $project->name }}</span>.</p>
                <div class="mt-5 flex flex-col sm:flex-row gap-3 max-w-2xl">
                    <input type="text" readonly id="socialMediaToken" value="{{ $project->lead_token }}" 
                        class="block w-full rounded-lg bg-black/20 border-white/10 text-white font-mono text-sm px-4 py-3 focus:outline-none focus:ring-0 select-all backdrop-blur-sm">
                    <button onclick="copyToClipboard('socialMediaToken', 'btnCopyToken')" id="btnCopyToken" 
                        class="inline-flex items-center justify-center px-5 py-3 rounded-lg text-sm font-bold bg-white text-indigo-600 hover:bg-indigo-50 active:bg-indigo-100 transition-colors shadow">
                        Copy Key
                    </button>
                </div>
            </div>
            <!-- Background Vector Accents -->
            <div class="absolute -right-8 -bottom-8 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/4 -top-8 w-32 h-32 bg-white/5 rounded-full blur-2xl pointer-events-none"></div>
        </div>
    </div>

    <!-- Tabs Container -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mb-8">
        <div class="border-b border-slate-200 bg-slate-50/50">
            <nav class="flex -mb-px px-6" aria-label="Tabs">
                <button onclick="switchTab('zapier')" id="tab-btn-zapier" 
                    class="tab-btn border-b-2 border-indigo-600 text-indigo-600 font-semibold py-4 px-4 text-sm inline-flex items-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    Social Media (Facebook & Instagram)
                </button>
                <button onclick="switchTab('webhook')" id="tab-btn-webhook" 
                    class="tab-btn border-b-2 border-transparent text-slate-500 hover:text-slate-700 font-medium py-4 px-4 text-sm inline-flex items-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    REST Webhook API
                </button>
                <button onclick="switchTab('widget')" id="tab-btn-widget" 
                    class="tab-btn border-b-2 border-transparent text-slate-500 hover:text-slate-700 font-medium py-4 px-4 text-sm inline-flex items-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Embed Form Widget
                </button>
                <button onclick="switchTab('sdk')" id="tab-btn-sdk" 
                    class="tab-btn border-b-2 border-transparent text-slate-500 hover:text-slate-700 font-medium py-4 px-4 text-sm inline-flex items-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    SDK Snippets
                </button>
            </nav>
        </div>

        <!-- Tab Contents -->
        <div class="p-6 sm:p-8">
            <!-- 1. Social Media (Zapier & Make) Tab -->
            <div id="tab-content-zapier" class="tab-pane">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <h4 class="text-lg font-bold text-slate-900 mb-2">Connect Social Media Leads (Facebook / Instagram) via Zapier or Make</h4>
                        <p class="text-sm text-slate-600 mb-6">Capture leads from Facebook Lead Ads, Instagram Ads, LinkedIn Lead Gen, Google Lead Forms, or TikTok Leads by connecting them using automation webhooks.</p>

                        <h5 class="text-sm font-bold text-slate-900 mb-3">Social Media Lead Workflow:</h5>
                        <ol class="space-y-4 text-sm text-slate-600 list-decimal pl-4">
                            <li><strong>Create a Webhook Action</strong>: In Zapier or Make, select "Webhooks" as the action step and choose **POST**.</li>
                            <li><strong>Configure URL</strong>: Set the destination URL to your webhook URL:
                                <code class="block mt-1 bg-slate-100 p-2 rounded text-xs select-all text-indigo-600 font-mono font-semibold">{{ route('api.leads.webhook', ['token' => $project->lead_token]) }}</code>
                            </li>
                            <li><strong>Set Payload Type</strong>: Select **JSON** payload format.</li>
                            <li><strong>Map Incoming Data</strong>: In the data mapping section, set target keys to your matching values:
                                <ul class="list-disc pl-5 mt-2 space-y-1 text-xs">
                                    <li><code class="bg-slate-100 px-1 rounded font-mono">name</code> -> Map to Facebook Lead "Full Name"</li>
                                    <li><code class="bg-slate-100 px-1 rounded font-mono">phone</code> -> Map to Facebook Lead "Phone Number"</li>
                                    <li><code class="bg-slate-100 px-1 rounded font-mono">email</code> -> Map to Facebook Lead "Email Address"</li>
                                    <li><code class="bg-slate-100 px-1 rounded font-mono">source</code> -> Map to text "Facebook Ads"</li>
                                </ul>
                            </li>
                            <li><strong>Test & Turn On</strong>: Run a test step to make sure inquiries are saved properly in your dashboard.</li>
                        </ol>

                        <!-- Facebook & Instagram Testing Guide -->
                        <div class="mt-6 bg-indigo-50/80 border border-indigo-200 rounded-2xl p-5 space-y-4">
                            <div class="flex items-center gap-2.5">
                                <span class="h-8 w-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-sm font-bold shrink-0">🧪</span>
                                <div>
                                    <h5 class="text-sm font-bold text-slate-900">3 Ways to Test Facebook & Instagram Leads</h5>
                                    <p class="text-xs text-slate-500">Verify your lead intake workflow before spending real advertising budget.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                <div class="bg-white p-3.5 rounded-xl border border-indigo-100 space-y-1.5 shadow-2xs">
                                    <span class="font-bold text-indigo-700 flex items-center gap-1.5">
                                        <span>1. One-Click In-App Simulator</span>
                                    </span>
                                    <p class="text-slate-600">Click the <strong>🧪 Test Facebook / IG Lead</strong> button at the top of this page to simulate an incoming lead directly in your browser.</p>
                                </div>

                                <div class="bg-white p-3.5 rounded-xl border border-indigo-100 space-y-1.5 shadow-2xs">
                                    <span class="font-bold text-indigo-700 flex items-center gap-1.5">
                                        <span>2. Meta Official Lead Ads Testing Tool</span>
                                    </span>
                                    <p class="text-slate-600">Meta provides an official sandbox to generate test leads directly from your Facebook Page form: 
                                        <a href="https://developers.facebook.com/tools/lead-ads-testing" target="_blank" rel="noopener" class="text-indigo-600 underline font-bold inline-flex items-center gap-1">developers.facebook.com/tools/lead-ads-testing ↗</a>
                                    </p>
                                </div>
                            </div>

                            <div class="bg-slate-900 rounded-xl p-3.5 text-slate-200 font-mono text-[11px] overflow-x-auto space-y-1.5">
                                <div class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">3. Windows PowerShell Command (Run in Terminal):</div>
                                <code class="text-emerald-400 block break-all">Invoke-RestMethod -Uri "{{ route('api.leads.webhook', ['token' => $project->lead_token]) }}" -Method Post -ContentType "application/json" -Body '{"name":"Rahul Sharma","phone":"9876543210","email":"rahul@test.com","source":"Facebook Lead Ads","flat_type":"3 BHK"}'</code>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
                        <h4 class="text-base font-bold text-slate-900 mb-3">Supported Social Media Platforms</h4>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-slate-200">
                                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-xs">F</div>
                                <span class="text-sm font-semibold text-slate-700">Facebook Lead Ads</span>
                            </div>
                            <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-slate-200">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-yellow-500 via-red-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs">I</div>
                                <span class="text-sm font-semibold text-slate-700">Instagram Leads</span>
                            </div>
                            <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-slate-200">
                                <div class="w-8 h-8 rounded-full bg-blue-800 flex items-center justify-center text-white font-bold text-xs">L</div>
                                <span class="text-sm font-semibold text-slate-700">LinkedIn Lead Gen</span>
                            </div>
                            <div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-slate-200">
                                <div class="w-8 h-8 rounded-full bg-red-600 flex items-center justify-center text-white font-bold text-xs">G</div>
                                <span class="text-sm font-semibold text-slate-700">Google Lead Forms</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Webhook API Tab -->
            <div id="tab-content-webhook" class="tab-pane hidden">
                <h4 class="text-lg font-bold text-slate-900 mb-2">Incoming Webhook API Endpoint</h4>
                <p class="text-sm text-slate-600 mb-4">Send POST requests containing lead information as JSON to the endpoint below. Supports automatic mapping for popular field aliases.</p>
                
                <div class="mb-6">
                    <label class="block text-xs font-semibold uppercase text-slate-500 tracking-wider mb-2">Endpoint URL</label>
                    <div class="flex items-center gap-2">
                        <input type="text" readonly id="webhookUrl" value="{{ route('api.leads.webhook', ['token' => $project->lead_token]) }}" 
                            class="block w-full rounded-lg border-slate-300 bg-slate-50 font-mono text-sm px-4 py-2.5 text-slate-700 select-all">
                        <button onclick="copyToClipboard('webhookUrl', 'btnCopyWebhook')" id="btnCopyWebhook" 
                            class="px-4 py-2.5 bg-slate-800 text-white rounded-lg text-sm font-semibold hover:bg-slate-900 transition shrink-0">
                            Copy URL
                        </button>
                    </div>
                </div>

                <h5 class="text-sm font-bold text-slate-800 mb-2">Supported JSON Fields</h5>
                <div class="overflow-x-auto border border-slate-200 rounded-lg">
                    <table class="min-w-full divide-y divide-slate-200 text-xs">
                        <thead class="bg-slate-50 font-semibold text-slate-700">
                            <tr>
                                <th class="px-6 py-3 text-left">Key</th>
                                <th class="px-6 py-3 text-left">Required</th>
                                <th class="px-6 py-3 text-left">Accepted Aliases</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-slate-600">
                            <tr>
                                <td class="px-6 py-3 font-mono font-bold text-indigo-600">name</td>
                                <td class="px-6 py-3 text-red-600 font-semibold">Yes</td>
                                <td class="px-6 py-3">customer_name, full_name, fullname, first_name, lead_name</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono font-bold text-indigo-600">phone</td>
                                <td class="px-6 py-3 text-red-600 font-semibold">Yes</td>
                                <td class="px-6 py-3">phone_number, mobile, contact, contact_number, phone_no, tel</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono font-bold text-indigo-600">email</td>
                                <td class="px-6 py-3">No</td>
                                <td class="px-6 py-3">email_address, mail</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono font-bold text-indigo-600">flat_type</td>
                                <td class="px-6 py-3">No</td>
                                <td class="px-6 py-3">flat, unit, unit_type, property_type, requirement, configuration</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono font-bold text-indigo-600">budget</td>
                                <td class="px-6 py-3">No</td>
                                <td class="px-6 py-3">price, investment, max_budget</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono font-bold text-indigo-600">message</td>
                                <td class="px-6 py-3">No</td>
                                <td class="px-6 py-3">notes, comments, description, remarks</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-3 font-mono font-bold text-indigo-600">source</td>
                                <td class="px-6 py-3">No</td>
                                <td class="px-6 py-3">source, lead_source, platform (defaults to webhook)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 3. Embed Widget Tab -->
            <div id="tab-content-widget" class="tab-pane hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-lg font-bold text-slate-900 mb-2">Embeddable Inquiry Form Widget</h4>
                        <p class="text-sm text-slate-600 mb-4">Paste this iframe code onto any page of your website to display a beautiful, modern inquiry form. Submissions will automatically populate as new inquiries under this project.</p>
                        
                        <div class="mb-4">
                            <label class="block text-xs font-semibold uppercase text-slate-500 tracking-wider mb-2">Iframe Code</label>
                            <div class="relative">
                                <textarea readonly id="widgetIframeCode" rows="4" 
                                    class="block w-full rounded-lg border-slate-300 bg-slate-50 font-mono text-xs p-4 focus:outline-none focus:ring-0 select-all"><iframe src="{{ route('public.inquiry.widget', ['token' => $project->lead_token]) }}" width="100%" height="480" frameborder="0" style="border:0; border-radius:12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);"></iframe></textarea>
                                <button onclick="copyToClipboard('widgetIframeCode', 'btnCopyWidget')" id="btnCopyWidget"
                                    class="absolute right-3 bottom-3 inline-flex items-center px-3 py-1.5 bg-indigo-600 text-white rounded text-xs font-semibold hover:bg-indigo-700 transition shadow">
                                    Copy Code
                                </button>
                            </div>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-amber-800 text-sm">
                            <h5 class="font-bold flex items-center gap-2 mb-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Optional Customizations
                            </h5>
                            <p class="text-xs">Add query parameters to the iframe URL to auto-track custom sources: e.g. append <code class="bg-amber-100 font-mono px-1 rounded">?source=my_landing_page</code></p>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold uppercase text-slate-500 tracking-wider mb-3">Live Widget Preview</h4>
                        <div class="border border-slate-200 rounded-xl overflow-hidden bg-slate-100 shadow-inner p-2">
                            <iframe src="{{ route('public.inquiry.widget', ['token' => $project->lead_token]) }}" width="100%" height="450" class="rounded-lg bg-white border-0"></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. SDK Tab -->
            <div id="tab-content-sdk" class="tab-pane hidden">
                <h4 class="text-lg font-bold text-slate-900 mb-2">Developer Code Snippets</h4>
                <p class="text-sm text-slate-600 mb-6">Connect programmatically from Social Media apps, custom frontends or backend applications using these templates:</p>

                <!-- Code Carousels/Select -->
                <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm">
                    <div class="bg-slate-50 border-b border-slate-200 px-4 py-2 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase">Javascript (Fetch API)</span>
                    </div>
                    <pre class="p-4 bg-slate-900 text-indigo-200 font-mono text-xs overflow-x-auto"><code>fetch("{{ route('api.leads.webhook', ['token' => $project->lead_token]) }}", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json"
  },
  body: JSON.stringify({
    name: "John Doe",
    phone: "+919876543210",
    email: "johndoe@example.com",
    flat_type: "3 BHK",
    budget: 8500000.00,
    message: "Interested in the project",
    source: "social_media_ad"
  })
})
.then(response => response.json())
.then(data => console.log(data))
.catch(error => console.error("Error:", error));</code></pre>
                </div>

                <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm mt-6">
                    <div class="bg-slate-50 border-b border-slate-200 px-4 py-2 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-500 uppercase">cURL Request</span>
                    </div>
                    <pre class="p-4 bg-slate-900 text-indigo-200 font-mono text-xs overflow-x-auto"><code>curl -X POST "{{ route('api.leads.webhook', ['token' => $project->lead_token]) }}" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "Jane Doe",
    "phone": "9999988888",
    "email": "jane@example.com",
    "source": "facebook_instagram"
  }'</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Social Media & Webhook Lead Logs -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-900">Recent Social Media & Webhook Leads</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                Live Status
            </span>
        </div>
        <div class="p-6">
            @if($recentLeads->isEmpty())
                <div class="text-center py-12 text-slate-500">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm">No leads have been received from Social Media or widgets yet.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50 font-semibold text-slate-700 text-xs uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Customer</th>
                                <th class="px-6 py-3 text-left">Contact Info</th>
                                <th class="px-6 py-3 text-left">Source / Channel</th>
                                <th class="px-6 py-3 text-left">Lead Type</th>
                                <th class="px-6 py-3 text-left">Created At</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 text-sm text-slate-600">
                            @foreach($recentLeads as $lead)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900">{{ $lead->customer_name }}</div>
                                        @if($lead->flat_type)
                                            <div class="text-xs text-slate-500 font-semibold">{{ $lead->flat_type }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>{{ $lead->phone }}</div>
                                        <div class="text-xs text-slate-500">{{ $lead->email ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 uppercase tracking-wide">
                                            {{ $lead->source }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $lead->type === 'widget' ? 'bg-indigo-50 text-indigo-700' : 'bg-emerald-50 text-emerald-700' }}">
                                            {{ $lead->type === 'widget' ? 'Widget' : 'Social Media / Webhook' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-xs">
                                        {{ $lead->created_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('inquiries.show', $lead) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-xs inline-flex items-center">
                                            View Inquiry
                                            <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Interactive Test Lead Modal -->
    <div id="testLeadModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center z-[999] p-4 transition-all" role="dialog" aria-modal="true" aria-labelledby="testLeadModalTitle">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/90 max-w-lg w-full p-6 sm:p-7 space-y-5">
            <div class="flex items-start justify-between">
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-lg shrink-0">
                        🧪
                    </div>
                    <div>
                        <h3 id="testLeadModalTitle" class="text-lg font-extrabold text-slate-900 leading-tight">Simulate Social Media Lead</h3>
                        <p class="text-xs text-slate-500">Submit a live test lead into <span class="font-bold text-indigo-600">{{ $project->name }}</span>.</p>
                    </div>
                </div>
                <button type="button" onclick="closeTestLeadModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="testLeadForm" onsubmit="submitTestLead(event)" class="space-y-3.5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Lead Source Platform</label>
                    <select id="test_source" class="block w-full rounded-xl border-slate-300 text-xs sm:text-sm font-semibold bg-slate-50 py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="Facebook Lead Ads">Facebook Lead Ads</option>
                        <option value="Instagram Ads">Instagram Ads</option>
                        <option value="Google Lead Forms">Google Lead Forms</option>
                        <option value="Website Webhook">Website Webhook</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" id="test_name" value="Rahul Sharma" required
                            class="block w-full rounded-xl border-slate-300 text-xs sm:text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number <span class="text-red-500">*</span></label>
                        <input type="text" id="test_phone" value="9876543210" required
                            class="block w-full rounded-xl border-slate-300 text-xs sm:text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email (Optional)</label>
                        <input type="email" id="test_email" value="rahul.sharma@example.com"
                            class="block w-full rounded-xl border-slate-300 text-xs sm:text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Unit / Flat Type</label>
                        <input type="text" id="test_flat" value="3 BHK"
                            class="block w-full rounded-xl border-slate-300 text-xs sm:text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Budget (INR)</label>
                    <input type="number" id="test_budget" value="7500000"
                        class="block w-full rounded-xl border-slate-300 text-xs sm:text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Customer Message / Requirement</label>
                    <textarea id="test_message" rows="2"
                        class="block w-full rounded-xl border-slate-300 text-xs sm:text-sm py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">Interested in brochure and site visit details.</textarea>
                </div>

                <div id="testLeadResult" class="hidden p-3 rounded-xl text-xs font-medium"></div>

                <div class="flex items-center justify-end space-x-2.5 pt-2">
                    <button type="button" onclick="closeTestLeadModal()" 
                        class="px-4 py-2 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors cursor-pointer">
                        Cancel
                    </button>
                    <button type="submit" id="btnSubmitTestLead"
                        class="px-5 py-2 text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors shadow-sm flex items-center gap-1.5 cursor-pointer">
                        <span id="btnSubmitTestText">🚀 Send Test Lead</span>
                        <svg id="btnSubmitTestSpinner" class="w-4 h-4 animate-spin hidden" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Test lead modal handlers
    function openTestLeadModal() {
        const modal = document.getElementById('testLeadModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        // generate a randomized phone number so repeated clicks don't hit duplicate phone check
        document.getElementById('test_phone').value = '98' + Math.floor(10000000 + Math.random() * 90000000);
        document.getElementById('testLeadResult').classList.add('hidden');
    }

    function closeTestLeadModal() {
        const modal = document.getElementById('testLeadModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function submitTestLead(e) {
        e.preventDefault();
        const btn = document.getElementById('btnSubmitTestLead');
        const btnText = document.getElementById('btnSubmitTestText');
        const spinner = document.getElementById('btnSubmitTestSpinner');
        const resultBox = document.getElementById('testLeadResult');

        btn.disabled = true;
        btnText.textContent = 'Submitting...';
        spinner.classList.remove('hidden');

        const payload = {
            name: document.getElementById('test_name').value,
            phone: document.getElementById('test_phone').value,
            email: document.getElementById('test_email').value,
            flat_type: document.getElementById('test_flat').value,
            budget: document.getElementById('test_budget').value,
            message: document.getElementById('test_message').value,
            source: document.getElementById('test_source').value
        };

        fetch("{{ route('api.leads.webhook', ['token' => $project->lead_token]) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const data = await response.json();
            btn.disabled = false;
            btnText.textContent = '🚀 Send Test Lead';
            spinner.classList.add('hidden');

            resultBox.classList.remove('hidden');
            if (response.ok && data.success) {
                resultBox.className = 'p-3 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200';
                resultBox.innerHTML = `✅ <strong>Lead captured successfully!</strong> Created Lead ID #${data.lead_id} under {{ $project->name }}. Reloading list in 1.5s...`;
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                resultBox.className = 'p-3 rounded-xl text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200';
                resultBox.textContent = '❌ ' + (data.error || 'Failed to capture lead. Please check the values.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btnText.textContent = '🚀 Send Test Lead';
            spinner.classList.add('hidden');
            resultBox.classList.remove('hidden');
            resultBox.className = 'p-3 rounded-xl text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200';
            resultBox.textContent = '❌ Error: ' + err.message;
        });
    }

    // Tab switching functionality
    function switchTab(tabId) {
        // Hide all panes
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
        
        // Show selected pane
        document.getElementById('tab-content-' + tabId).classList.remove('hidden');

        // Reset tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-indigo-600', 'text-indigo-600', 'font-semibold');
            btn.classList.add('border-transparent', 'text-slate-500', 'font-medium');
        });

        // Set active tab button
        const activeBtn = document.getElementById('tab-btn-' + tabId);
        activeBtn.classList.remove('border-transparent', 'text-slate-500', 'font-medium');
        activeBtn.classList.add('border-indigo-600', 'text-indigo-600', 'font-semibold');
    }

    // Copy to clipboard helper
    function copyToClipboard(inputId, buttonId) {
        const copyText = document.getElementById(inputId);
        copyText.select();
        copyText.setSelectionRange(0, 99999); // For mobile devices

        navigator.clipboard.writeText(copyText.value).then(() => {
            const btn = document.getElementById(buttonId);
            const originalText = btn.textContent;
            
            btn.textContent = "Copied!";
            btn.classList.remove('bg-indigo-600', 'bg-white', 'text-indigo-600');
            btn.classList.add('bg-green-600', 'text-white');
            
            setTimeout(() => {
                btn.textContent = originalText;
                btn.classList.remove('bg-green-600');
                if (buttonId === 'btnCopyToken') {
                    btn.classList.add('bg-white', 'text-indigo-600');
                } else {
                    btn.classList.add('bg-indigo-600', 'text-white');
                }
            }, 2000);
        }).catch(err => {
            showAlert('Copy Failed', 'Failed to copy text: ' + err, { type: 'danger' });
        });
    }
</script>
@endsection
