@extends('layouts.app')

@section('title', 'Follow-up Reminders')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 md:mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Follow-up Reminders</h1>
        <p class="mt-1 text-sm text-slate-600">Manage and track your scheduled follow-ups</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="card p-4 border-l-4 border-red-500">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M13.477 14.89A6 6 0 112.5 5.5a1 1 0 00-2 0 8 8 0 1115.464 7.89l-2.168-2.168a1 1 0 00-1.414 1.414l2.5 2.5a1 1 0 001.414 0l2.5-2.5a1 1 0 00-1.414-1.414l-2.001 2.001z" clip-rule="evenodd"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600">Overdue</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $stats['overdue'] }}</p>
                </div>
            </div>
        </div>
        <div class="card p-4 border-l-4 border-sky-500">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-sky-600" fill="currentColor" viewBox="0 0 20 20"><path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600">Today</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $stats['today'] }}</p>
                </div>
            </div>
        </div>
        <div class="card p-4 border-l-4 border-amber-500">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2 1 1 0 100 2H3a1 1 0 00-1 1v10a1 1 0 001 1h14a1 1 0 001-1V8a1 1 0 00-1-1h2a1 1 0 100-2 2 2 0 01-2-2V5a2 2 0 01-2-2H6a2 2 0 01-2 2zm0 5a1 1 0 000 2h10a1 1 0 100-2H4z" clip-rule="evenodd"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600">Next 7 Days</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $stats['upcoming'] }}</p>
                </div>
            </div>
        </div>
        <div class="card p-4 border-l-4 border-emerald-500">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="h-5 w-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2 1 1 0 100 2H3a1 1 0 00-1 1v10a1 1 0 001 1h14a1 1 0 001-1V8a1 1 0 00-1-1h2a1 1 0 100-2 2 2 0 01-2-2V5a2 2 0 01-2-2H6a2 2 0 01-2 2zm0 5a1 1 0 000 2h10a1 1 0 100-2H4z" clip-rule="evenodd"/></svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-600">Total Pending</p>
                    <p class="text-2xl font-bold text-slate-900">{{ $stats['total_pending'] }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($overdue->count() > 0)
    <div class="card mb-6 overflow-hidden">
        <div class="px-4 py-4 sm:px-6 bg-red-500 text-white">
            <h3 class="text-lg font-semibold">Overdue Follow-ups ({{ $overdue->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="table-header"><tr><th class="px-4 sm:px-6 py-3">Customer</th><th class="px-4 sm:px-6 py-3">Project</th><th class="px-4 sm:px-6 py-3">Due Date</th><th class="px-4 sm:px-6 py-3">Assigned To</th><th class="px-4 sm:px-6 py-3">Actions</th></tr></thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @foreach($overdue as $inquiry)
                    <tr class="hover:bg-red-50/50 transition-colors">
                        <td class="px-4 sm:px-6 py-3"><a href="{{ route('inquiries.show', $inquiry) }}" class="font-medium text-primary-600 hover:text-primary-700">{{ $inquiry->customer_name }}</a></td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-slate-600">{{ $inquiry->project->name }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm font-semibold text-red-600">{{ $inquiry->next_follow_up_date->format('M d, Y H:i') }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-slate-600">{{ $inquiry->assignedUser->name ?? 'Unassigned' }}</td>
                        <td class="px-4 sm:px-6 py-3"><a href="{{ route('inquiries.show', $inquiry) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($today->count() > 0)
    <div class="card mb-6 overflow-hidden">
        <div class="px-4 py-4 sm:px-6 bg-sky-500 text-white">
            <h3 class="text-lg font-semibold">Today's Follow-ups ({{ $today->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="table-header"><tr><th class="px-4 sm:px-6 py-3">Customer</th><th class="px-4 sm:px-6 py-3">Project</th><th class="px-4 sm:px-6 py-3">Time</th><th class="px-4 sm:px-6 py-3">Assigned To</th><th class="px-4 sm:px-6 py-3">Actions</th></tr></thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @foreach($today as $inquiry)
                    <tr class="hover:bg-sky-50/50 transition-colors">
                        <td class="px-4 sm:px-6 py-3"><a href="{{ route('inquiries.show', $inquiry) }}" class="font-medium text-primary-600 hover:text-primary-700">{{ $inquiry->customer_name }}</a></td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-slate-600">{{ $inquiry->project->name }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-slate-600">{{ $inquiry->next_follow_up_date->format('H:i A') }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-slate-600">{{ $inquiry->assignedUser->name ?? 'Unassigned' }}</td>
                        <td class="px-4 sm:px-6 py-3"><a href="{{ route('inquiries.show', $inquiry) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($upcoming->count() > 0)
    <div class="card overflow-hidden">
        <div class="px-4 py-4 sm:px-6 bg-amber-500 text-white">
            <h3 class="text-lg font-semibold">Upcoming Follow-ups ({{ $upcoming->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="table-header"><tr><th class="px-4 sm:px-6 py-3">Customer</th><th class="px-4 sm:px-6 py-3">Project</th><th class="px-4 sm:px-6 py-3">Scheduled Date</th><th class="px-4 sm:px-6 py-3">Assigned To</th><th class="px-4 sm:px-6 py-3">Actions</th></tr></thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @foreach($upcoming as $inquiry)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-4 sm:px-6 py-3"><a href="{{ route('inquiries.show', $inquiry) }}" class="font-medium text-primary-600 hover:text-primary-700">{{ $inquiry->customer_name }}</a></td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-slate-600">{{ $inquiry->project->name }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-slate-600">{{ $inquiry->next_follow_up_date->format('M d, Y H:i') }}</td>
                        <td class="px-4 sm:px-6 py-3 text-sm text-slate-600">{{ $inquiry->assignedUser->name ?? 'Unassigned' }}</td>
                        <td class="px-4 sm:px-6 py-3"><a href="{{ route('inquiries.show', $inquiry) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($stats['total_pending'] === 0)
    <div class="card p-12 text-center">
        <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h3 class="text-lg font-semibold text-slate-900">No pending follow-ups</h3>
        <p class="mt-2 text-sm text-slate-600">Great! All follow-ups are up to date.</p>
    </div>
    @endif
</div>
@endsection
