@extends('layouts.app')

@section('title', 'Add Inquiry - ' . $project->name)

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg p-6">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Add Manual Inquiry</h2>
            <p class="mt-1 text-sm text-gray-600">
                Project: <span class="font-medium">{{ $project->name }}</span>
            </p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative" role="alert">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('inquiries.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer Name *</label>
                <input
                    id="customer_name"
                    name="customer_name"
                    type="text"
                    required
                    value="{{ old('customer_name') }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Enter customer's full name"
                >
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number *</label>
                <input
                    id="phone"
                    name="phone"
                    type="tel"
                    required
                    value="{{ old('phone') }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Enter phone number"
                >
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Enter email address"
                >
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="budget" class="block text-sm font-medium text-gray-700">Budget (₹)</label>
                    <input
                        id="budget"
                        name="budget"
                        type="number"
                        step="0.01"
                        value="{{ old('budget') }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter budget"
                    >
                </div>

                <div>
                    <label for="flat_type" class="block text-sm font-medium text-gray-700">Interested Flat Type</label>
                    <input
                        id="flat_type"
                        name="flat_type"
                        type="text"
                        value="{{ old('flat_type') }}"
                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="e.g., 2BHK, 3BHK"
                    >
                </div>
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea
                    id="message"
                    name="message"
                    rows="4"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    placeholder="Enter any additional message or notes"
                >{{ old('message') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('inquiries.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Create Inquiry
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
