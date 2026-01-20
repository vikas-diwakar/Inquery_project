@extends('layouts.app')

@section('title', 'Brochure QR Codes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('forms-qr.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm mb-2 inline-block">
            ← Back to Forms & QR Codes
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Brochure QR Codes</h1>
        <p class="mt-2 text-sm text-gray-600">Manage QR codes for project brochures</p>
    </div>

    @if($brochures->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($brochures as $brochure)
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-2">{{ $brochure->project->name }}</h3>
                    <p class="text-sm text-gray-600 mb-2">{{ $brochure->file_name }}</p>
                    <a href="{{ route('forms-qr.show-brochure-qr', $brochure) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm">
                        View QR Code
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white shadow rounded-lg p-6 text-center">
            <p class="text-gray-500 mb-4">No brochures found.</p>
            <a href="{{ route('brochures.create') }}" class="text-indigo-600 hover:text-indigo-800">
                Upload a brochure to generate QR codes
            </a>
        </div>
    @endif
</div>
@endsection
