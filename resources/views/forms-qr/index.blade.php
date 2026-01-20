@extends('layouts.app')

@section('title', 'Forms & QR Codes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Forms & QR Codes Management</h1>
        <p class="mt-2 text-sm text-gray-600">Create and manage inquiry forms with QR codes and brochure QR codes</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Create Inquiry Form QR Card -->
        <div class="bg-white shadow rounded-lg p-6 border-2 border-indigo-200 hover:border-indigo-400 transition">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h2 class="ml-4 text-xl font-semibold text-gray-900">Create Inquiry Form QR</h2>
            </div>
            <p class="text-gray-600 mb-4">
                Generate unique QR codes for project inquiry forms. Public users can scan these QR codes to access and submit inquiry forms.
            </p>
            <a href="{{ route('forms-qr.create-inquiry-form') }}" class="inline-flex items-center bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Inquiry Form QR
            </a>
        </div>

        <!-- Brochure QR Card -->
        <div class="bg-white shadow rounded-lg p-6 border-2 border-green-200 hover:border-green-400 transition">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h2 class="ml-4 text-xl font-semibold text-gray-900">Brochure QR Codes</h2>
            </div>
            <p class="text-gray-600 mb-4">
                View and manage QR codes for project brochures. Users can scan these QR codes to download brochures directly.
            </p>
            <a href="{{ route('forms-qr.brochure-qr') }}" class="inline-flex items-center bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Brochure QR Codes
            </a>
        </div>
    </div>

    <!-- Existing Inquiry Form QR Codes -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Existing Inquiry Form QR Codes</h2>
        @if($projects->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">QR Code Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($projects as $project)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $project->location ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($project->inquiry_qr_code)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Generated
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Not Generated
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($project->inquiry_qr_code)
                                        <a href="{{ route('forms-qr.show-inquiry-qr', $project) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">View QR</a>
                                    @else
                                        <a href="{{ route('forms-qr.create-inquiry-form') }}?project={{ $project->id }}" class="text-indigo-600 hover:text-indigo-900">Generate QR</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500">No projects found. <a href="{{ route('projects.create') }}" class="text-indigo-600 hover:text-indigo-800">Create a project</a> first to generate inquiry form QR codes.</p>
        @endif
    </div>
</div>
@endsection
