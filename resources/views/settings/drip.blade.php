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

            <form action="{{ route('settings.drip.process-now') }}" method="POST">
                @csrf
                <input type="hidden" name="force" value="1">
                <button type="submit" class="btn-primary text-xs space-x-2 shadow-lg shadow-indigo-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span>Test Dispatch All Pending Drips Now</span>
                </button>
            </form>
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

    <!-- Recent Drip Logs Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 overflow-hidden space-y-4 p-6 sm:p-8">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Recent Automated Drip Activity Logs</h3>
                <p class="text-xs text-slate-500">Live record of scheduled & dispatched multi-day lead nurture steps.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/80 text-slate-500 uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="p-3">Customer Lead</th>
                        <th class="p-3">Project</th>
                        <th class="p-3">Drip Step</th>
                        <th class="p-3">Scheduled For</th>
                        <th class="p-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentLogs as $log)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="p-3 font-bold text-slate-900">
                                {{ $log->inquiry->customer_name ?? 'Inquiry #' . $log->inquiry_id }}
                                <div class="text-[10px] text-slate-400 font-normal">{{ $log->inquiry->phone ?? '' }}</div>
                            </td>
                            <td class="p-3 font-semibold text-indigo-600">{{ $log->inquiry->project->name ?? 'Project' }}</td>
                            <td class="p-3 font-bold text-slate-800">{{ $log->step->step_title ?? 'Step' }}</td>
                            <td class="p-3 text-slate-500">{{ $log->scheduled_for->format('M d, Y h:i A') }}</td>
                            <td class="p-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $log->status_badge }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-400">No automated drip logs recorded yet. New inquiries will automatically enroll.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
