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
                <div id="qrcode" class="border-2 border-gray-200 p-4 rounded-lg bg-white mb-4 flex justify-center"></div>
                <button id="downloadQR" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
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

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js" onerror="this.onerror=null; this.src='https://unpkg.com/qrcode@1.5.3/build/qrcode.min.js';"></script>
<script>
    const brochureUrl = '{{ route('public.brochure.download', $brochure) }}';
    let qrCodeCanvas = null;

    // Function to generate QR code
    function generateQRCode() {
        const qrCodeElement = document.getElementById('qrcode');
        
        if (!qrCodeElement) {
            console.error('QR code element not found');
            return;
        }

        // Check if QRCode library is loaded
        if (typeof QRCode === 'undefined') {
            console.error('QRCode library not loaded');
            qrCodeElement.innerHTML = '<p class="text-red-500 text-sm">Error: QR Code library failed to load. Please refresh the page.</p>';
            return;
        }

        QRCode.toCanvas(qrCodeElement, brochureUrl, {
            width: 300,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#FFFFFF'
            }
        }, function (error, canvas) {
            if (error) {
                console.error('QR Code generation error:', error);
                qrCodeElement.innerHTML = '<p class="text-red-500 text-sm">Error generating QR code. Please try again.</p>';
                return;
            }
            qrCodeCanvas = canvas;
        });
    }

    // Wait for both DOM and QRCode library to be ready
    function initQRCode() {
        if (typeof QRCode !== 'undefined') {
            generateQRCode();
        } else {
            // If library not loaded yet, wait a bit more
            setTimeout(function() {
                if (typeof QRCode !== 'undefined') {
                    generateQRCode();
                } else {
                    console.error('QRCode library still not loaded after timeout');
                    document.getElementById('qrcode').innerHTML = '<p class="text-red-500 text-sm">Error: QR Code library failed to load. Please check your internet connection and refresh the page.</p>';
                }
            }, 500);
        }
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initQRCode);
    } else {
        // DOM is already ready
        initQRCode();
    }

    document.getElementById('downloadQR').addEventListener('click', function() {
        if (!qrCodeCanvas) {
            alert('QR code is still loading. Please wait a moment and try again.');
            return;
        }

        qrCodeCanvas.toBlob(function(blob) {
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
