@extends('layouts.app')

@section('title', 'Inquiry Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('inquiries.index') }}" class="text-indigo-600 hover:text-indigo-800">← Back to Inquiries</a>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Inquiry Details</h2>
        
        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
            <div>
                <dt class="text-sm font-medium text-gray-500">Customer Name</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->customer_name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Phone</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->phone }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Email</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->email ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Project</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->project->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Budget</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->budget ? '₹' . number_format($inquiry->budget) : 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Unit/Property Type</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->selectedUnitOption ? $inquiry->selectedUnitOption->option_name : 'N/A' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Message</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->message ?? 'N/A' }}</dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Description/Notes</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->description ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        @if($inquiry->status === 'new') bg-yellow-100 text-yellow-800
                        @elseif($inquiry->status === 'contacted') bg-blue-100 text-blue-800
                        @elseif($inquiry->status === 'interested') bg-indigo-100 text-indigo-800
                        @elseif($inquiry->status === 'site_visit') bg-purple-100 text-purple-800
                        @elseif($inquiry->status === 'booked') bg-green-100 text-green-800
                        @elseif($inquiry->status === 'lost') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ Str::title(str_replace('_', ' ', $inquiry->status)) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Submitted Date</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $inquiry->created_at->format('M d, Y h:i A') }}</dd>
            </div>
        </dl>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Update Status</h2>
        <form action="{{ route('inquiries.update', $inquiry) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="new" {{ $inquiry->status === 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ $inquiry->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="interested" {{ $inquiry->status === 'interested' ? 'selected' : '' }}>Interested</option>
                        <option value="site_visit" {{ $inquiry->status === 'site_visit' ? 'selected' : '' }}>Site Visit</option>
                        <option value="booked" {{ $inquiry->status === 'booked' ? 'selected' : '' }}>Booked</option>
                        <option value="lost" {{ $inquiry->status === 'lost' ? 'selected' : '' }}>Lost</option>
                    </select>
                </div>
                <div>
                    <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assign To</label>
                    <select name="assigned_to" id="assigned_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">Unassigned</option>
                        @foreach(\App\Models\User::where('company_id', auth()->user()->company_id)->get() as $user)
                            <option value="{{ $user->id }}" {{ $inquiry->assigned_to === $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="mt-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description/Notes</label>
                <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Add notes about this inquiry...">{{ $inquiry->description }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Add any additional notes or follow-up information about this inquiry.</p>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Update Status
                </button>
            </div>
        </form>
    </div>

    {{-- Follow-up scheduler (schedule or view follow-up details) --}}
    <div class="mt-6">
        @include('components.follow-up-scheduler', ['inquiry' => $inquiry])
    </div>
</div>
@endsection
