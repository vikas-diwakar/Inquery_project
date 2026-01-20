@extends('layouts.app')

@section('title', 'Inquiry Form QR - ' . $project->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('forms-qr.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm mb-2 inline-block">
            ← Back to Forms & QR Codes
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Inquiry Form QR Code</h1>
        <p class="mt-2 text-sm text-gray-600">Project: <strong>{{ $project->name }}</strong></p>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">QR Code Details</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                @if($project->inquiry_qr_code && Storage::disk('public')->exists($project->inquiry_qr_code))
                    <div class="border-2 border-gray-200 p-4 rounded-lg bg-white mb-4 flex justify-center">
                        <img src="{{ Storage::url($project->inquiry_qr_code) }}" alt="QR Code" class="w-64 h-64 object-contain">
                    </div>
                    <a href="{{ route('forms-qr.download-inquiry-qr', $project) }}" class="block w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-center">
                        Download QR Code
                    </a>
                @else
                    <div class="border-2 border-gray-200 p-4 rounded-lg bg-white mb-4 flex justify-center items-center" style="min-height: 256px;">
                        <p class="text-gray-500 text-sm">QR code not generated yet</p>
                    </div>
                    <a href="{{ route('forms-qr.create-inquiry-form') }}?project={{ $project->id }}" class="block w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-center">
                        Generate QR Code
                    </a>
                @endif
            </div>
            
            <div>
                <h3 class="text-md font-semibold mb-3">Information</h3>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">Project Name:</dt>
                        <dd class="text-gray-900">{{ $project->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">QR Code ID:</dt>
                        <dd class="text-gray-900">{{ $project->getQrCodeIdentifier() }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">Inquiry Form URL:</dt>
                        <dd class="text-gray-900 break-all">
                            <a href="{{ route('public.inquiry.form', $project) }}" target="_blank" class="text-indigo-600 hover:text-indigo-800">
                                {{ route('public.inquiry.form', $project) }}
                            </a>
                        </dd>
                    </div>
                </dl>
                
                <div class="mt-4 pt-4 border-t">
                    <button onclick="copyToClipboard('{{ route('public.inquiry.form', $project) }}')" class="text-sm text-indigo-600 hover:text-indigo-800 underline">
                        Copy Link to Clipboard
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Inquiry Form Fields</h2>
        <p class="text-sm text-gray-600 mb-4">
            When users scan this QR code, they will see a form with the following fields:
        </p>
        <div class="bg-gray-50 rounded-lg p-4">
            <ul class="space-y-2 text-sm text-gray-700">
                <li><strong>Customer Name</strong> <span class="text-red-600">*</span></li>
                <li><strong>Phone</strong> <span class="text-red-600">*</span></li>
                <li><strong>Email</strong> (Optional)</li>
                <li><strong>Budget</strong> (Optional)</li>
                <li><strong>Flat Type</strong> (Optional)</li>
                <li><strong>Message</strong> (Optional)</li>
            </ul>
            <p class="mt-3 text-xs text-gray-500"><span class="text-red-600">*</span> Required fields</p>
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
@endsection
