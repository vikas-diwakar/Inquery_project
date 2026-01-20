@extends('layouts.app')

@section('title', 'Select Project')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Select a Project</h1>
            <p class="mt-2 text-sm text-gray-600">Choose a project to access its dashboard and manage inquiries, brochures, and QR codes.</p>
        </div>
        <a href="{{ route('projects.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            + Create New Project
        </a>
    </div>

    @if($projects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projects as $project)
                <div class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition cursor-pointer border-2 border-transparent hover:border-indigo-500" onclick="window.location.href='{{ route('projects.select', $project) }}'">
                    <div class="flex items-start justify-between mb-4">
                        @if($project->logo)
                            <img src="{{ Storage::url($project->logo) }}" alt="{{ $project->name }}" class="h-16 w-16 rounded-lg object-cover">
                        @else
                            <div class="h-16 w-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        @endif
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($project->status === 'ongoing') bg-green-100 text-green-800
                            @elseif($project->status === 'completed') bg-gray-100 text-gray-800
                            @elseif($project->status === 'on_hold') bg-yellow-100 text-yellow-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $project->name }}</h3>
                    @if($project->location)
                        <p class="text-sm text-gray-600 mb-4">{{ $project->location }}</p>
                    @endif
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                        <span>{{ $project->inquiries->count() }} Inquiries</span>
                        <span>{{ $project->brochures->count() }} Brochures</span>
                    </div>
                    <a href="{{ route('projects.select', $project) }}" class="block w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-center">
                        Select Project
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white shadow rounded-lg p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No projects</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new project.</p>
            <div class="mt-6">
                <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Project
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
