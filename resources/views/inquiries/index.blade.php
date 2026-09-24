@extends('layouts.app')

@section('title', 'Inquiries - ' . $project->name)

@section('content')
<div class="max-w-7xl mx-auto py-4 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-200/80">Lead Management & AI Scoring</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1.5">Project Inquiries</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Project: <span class="font-bold text-slate-900">{{ $project->name }}</span></p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Dynamic Bulk Custom Drip Trigger -->
            <button type="button" id="bulkDripBtn" onclick="openBulkDripModal()" 
                style="background: linear-gradient(135deg, #059669 0%, #0d9488 100%); color: #ffffff !important;"
                class="hidden inline-flex items-center text-xs font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-emerald-600/25 space-x-2 transition-all transform hover:-translate-y-0.5 cursor-pointer">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span style="color: #ffffff !important;">⚡ Send WhatsApp (<span id="bulkDripCount">0</span>)</span>
            </button>

            <a href="{{ route('inquiries.export', request()->query()) }}" class="btn-secondary text-xs space-x-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('inquiries.create') }}" class="btn-primary text-xs space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Inquiry</span>
            </a>
        </div>
    </div>

    <!-- Active Selection Bar (Appears when items are selected) -->
    <div id="selectionBanner" class="hidden bg-emerald-50 border border-emerald-200/90 rounded-2xl px-5 py-3.5 flex flex-wrap items-center justify-between gap-3 text-xs text-emerald-900 shadow-sm transition-all">
        <div class="flex items-center space-x-2.5 font-semibold">
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-600 animate-ping"></span>
            <span><strong id="bannerSelectedCount" class="font-black text-emerald-800 text-sm">0</strong> lead(s) selected</span>
        </div>
        <div class="flex items-center space-x-3">
            <button type="button" onclick="clearAllSelections()" class="text-emerald-700 hover:text-emerald-900 font-bold underline cursor-pointer">Clear Selection</button>
            <button type="button" onclick="openBulkDripModal()" 
                style="background: #059669; color: #ffffff !important;"
                class="inline-flex items-center space-x-1.5 py-2 px-4 rounded-xl text-xs font-bold shadow-md shadow-emerald-700/20 hover:bg-emerald-700 transition-all cursor-pointer">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span style="color: #ffffff !important;">Compose WhatsApp Message</span>
            </button>
        </div>
    </div>

    <!-- Filters & AI Lead Grade Tabs -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 space-y-4">
        <!-- Lead Grade Filter Pills -->
        <div class="flex items-center space-x-2 overflow-x-auto pb-2 border-b border-slate-100">
            <a href="{{ route('inquiries.index', array_merge(request()->except('grade'), ['grade' => ''])) }}" 
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 {{ !request('grade') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                All Leads
            </a>
            <a href="{{ route('inquiries.index', array_merge(request()->except('grade'), ['grade' => 'hot'])) }}" 
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center space-x-1.5 {{ request('grade') === 'hot' ? 'bg-rose-600 text-white shadow-md shadow-rose-500/20' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100' }}">
                <span>🔥 HOT Intent (70+)</span>
            </a>
            <a href="{{ route('inquiries.index', array_merge(request()->except('grade'), ['grade' => 'warm'])) }}" 
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center space-x-1.5 {{ request('grade') === 'warm' ? 'bg-amber-500 text-white shadow-md shadow-amber-500/20' : 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100' }}">
                <span>☀️ WARM Intent (40–69)</span>
            </a>
            <a href="{{ route('inquiries.index', array_merge(request()->except('grade'), ['grade' => 'cold'])) }}" 
                class="px-4 py-2 rounded-xl text-xs font-bold transition-all shrink-0 flex items-center space-x-1.5 {{ request('grade') === 'cold' ? 'bg-slate-700 text-white shadow-md shadow-slate-500/20' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200' }}">
                <span>❄️ COLD Intent (<40)</span>
            </a>
        </div>

        <form method="GET" action="{{ route('inquiries.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="space-y-1">
                <label for="search" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, Phone, Email" class="input-field py-2 text-xs">
            </div>
            
            <div class="space-y-1">
                <label for="status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Status</label>
                <select name="status" id="status" class="input-field py-2 text-xs cursor-pointer">
                    <option value="">All Statuses</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="interested" {{ request('status') === 'interested' ? 'selected' : '' }}>Interested</option>
                    <option value="site_visit" {{ request('status') === 'site_visit' ? 'selected' : '' }}>Site Visit</option>
                    <option value="booked" {{ request('status') === 'booked' ? 'selected' : '' }}>Booked</option>
                    <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>

            <div class="space-y-1">
                <label for="date_from" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">From Date</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="input-field py-2 text-xs">
            </div>

            <div class="space-y-1">
                <label for="date_to" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">To Date</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="input-field py-2 text-xs">
            </div>

            <div class="sm:col-span-2 md:col-span-4 flex justify-end space-x-3 pt-2">
                <a href="{{ route('inquiries.index') }}" class="btn-secondary text-xs py-2 px-4">Clear Filters</a>
                <button type="submit" class="btn-primary text-xs py-2 px-5">Apply Filters</button>
            </div>
        </form>
    </div>

    <!-- Inquiries Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="p-4 w-10">
                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" 
                                class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" 
                                title="Select All On This Page">
                        </th>
                        <th class="p-4">Customer</th>
                        <th class="p-4">AI Intent Score</th>
                        <th class="p-4">Assigned Executive</th>
                        <th class="p-4">Unit Option / Budget</th>
                        <th class="p-4">Lead Status</th>
                        <th class="p-4">Date</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($inquiries as $inquiry)
                        <tr class="hover:bg-slate-50/60 transition-colors inquiry-row" id="row-{{ $inquiry->id }}">
                            <td class="p-4">
                                <input type="checkbox" value="{{ $inquiry->id }}" 
                                    data-id="{{ $inquiry->id }}"
                                    data-name="{{ e($inquiry->customer_name) }}"
                                    data-phone="{{ e($inquiry->phone) }}"
                                    data-project="{{ e($project->name) }}"
                                    data-executive="{{ e($inquiry->assignedUser?->name ?? 'Sales Desk') }}"
                                    onchange="handleRowCheckboxChange()"
                                    class="inquiry-row-checkbox h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            </td>

                            <td class="p-4">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $inquiry->customer_name }}</div>
                                <div class="text-[11px] text-slate-500 font-medium flex items-center space-x-1.5 mt-0.5">
                                    <span>📱 {{ $inquiry->phone }}</span>
                                    @if($inquiry->whatsapp_status === 'sent')
                                        <span class="inline-flex items-center text-[10px] text-emerald-600 font-extrabold" title="WhatsApp Sent: {{ $inquiry->whatsapp_sent_at?->format('M d H:i') }}">
                                            ✓✓ Sent
                                        </span>
                                    @endif
                                </div>
                                @if($inquiry->email)
                                    <div class="text-[10px] text-slate-400 truncate max-w-[150px]">{{ $inquiry->email }}</div>
                                @endif
                            </td>

                            <!-- AI Intent Score & Grade Badge -->
                            <td class="p-4">
                                <div class="flex items-center space-x-2">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] border {{ $inquiry->grade_badge['class'] }}">
                                        {{ $inquiry->grade_badge['label'] }} ({{ $inquiry->lead_score ?? 0 }}/100)
                                    </span>
                                </div>
                            </td>

                            <!-- Assigned Sales Executive -->
                            <td class="p-4">
                                @if($inquiry->assignedUser)
                                    <div class="flex items-center space-x-2">
                                        <div class="h-6 w-6 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-[10px] shrink-0">
                                            {{ strtoupper(substr($inquiry->assignedUser->name, 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-800 text-xs">{{ $inquiry->assignedUser->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-400 italic text-[11px]">Unassigned</span>
                                @endif
                            </td>

                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ $inquiry->selectedUnitOption ? $inquiry->selectedUnitOption->option_name : 'N/A' }}</div>
                                <div class="text-xs font-semibold text-emerald-700">{{ $inquiry->budget ? '₹' . number_format($inquiry->budget) : 'N/A' }}</div>
                            </td>

                            <td class="p-4">
                                <select class="status-select rounded-xl border border-slate-200 text-xs font-bold px-2.5 py-1.5 bg-slate-50 cursor-pointer" data-inquiry-id="{{ $inquiry->id }}">
                                    <option value="new" {{ $inquiry->status === 'new' ? 'selected' : '' }}>New Lead</option>
                                    <option value="contacted" {{ $inquiry->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                    <option value="interested" {{ $inquiry->status === 'interested' ? 'selected' : '' }}>Interested</option>
                                    <option value="site_visit" {{ $inquiry->status === 'site_visit' ? 'selected' : '' }}>Site Visit</option>
                                    <option value="booked" {{ $inquiry->status === 'booked' ? 'selected' : '' }}>Booked</option>
                                    <option value="lost" {{ $inquiry->status === 'lost' ? 'selected' : '' }}>Lost</option>
                                </select>
                            </td>

                            <td class="p-4 text-slate-500 font-medium whitespace-nowrap">
                                {{ $inquiry->created_at->format('M d, Y') }}
                            </td>

                            <td class="p-4 text-right space-x-2 whitespace-nowrap">
                                <!-- Send Custom Drip Button -->
                                <button type="button" 
                                    onclick="openSingleDripModal({{ $inquiry->id }}, '{{ addslashes($inquiry->customer_name) }}', '{{ addslashes($inquiry->phone) }}', '{{ addslashes($project->name) }}', '{{ addslashes($inquiry->assignedUser?->name ?? 'Sales Desk') }}')" 
                                    style="background-color: #ecfdf5; color: #047857; border-color: #a7f3d0;"
                                    class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl text-xs font-bold hover:bg-emerald-100 border transition-all shadow-xs cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span>Send Drip</span>
                                </button>

                                <a href="{{ route('inquiries.show', $inquiry) }}" class="btn-secondary text-[11px] py-1 px-2.5">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-12 text-center text-slate-400">No inquiries found matching your filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($inquiries->hasPages())
        <div class="pt-2">
            {{ $inquiries->links() }}
        </div>
    @endif
</div>

<!-- ================================================================= -->
<!-- MODAL: High-Visibility & Responsive WhatsApp Message Composer     -->
<!-- ================================================================= -->
<div id="customDripModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5">
    <div class="bg-white rounded-3xl shadow-2xl max-w-5xl w-full border border-slate-200 overflow-hidden flex flex-col max-h-[92vh] animate-in fade-in zoom-in-95 duration-200">
        
        <!-- Top Accent Header (Explicit background color & high contrast text) -->
        <div style="background: linear-gradient(135deg, #065f46 0%, #047857 50%, #1e1b4b 100%); color: #ffffff;" 
             class="px-6 py-4 flex items-center justify-between shrink-0 shadow-md">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-lg font-black shadow-inner">
                    ⚡
                </div>
                <div>
                    <h3 style="color: #ffffff !important;" class="font-extrabold text-base sm:text-lg tracking-tight leading-tight">Send WhatsApp Message</h3>
                    <p style="color: #d1fae5 !important;" class="text-xs font-medium mt-0.5">Write a personalized message to send instantly to selected leads.</p>
                </div>
            </div>
            <button type="button" onclick="closeCustomDripModal()" 
                style="color: #ffffff; background: rgba(255, 255, 255, 0.25);"
                class="h-8 w-8 rounded-full hover:bg-white/40 flex items-center justify-center text-sm font-bold transition-all cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Form Content & Responsive 2-Column Body -->
        <form id="customDripForm" onsubmit="submitCustomDrip(event)" class="overflow-y-auto flex-1 flex flex-col justify-between">
            @csrf

            <div class="p-5 sm:p-6 space-y-5">
                <!-- Target Inquiries Display -->
                <div class="bg-slate-50 border border-slate-200/90 rounded-2xl p-4 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-800 flex items-center">
                            <svg class="w-4 h-4 text-emerald-600 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Selected Recipients (<span id="modalRecipientCount">0</span>)
                        </span>
                        <span class="text-[11px] text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md font-bold border border-emerald-200">
                            Channel: WhatsApp Direct
                        </span>
                    </div>
                    <div id="modalRecipientsList" class="flex flex-wrap gap-2 max-h-24 overflow-y-auto pt-1">
                        <!-- Populated dynamically via JS -->
                    </div>
                    <input type="hidden" name="inquiry_ids" id="modalInquiryIdsInput">
                </div>

                <!-- 2-Column Responsive Layout for Composer & Live WhatsApp Phone -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- Left Column: Message Writing & Placeholders (7 cols on lg) -->
                    <div class="lg:col-span-7 space-y-4">
                        <!-- Dynamic Placeholders -->
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Click to Insert Dynamic Placeholders:
                            </label>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" onclick="insertTag('{customer_name}')" 
                                    style="background-color: #e0e7ff; color: #3730a3; border-color: #c7d2fe;"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold border hover:bg-indigo-100 transition-colors cursor-pointer">
                                    + {customer_name}
                                </button>
                                <button type="button" onclick="insertTag('{project_name}')" 
                                    style="background-color: #e0e7ff; color: #3730a3; border-color: #c7d2fe;"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold border hover:bg-indigo-100 transition-colors cursor-pointer">
                                    + {project_name}
                                </button>
                                <button type="button" onclick="insertTag('{executive_name}')" 
                                    style="background-color: #e0e7ff; color: #3730a3; border-color: #c7d2fe;"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold border hover:bg-indigo-100 transition-colors cursor-pointer">
                                    + {executive_name}
                                </button>
                                <button type="button" onclick="insertTag('{brochure_url}')" 
                                    style="background-color: #d1fae5; color: #065f46; border-color: #a7f3d0;"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold border hover:bg-emerald-100 transition-colors cursor-pointer">
                                    + {brochure_url}
                                </button>
                                <button type="button" onclick="insertTag('{company_name}')" 
                                    style="background-color: #f1f5f9; color: #334155; border-color: #cbd5e1;"
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold border hover:bg-slate-200 transition-colors cursor-pointer">
                                    + {company_name}
                                </button>
                            </div>
                        </div>

                        <!-- Message Content Textarea -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label for="modalMessageContent" class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                    Message Content <span class="text-rose-500">*</span>
                                </label>
                                <span id="charCounter" class="text-xs text-slate-500 font-bold bg-slate-100 px-2 py-0.5 rounded-md">0 characters</span>
                            </div>
                            <textarea id="modalMessageContent" name="message_content" rows="7" required
                                oninput="updateLivePreview()"
                                placeholder="Hello {customer_name}! 👋&#10;&#10;Thank you for showing interest in {project_name}. We would be delighted to assist you with unit pricing, floor plans, and site visits.&#10;&#10;Best regards,&#10;{executive_name}"
                                class="w-full text-xs sm:text-sm font-mono leading-relaxed p-3.5 bg-slate-50 border border-slate-300 rounded-2xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-600 transition-all text-slate-900"></textarea>
                            <p class="text-[11px] text-slate-500">💡 WhatsApp supports <strong>*bold*</strong>, <em>_italics_</em>, ~strike~, and emojis.</p>
                        </div>
                    </div>

                    <!-- Right Column: Realistic WhatsApp Phone Mockup (5 cols on lg) -->
                    <div class="lg:col-span-5 space-y-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center">
                            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                            Live WhatsApp Customer Preview:
                        </label>
                        
                        <!-- Phone Mockup Frame -->
                        <div class="rounded-3xl border-4 border-slate-800 bg-slate-900 shadow-xl overflow-hidden p-1">
                            <!-- WhatsApp App Header -->
                            <div style="background-color: #075E54; color: #ffffff;" class="px-3.5 py-2.5 flex items-center justify-between rounded-t-2xl">
                                <div class="flex items-center space-x-2">
                                    <div class="h-7 w-7 rounded-full bg-white/20 flex items-center justify-center font-bold text-white text-xs">
                                        👤
                                    </div>
                                    <div class="leading-tight">
                                        <div id="previewRecipientName" style="color: #ffffff !important;" class="font-bold text-xs truncate max-w-[150px]">Customer</div>
                                        <div style="color: #a7f3d0 !important;" class="text-[9px] font-semibold">Online</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 text-white/80 text-xs">
                                    <span>📞</span>
                                    <span>⋮</span>
                                </div>
                            </div>

                            <!-- WhatsApp Chat Body with Wallpaper -->
                            <div style="background-color: #efeae2; min-height: 240px; max-height: 320px; background-image: radial-gradient(#d6cbbe 1.2px, transparent 1.2px); background-size: 16px 16px;" 
                                 class="p-3 overflow-y-auto rounded-b-2xl flex flex-col justify-end">
                                
                                <div class="self-center bg-white/80 backdrop-blur-xs text-[10px] text-slate-600 font-semibold px-2.5 py-0.5 rounded-md shadow-xs my-2">
                                    TODAY
                                </div>

                                <!-- Speech Bubble -->
                                <div style="background-color: #ffffff; border-radius: 12px; border-top-left-radius: 0px;" 
                                     class="p-3 shadow-md max-w-[94%] self-start space-y-1.5 border border-slate-200/60 relative">
                                    
                                    <div class="text-[10px] font-bold text-emerald-800 border-b border-emerald-100 pb-1 flex items-center justify-between">
                                        <span id="previewHeaderSender">{{ auth()->user()->company->name ?? 'Real Estate Team' }}</span>
                                        <span class="text-slate-400 font-normal">Now</span>
                                    </div>

                                    <div id="liveMessagePreviewText" class="whitespace-pre-line text-xs font-sans text-slate-900 leading-relaxed break-words">
                                        Your WhatsApp message will appear here in real-time...
                                    </div>

                                    <div class="text-right text-[9px] text-slate-400 flex items-center justify-end space-x-1 pt-1">
                                        <span id="previewTimestamp">12:00 PM</span>
                                        <span style="color: #34B7F1; font-weight: bold;">✓✓</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error / Success Feedback Alerts -->
                <div id="modalAlertError" class="hidden bg-rose-50 border border-rose-300 text-rose-800 text-xs font-bold p-3.5 rounded-xl"></div>
                <div id="modalAlertSuccess" class="hidden bg-emerald-50 border border-emerald-300 text-emerald-900 text-xs font-bold p-3.5 rounded-xl"></div>
            </div>

            <!-- Modal Sticky Footer (Always high-contrast & visible) -->
            <div class="p-4 sm:px-6 border-t border-slate-200 bg-slate-50 shrink-0 flex items-center justify-between gap-3">
                <button type="button" onclick="closeCustomDripModal()" class="btn-secondary text-xs py-2.5 px-5 font-bold cursor-pointer">
                    Cancel
                </button>
                <button type="submit" id="modalSendBtn" 
                    style="background-color: #059669 !important; background-image: linear-gradient(135deg, #059669 0%, #047857 100%) !important; color: #ffffff !important; box-shadow: 0 4px 14px 0 rgba(5, 150, 105, 0.4);"
                    class="inline-flex items-center justify-center py-2.5 px-6 rounded-xl font-bold text-xs text-white space-x-2 transition-all hover:opacity-95 cursor-pointer">
                    <svg id="modalSendIcon" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span id="modalSendText" style="color: #ffffff !important; font-weight: 800;">🚀 Send WhatsApp Message Now</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // State management for selected leads
    let selectedLeads = [];
    const projectNameDefault = "{{ e($project->name) }}";

    document.addEventListener('DOMContentLoaded', function() {
        // Status select dropdown change handler
        const selects = document.querySelectorAll('.status-select');
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';

        selects.forEach(function(sel) {
            sel.addEventListener('change', function() {
                const inquiryId = this.dataset.inquiryId;
                const status = this.value;
                const url = `/inquiries/${inquiryId}/status`;

                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status })
                }).then(res => res.json())
                .then(data => {
                    if (data && data.success) {
                        location.reload();
                    } else {
                        alert('Failed to update status');
                    }
                }).catch(() => alert('Failed to update status'));
            });
        });
    });

    // Checkbox selection logic
    function handleRowCheckboxChange() {
        const checkboxes = document.querySelectorAll('.inquiry-row-checkbox:checked');
        selectedLeads = [];

        checkboxes.forEach(cb => {
            selectedLeads.push({
                id: parseInt(cb.value),
                name: cb.dataset.name,
                phone: cb.dataset.phone,
                project: cb.dataset.project,
                executive: cb.dataset.executive
            });
        });

        updateSelectionUI();
    }

    function toggleSelectAll(masterCb) {
        const checkboxes = document.querySelectorAll('.inquiry-row-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = masterCb.checked;
        });
        handleRowCheckboxChange();
    }

    function clearAllSelections() {
        const masterCb = document.getElementById('selectAllCheckbox');
        if (masterCb) masterCb.checked = false;

        const checkboxes = document.querySelectorAll('.inquiry-row-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = false;
        });
        selectedLeads = [];
        updateSelectionUI();
    }

    function updateSelectionUI() {
        const count = selectedLeads.length;
        const banner = document.getElementById('selectionBanner');
        const bulkBtn = document.getElementById('bulkDripBtn');
        const countSpan = document.getElementById('bulkDripCount');
        const bannerCountSpan = document.getElementById('bannerSelectedCount');

        if (countSpan) countSpan.textContent = count;
        if (bannerCountSpan) bannerCountSpan.textContent = count;

        if (count > 0) {
            banner?.classList.remove('hidden');
            bulkBtn?.classList.remove('hidden');
        } else {
            banner?.classList.add('hidden');
            bulkBtn?.classList.add('hidden');
        }
    }

    // Modal popup triggers
    function openSingleDripModal(id, name, phone, project, executive) {
        selectedLeads = [{
            id: id,
            name: name,
            phone: phone,
            project: project,
            executive: executive
        }];
        populateModalData();
        openCustomDripModal();
    }

    function openBulkDripModal() {
        if (selectedLeads.length === 0) {
            alert('Please select at least one inquiry checkbox to send WhatsApp message.');
            return;
        }
        populateModalData();
        openCustomDripModal();
    }

    function populateModalData() {
        const container = document.getElementById('modalRecipientsList');
        const countBadge = document.getElementById('modalRecipientCount');
        const hiddenInput = document.getElementById('modalInquiryIdsInput');
        const previewName = document.getElementById('previewRecipientName');

        if (!container) return;

        container.innerHTML = '';
        const ids = selectedLeads.map(l => l.id);
        if (hiddenInput) hiddenInput.value = JSON.stringify(ids);
        if (countBadge) countBadge.textContent = selectedLeads.length;

        if (selectedLeads.length > 0 && previewName) {
            previewName.textContent = selectedLeads[0].name + ' (' + selectedLeads[0].phone + ')';
        }

        selectedLeads.forEach(lead => {
            const badge = document.createElement('span');
            badge.style.backgroundColor = '#d1fae5';
            badge.style.color = '#065f46';
            badge.style.borderColor = '#6ee7b7';
            badge.className = 'inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border shadow-xs';
            badge.textContent = `👤 ${lead.name} (${lead.phone})`;
            container.appendChild(badge);
        });

        // Set default starter message if textarea is empty
        const textarea = document.getElementById('modalMessageContent');
        if (textarea && !textarea.value.trim()) {
            textarea.value = "Hello {customer_name}! 👋\n\nThank you for showing interest in {project_name}. We would be delighted to assist you with unit pricing, floor plans, and site visits.\n\nBest regards,\n{executive_name}";
        }

        updateLivePreview();
    }

    function insertTag(tag) {
        const textarea = document.getElementById('modalMessageContent');
        if (!textarea) return;

        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;

        textarea.value = text.substring(0, start) + tag + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + tag.length;
        textarea.focus();

        updateLivePreview();
    }

    function updateLivePreview() {
        const textarea = document.getElementById('modalMessageContent');
        const previewElem = document.getElementById('liveMessagePreviewText');
        const charCounter = document.getElementById('charCounter');
        const timestampElem = document.getElementById('previewTimestamp');

        if (!textarea || !previewElem) return;

        const raw = textarea.value;
        if (charCounter) {
            charCounter.textContent = `${raw.length} characters`;
        }

        if (timestampElem) {
            const now = new Date();
            timestampElem.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        }

        if (!raw.trim()) {
            previewElem.textContent = 'Your WhatsApp message will appear here in real-time...';
            return;
        }

        const sampleLead = selectedLeads.length > 0 ? selectedLeads[0] : {
            name: 'Rahul Sharma',
            phone: '+91 98765 43210',
            project: projectNameDefault,
            executive: 'Sales Desk'
        };

        let compiled = raw
            .replace(/{customer_name}/g, sampleLead.name)
            .replace(/{project_name}/g, sampleLead.project || projectNameDefault)
            .replace(/{executive_name}/g, sampleLead.executive || 'Sales Desk')
            .replace(/{company_name}/g, "{{ e(auth()->user()->company->name ?? 'Real Estate Team') }}")
            .replace(/{brochure_url}/g, window.location.origin + '/brochure/demo/download')
            .replace(/{phone}/g, sampleLead.phone);

        previewElem.textContent = compiled;
    }

    function openCustomDripModal() {
        const modal = document.getElementById('customDripModal');
        const errAlert = document.getElementById('modalAlertError');
        const succAlert = document.getElementById('modalAlertSuccess');
        if (errAlert) errAlert.classList.add('hidden');
        if (succAlert) succAlert.classList.add('hidden');

        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeCustomDripModal() {
        const modal = document.getElementById('customDripModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    // Submit custom drip form via AJAX
    function submitCustomDrip(event) {
        event.preventDefault();

        const btn = document.getElementById('modalSendBtn');
        const textSpan = document.getElementById('modalSendText');
        const errAlert = document.getElementById('modalAlertError');
        const succAlert = document.getElementById('modalAlertSuccess');

        if (errAlert) errAlert.classList.add('hidden');
        if (succAlert) succAlert.classList.add('hidden');

        const messageContent = document.getElementById('modalMessageContent').value.trim();
        const inquiryIds = selectedLeads.map(l => l.id);

        if (inquiryIds.length === 0) {
            if (errAlert) {
                errAlert.textContent = 'Please select at least one inquiry recipient.';
                errAlert.classList.remove('hidden');
            }
            return;
        }

        if (!messageContent) {
            if (errAlert) {
                errAlert.textContent = 'Please provide message content.';
                errAlert.classList.remove('hidden');
            }
            return;
        }

        // Loading state
        if (btn) btn.disabled = true;
        if (textSpan) textSpan.textContent = '⏳ Dispatching WhatsApp Messages...';

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        fetch("{{ route('inquiries.send-custom-drip') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                inquiry_ids: inquiryIds,
                message_content: messageContent,
                lead_drip_step_id: null
            })
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            if (status >= 200 && status < 300 && body.success) {
                if (succAlert) {
                    succAlert.textContent = `✓ ${body.message || 'WhatsApp message dispatched successfully!'}`;
                    succAlert.classList.remove('hidden');
                }
                setTimeout(() => {
                    closeCustomDripModal();
                    location.reload();
                }, 1200);
            } else {
                if (errAlert) {
                    errAlert.textContent = body.message || 'Failed to dispatch message.';
                    errAlert.classList.remove('hidden');
                }
                if (btn) btn.disabled = false;
                if (textSpan) textSpan.textContent = '🚀 Send WhatsApp Message Now';
            }
        })
        .catch(err => {
            if (errAlert) {
                errAlert.textContent = 'Network or server error while dispatching messages.';
                errAlert.classList.remove('hidden');
            }
            if (btn) btn.disabled = false;
            if (textSpan) textSpan.textContent = '🚀 Send WhatsApp Message Now';
        });
    }
</script>
@endsection
