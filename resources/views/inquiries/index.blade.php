@extends('layouts.app')

@section('title', 'Inquiries')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Inquiries</h1>
            <p class="mt-1 text-sm text-gray-600">Project: <strong>{{ $project->name }}</strong></p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('inquiries.export', request()->query()) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                📊 Export to Excel
            </a>
            <a href="{{ route('inquiries.create') }}" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                + Add Inquiry
            </a>
            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800 py-2">
                ← Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <form method="GET" action="{{ route('inquiries.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Name, Phone, Email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
                    <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">All Status</option>
                    <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                    <option value="interested" {{ request('status') === 'interested' ? 'selected' : '' }}>Interested</option>
                    <option value="site_visit" {{ request('status') === 'site_visit' ? 'selected' : '' }}>Site Visit</option>
                    <option value="booked" {{ request('status') === 'booked' ? 'selected' : '' }}>Booked</option>
                    <option value="lost" {{ request('status') === 'lost' ? 'selected' : '' }}>Lost</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700">To Date</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            </div>
            <div class="md:col-span-4 flex justify-end space-x-2">
                <a href="{{ route('inquiries.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Clear</a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">Filter</button>
            </div>
        </form>
    </div>

    <!-- Inquiries Table -->
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit/Property Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($inquiries as $inquiry)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $inquiry->customer_name }}</div>
                                <div class="text-sm text-gray-500">{{ $inquiry->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $inquiry->phone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $inquiry->selectedUnitOption ? $inquiry->selectedUnitOption->option_name : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $inquiry->budget ? '₹' . number_format($inquiry->budget) : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select class="status-select mt-1 block rounded-md border border-gray-200 text-xs font-semibold px-2 py-1" data-inquiry-id="{{ $inquiry->id }}">
                                    <option value="new" {{ $inquiry->status === 'new' ? 'selected' : '' }}>New</option>
                                    <option value="contacted" {{ $inquiry->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                    <option value="interested" {{ $inquiry->status === 'interested' ? 'selected' : '' }}>Interested</option>
                                    <option value="site_visit" {{ $inquiry->status === 'site_visit' ? 'selected' : '' }}>Site Visit</option>
                                    <option value="booked" {{ $inquiry->status === 'booked' ? 'selected' : '' }}>Booked</option>
                                    <option value="lost" {{ $inquiry->status === 'lost' ? 'selected' : '' }}>Lost</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $inquiry->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <a href="{{ route('inquiries.show', $inquiry) }}" class="text-indigo-600 hover:text-indigo-900">View</a>

                                {{-- Quick follow-up schedule buttons (allow scheduling from listing) --}}
                                <div class="inline-flex items-center space-x-1">
                                    @foreach(['Today' => 0, 'Tomorrow' => 1, '3 Days' => 3] as $label => $days)
                                        <form action="{{ route('follow-ups.store', $inquiry) }}" method="POST" style="display:inline;">
                                            @csrf
                                            <input type="hidden" name="follow_up_date" value="{{ now()->addDays($days)->setHour(10)->setMinute(0)->format('Y-m-d H:i:s') }}">
                                            <button type="submit" title="Schedule: {{ $label }}" class="px-2 py-1 text-xs bg-gray-100 rounded border hover:bg-gray-200">{{ $label }}</button>
                                        </form>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No inquiries found for this project</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $inquiries->links() }}
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('.status-select');
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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
                        // simple: reload to reflect updated styles/filters
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
