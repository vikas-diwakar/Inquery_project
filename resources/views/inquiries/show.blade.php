@extends('layouts.app')

@section('title', 'Inquiry Details - ' . $inquiry->customer_name)

@section('content')
<div class="max-w-4xl mx-auto py-4 space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('inquiries.index') }}" class="inline-flex items-center space-x-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Inquiries</span>
        </a>

        <!-- Resend WhatsApp Button -->
        <form action="{{ route('inquiries.resend-whatsapp', $inquiry) }}" method="POST">
            @csrf
            <button type="submit" class="btn-secondary text-xs space-x-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span>Resend WhatsApp Brochure</span>
            </button>
        </form>
    </div>

    <!-- AI Lead Quality Intent Breakdown Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-slate-100 pb-3">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-600">AI Scoring Engine</span>
                <h3 class="text-base font-bold text-slate-900 flex items-center space-x-2">
                    <span>Lead Intent Score:</span>
                    <span class="px-2.5 py-1 rounded-full text-xs border {{ $inquiry->grade_badge['class'] }}">
                        {{ $inquiry->grade_badge['label'] }} ({{ $inquiry->lead_score ?? 0 }}/100)
                    </span>
                </h3>
            </div>

            <!-- Score Progress Bar -->
            <div class="w-full sm:w-48 bg-slate-100 rounded-full h-3 overflow-hidden border border-slate-200">
                <div class="h-full {{ $inquiry->lead_score >= 70 ? 'bg-rose-500' : ($inquiry->lead_score >= 40 ? 'bg-amber-500' : 'bg-slate-400') }} transition-all" style="width: {{ min(100, max(5, $inquiry->lead_score ?? 0)) }}%"></div>
            </div>
        </div>

        <div class="space-y-2">
            <h4 class="text-xs font-semibold text-slate-600 uppercase tracking-wider">Score Calculation Factors</h4>
            <div class="flex flex-wrap gap-2">
                @if(is_array($inquiry->score_breakdown))
                    @foreach($inquiry->score_breakdown as $factor)
                        <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700">
                            <span class="text-emerald-600">+{{ $factor['points'] }}</span>
                            <span>{{ $factor['factor'] }}</span>
                        </span>
                    @endforeach
                @else
                    <span class="text-xs text-slate-400">Score evaluated automatically upon inquiry creation.</span>
                @endif
            </div>
        </div>
    </div>

    <!-- WhatsApp Delivery Status Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center space-x-3">
            <div class="h-10 w-10 rounded-2xl {{ $inquiry->hasWhatsAppBeenSent() ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }} flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h3 class="text-sm font-bold text-slate-900">Automated WhatsApp Brochure</h3>
                    @if($inquiry->hasWhatsAppBeenSent())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Sent ✓
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                            Pending / Not Sent
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">
                    @if($inquiry->whatsapp_sent_at)
                        Dispatched on {{ $inquiry->whatsapp_sent_at->format('M d, Y h:i A') }} to {{ $inquiry->phone }}
                    @else
                        No WhatsApp brochure has been sent yet to {{ $inquiry->phone }}
                    @endif
                </p>
            </div>
        </div>

        @if($inquiry->whatsapp_last_message)
            <div class="text-xs font-mono bg-slate-50 border border-slate-200/80 p-3 rounded-xl max-w-sm overflow-hidden text-slate-700 truncate">
                "{{ Str::limit($inquiry->whatsapp_last_message, 80) }}"
            </div>
        @endif
    </div>

    <!-- Inquiry Info Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-6">
        <h2 class="text-xl font-extrabold text-slate-900">Inquiry Details</h2>
        
        <dl class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Customer Name</dt>
                <dd class="mt-1 text-sm font-bold text-slate-900">{{ $inquiry->customer_name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Phone</dt>
                <dd class="mt-1 text-sm font-bold text-slate-900">{{ $inquiry->phone }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Email</dt>
                <dd class="mt-1 text-sm text-slate-800">{{ $inquiry->email ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Project</dt>
                <dd class="mt-1 text-sm font-bold text-slate-900">{{ $inquiry->project->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Budget</dt>
                <dd class="mt-1 text-sm font-bold text-emerald-700">{{ $inquiry->budget ? '₹' . number_format($inquiry->budget) : 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Assigned Physical Unit</dt>
                <dd class="mt-1 text-sm font-bold text-indigo-700">
                    @if($inquiry->projectUnit)
                        🏢 {{ $inquiry->projectUnit->tower_name }} — {{ $inquiry->projectUnit->unit_number }} ({{ $inquiry->projectUnit->unit_type }})
                        <span class="ml-1 text-xs px-2 py-0.5 rounded-full font-bold border {{ $inquiry->projectUnit->status_badge }}">
                            {{ ucfirst(str_replace('_', ' ', $inquiry->projectUnit->status)) }}
                        </span>
                    @else
                        <span class="text-slate-400 font-normal">No physical unit assigned yet</span>
                    @endif
                </dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Customer Message</dt>
                <dd class="mt-1 text-sm text-slate-800 bg-slate-50 p-3 rounded-xl border border-slate-200/60">{{ $inquiry->message ?? 'No additional message.' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Inquiry Status</dt>
                <dd class="mt-1">
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border 
                        @if($inquiry->status === 'new') bg-amber-50 text-amber-700 border-amber-200
                        @elseif($inquiry->status === 'contacted') bg-blue-50 text-blue-700 border-blue-200
                        @elseif($inquiry->status === 'interested') bg-indigo-50 text-indigo-700 border-indigo-200
                        @elseif($inquiry->status === 'site_visit') bg-purple-50 text-purple-700 border-purple-200
                        @elseif($inquiry->status === 'booked') bg-emerald-50 text-emerald-700 border-emerald-200
                        @elseif($inquiry->status === 'lost') bg-rose-50 text-rose-700 border-rose-200
                        @else bg-slate-100 text-slate-800 border-slate-200
                        @endif">
                        {{ Str::title(str_replace('_', ' ', $inquiry->status)) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Submitted Date</dt>
                <dd class="mt-1 text-sm text-slate-600">{{ $inquiry->created_at->format('M d, Y h:i A') }}</dd>
            </div>
        </dl>
    </div>

    <!-- Update Status Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-4">
        <h2 class="text-lg font-bold text-slate-900">Update Lead Assignment & Unit Booking</h2>
        <form action="{{ route('inquiries.update', $inquiry) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="space-y-1.5">
                    <label for="status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Lead Status</label>
                    <select name="status" id="status" required class="input-field cursor-pointer font-semibold">
                        <option value="new" {{ $inquiry->status === 'new' ? 'selected' : '' }}>New Lead</option>
                        <option value="contacted" {{ $inquiry->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="interested" {{ $inquiry->status === 'interested' ? 'selected' : '' }}>Interested (Reserves Unit 🟨)</option>
                        <option value="site_visit" {{ $inquiry->status === 'site_visit' ? 'selected' : '' }}>Site Visit Scheduled (Reserves Unit 🟨)</option>
                        <option value="booked" {{ $inquiry->status === 'booked' ? 'selected' : '' }}>Booked / Closed (Marks Unit Sold 🟥)</option>
                        <option value="lost" {{ $inquiry->status === 'lost' ? 'selected' : '' }}>Lost / Uninterested</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="project_unit_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Assign Specific Inventory Unit</label>
                    <select name="project_unit_id" id="project_unit_id" class="input-field cursor-pointer font-semibold">
                        <option value="">-- Select Inventory Unit --</option>
                        @foreach($inquiry->project->units as $pUnit)
                            <option value="{{ $pUnit->id }}" {{ $inquiry->project_unit_id === $pUnit->id ? 'selected' : '' }}>
                                {{ $pUnit->tower_name }} — {{ $pUnit->unit_number }} ({{ $pUnit->unit_type }}) [{{ strtoupper($pUnit->status) }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label for="assigned_to" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Assigned Executive</label>
                    <select name="assigned_to" id="assigned_to" class="input-field cursor-pointer">
                        <option value="">-- Unassigned --</option>
                        @foreach(\App\Models\User::where('company_id', auth()->user()->company_id)->get() as $user)
                            <option value="{{ $user->id }}" {{ $inquiry->assigned_to === $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="space-y-1.5">
                <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Internal Follow-Up Notes</label>
                <textarea name="description" id="description" rows="3" class="input-field" placeholder="Add notes about conversations, site visit preferences, etc.">{{ $inquiry->description }}</textarea>
            </div>
            
            <div class="pt-2 flex justify-end">
                <button type="submit" class="btn-primary space-x-2">
                    <span>Update Status & Notes</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Automated Lead Drip Nurturing Timeline Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Automated Lead Nurturing Drip Timeline</h2>
                <p class="text-xs text-slate-500">Multi-touch scheduled WhatsApp sequence (Day 1, Day 3, Day 7, Day 14).</p>
            </div>
            <a href="{{ route('settings.drip') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Configure Drip Templates →</a>
        </div>

        <div class="space-y-3">
            @forelse($inquiry->dripLogs as $dripLog)
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between p-3.5 rounded-2xl bg-slate-50 border border-slate-200/70 gap-2">
                    <div class="flex items-center space-x-3">
                        <div class="h-9 w-9 rounded-xl bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shrink-0">
                            D{{ $dripLog->step->day_offset ?? '?' }}
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">{{ $dripLog->step->step_title ?? 'Drip Step' }}</h4>
                            <p class="text-[10px] text-slate-500">Scheduled for {{ $dripLog->scheduled_for->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $dripLog->status_badge }}">
                            {{ ucfirst($dripLog->status) }}
                        </span>
                        @if($dripLog->sent_at)
                            <span class="text-[10px] text-slate-400 font-medium">Sent on {{ $dripLog->sent_at->format('M d, h:i A') }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-4">No drip sequence logs scheduled yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Follow-up scheduler -->
    <div class="mt-6">
        @include('components.follow-up-scheduler', ['inquiry' => $inquiry])
    </div>
</div>
@endsection

