@extends('layouts.app')

@section('title', 'Inquiry Form QR - ' . $project->name)

@section('content')
<div class="max-w-4xl mx-auto py-4 space-y-6">
    <!-- Back Link & Header -->
    <div class="space-y-1">
        <a href="{{ route('forms-qr.index') }}" class="inline-flex items-center space-x-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Back to Forms & QR Codes</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Inquiry Form QR Code</h1>
        <p class="text-xs sm:text-sm text-slate-500">Project: <span class="font-bold text-slate-800">{{ $project->name }}</span></p>
    </div>

    <!-- QR Code & Details Container Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8">
        <h2 class="text-lg font-bold text-slate-900 mb-6">QR Code Details</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <div class="space-y-4">
                @if($project->inquiry_qr_code && Storage::disk('public')->exists($project->inquiry_qr_code))
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 flex justify-center shadow-inner">
                        <img id="inquiryQrImg" src="{{ Storage::url($project->inquiry_qr_code) }}" alt="Inquiry QR Code" class="w-64 h-64 object-contain">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <button id="downloadPngBtn" type="button" class="btn-primary space-x-2 w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download PNG</span>
                        </button>
                        <button id="downloadJpegBtn" type="button" class="btn-secondary space-x-2 w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            <span>Download JPEG</span>
                        </button>
                    </div>
                @else
                    <div class="p-8 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col justify-center items-center text-center space-y-3 min-h-[256px]">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <p class="text-xs text-slate-500 font-medium">QR code not generated yet</p>
                    </div>
                    <a href="{{ route('forms-qr.create-inquiry-form') }}?project={{ $project->id }}" class="btn-primary w-full text-center">
                        Generate QR Code
                    </a>
                @endif
            </div>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-3">Project Metadata</h3>
                    <dl class="space-y-3 text-sm">
                        <div class="flex flex-col sm:flex-row sm:justify-between py-2 border-b border-slate-100">
                            <dt class="font-semibold text-slate-500">Project Name:</dt>
                            <dd class="font-bold text-slate-900 sm:text-right">{{ $project->name }}</dd>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:justify-between py-2 border-b border-slate-100">
                            <dt class="font-semibold text-slate-500">QR Code Identifier:</dt>
                            <dd class="font-mono text-xs bg-slate-100 px-2 py-1 rounded text-slate-700 sm:text-right">{{ $project->getQrCodeIdentifier() }}</dd>
                        </div>
                        <div class="flex flex-col py-2 border-b border-slate-100">
                            <dt class="font-semibold text-slate-500 mb-1">Inquiry Form Link:</dt>
                            <dd class="text-indigo-600 font-medium break-all text-xs bg-indigo-50/60 p-2.5 rounded-xl border border-indigo-100">
                                <a href="{{ route('public.inquiry.form', $project) }}" target="_blank" class="hover:underline">
                                    {{ route('public.inquiry.form', $project) }}
                                </a>
                            </dd>
                        </div>
                    </dl>
                </div>
                
                <div class="pt-2">
                    <button type="button" onclick="copyToClipboard('{{ route('public.inquiry.form', $project) }}')" class="btn-secondary text-xs space-x-2 w-full justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        <span>Copy Link to Clipboard</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Inquiry Form Fields Card -->
    <div class="bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Inquiry Form Field Structure</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                When prospective buyers scan this QR code, they will see an inquiry form with the following fields:
            </p>
        </div>

        <div class="bg-slate-50/80 border border-slate-200/60 rounded-2xl p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                <div class="flex items-center space-x-2">
                    <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
                    <span class="font-bold text-slate-800">Customer Name</span>
                    <span class="text-rose-500 font-bold">*</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="h-2 w-2 rounded-full bg-indigo-600"></span>
                    <span class="font-bold text-slate-800">Phone Number</span>
                    <span class="text-rose-500 font-bold">*</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                    <span class="font-medium text-slate-700">Email Address</span>
                    <span class="text-xs text-slate-400">(Optional)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                    <span class="font-medium text-slate-700">Budget ($)</span>
                    <span class="text-xs text-slate-400">(Optional)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                    <span class="font-medium text-slate-700">Flat / Unit Type</span>
                    <span class="text-xs text-slate-400">(Optional)</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                    <span class="font-medium text-slate-700">Message / Requirement</span>
                    <span class="text-xs text-slate-400">(Optional)</span>
                </div>
            </div>
            <p class="mt-4 pt-3 border-t border-slate-200/60 text-xs text-slate-400"><span class="text-rose-500 font-bold">*</span> Required fields for lead submission</p>
        </div>
    </div>
</div>

<script>
    // Copy to clipboard functionality
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
<script>
    // Convert remote SVG to PNG/JPEG and trigger download (client-side)
    async function downloadSvgAsImage(svgUrl, filename, type = 'png', quality = 0.92) {
        try {
            const res = await fetch(svgUrl);
            if (!res.ok) throw new Error('Failed to fetch SVG');
            const svgText = await res.text();

            // Create a blob URL for the SVG text
            const svgBlob = new Blob([svgText], { type: 'image/svg+xml;charset=utf-8' });
            const url = URL.createObjectURL(svgBlob);

            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                canvas.width = img.width || 300;
                canvas.height = img.height || 300;
                const ctx = canvas.getContext('2d');
                // fill white for JPEG
                if (type === 'jpeg') {
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                }
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                const mime = type === 'jpeg' ? 'image/jpeg' : 'image/png';
                canvas.toBlob(function(blob) {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(url);
                }, mime, quality);
            };
            img.onerror = function() {
                URL.revokeObjectURL(url);
                // fallback to server download route
                window.location.href = '{{ route('forms-qr.download-inquiry-qr', $project) }}';
            };
            img.src = url;
        } catch (err) {
            // fallback to server download route
            window.location.href = '{{ route('forms-qr.download-inquiry-qr', $project) }}';
        }
    }

    document.getElementById('downloadPngBtn').addEventListener('click', function() {
        const src = document.getElementById('inquiryQrImg').src;
        downloadSvgAsImage(src, 'inquiry-qr-{{ $project->getQrCodeIdentifier() }}.png', 'png');
    });

    document.getElementById('downloadJpegBtn').addEventListener('click', function() {
        const src = document.getElementById('inquiryQrImg').src;
        downloadSvgAsImage(src, 'inquiry-qr-{{ $project->getQrCodeIdentifier() }}.jpg', 'jpeg', 0.9);
    });
</script>
@endsection
