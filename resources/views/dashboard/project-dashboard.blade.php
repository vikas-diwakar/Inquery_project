@extends('layouts.app')

@section('title', $project->name . ' - Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center space-x-3 mb-1.5">
                <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:text-indigo-800 text-xs sm:text-sm font-semibold flex items-center gap-1">
                    <span>← All Projects</span>
                </a>
                <span class="text-gray-300">|</span>
                <span class="text-xs sm:text-sm text-gray-500 font-medium">Active Workspace</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ $project->name }}</h1>
            @if($project->location)
                <p class="mt-1 text-xs sm:text-sm text-slate-500 flex items-center">
                    <svg class="w-3.5 h-3.5 text-slate-400 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>{{ $project->location }}</span>
                </p>
            @endif
        </div>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <form action="{{ route('projects.clear-selection') }}" method="POST" class="flex-1 sm:flex-initial">
                @csrf
                <button type="submit" class="w-full sm:w-auto bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-xl hover:bg-slate-50 text-xs sm:text-sm font-semibold shadow-xs transition-colors cursor-pointer">
                    Change Project
                </button>
            </form>
            @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}" class="flex-1 sm:flex-initial text-center bg-indigo-50 border border-indigo-100 text-indigo-700 px-4 py-2 rounded-xl hover:bg-indigo-100 text-xs sm:text-sm font-semibold transition-colors">
                    Edit Project
                </a>
            @endcan
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 sm:gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Inquiries</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $totalInquiries }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">New Inquiries</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $newInquiries }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Booked</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $bookedInquiries }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Brochures</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $totalBrochures }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Follow-ups Card -->
        <a href="{{ route('follow-ups.index') }}" class="bg-white overflow-hidden shadow rounded-lg hover:shadow-lg transition">
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Follow-ups</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $todayFollowUps ?? 0 }}</dd>
                        </div>
                    </div>
                    @if(($todayFollowUps ?? 0) > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $todayFollowUps }}</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">0</span>
                    @endif
                </div>
                <p class="mt-3 text-sm text-gray-500">Click to view today's follow-up list</p>
            </div>
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-8">
        <a href="{{ route('inquiries.index') }}" class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-6 hover:shadow-md hover:border-indigo-300 transition-all text-center group">
            <div class="w-12 h-12 mx-auto rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 mb-2 group-hover:scale-105 transition-transform">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Inquiries</h3>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">View all inquiries</p>
        </a>

        <a href="{{ route('brochures.index') }}" class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-6 hover:shadow-md hover:border-emerald-300 transition-all text-center group">
            <div class="w-12 h-12 mx-auto rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 mb-2 group-hover:scale-105 transition-transform">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Brochures</h3>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Manage brochures</p>
        </a>

        <a href="{{ route('forms-qr.index') }}" class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-6 hover:shadow-md hover:border-purple-300 transition-all text-center group">
            <div class="w-12 h-12 mx-auto rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 mb-2 group-hover:scale-105 transition-transform">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="font-bold text-slate-900 text-xs sm:text-sm">Inquiry Form</h3>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Create inquiry form</p>
        </a>

        <a href="{{ route('forms-qr.show-inquiry-qr') }}" class="bg-white rounded-2xl border border-slate-200/80 p-4 sm:p-6 hover:shadow-md hover:border-blue-300 transition-all text-center group">
            <div class="w-12 h-12 mx-auto rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 mb-2 group-hover:scale-105 transition-transform">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
            </div>
            <h3 class="font-bold text-slate-900 text-xs sm:text-sm">QR Code</h3>
            <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">View QR code</p>
        </a>
    </div>

    <!-- Recent Inquiries -->
    <div class="bg-white shadow rounded-2xl border border-slate-200/80 overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <h3 class="text-base sm:text-lg font-bold text-slate-900">Recent Inquiries</h3>
                <a href="{{ route('inquiries.index') }}" class="text-xs sm:text-sm font-semibold text-indigo-600 hover:text-indigo-800">View all inquiries →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentInquiries as $inquiry)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $inquiry->customer_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $inquiry->phone }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($inquiry->status === 'new') bg-yellow-100 text-yellow-800
                                        @elseif($inquiry->status === 'booked') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($inquiry->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $inquiry->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No inquiries yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Analytics -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Inquiry Trend (Last 30 days)</h3>
                <div class="text-sm text-gray-500">Updated: {{ now()->format('M d, Y') }}</div>
            </div>
            <canvas id="inquiryTrendChart" height="120"></canvas>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Project Analytics</h3>
            <div class="space-y-4">
                <div>
                    <dt class="text-sm text-gray-500">Most Demanded</dt>
                    @if(!empty($topUnits))
                        <ul class="space-y-2">
                            @foreach($topUnits as $u)
                                <li class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700">{{ $u['name'] }}</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $u['percent'] }}% <span class="text-xs text-gray-500">({{ $u['count'] }})</span></span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <dd class="text-lg font-semibold text-gray-900">N/A</dd>
                    @endif
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Conversion Rate</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $conversionRate }}% <span class="text-sm text-gray-500">(Booked / Total)</span></dd>
                </div>
                <div>
                    <dt class="text-sm text-gray-500">Total Inquiries</dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $totalInquiries }}</dd>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function() {
            const ctx = document.getElementById('inquiryTrendChart').getContext('2d');
            const labels = {!! json_encode($labels) !!};
            const data = {!! json_encode($trendData) !!};

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Inquiries',
                        data: data,
                        borderColor: '#6366F1',
                        backgroundColor: 'rgba(99,102,241,0.06)',
                        fill: true,
                        tension: 0.25,
                        pointRadius: 2
                    }]
                },
                options: {
                    scales: {
                        x: { display: true },
                        y: { beginAtZero: true }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        })();
    </script>
</div>
@endsection
