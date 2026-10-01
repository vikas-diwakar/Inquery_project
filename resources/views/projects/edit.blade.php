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

                <!-- Unit/Property Type Options with Add Custom Option -->
                <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl">
                    <label class="block text-sm font-bold text-slate-800 mb-1">Unit/Property Type Options</label>
                    <p class="text-xs text-slate-500 mb-3">Select available unit types or add custom options. These will appear on the customer inquiry form.</p>

                    @php
                        $predefinedOptions = [
                            '1 BHK', '2 BHK', '3 BHK', '4 BHK', '5 BHK',
                            'Studio', 'Penthouse', 'Villa', 'Shop', 'Office', 'Plot'
                        ];
                        $savedOptions = $project->unitOptions->pluck('option_name')->toArray();
                        $customSavedOptions = array_diff($savedOptions, $predefinedOptions);
                    @endphp

                    <!-- Checkbox Grid -->
                    <div id="unit-options-container" class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-4">
                        @foreach($predefinedOptions as $option)
                            <label class="flex items-center p-2 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 cursor-pointer">
                                <input type="checkbox" name="selected_unit_options[]" value="{{ $option }}" 
                                    {{ in_array($option, $savedOptions) ? 'checked' : '' }} 
                                    class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm text-slate-700 font-medium">{{ $option }}</span>
                            </label>
                        @endforeach

                        @foreach($customSavedOptions as $customOpt)
                            <label class="flex items-center justify-between p-2 rounded-lg bg-emerald-50 border border-emerald-200 custom-chip cursor-pointer">
                                <div class="flex items-center">
                                    <input type="checkbox" name="selected_unit_options[]" value="{{ $customOpt }}" checked 
                                        class="rounded border-emerald-400 text-emerald-600 focus:ring-emerald-500">
                                    <span class="ml-2 text-sm text-emerald-900 font-semibold">{{ $customOpt }}</span>
                                </div>
                                <button type="button" onclick="this.closest('.custom-chip').remove()" class="text-xs text-slate-400 hover:text-rose-600 ml-2" title="Remove">✕</button>
                            </label>
                        @endforeach
                    </div>

                    <!-- ➕ Add Custom Unit Type Input Box -->
                    <div class="flex items-center gap-2 pt-3 border-t border-slate-200">
                        <input type="text" id="new-custom-unit-input" 
                            placeholder="Type custom type (e.g. Duplex, 2.5 BHK, Farmhouse, Showroom) and click Add..." 
                            class="input-field border px-3 py-2 text-xs flex-1 rounded-lg bg-white"
                            onkeydown="if(event.key === 'Enter'){ event.preventDefault(); addCustomUnitOption(); }">
                        <button type="button" onclick="addCustomUnitOption()" class="btn-secondary px-4 py-2 text-xs font-semibold rounded-lg flex items-center gap-1">
                            <span>+ Add Type</span>
                        </button>
                    </div>
                </div>

                <script>
                function addCustomUnitOption() {
                    const input = document.getElementById('new-custom-unit-input');
                    const val = input.value.trim();
                    if (!val) return;

                    const existing = Array.from(document.querySelectorAll('input[name="selected_unit_options[]"]')).map(el => el.value.toLowerCase());
                    if (existing.includes(val.toLowerCase())) {
                        alert('This unit type is already added!');
                        input.value = '';
                        return;
                    }

                    const container = document.getElementById('unit-options-container');
                    const label = document.createElement('label');
                    label.className = 'flex items-center justify-between p-2 rounded-lg bg-emerald-50 border border-emerald-200 custom-chip cursor-pointer';
                    label.innerHTML = `
                        <div class="flex items-center">
                            <input type="checkbox" name="selected_unit_options[]" value="${val}" checked class="rounded border-emerald-400 text-emerald-600 focus:ring-emerald-500">
                            <span class="ml-2 text-sm text-emerald-900 font-semibold">${val}</span>
                        </div>
                        <button type="button" onclick="this.closest(\'.custom-chip\').remove()" class="text-xs text-slate-400 hover:text-rose-600 ml-2" title="Remove">✕</button>
                    `;

                    container.appendChild(label);
                    input.value = '';
                }
                </script>
            </div>
            
            <div class="mt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-slate-200">
                <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3 w-full sm:w-auto">
                    <button type="submit" class="btn-primary w-full sm:w-auto justify-center">Update Project</button>
                    <a href="{{ route('projects.index') }}" class="btn-secondary w-full sm:w-auto text-center justify-center">Cancel</a>
                </div>
                @can('delete', $project)
                    <button type="button" 
                        onclick="showConfirmationModal('Delete Project', 'Are you sure you want to delete \'{{ addslashes($project->name) }}\'? All existing records will be archived safely in the database.', function() { document.getElementById('delete-project-form-{{ $project->id }}').submit(); })"
                        class="px-4 py-2 rounded-xl text-sm font-semibold bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition-all w-full sm:w-auto text-center">
                        Delete Project
                    </button>
                @endcan
            </div>
        </form>

        @can('delete', $project)
            <form id="delete-project-form-{{ $project->id }}" action="{{ route('projects.destroy', $project) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endcan
    </div>
</div>


@endsection
