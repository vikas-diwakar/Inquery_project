@extends('layouts.app')

@section('title', 'Lead Integrations')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Lead Integrations</h1>
            <p class="mt-2 text-sm text-slate-600">Connect external forms, social media platforms, or embed a widget to capture inquiries automatically.</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
            <form action="{{ route('projects.regenerate-token', $project) }}" method="POST" onsubmit="return confirm('Warning: Regenerating the token will break all current external forms and webhook integrations using the old token. Are you sure you want to proceed?');">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2.5 border border-slate-300 rounded-lg text-sm font-semibold text-red-600 hover:text-red-700 hover:bg-red-50 bg-white transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3m0 0l3 3m-3-3v8"/></svg>
                    Regenerate Token
                </button>
            </form>
        </div>
    </div>

    <!-- Integration Token Info Box -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-xl overflow-hidden mb-8">
        <div class="px-6 py-8 sm:px-8 text-white relative">
            <div class="relative z-10">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-white/20 text-white mb-3 backdrop-blur-sm border border-white/15">
                    API Access Token
                </span>
                <h3 class="text-xl font-bold">Project Integration Key</h3>
                <p class="mt-2 text-sm text-indigo-100 max-w-xl">Use this key to authenticate external requests. Keep it secure—anyone with this token can submit leads to your project.</p>
                <div class="mt-5 flex flex-col sm:flex-row gap-3 max-w-2xl">
                    <input type="text" readonly id="integrationToken" value="{{ $project->lead_token }}" 
                        class="block w-full rounded-lg bg-black/20 border-white/10 text-white font-mono text-sm px-4 py-3 focus:outline-none focus:ring-0 select-all backdrop-blur-sm">
                    <button onclick="copyToClipboard('integrationToken', 'btnCopyToken')" id="btnCopyToken" 
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
                <button onclick="switchTab('widget')" id="tab-btn-widget" 
                    class="tab-btn border-b-2 border-indigo-600 text-indigo-600 font-semibold py-4 px-4 text-sm inline-flex items-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Iframe Widget
                </button>
                <button onclick="switchTab('webhook')" id="tab-btn-webhook" 
                    class="tab-btn border-b-2 border-transparent text-slate-500 hover:text-slate-700 font-medium py-4 px-4 text-sm inline-flex items-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    REST Webhook API
                </button>
                <button onclick="switchTab('zapier')" id="tab-btn-zapier" 
                    class="tab-btn border-b-2 border-transparent text-slate-500 hover:text-slate-700 font-medium py-4 px-4 text-sm inline-flex items-center gap-2 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Zapier & Make (Social Media)
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
            <!-- 1. Iframe Widget Tab -->
            <div id="tab-content-widget" class="tab-pane">
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

            <!-- 2. Webhook API Tab -->
            <div id="tab-content-webhook" class="tab-pane hidden">
                <div class="max-w-3xl">
                    <h4 class="text-lg font-bold text-slate-900 mb-2">Direct HTTP Webhook Endpoint</h4>
                    <p class="text-sm text-slate-600 mb-6">Send standard JSON payloads from your custom scripts or backend code to import leads. The API automatically maps key fields dynamically, so you don't have to match key names exactly.</p>
                    
                    <div class="mb-6">
                        <label class="block text-xs font-semibold uppercase text-slate-500 tracking-wider mb-2">Endpoint URL (POST)</label>
                        <div class="flex gap-2">
                            <input type="text" readonly id="webhookUrl" value="{{ route('api.leads.webhook', ['token' => $project->lead_token]) }}"
                                class="block w-full rounded-lg border-slate-300 bg-slate-50 font-mono text-xs px-4 py-3 select-all">
                            <button onclick="copyToClipboard('webhookUrl', 'btnCopyUrl')" id="btnCopyUrl"
                                class="inline-flex items-center px-4 bg-indigo-600 text-white rounded-lg text-sm font-semibold hover:bg-indigo-700 transition shadow whitespace-nowrap">
                                Copy URL
                            </button>
                        </div>
                    </div>

                    <h5 class="text-sm font-bold text-slate-900 mb-3">Smart Payload Mapping Parameters</h5>
                    <div class="border border-slate-200 rounded-xl overflow-hidden mb-6">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50 font-semibold text-slate-700">
                                <tr>
                                    <th class="px-6 py-3 text-left">Property</th>
                                    <th class="px-6 py-3 text-left">Required</th>
                                    <th class="px-6 py-3 text-left">Acceptable Keys (Case-insensitive)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 text-slate-600">
                                <tr>
                                    <td class="px-6 py-3 font-semibold text-slate-900 font-mono">customer_name</td>
                                    <td class="px-6 py-3"><span class="text-red-500 font-bold">Yes</span></td>
                                    <td class="px-6 py-3">customer_name, name, full_name, fullname, first_name (+ last_name)</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-3 font-semibold text-slate-900 font-mono">phone</td>
                                    <td class="px-6 py-3"><span class="text-red-500 font-bold">Yes</span></td>
                                    <td class="px-6 py-3">phone, phone_number, mobile, contact, contact_number, tel</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-3 font-semibold text-slate-900 font-mono">email</td>
                                    <td class="px-6 py-3">No</td>
                                    <td class="px-6 py-3">email, email_address, mail</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-3 font-semibold text-slate-900 font-mono">budget</td>
                                    <td class="px-6 py-3">No</td>
                                    <td class="px-6 py-3">budget, price, investment</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-3 font-semibold text-slate-900 font-mono">flat_type</td>
                                    <td class="px-6 py-3">No</td>
                                    <td class="px-6 py-3">flat_type, flat, unit, unit_type, property_type, configuration</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-3 font-semibold text-slate-900 font-mono">message</td>
                                    <td class="px-6 py-3">No</td>
                                    <td class="px-6 py-3">message, notes, comments, description, remarks</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-3 font-semibold text-slate-900 font-mono">source</td>
                                    <td class="px-6 py-3">No</td>
                                    <td class="px-6 py-3">source, lead_source, platform (defaults to webhook)</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 3. Zapier & Make Tab -->
            <div id="tab-content-zapier" class="tab-pane hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <div class="lg:col-span-2">
                        <h4 class="text-lg font-bold text-slate-900 mb-2">Integrate Social Media Leads via Zapier / Make</h4>
                        <p class="text-sm text-slate-600 mb-6">Capture leads from Facebook Lead Ads, Instagram Ads, LinkedIn Lead Gen, Google Lead Forms, or TikTok Leads by connecting them using automation webhooks.</p>

                        <h5 class="text-sm font-bold text-slate-900 mb-3">Integration Workflow:</h5>
                        <ol class="space-y-4 text-sm text-slate-600 list-decimal pl-4">
                            <li><strong>Create a Webhook Action</strong>: In Zapier, select "Webhooks by Zapier" as the action step and choose **POST**.</li>
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
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-6">
                        <h4 class="text-base font-bold text-slate-900 mb-3">Supported Platforms</h4>
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

            <!-- 4. SDK Tab -->
            <div id="tab-content-sdk" class="tab-pane hidden">
                <h4 class="text-lg font-bold text-slate-900 mb-2">Developer Code Snippets</h4>
                <p class="text-sm text-slate-600 mb-6">Integrate programmatically from custom frontends or backend applications using these templates:</p>

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
    source: "custom_website"
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
    "source": "walk_in_tablet"
  }'</code></pre>
                </div>
            </div>
        </div>
    </div>

    <!-- Webhook Integration Logs -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="border-b border-slate-200 px-6 py-4 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-900">Recent API/Widget Leads</h3>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">
                Live Status
            </span>
        </div>
        <div class="p-6">
            @if($recentLeads->isEmpty())
                <div class="text-center py-12 text-slate-500">
                    <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm">No leads have been received from API integrations or widgets yet.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50 font-semibold text-slate-700 text-xs uppercase">
                            <tr>
                                <th class="px-6 py-3 text-left">Customer</th>
                                <th class="px-6 py-3 text-left">Contact Info</th>
                                <th class="px-6 py-3 text-left">Source</th>
                                <th class="px-6 py-3 text-left">Integration Type</th>
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
                                            {{ $lead->type === 'widget' ? 'Widget' : 'API Webhook' }}
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
</div>

<script>
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
            alert('Failed to copy text: ' + err);
        });
    }
</script>
@endsection
