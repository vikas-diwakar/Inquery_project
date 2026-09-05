@extends('layouts.app')

@section('title', 'Upload Brochure - PropDrip')

@section('content')
<div class="max-w-2xl mx-auto py-4">
    <div class="mb-6">
        <a href="{{ route('brochures.index') }}" class="inline-flex items-center space-x-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Brochures</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
        <div class="bg-slate-900 p-6 sm:p-8 text-white flex items-center justify-between relative overflow-hidden">
            <div class="absolute -top-12 -right-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
            <div class="space-y-1 relative z-10">
                <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight">Upload Project Brochure</h1>
                <p class="text-xs sm:text-sm text-slate-300">Project: <span class="font-semibold text-indigo-300">{{ $project->name }}</span></p>
            </div>
            <div class="h-12 w-12 rounded-2xl bg-indigo-600/30 border border-indigo-400/30 flex items-center justify-center text-indigo-300 relative z-10 shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>

        <form action="{{ route('brochures.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label for="brochure_file" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Brochure File (PDF) <span class="text-rose-500">*</span></label>
                    <input type="file" name="brochure_file" id="brochure_file" accept=".pdf" required 
                        class="block w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer">
                    <p class="text-xs text-slate-400">Maximum PDF file size: 10MB</p>
                </div>
            </div>
            
            <div class="pt-6 border-t border-slate-200 flex items-center justify-between">
                <a href="{{ route('brochures.index') }}" class="btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn-primary space-x-2">
                    <span>Upload Brochure</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

