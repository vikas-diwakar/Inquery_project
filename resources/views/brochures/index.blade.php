@extends('layouts.app')

@section('title', 'Brochures')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Brochures</h1>
        <a href="{{ route('brochures.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
            Upload Brochure
        </a>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-md">
        <ul class="divide-y divide-gray-200">
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
                                    <p class="text-sm text-gray-500">{{ $brochure->project->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('public.brochure.download', $brochure) }}" class="text-indigo-600 hover:text-indigo-900">Download</a>
                                <form action="{{ route('brochures.destroy', $brochure) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </div>
                        </div>
                        @if($brochure->qr_code)
                            <div class="mt-4 flex items-center space-x-4">
                                <div id="qrcode-{{ $brochure->id }}" class="border-2 border-gray-200 p-2 rounded-lg"></div>
                                <div>
                                    <p class="text-xs text-gray-600">Scan to download brochure</p>
                                    <a href="{{ $brochure->qr_code }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800 break-all">{{ $brochure->qr_code }}</a>
                                </div>
                            </div>
                        @endif
                    </div>
                </li>
            @empty
                <li class="px-4 py-4 text-center text-gray-500">No brochures uploaded yet. <a href="{{ route('brochures.create') }}" class="text-indigo-600">Upload one</a></li>
            @endforelse
        </ul>
    </div>

    <div class="mt-4">
        {{ $brochures->links() }}
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
    @foreach($brochures as $brochure)
        @if($brochure->qr_code)
            QRCode.toCanvas(document.getElementById('qrcode-{{ $brochure->id }}'), '{{ $brochure->qr_code }}', {
                width: 150,
                margin: 2
            }, function (error) {
                if (error) console.error(error);
            });
        @endif
    @endforeach
</script>
@endsection
