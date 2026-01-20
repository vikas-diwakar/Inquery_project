@extends('layouts.app')

@section('title', 'Upload Brochure')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Upload Brochure</h2>
        
        <div class="mb-6 p-4 bg-indigo-50 rounded-lg">
            <p class="text-sm font-medium text-indigo-900 mb-1">Selected Project:</p>
            <p class="text-lg font-semibold text-indigo-700">{{ $project->name }}</p>
            @if($project->location)
                <p class="text-sm text-indigo-600 mt-1">{{ $project->location }}</p>
            @endif
        </div>

        <form action="{{ route('brochures.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="brochure_file" class="block text-sm font-medium text-gray-700">Brochure File (PDF) *</label>
                    <input type="file" name="brochure_file" id="brochure_file" accept=".pdf" required class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="mt-1 text-sm text-gray-500">Maximum file size: 10MB</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('brochures.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Upload Brochure
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
