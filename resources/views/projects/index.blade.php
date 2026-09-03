@extends('layouts.app')

@section('title', 'Projects Management - Property Inquiry SaaS')

@section('content')
<div class="max-w-7xl mx-auto py-4 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Real Estate Projects</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Configure property developments, unit stacking inventory, and 360° virtual tours.</p>
        </div>
        <a href="{{ route('projects.create') }}" class="btn-primary space-x-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Add New Project</span>
        </a>
    </div>

    <!-- Projects Grid / Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($projects as $project)
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 flex flex-col justify-between space-y-5 hover:border-indigo-300 transition-all group">
                <div class="space-y-4">
                    <!-- Top Header -->
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            @if($project->logo)
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($project->logo) }}" alt="{{ $project->name }}" class="h-12 w-12 rounded-2xl object-cover border border-slate-200/80 shadow-xs">
                            @else
                                <div class="h-12 w-12 rounded-2xl bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-extrabold text-lg flex items-center justify-center shadow-md shadow-indigo-500/20">
                                    {{ strtoupper(substr($project->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-indigo-600 transition-colors leading-snug">{{ $project->name }}</h3>
                                <p class="text-xs text-slate-500 font-medium flex items-center mt-0.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>{{ $project->location ?? 'Location Unspecified' }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Status Pill -->
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border 
                            @if($project->status === 'ongoing') bg-emerald-50 text-emerald-700 border-emerald-200
                            @elseif($project->status === 'completed') bg-slate-100 text-slate-700 border-slate-200
                            @elseif($project->status === 'on_hold') bg-amber-50 text-amber-700 border-amber-200
                            @else bg-indigo-50 text-indigo-700 border-indigo-200
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                        </span>
                    </div>

                    <!-- Virtual Tour Indicator -->
                    @if($project->virtual_tour_url)
                        <div class="inline-flex items-center space-x-1.5 text-xs font-semibold text-purple-700 bg-purple-50 px-2.5 py-1 rounded-xl border border-purple-200">
                            <span>🕶️ 360° Virtual Tour Available</span>
                        </div>
                    @endif
                </div>

                <!-- Action Footer Buttons -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-2">
                    <a href="{{ route('projects.select', $project) }}" class="btn-primary text-xs py-2 px-3 space-x-1 flex-1 text-center justify-center">
                        <span>Select Project</span>
                    </a>
                    
                    <a href="{{ route('projects.units.index', $project) }}" class="btn-secondary text-xs py-2 px-3 space-x-1" title="Manage Stacking Chart Units">
                        <span>🏗️ Stacking Chart</span>
                    </a>

                    <a href="{{ route('projects.edit', $project) }}" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" title="Edit Project">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-3xl p-12 text-center border border-slate-200/80 space-y-4">
                <p class="text-sm text-slate-500">No projects created yet in this workspace.</p>
                <a href="{{ route('projects.create') }}" class="btn-primary text-xs">Create First Project</a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
        <div class="pt-2">
            {{ $projects->links() }}
        </div>
    @endif
</div>
@endsection

