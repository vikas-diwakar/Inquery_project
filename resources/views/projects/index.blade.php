@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Projects</h1>
        <a href="{{ route('projects.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            Add New Project
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
            @forelse($projects as $project)
                <li>
                    <a href="{{ route('projects.show', $project) }}" class="block hover:bg-gray-50">
                        <div class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    @if($project->logo)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($project->logo) }}" alt="{{ $project->name }}" class="h-12 w-12 rounded-lg object-cover mr-4">
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-indigo-600 truncate">{{ $project->name }}</p>
                                        <p class="mt-1 text-sm text-gray-500">{{ $project->location }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($project->status === 'ongoing') bg-green-100 text-green-800
                                        @elseif($project->status === 'completed') bg-gray-100 text-gray-800
                                        @elseif($project->status === 'on_hold') bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('projects.edit', $project) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            @empty
                <li class="px-4 py-4 text-center text-gray-500">No projects found. <a href="{{ route('projects.create') }}" class="text-indigo-600">Create one</a></li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $projects->links() }}
    </div>
</div>
@endsection
