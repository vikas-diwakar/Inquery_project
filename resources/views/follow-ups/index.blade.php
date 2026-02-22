<!-- follow-ups/index.blade.php - Follow-up Dashboard -->
@extends('layouts.app')

@section('title', 'Follow-up Reminders')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-red-50 dark:bg-red-900 border-l-4 border-red-500 p-4 rounded">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 112.5 5.5a1 1 0 00-2 0 8 8 0 1115.464 7.89l-2.168-2.168a1 1 0 00-1.414 1.414l2.5 2.5a1 1 0 001.414 0l2.5-2.5a1 1 0 00-1.414-1.414l-2.001 2.001z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-red-700 dark:text-red-300">Overdue</p>
                            <p class="text-2xl font-semibold text-red-900 dark:text-red-100">{{ $stats['overdue'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500 p-4 rounded">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.3A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Today</p>
                            <p class="text-2xl font-semibold text-blue-900 dark:text-blue-100">{{ $stats['today'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-500 p-4 rounded">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2 1 1 0 100 2H3a1 1 0 00-1 1v10a1 1 0 001 1h14a1 1 0 001-1V8a1 1 0 00-1-1h2a1 1 0 100-2 2 2 0 01-2-2V5a2 2 0 01-2-2H6a2 2 0 01-2 2zm0 5a1 1 0 000 2h10a1 1 0 100-2H4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-yellow-700 dark:text-yellow-300">Next 7 Days</p>
                            <p class="text-2xl font-semibold text-yellow-900 dark:text-yellow-100">{{ $stats['upcoming'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 dark:bg-green-900 border-l-4 border-green-500 p-4 rounded">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2 1 1 0 100 2H3a1 1 0 00-1 1v10a1 1 0 001 1h14a1 1 0 001-1V8a1 1 0 00-1-1h2a1 1 0 100-2 2 2 0 01-2-2V5a2 2 0 01-2-2H6a2 2 0 01-2 2zm0 5a1 1 0 000 2h10a1 1 0 100-2H4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-green-700 dark:text-green-300">Total Pending</p>
                            <p class="text-2xl font-semibold text-green-900 dark:text-green-100">{{ $stats['total_pending'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overdue Follow-ups -->
            @if($overdue->count() > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 bg-red-500 text-white">
                    <h3 class="text-lg font-medium">⚠️ Overdue Follow-ups ({{ $overdue->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Customer</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Project</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Due Date</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Assigned To</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($overdue as $inquiry)
                            <tr class="bg-red-50 dark:bg-red-900 hover:bg-red-100 dark:hover:bg-red-800">
                                <td class="px-4 py-3">
                                    <a href="{{ route('inquiries.show', $inquiry) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                        {{ $inquiry->customer_name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $inquiry->project->name }}</td>
                                <td class="px-4 py-3 text-sm text-red-600 font-semibold">{{ $inquiry->next_follow_up_date->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inquiry->assignedUser->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('inquiries.show', $inquiry) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">View</a>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Today's Follow-ups -->
            @if($today->count() > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="px-4 py-5 sm:px-6 bg-blue-500 text-white">
                    <h3 class="text-lg font-medium">📅 Today's Follow-ups ({{ $today->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Customer</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Project</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Time</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Assigned To</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($today as $inquiry)
                            <tr class="bg-blue-50 dark:bg-blue-900 hover:bg-blue-100 dark:hover:bg-blue-800">
                                <td class="px-4 py-3">
                                    <a href="{{ route('inquiries.show', $inquiry) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                        {{ $inquiry->customer_name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $inquiry->project->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inquiry->next_follow_up_date->format('H:i A') }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inquiry->assignedUser->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('inquiries.show', $inquiry) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">View</a>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Upcoming Follow-ups -->
            @if($upcoming->count() > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-yellow-500 text-white">
                    <h3 class="text-lg font-medium">🗓️ Upcoming Follow-ups ({{ $upcoming->count() }})</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Customer</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Project</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Scheduled Date</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Assigned To</th>
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-900 dark:text-gray-100">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($upcoming as $inquiry)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-4 py-3">
                                    <a href="{{ route('inquiries.show', $inquiry) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                        {{ $inquiry->customer_name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $inquiry->project->name }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inquiry->next_follow_up_date->format('M d, Y H:i') }}</td>
                                <td class="px-4 py-3 text-sm">{{ $inquiry->assignedUser->name ?? 'Unassigned' }}</td>
                                <td class="px-4 py-3 text-sm">
                                    <a href="{{ route('inquiries.show', $inquiry) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400">View</a>
                                </td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            @if($stats['total_pending'] === 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-gray-100">No pending follow-ups</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Great! All follow-ups are up to date.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection
