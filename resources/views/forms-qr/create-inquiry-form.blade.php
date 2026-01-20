@extends('layouts.app')

@section('title', 'Create Inquiry Form QR')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Inquiry Form QR</h1>
        <p class="mt-2 text-sm text-gray-600">Select a project to generate or regenerate its inquiry form QR code</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Inquiry Form Fields</h2>
        <p class="text-sm text-gray-600 mb-6">
            The inquiry form includes the following fields that users will fill when they scan the QR code:
        </p>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <ul class="space-y-2 text-sm text-gray-700">
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <strong>Customer Name</strong> (Required)
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <strong>Phone</strong> (Required)
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <strong>Email</strong> (Optional)
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <strong>Budget</strong> (Optional)
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <strong>Flat Type</strong> (Optional - e.g., 2BHK, 3BHK)
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <strong>Message</strong> (Optional)
                </li>
            </ul>
        </div>

        <form action="{{ route('forms-qr.generate-inquiry-qr') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Select Project *
                </label>
                <select id="project_id" name="project_id" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Select a Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id') == $project->id || (request('project') == $project->id) ? 'selected' : '' }}>
                            {{ $project->name }} 
                            @if($project->location) - {{ $project->location }} @endif
                            @if($project->inquiry_qr_code) (QR Exists) @endif
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between pt-4 border-t">
                <a href="{{ route('forms-qr.index') }}" class="text-gray-600 hover:text-gray-800">
                    ← Back to Forms & QR Codes
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                    Generate QR Code
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
