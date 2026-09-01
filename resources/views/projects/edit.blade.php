@extends('layouts.app')

@section('title', 'Edit Project')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('projects.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">← Back to Projects</a>
    </div>
    <div class="card p-6">
        <h2 class="text-xl font-bold text-slate-900 mb-6">Edit Project</h2>
        
        <form action="{{ route('projects.update', $project) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Project Name *</label>
                    <input type="text" name="name" id="name" required value="{{ old('name', $project->name) }}" class="input-field border px-3 py-2.5 mt-1">
                </div>
                
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $project->location) }}" class="input-field border px-3 py-2.5 mt-1">
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" class="input-field border px-3 py-2.5 mt-1">{{ old('description', $project->description) }}</textarea>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" class="input-field border px-3 py-2.5 mt-1">
                    </div>
                    
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                        <select name="status" id="status" required class="input-field border px-3 py-2.5 mt-1">
                            <option value="planning" {{ old('status', $project->status) === 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="ongoing" {{ old('status', $project->status) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ old('status', $project->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="on_hold" {{ old('status', $project->status) === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                    </div>
                </div>
                
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700">Project Logo</label>
                    @if($project->logo)
                        <div class="mt-2 mb-2">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($project->logo) }}" alt="{{ $project->name }}" class="h-20 w-20 rounded-xl object-cover border border-slate-200">
                        </div>
                    @endif
                    <input type="file" name="logo" id="logo" accept="image/*" class="block w-full text-sm text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:cursor-pointer">
                </div>

                <!-- Unit/Property Type Options -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit/Property Type Options</label>
                    <p class="text-sm text-gray-500 mb-4">Select the available unit/property types for this project. These will be shown in the inquiry form.</p>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @php
                            $predefinedOptions = [
                                '1 BHK', '2 BHK', '3 BHK', '4 BHK', '5 BHK',
                                'Studio', 'Penthouse', 'Villa', 'Shop', 'Office', 'Plot'
                            ];
                            $selectedOptions = $project->unitOptions->pluck('option_name')->toArray();
                        @endphp

                        @foreach($predefinedOptions as $option)
                            <label class="flex items-center">
                                <input type="checkbox" name="selected_unit_options[]" value="{{ $option }}" {{ in_array($option, $selectedOptions) ? 'checked' : '' }} class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm text-slate-700">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>

                    <p class="mt-2 text-xs text-gray-500">Select all unit/property types available in this project.</p>
                </div>
            </div>
            
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('projects.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">Update Project</button>
            </div>
        </form>
    </div>
</div>


@endsection
