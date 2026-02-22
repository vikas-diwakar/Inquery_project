@extends('layouts.app')

@section('title', 'Brochure QR - ' . $brochure->file_name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('forms-qr.brochure-qr') }}" class="text-indigo-600 hover:text-indigo-800 text-sm mb-2 inline-block">
            ← Back to Brochure QR Codes
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Brochure QR Code</h1>
        <p class="mt-2 text-sm text-gray-600">Project: <strong>{{ $brochure->project->name }}</strong></p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">QR Code Details</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="border-2 border-gray-200 p-4 rounded-lg bg-white mb-4 flex justify-center">
                    @php
                        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(300)
                            ->margin(2)
                            ->format('svg')
                            ->generate(route('public.brochure.download', $brochure));
                    @endphp
                    {!! $qrCode !!}
                </div>
                <button id="downloadQR" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                    Download QR Code
                </button>
            </div>
            
            <div>
                <h3 class="text-md font-semibold mb-3">Information</h3>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Project:</dt>
                        <dd class="text-gray-900">{{ $brochure->project->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Brochure File:</dt>
                        <dd class="text-gray-900">{{ $brochure->file_name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Download URL:</dt>
                        <dd class="text-gray-900 break-all">
                            <a href="{{ route('public.brochure.download', $brochure) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">
                                {{ route('public.brochure.download', $brochure) }}
                            </a>
                        </dd>
                    </div>
                </dl>
                
                <div class="mt-4 pt-4 border-t">
                    <button onclick="copyToClipboard('{{ route('public.brochure.download', $brochure) }}')" class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                        Copy Link to Clipboard
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('downloadQR').addEventListener('click', function() {
        const svgElement = document.querySelector('svg');
        if (!svgElement) {
            alert('QR code is not available. Please refresh the page.');
            return;
        }

        // Convert SVG to canvas and download
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const svgString = new XMLSerializer().serializeToString(svgElement);
        const img = new Image();

        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);

            canvas.toBlob(function(blob) {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                const fileName = '{{ addslashes($brochure->file_name) }}'.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                a.download = 'brochure-qr-' + fileName + '.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
            }, 'image/png');
        };

        img.src = 'data:image/svg+xml;base64,' + btoa(svgString);
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Link copied to clipboard!');
        }, function(err) {
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
