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
                            <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="border-2 border-slate-200 p-2 rounded-xl bg-white shadow-sm shrink-0">
                                        <img src="{{ Storage::url('qrcodes/brochure_' . $brochure->id . '.svg') }}" alt="Brochure QR Code" class="w-20 h-20 object-contain">
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-700">Scan to download brochure</p>
                                        <a href="{{ $brochure->qr_code }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-800 break-all font-mono">{{ $brochure->qr_code }}</a>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0">
                                    <button type="button" 
                                            onclick="downloadBrandedBrochureQr('{{ Storage::url('qrcodes/brochure_' . $brochure->id . '.svg') }}', '{{ addslashes($project->company->name ?? auth()->user()->company->name ?? 'Company') }}', '{{ addslashes($project->name) }}', '{{ addslashes($brochure->file_name) }}')" 
                                            class="inline-flex items-center justify-center px-3 py-2 border border-slate-300 rounded-lg text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 transition-colors shadow-sm space-x-1.5">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        <span>Download QR Code</span>
                                    </button>
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

<script>
    function slugify(text) {
        return text.toString().toLowerCase().trim()
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }

    async function downloadBrandedBrochureQr(svgUrl, companyName, projectName, fileName) {
        try {
            const res = await fetch(svgUrl);
            if (!res.ok) throw new Error('Failed to fetch SVG');
            const svgText = await res.text();

            const svgBlob = new Blob([svgText], { type: 'image/svg+xml;charset=utf-8' });
            const url = URL.createObjectURL(svgBlob);

            const img = new Image();
            img.onload = function() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                // Canvas dimensions (600x750 high resolution card)
                const width = 600;
                const height = 750;
                canvas.width = width;
                canvas.height = height;

                // Background
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, width, height);

                // Outer border frame
                ctx.strokeStyle = '#cbd5e1';
                ctx.lineWidth = 4;
                ctx.strokeRect(16, 16, width - 32, height - 32);

                // Top Accent Line (Emerald for brochures)
                ctx.fillStyle = '#059669';
                ctx.fillRect(16, 16, width - 32, 10);

                // Render Company Name
                ctx.fillStyle = '#0f172a';
                ctx.font = 'bold 26px sans-serif';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'top';
                ctx.fillText(companyName.toUpperCase(), width / 2, 48);

                // Render Project Name
                ctx.fillStyle = '#059669';
                ctx.font = 'bold 20px sans-serif';
                ctx.fillText(projectName + ' - Project Brochure', width / 2, 92);

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

                // Draw actual QR Image
                ctx.drawImage(img, qrX, qrY, qrSize, qrSize);

                // Footer Text
                ctx.fillStyle = '#334155';
                ctx.font = '600 16px sans-serif';
                ctx.fillText('Scan QR Code with your mobile camera', width / 2, 595);
                ctx.fillText('to download official project brochure PDF', width / 2, 622);

                // Sub-footer Tag
                ctx.fillStyle = '#64748b';
                ctx.font = '500 13px sans-serif';
                ctx.fillText(companyName + ' • ' + projectName, width / 2, 685);

                const companySlug = slugify(companyName) || 'company';
                const projectSlug = slugify(projectName) || 'project';
                const downloadFileName = `${companySlug}-${projectSlug}-brochure-qr.png`;

                canvas.toBlob(function(blob) {
                    const a = document.createElement('a');
                    a.href = URL.createObjectURL(blob);
                    a.download = downloadFileName;
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(url);
                }, 'image/png');
            };
            img.onerror = function() {
                URL.revokeObjectURL(url);
                alert('Could not generate QR image download. Please try again.');
            };
            img.src = url;
        } catch (err) {
            alert('Could not generate QR image download. Please try again.');
        }
    }
</script>
@endsection
