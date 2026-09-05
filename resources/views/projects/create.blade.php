@extends('layouts.app')

@section('title', 'Create Project - PropDrip')

@section('content')
<div class="max-w-3xl mx-auto py-4">
    <!-- Back Link -->
    <div class="mb-6">
        <a href="{{ route('projects.index') }}" class="inline-flex items-center space-x-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Projects</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
        <!-- Banner Header -->
        <div class="bg-slate-900 p-6 sm:p-8 text-white flex items-center justify-between relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="space-y-1 relative z-10">
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">Create New Property Project</h1>
                <p class="text-xs sm:text-sm text-slate-300">Set up project details, unit types, and automatic QR inquiry forms.</p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-600/30 border border-indigo-400/30 flex items-center justify-center text-indigo-300 relative z-10 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
        </div>

        <form action="{{ route('projects.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            
            <div class="space-y-5">
                <!-- Project Name -->
                <div class="space-y-1.5">
                    <label for="name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Project Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}" class="input-field" placeholder="e.g. Skyline Towers Phase 1">
                </div>
                
                <!-- Location -->
                <div class="space-y-1.5">
                    <label for="location" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Location / City</label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" class="input-field" placeholder="e.g. Downtown Business District, New York">
                </div>
                
                <!-- Description -->
                <div class="space-y-1.5">
                    <label for="description" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Project Description</label>
                    <textarea name="description" id="description" rows="3" class="input-field" placeholder="Brief overview of luxury residential units, amenities, etc.">{{ old('description') }}</textarea>
                </div>
                
                <!-- 360 Virtual Tour URL -->
                <div class="space-y-1.5">
                    <label for="virtual_tour_url" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">360° Virtual Walkthrough Tour Link (Matterport / Kuula / YouTube 360)</label>
                    <input type="url" name="virtual_tour_url" id="virtual_tour_url" value="{{ old('virtual_tour_url') }}" class="input-field" placeholder="https://my.matterport.com/show/?m=example or https://kuula.co/share/...">
                    <p class="text-xs text-slate-400">Embed URL for interactive 360 degree virtual tour.</p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="start_date" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="input-field">
                    </div>
                    
                    <div class="space-y-1.5">
                        <label for="status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Project Status <span class="text-rose-500">*</span></label>
                        <select name="status" id="status" required class="input-field cursor-pointer">
                            <option value="planning" {{ old('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="ongoing" {{ old('status', 'ongoing') === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="on_hold" {{ old('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="logo" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Project Logo / Image</label>
                        <input type="file" name="logo" id="logo" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>

                    <div class="space-y-1.5">
                        <label for="master_plan_image" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Master Layout Plan Image</label>
                        <input type="file" name="master_plan_image" id="master_plan_image" accept="image/*" class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    </div>
                </div>

                <!-- Property Unit Options -->
                <div class="space-y-2 pt-2">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Unit / Property Type Configurations</label>
                    <p class="text-xs text-slate-500">Select property configurations available in this project for customer inquiries.</p>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3 border border-slate-200 rounded-2xl p-4 bg-slate-50/80">
                        @php
                            $predefinedOptions = [
                                '1 BHK', '2 BHK', '3 BHK', '4 BHK', '5 BHK',
                                'Studio', 'Penthouse', 'Villa', 'Shop', 'Office', 'Plot'
                            ];
                        @endphp

                        @foreach($predefinedOptions as $option)
                            <label class="flex items-center p-2.5 rounded-xl bg-white border border-slate-200/80 hover:border-indigo-300 cursor-pointer space-x-2.5 transition-all">
                                <input type="checkbox" name="selected_unit_options[]" value="{{ $option }}" {{ in_array($option, old('selected_unit_options', [])) ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-semibold text-slate-800">{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('projects.index') }}" class="btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn-primary space-x-2">
                    <span>Create Project</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

