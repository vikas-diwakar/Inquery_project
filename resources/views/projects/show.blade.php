@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">{{ $project->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('projects.edit', $project) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                Edit
            </a>
            <a href="{{ route('projects.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Project Details</h2>
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $project->location ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($project->status === 'ongoing') bg-green-100 text-green-800
                                @elseif($project->status === 'completed') bg-gray-100 text-gray-800
                                @elseif($project->status === 'on_hold') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $project->start_date?->format('M d, Y') ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Inquiries</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $project->inquiries->count() }}</dd>
                    </div>
                    @if($project->description)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $project->description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Inquiry Form QR Code</h2>
                <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4">
                    <div class="flex flex-col items-center">
                        <div id="qrcode" class="border-2 border-gray-200 p-4 rounded-lg bg-white"></div>
                        <button id="downloadQR" class="mt-3 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                            Download QR Code
                        </button>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-2">
                            This is a unique QR code for <strong>{{ $project->name }}</strong>. 
                            Public users can scan this QR code to access and submit the inquiry form for this project.
                        </p>
                        <p class="text-xs text-gray-500 mb-3">
                            QR Code ID: {{ $project->getQrCodeIdentifier() }} (Company-Project unique)
                        </p>
                        <div class="space-y-2">
                            <a href="{{ route('public.inquiry.form', $project) }}" target="_blank" class="block text-sm text-indigo-600 hover:text-indigo-800 break-all">
                                {{ route('public.inquiry.form', $project) }}
                            </a>
                            <button onclick="copyToClipboard('{{ route('public.inquiry.form', $project) }}')" class="text-xs text-gray-600 hover:text-gray-800 underline">
                                Copy Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Project Logo</h2>
                @if($project->logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($project->logo) }}" alt="{{ $project->name }}" class="w-full rounded-lg">
                @else
                    <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                        No logo
                    </div>
                @endif
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Brochures</h2>
                @forelse($project->brochures as $brochure)
                    <div class="mb-4 p-3 border rounded-lg">
                        <p class="text-sm font-medium text-gray-900">{{ $brochure->file_name }}</p>
                        <a href="{{ route('public.brochure.download', $brochure) }}" class="text-sm text-indigo-600 hover:text-indigo-800">Download</a>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No brochures uploaded yet</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
    const inquiryUrl = '{{ route('public.inquiry.form', $project) }}';
    const qrCodeElement = document.getElementById('qrcode');
    let qrCodeCanvas = null;

    // Generate QR code
    QRCode.toCanvas(qrCodeElement, inquiryUrl, {
        width: 250,
        margin: 2,
        color: {
            dark: '#000000',
            light: '#FFFFFF'
        }
    }, function (error, canvas) {
        if (error) {
            console.error('QR Code generation error:', error);
            return;
        }
        qrCodeCanvas = canvas;
    });

    // Download QR code functionality
    document.getElementById('downloadQR').addEventListener('click', function() {
        if (!qrCodeCanvas) {
            alert('QR code is still loading. Please wait a moment and try again.');
            return;
        }

        // Convert canvas to blob and download
        qrCodeCanvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            const projectName = '{{ addslashes($project->name) }}'.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
            a.download = 'inquiry-qr-{{ $project->company_id }}-{{ $project->id }}-' + projectName + '.png';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }, 'image/png');
    });

    // Copy to clipboard functionality
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Link copied to clipboard!');
        }, function(err) {
            // Fallback for older browsers
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                alert('Link copied to clipboard!');
            } catch (err) {
                alert('Failed to copy link. Please copy manually.');
            }
            document.body.removeChild(textarea);
        });
    }
</script>
@endsection
