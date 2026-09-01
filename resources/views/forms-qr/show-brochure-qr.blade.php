@extends('layouts.app')

@section('title', 'Brochure QR - ' . $brochure->file_name)

@section('content')
<div class="max-w-4xl mx-auto py-4 space-y-6">
    <!-- Back Link & Header -->
    <div class="space-y-1">
        <a href="{{ route('forms-qr.brochure-qr') }}" class="inline-flex items-center space-x-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Brochure QR Codes</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Brochure Download QR Code</h1>
        <p class="text-xs sm:text-sm text-slate-500">Project: <span class="font-bold text-slate-800">{{ $brochure->project->name }}</span></p>
    </div>

    <!-- QR Code Details Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-6">QR Code Details</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <div class="space-y-4">
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 flex justify-center shadow-inner">
                    @php
                        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(280)
                            ->margin(2)
                            ->format('svg')
                            ->generate(route('public.brochure.download', $brochure));
                    @endphp
                    {!! $qrCode !!}
                </div>
                <button id="downloadQR" type="button" class="btn-primary space-x-2 w-full justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    <span>Download QR Code Image</span>
                </button>
            </div>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3">Brochure Information</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex flex-col sm:flex-row sm:justify-between py-2 border-b border-slate-100">
                            <dt class="font-semibold text-slate-500">Project Name:</dt>
                            <dd class="font-bold text-slate-900 sm:text-right">{{ $brochure->project->name }}</dd>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between py-2 border-b border-slate-100">
                            <dt class="font-semibold text-slate-500">Brochure File:</dt>
                            <dd class="font-medium text-slate-800 sm:text-right">{{ $brochure->file_name }}</dd>
                        </div>
                        <div class="flex flex-col py-2 border-b border-slate-100">
                            <dt class="font-semibold text-slate-500 mb-1">Direct Download Link:</dt>
                            <dd class="text-indigo-600 font-medium break-all text-xs bg-indigo-50/60 p-2.5 rounded-xl border border-indigo-100">
                                <a href="{{ route('public.brochure.download', $brochure) }}" target="_blank" class="hover:underline">
                                    {{ route('public.brochure.download', $brochure) }}
                                </a>
                            </dd>
                        </div>
                    </dl>
                </div>
                
                <div class="pt-2">
                    <button type="button" onclick="copyToClipboard('{{ route('public.brochure.download', $brochure) }}')" class="btn-secondary text-xs space-x-2 w-full justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        <span>Copy Direct Link to Clipboard</span>
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
