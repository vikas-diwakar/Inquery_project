@extends('layouts.app')

@section('title', 'Forms & QR Codes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 md:mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Forms & QR Codes</h1>
        <p class="mt-1 text-sm text-slate-600">Create and manage inquiry forms with QR codes and brochure QR codes</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-8">
        <div class="card card-hover p-6 border-2 border-transparent hover:border-primary-200 transition-colors">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h2 class="text-lg font-semibold text-slate-900">Create Inquiry Form QR</h2>
            </div>
            <p class="text-slate-600 mb-4 text-sm">Generate unique QR codes for project inquiry forms. Public users can scan these to access and submit inquiry forms.</p>
            <a href="{{ route('forms-qr.create-inquiry-form') }}" class="btn-primary inline-flex">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create Inquiry Form QR
            </a>
        </div>
        <div class="card card-hover p-6 border-2 border-transparent hover:border-emerald-200 transition-colors">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h2 class="text-lg font-semibold text-slate-900">Brochure QR Codes</h2>
            </div>
            <p class="text-slate-600 mb-4 text-sm">View and manage QR codes for project brochures. Users can scan these to download brochures directly.</p>
            <a href="{{ route('forms-qr.brochure-qr') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                View Brochure QR Codes
            </a>
        </div>
    </div>

    <div class="card p-6">
        <h2 class="text-lg font-semibold text-slate-900 mb-4">Inquiry Form QR Code for: {{ $project->name }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm font-medium text-slate-500">Project Name</p>
                <p class="text-base font-semibold text-slate-900">{{ $project->name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">Location</p>
                <p class="text-base text-slate-900">{{ $project->location ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-slate-500">QR Code Status</p>
                @if($project->inquiry_qr_code && Storage::disk('public')->exists($project->inquiry_qr_code))
                    <span class="badge bg-emerald-100 text-emerald-800">Generated</span>
                @else
                    <span class="badge bg-amber-100 text-amber-800">Not Generated</span>
                @endif
            </div>
        </div>
        <div class="mt-4">
            @if($project->inquiry_qr_code && Storage::disk('public')->exists($project->inquiry_qr_code))
                <a href="{{ route('forms-qr.show-inquiry-qr') }}" class="btn-primary">View QR Code</a>
            @else
                <a href="{{ route('forms-qr.create-inquiry-form') }}" class="btn-primary">Generate QR Code</a>
            @endif
        </div>
    </div>
</div>
@endsection
