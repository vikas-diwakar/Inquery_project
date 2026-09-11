@extends('layouts.app')

@section('title', 'Automated Lead Drip Sequences - PropDrip')

@section('content')
<div class="max-w-7xl mx-auto py-4 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-full border border-indigo-200/80">Lead Nurturing Automation</span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1.5">Automated Lead Drip Workflows</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Prevent leads from going cold with automated multi-day WhatsApp messages (Day 1, Day 3, Day 7, Day 14).</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('settings.drip.enroll-past') }}" method="POST">
                @csrf
                <button type="submit" class="btn-secondary text-xs space-x-2">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Enroll All Past Leads</span>
                </button>
            </form>

            <button type="button" onclick="openModal('selectUsersDripModal')" class="btn-primary text-xs space-x-2 shadow-lg shadow-indigo-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Dispatch Selected Users Drips</span>
                @if($stats['pending_drips'] > 0)
                    <span class="ml-1 px-1.5 py-0.5 rounded-full text-[10px] bg-white/20 font-bold">{{ $stats['pending_drips'] }}</span>
                @endif
            </button>
        </div>
    </div>

    <!-- Drip Sequence Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center space-x-4">
            <div class="h-12 w-12 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-extrabold text-lg">
                {{ $stats['total_steps'] }}
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Sequence Steps</p>
                <p class="text-sm font-bold text-slate-900">Total Configured</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-emerald-200/80 shadow-sm flex items-center space-x-4">
            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center font-extrabold text-lg">
                {{ $stats['active_steps'] }}
            </div>
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Active Steps ⚡</p>
                <p class="text-sm font-bold text-slate-900">Live Workflows</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-amber-200/80 shadow-sm flex items-center space-x-4">
            <div class="h-12 w-12 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center font-extrabold text-lg">
                {{ $stats['pending_drips'] }}
            </div>
            <div>
                <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Scheduled ⏳</p>
                <p class="text-sm font-bold text-slate-900">Pending Messages</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-purple-200/80 shadow-sm flex items-center space-x-4">
            <div class="h-12 w-12 rounded-xl bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center font-extrabold text-lg">
                {{ $stats['sent_drips'] }}
            </div>
            <div>
                <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Delivered ✓</p>
                <p class="text-sm font-bold text-slate-900">Sent Drips</p>
            </div>
        </div>
    </div>

    <!-- Timeline Sequence Cards Grid -->
    <div class="space-y-6">
        <h2 class="text-lg font-extrabold text-slate-900 flex items-center">
            <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Drip Sequence Timeline (3–6 Month Nurturing)</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($steps as $step)
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 space-y-4 hover:border-indigo-300 transition-all flex flex-col justify-between">
                    <form action="{{ route('settings.drip.store') }}" method="POST" class="space-y-4 flex-1 flex flex-col justify-between">
                        @csrf
                        <input type="hidden" name="day_offset" value="{{ $step->day_offset }}">
                        <input type="hidden" name="channel" value="whatsapp">

                        <div class="space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="h-8 w-8 rounded-xl bg-indigo-600 text-white font-extrabold text-xs flex items-center justify-center shadow-md shadow-indigo-500/20">
                                        D{{ $step->day_offset }}
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">Day {{ $step->day_offset }} Drip Step</h3>
                                        <p class="text-[10px] text-slate-400 font-semibold">Scheduled {{ $step->day_offset }} days after lead creation</p>
                                    </div>
                                </div>

                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" {{ $step->is_active ? 'checked' : '' }} onchange="this.form.submit()" class="sr-only peer">
                                    <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider">Step Title</label>
                                <input type="text" name="step_title" value="{{ old('step_title', $step->step_title) }}" required class="input-field py-2 text-xs font-bold">
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[11px] font-semibold text-slate-600 uppercase tracking-wider">WhatsApp Message Template</label>
                                <textarea name="message_template" rows="4" required class="input-field text-xs leading-relaxed font-mono">{{ old('message_template', $step->message_template) }}</textarea>
                            </div>

                            <!-- Merge Tag Helper Pills -->
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                <span class="text-[10px] bg-slate-100 text-slate-600 font-mono px-2 py-0.5 rounded-md border border-slate-200">{customer_name}</span>
                                <span class="text-[10px] bg-slate-100 text-slate-600 font-mono px-2 py-0.5 rounded-md border border-slate-200">{project_name}</span>
                                <span class="text-[10px] bg-slate-100 text-slate-600 font-mono px-2 py-0.5 rounded-md border border-slate-200">{brochure_url}</span>
                                <span class="text-[10px] bg-slate-100 text-slate-600 font-mono px-2 py-0.5 rounded-md border border-slate-200">{executive_name}</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100 flex justify-end">
                            <button type="submit" class="btn-secondary text-xs py-1.5 px-3 space-x-1">
                                <span>Save Step Template</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Automated Drip Activity Logs & Queue -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden space-y-4 p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900 flex items-center space-x-2">
                    <span>Automated Drip Activity Logs & Queue</span>
                    @if($stats['pending_drips'] > 0)
                        <span class="text-xs bg-amber-50 text-amber-700 border border-amber-200 px-2 py-0.5 rounded-full font-bold">
                            {{ $stats['pending_drips'] }} Pending
                        </span>
                    @endif
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Select specific users with checkboxes below to dispatch scheduled WhatsApp drips on-demand.</p>
            </div>

            <!-- Table Selection Actions -->
            <div id="tableBulkActions" class="flex items-center space-x-2">
                <span id="tableSelectedCountBadge" class="hidden text-xs font-bold text-indigo-700 bg-indigo-50 border border-indigo-200 px-3 py-1.5 rounded-xl">
                    0 users selected
                </span>
                <button type="submit" form="tableBulkDispatchForm" id="tableDispatchBtn" class="hidden btn-primary text-xs py-1.5 px-3.5 space-x-1.5 shadow-md shadow-indigo-500/20">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Dispatch Selected</span>
                </button>
                <button type="button" id="tableDiscardBtn" onclick="submitTableBulkDiscard()" class="hidden text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 py-1.5 px-3 rounded-xl transition-all flex items-center space-x-1.5 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Discard Selected</span>
                </button>
            </div>
        </div>

        <form id="tableBulkDispatchForm" action="{{ route('settings.drip.process-selected') }}" method="POST">
            @csrf
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="p-3 w-10 text-center">
                                <input type="checkbox" id="selectAllTableCheckbox" onchange="toggleSelectAllTable(this)" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer" title="Select All Pending Users">
                            </th>
                            <th class="p-3">Customer Lead</th>
                            <th class="p-3">Project</th>
                            <th class="p-3">Drip Step</th>
                            <th class="p-3">Scheduled For</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentLogs as $log)
                            <tr class="hover:bg-slate-50/60 transition-colors {{ $log->status === 'pending' ? 'bg-amber-50/20' : '' }}">
                                <td class="p-3 text-center">
                                    @if($log->status === 'pending' || $log->status === 'failed')
                                        <input type="checkbox" name="selected_drip_ids[]" value="{{ $log->id }}" onchange="updateTableSelectionCount()" class="table-drip-checkbox h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    @else
                                        <span class="text-slate-300 text-xs">•</span>
                                    @endif
                                </td>
                                <td class="p-3 font-bold text-slate-900">
                                    {{ $log->inquiry->customer_name ?? 'Inquiry #' . $log->inquiry_id }}
                                    <div class="text-[10px] text-slate-400 font-normal">{{ $log->inquiry->phone ?? '' }}</div>
                                </td>
                                <td class="p-3 font-semibold text-indigo-600">{{ $log->inquiry->project->name ?? 'Project' }}</td>
                                <td class="p-3 font-bold text-slate-800">
                                    <span class="inline-flex items-center space-x-1">
                                        <span class="h-4 w-4 rounded-md bg-indigo-100 text-indigo-700 text-[10px] font-extrabold flex items-center justify-center shrink-0">
                                             D{{ $log->step->day_offset ?? '?' }}
                                        </span>
                                        <span>{{ $log->step->step_title ?? 'Drip Step' }}</span>
                                    </span>
                                </td>
                                <td class="p-3 text-slate-500">
                                    {{ $log->scheduled_for->format('M d, Y h:i A') }}
                                    @if($log->status === 'pending' && $log->scheduled_for->isPast())
                                        <span class="text-[10px] font-bold text-amber-600 ml-1">(Due Now)</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $log->status_badge }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    @if($log->status === 'pending' || $log->status === 'failed')
                                        <div class="inline-flex items-center justify-end space-x-1.5">
                                            <button type="button" onclick="dispatchSingleLog({{ $log->id }}, '{{ addslashes($log->inquiry->customer_name ?? 'Customer') }}')" 
                                                class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200/80 px-2.5 py-1 rounded-lg transition-colors inline-flex items-center space-x-1" title="Dispatch this user's drip message now">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                                <span>Dispatch</span>
                                            </button>
                                            <button type="button" onclick="discardSingleLog({{ $log->id }}, '{{ addslashes($log->inquiry->customer_name ?? 'Customer') }}')" 
                                                class="text-xs font-bold text-rose-600 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 px-2.5 py-1 rounded-lg transition-colors inline-flex items-center space-x-1" title="Discard this drip and remove it from queue">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Discard</span>
                                            </button>
                                        </div>
                                    @elseif($log->status === 'sent')
                                        <span class="text-[11px] text-emerald-600 font-bold inline-flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>Sent {{ $log->sent_at ? $log->sent_at->diffForHumans() : '' }}</span>
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Discarded</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    No pending automated drips in queue. Click <strong>"Enroll All Past Leads"</strong> to schedule sequences for existing inquiries.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Select Users to Dispatch Drips -->
<div id="selectUsersDripModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden overflow-y-auto" style="display: none;">
    <div class="min-h-full flex items-center justify-center p-4 text-center">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-2xl w-full p-6 sm:p-8 space-y-5 text-left relative m-auto transform transition-all">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 flex items-center space-x-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Dispatch Selected Users Drips</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Select the specific customer leads to send scheduled WhatsApp drip messages to right now.</p>
                </div>
                <button type="button" onclick="closeModal('selectUsersDripModal')" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Search & Bulk Selection Toolbar -->
            <div class="space-y-3">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input type="text" id="modalUserSearch" onkeyup="filterModalUsers()" placeholder="Search user by customer name, phone, or project..." class="input-field pl-9 py-2 text-xs">
                </div>

                <div class="flex items-center justify-between text-xs">
                    <div class="space-x-2">
                        <button type="button" onclick="setModalSelectAll(true)" class="text-indigo-600 hover:text-indigo-700 font-bold">Select All ({{ $pendingLogs->count() }})</button>
                        <span class="text-slate-300">•</span>
                        <button type="button" onclick="setModalSelectAll(false)" class="text-slate-500 hover:text-slate-700 font-medium">Deselect All</button>
                    </div>
                    <span id="modalSelectedCounterBadge" class="font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">
                        <span id="modalSelectedCount">0</span> of {{ $pendingLogs->count() }} selected
                    </span>
                </div>
            </div>

            <!-- Scrollable Pending Users List -->
            <form id="modalDispatchForm" action="{{ route('settings.drip.process-selected') }}" method="POST">
                @csrf
                <div class="max-h-72 overflow-y-auto space-y-2 pr-1 divide-y divide-slate-100 border border-slate-200/70 rounded-2xl p-2 bg-slate-50/50">
                    @forelse($pendingLogs as $pendingLog)
                        <label class="modal-user-item flex items-center justify-between p-3 rounded-xl hover:bg-white border border-transparent hover:border-slate-200 transition-all cursor-pointer group"
                            data-search="{{ strtolower(($pendingLog->inquiry->customer_name ?? '') . ' ' . ($pendingLog->inquiry->phone ?? '') . ' ' . ($pendingLog->inquiry->project->name ?? '') . ' ' . ($pendingLog->step->step_title ?? '')) }}">
                            <div class="flex items-center space-x-3">
                                <input type="checkbox" name="selected_drip_ids[]" value="{{ $pendingLog->id }}" onchange="updateModalCount()" 
                                    class="modal-drip-checkbox h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                <div>
                                    <div class="font-bold text-xs text-slate-900 group-hover:text-indigo-600 transition-colors">
                                        {{ $pendingLog->inquiry->customer_name ?? 'Inquiry #' . $pendingLog->inquiry_id }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 flex items-center space-x-2 mt-0.5">
                                        <span>📱 {{ $pendingLog->inquiry->phone ?? 'No Phone' }}</span>
                                        <span>•</span>
                                        <span class="text-indigo-600 font-semibold">{{ $pendingLog->inquiry->project->name ?? 'Project' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    D{{ $pendingLog->step->day_offset ?? '1' }}: {{ Str::limit($pendingLog->step->step_title ?? 'Drip', 25) }}
                                </span>
                                <div class="text-[10px] text-slate-400 mt-1">
                                    {{ $pendingLog->scheduled_for->format('M d, h:i A') }}
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-8 space-y-2">
                            <p class="text-xs text-slate-500 font-medium">No users currently waiting in the pending drip queue.</p>
                            <p class="text-[11px] text-slate-400">Click <strong>"Enroll All Past Leads"</strong> to schedule automated drip sequences for existing leads.</p>
                        </div>
                    @endforelse
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-slate-200 flex items-center justify-between gap-3">
                <button type="button" onclick="closeModal('selectUsersDripModal')" class="btn-secondary text-xs py-2 px-4">Cancel</button>

                <div class="flex items-center space-x-2">
                    <button type="button" onclick="submitModalDiscard()" id="modalDiscardSubmitBtn" disabled
                        class="text-xs font-bold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 py-2.5 px-4 rounded-xl opacity-50 cursor-not-allowed transition-all flex items-center space-x-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Discard Selected</span>
                    </button>

                    <button type="submit" form="modalDispatchForm" id="modalDispatchSubmitBtn" disabled
                        class="btn-primary text-xs py-2.5 px-5 space-x-1.5 opacity-50 cursor-not-allowed transition-all font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Dispatch Selected Users</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Single Dispatch Form -->
<form id="singleDispatchForm" method="POST" class="hidden">
    @csrf
</form>

<!-- Hidden Single Discard Form -->
<form id="singleDiscardForm" method="POST" class="hidden">
    @csrf
</form>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'block';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    // Modal Users Search Filter
    function filterModalUsers() {
        const query = document.getElementById('modalUserSearch').value.toLowerCase().trim();
        document.querySelectorAll('.modal-user-item').forEach(item => {
            const data = item.getAttribute('data-search') || '';
            if (!query || data.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Modal Select All / Deselect All
    function setModalSelectAll(checked) {
        document.querySelectorAll('.modal-drip-checkbox').forEach(cb => {
            // Only toggle visible items if search filter is active
            const parent = cb.closest('.modal-user-item');
            if (!parent || parent.style.display !== 'none') {
                cb.checked = checked;
            }
        });
        updateModalCount();
    }

    // Update Modal Selected Counter & Button State
    function updateModalCount() {
        const count = document.querySelectorAll('.modal-drip-checkbox:checked').length;
        document.getElementById('modalSelectedCount').textContent = count;
        const submitBtn = document.getElementById('modalDispatchSubmitBtn');
        const discardBtn = document.getElementById('modalDiscardSubmitBtn');

        if (count > 0) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            submitBtn.querySelector('span').textContent = 'Dispatch to ' + count + ' Selected User' + (count > 1 ? 's' : '');

            if (discardBtn) {
                discardBtn.disabled = false;
                discardBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                discardBtn.querySelector('span').textContent = 'Discard ' + count + ' Selected';
            }
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.querySelector('span').textContent = 'Dispatch to Selected Users';

            if (discardBtn) {
                discardBtn.disabled = true;
                discardBtn.classList.add('opacity-50', 'cursor-not-allowed');
                discardBtn.querySelector('span').textContent = 'Discard Selected';
            }
        }
    }

    // Modal Discard Action
    function submitModalDiscard() {
        const checked = document.querySelectorAll('.modal-drip-checkbox:checked');
        if (checked.length === 0) return;

        const confirmMsg = 'Discard ' + checked.length + ' selected drip(s)? They will be removed from the pending list.';
        if (typeof showConfirmationModal === 'function') {
            showConfirmationModal(
                'Discard Selected Drips',
                confirmMsg,
                function() {
                    const form = document.getElementById('modalDispatchForm');
                    form.action = '{{ route("settings.drip.discard-selected") }}';
                    form.submit();
                },
                {
                    confirmText: 'Yes, Discard Them',
                    btnClass: 'btn-danger'
                }
            );
        } else {
            if (confirm(confirmMsg)) {
                const form = document.getElementById('modalDispatchForm');
                form.action = '{{ route("settings.drip.discard-selected") }}';
                form.submit();
            }
        }
    }

    // Table Select All Checkbox
    function toggleSelectAllTable(masterCb) {
        document.querySelectorAll('.table-drip-checkbox').forEach(cb => {
            cb.checked = masterCb.checked;
        });
        updateTableSelectionCount();
    }

    // Update Table Selection Counter & Buttons
    function updateTableSelectionCount() {
        const checked = document.querySelectorAll('.table-drip-checkbox:checked');
        const count = checked.length;
        const badge = document.getElementById('tableSelectedCountBadge');
        const btn = document.getElementById('tableDispatchBtn');
        const discardBtn = document.getElementById('tableDiscardBtn');
        const masterCb = document.getElementById('selectAllTableCheckbox');

        if (count > 0) {
            badge.classList.remove('hidden');
            badge.textContent = count + ' user' + (count > 1 ? 's' : '') + ' selected';
            btn.classList.remove('hidden');
            btn.querySelector('span').textContent = 'Dispatch ' + count + ' Selected';
            if (discardBtn) {
                discardBtn.classList.remove('hidden');
                discardBtn.querySelector('span').textContent = 'Discard ' + count + ' Selected';
            }
        } else {
            badge.classList.add('hidden');
            btn.classList.add('hidden');
            if (discardBtn) discardBtn.classList.add('hidden');
            if (masterCb) masterCb.checked = false;
        }
    }

    // Table Bulk Discard Action
    function submitTableBulkDiscard() {
        const checked = document.querySelectorAll('.table-drip-checkbox:checked');
        if (checked.length === 0) return;

        const confirmMsg = 'Discard ' + checked.length + ' selected drip(s)? They will be removed from the pending list.';
        if (typeof showConfirmationModal === 'function') {
            showConfirmationModal(
                'Discard Selected Drips',
                confirmMsg,
                function() {
                    const form = document.getElementById('tableBulkDispatchForm');
                    form.action = '{{ route("settings.drip.discard-selected") }}';
                    form.submit();
                },
                {
                    confirmText: 'Yes, Discard Them',
                    btnClass: 'btn-danger'
                }
            );
        } else {
            if (confirm(confirmMsg)) {
                const form = document.getElementById('tableBulkDispatchForm');
                form.action = '{{ route("settings.drip.discard-selected") }}';
                form.submit();
            }
        }
    }

    // Dispatch Single User Log
    function dispatchSingleLog(logId, customerName) {
        if (typeof showConfirmationModal === 'function') {
            showConfirmationModal(
                'Dispatch Drip Message',
                'Dispatch scheduled WhatsApp drip message now to ' + customerName + '?',
                function() {
                    const form = document.getElementById('singleDispatchForm');
                    form.action = '/settings/drip/' + logId + '/process-single';
                    form.submit();
                },
                {
                    confirmText: 'Yes, Dispatch Now',
                    btnClass: 'btn-primary'
                }
            );
        } else {
            if (confirm('Dispatch scheduled WhatsApp drip message now to ' + customerName + '?')) {
                const form = document.getElementById('singleDispatchForm');
                form.action = '/settings/drip/' + logId + '/process-single';
                form.submit();
            }
        }
    }

    // Discard Single User Log
    function discardSingleLog(logId, customerName) {
        const confirmMsg = 'Discard scheduled drip message for ' + customerName + '? This will remove it from the pending list.';
        if (typeof showConfirmationModal === 'function') {
            showConfirmationModal(
                'Discard Drip',
                confirmMsg,
                function() {
                    const form = document.getElementById('singleDiscardForm');
                    form.action = '/settings/drip/' + logId + '/discard';
                    form.submit();
                },
                {
                    confirmText: 'Yes, Discard',
                    btnClass: 'btn-danger'
                }
            );
        } else {
            if (confirm(confirmMsg)) {
                const form = document.getElementById('singleDiscardForm');
                form.action = '/settings/drip/' + logId + '/discard';
                form.submit();
            }
        }
    }

    // Close modal on click outside (backdrop)
    const dripModal = document.getElementById('selectUsersDripModal');
    if (dripModal) {
        dripModal.addEventListener('click', function(e) {
            if (e.target === this || e.target.classList.contains('min-h-full')) {
                closeModal('selectUsersDripModal');
            }
        });
    }

    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('selectUsersDripModal');
        }
    });
</script>
@endsection
