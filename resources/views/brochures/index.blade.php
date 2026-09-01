@extends('layouts.app')

@section('title', 'Brochures')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-900">Brochures</h1>
            <p class="mt-1 text-sm text-slate-600">Project: <strong>{{ $project->name }}</strong></p>
            <a href="{{ route('dashboard') }}" class="mt-2 inline-block text-sm font-medium text-primary-600 hover:text-primary-700">← Back to Dashboard</a>
        </div>
        <a href="{{ route('brochures.create') }}" class="btn-primary shrink-0">Upload Brochure</a>
    </div>

    <div class="card overflow-hidden">
        <ul class="divide-y divide-slate-200">
            @forelse($brochures as $brochure)
                <li>
                    <div class="px-4 py-4 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="h-10 w-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ $brochure->file_name }}</p>
                                    <p class="text-sm text-gray-500">Uploaded: {{ $brochure->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('public.brochure.download', $brochure) }}" class="text-sm font-medium text-primary-600 hover:text-primary-700">Download</a>
                                <button type="button"
                                        onclick="console.log('Delete button clicked'); showConfirmationModal('Delete Brochure', 'Are you sure you want to delete this brochure? This action cannot be undone.', function() { console.log('Callback executed'); document.getElementById('delete-form-{{ $brochure->id }}').submit(); })"
                                        class="text-red-600 hover:text-red-900">Delete</button>
                                <form id="delete-form-{{ $brochure->id }}" action="{{ route('brochures.destroy', $brochure) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </div>
                        @if($brochure->qr_code)
                            <div class="mt-4 flex items-center space-x-4">
                                <div class="border-2 border-gray-200 p-2 rounded-lg">
                                    <img src="{{ Storage::url('qrcodes/brochure_' . $brochure->id . '.svg') }}" alt="QR Code" class="w-24 h-24">
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600">Scan to download brochure</p>
                                    <a href="{{ $brochure->qr_code }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800 break-all">{{ $brochure->qr_code }}</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </li>
            @empty
                <li class="px-4 py-12 text-center text-slate-500">No brochures uploaded yet. <a href="{{ route('brochures.create') }}" class="font-semibold text-primary-600 hover:text-primary-700">Upload one</a></li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $brochures->links() }}
    </div>
</div>
@endsection
