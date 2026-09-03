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
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="p-4">
                                <div class="font-extrabold text-slate-900 text-sm">{{ $inquiry->customer_name }}</div>
                                <div class="text-[11px] text-slate-500 font-medium">{{ $inquiry->phone }}</div>
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

                            <td class="p-4 text-right space-x-2">
                                <a href="{{ route('inquiries.show', $inquiry) }}" class="btn-secondary text-[11px] py-1 px-2.5">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-slate-400">No inquiries found matching your filters.</td>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
</script>
@endsection

