@extends('layouts.app')

@section('title', $project->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-slate-900">{{ $project->name }}</h1>
        <div class="flex space-x-2">
            @can('update', $project)
                <a href="{{ route('projects.edit', $project) }}" class="btn-primary px-4 py-2">
                    Edit
                </a>
            @endcan
            @can('delete', $project)
                <button type="button" 
                    onclick="showConfirmationModal('Delete Project', 'Are you sure you want to delete \'{{ addslashes($project->name) }}\'? All existing records will be archived safely in the database.', function() { document.getElementById('delete-project-form-{{ $project->id }}').submit(); })"
                    class="px-4 py-2 rounded-xl text-sm font-semibold bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition-all">
                    Delete Project
                </button>
                <form id="delete-project-form-{{ $project->id }}" action="{{ route('projects.destroy', $project) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            @endcan
            <a href="{{ route('projects.index') }}" class="btn-secondary px-4 py-2">
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
                        <dt class="text-sm font-medium text-slate-500">Location</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $project->location ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($project->status === 'ongoing') bg-green-100 text-green-800
                                @elseif($project->status === 'completed') bg-slate-100 text-slate-800
                                @elseif($project->status === 'on_hold') bg-yellow-100 text-yellow-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Start Date</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $project->start_date?->format('M d, Y') ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-500">Total Inquiries</dt>
                        <dd class="mt-1 text-sm text-slate-900">{{ $project->inquiries->count() }}</dd>
                    </div>
                    @if($project->description)
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-slate-500">Description</dt>
                            <dd class="mt-1 text-sm text-slate-900">{{ $project->description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Inquiry Form QR Code</h2>
                <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4">
                    <div class="flex flex-col items-center">
                        <div id="qrcode" class="border-2 border-gray-200 p-4 rounded-lg bg-white"></div>
                        <button id="downloadQR" class="mt-3 btn-primary text-sm px-4 py-2">
                            Download QR Code
                        </button>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-slate-600 mb-2">
                            This is a unique QR code for <strong>{{ $project->name }}</strong>. 
                            Public users can scan this QR code to access and submit the inquiry form for this project.
                        </p>
                        <p class="text-xs text-slate-500 mb-3">
                            QR Code ID: {{ $project->getQrCodeIdentifier() }} (Company-Project unique)
                        </p>
                        <div class="space-y-2">
                            <a href="{{ route('public.inquiry.form', $project) }}" target="_blank" class="block text-sm text-primary-600 hover:text-primary-700 break-all">
                                {{ route('public.inquiry.form', $project) }}
                            </a>
                            <button onclick="copyToClipboard('{{ route('public.inquiry.form', $project) }}')" class="text-xs text-slate-600 hover:text-slate-800 underline">
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
                    <div class="w-full h-48 bg-slate-200 rounded-lg flex items-center justify-center text-slate-400">
                        No logo
                    </div>
                @endif
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Brochures</h2>
                @forelse($project->brochures as $brochure)
                    <div class="mb-4 p-3 border rounded-lg">
                        <p class="text-sm font-medium text-slate-900">{{ $brochure->file_name }}</p>
                        <a href="{{ route('public.brochure.download', $brochure) }}" class="text-sm text-primary-600 hover:text-primary-700">Download</a>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No brochures uploaded yet</p>
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

        const companyName = @json($project->company->name ?? auth()->user()->company->name ?? 'Company');
        const projectName = @json($project->name);

        function slugify(text) {
            return text.toString().toLowerCase().trim()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // Canvas dimensions (high resolution printable card)
        const width = 600;
        const height = 750;
        canvas.width = width;
        canvas.height = height;

        // Background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, width, height);

        // Outer border card frame
        ctx.strokeStyle = '#cbd5e1';
        ctx.lineWidth = 4;
        ctx.strokeRect(16, 16, width - 32, height - 32);

        // Header Top Brand Accent Line
        ctx.fillStyle = '#4f46e5';
        ctx.fillRect(16, 16, width - 32, 10);

        // Render Company Name
        ctx.fillStyle = '#0f172a';
        ctx.font = 'bold 26px sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        ctx.fillText(companyName.toUpperCase(), width / 2, 48);

        // Render Project Name
        ctx.fillStyle = '#4f46e5';
        ctx.font = 'bold 20px sans-serif';
        ctx.fillText(projectName + ' - Inquiry Form', width / 2, 92);

        // Divider line
        ctx.strokeStyle = '#e2e8f0';
        ctx.lineWidth = 2;
        ctx.beginPath();
        ctx.moveTo(80, 135);
        ctx.lineTo(width - 80, 135);
        ctx.stroke();

        // Draw QR Code frame
        const qrSize = 360;
        const qrX = (width - qrSize) / 2;
        const qrY = 165;

        ctx.fillStyle = '#f8fafc';
        ctx.fillRect(qrX - 15, qrY - 15, qrSize + 30, qrSize + 30);
        ctx.strokeStyle = '#cbd5e1';
        ctx.lineWidth = 1;
        ctx.strokeRect(qrX - 15, qrY - 15, qrSize + 30, qrSize + 30);

        // Draw actual QR Canvas
        ctx.drawImage(qrCodeCanvas, qrX, qrY, qrSize, qrSize);

        // Footer Text
        ctx.fillStyle = '#334155';
        ctx.font = '600 16px sans-serif';
        ctx.fillText('Scan QR Code with your mobile camera', width / 2, 595);
        ctx.fillText('to view & submit property inquiry form', width / 2, 622);

        // Sub-footer Tag
        ctx.fillStyle = '#64748b';
        ctx.font = '500 13px sans-serif';
        ctx.fillText(companyName + ' • ' + projectName, width / 2, 685);

        const companySlug = slugify(companyName) || 'company';
        const projectSlug = slugify(projectName) || 'project';
        const filename = `${companySlug}-${projectSlug}-inquiry-qr.png`;

        canvas.toBlob(function(blob) {
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
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
