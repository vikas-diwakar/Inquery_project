@extends('layouts.app')

@section('title', 'Create Inquiry Form QR')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-slate-900">Create Inquiry Form QR</h1>
        <p class="mt-2 text-sm text-slate-600">Configure inquiry form settings and generate or regenerate its QR code</p>
    </div>

    <div class="bg-white shadow-xl rounded-3xl border border-slate-200/80 p-6 sm:p-8 space-y-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Inquiry Form Fields & Features</h2>
            <p class="text-sm text-slate-600 mt-1">
                The public inquiry form includes the following fields and sections when users scan the QR code:
            </p>
        </div>
        
        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5">
            <ul class="space-y-3 text-sm text-slate-700">
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-emerald-500 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><strong>Customer Name</strong> <span class="text-rose-500 font-semibold">(Required)</span></span>
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-emerald-500 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><strong>Phone</strong> <span class="text-rose-500 font-semibold">(Required)</span></span>
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-emerald-500 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><strong>Email Address</strong> <span class="text-slate-500">(Optional)</span></span>
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-emerald-500 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><strong>Budget</strong> <span class="text-slate-500">(Optional)</span></span>
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-emerald-500 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><strong>Flat Type / Preferred Option</strong> <span class="text-slate-500">(Optional - e.g., 2BHK, 3BHK)</span></span>
                </li>
                <li class="flex items-center">
                    <svg class="w-5 h-5 text-emerald-500 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><strong>Message / Requirements</strong> <span class="text-slate-500">(Optional)</span></span>
                </li>
                <li class="flex items-center justify-between pt-2 border-t border-slate-200">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <span><strong>Stacking Chart & Live Unit Availability</strong></span>
                    </div>
                    <span id="stacking-chart-status-badge" class="text-xs font-extrabold px-3 py-1 rounded-full {{ ($project->show_stacking_chart ?? true) ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                        {{ ($project->show_stacking_chart ?? true) ? '● ON (Showing)' : '○ OFF (Hidden)' }}
                    </span>
                </li>
            </ul>
        </div>

        <div class="p-4 bg-indigo-50/80 border border-indigo-100 rounded-2xl">
            <p class="text-xs font-bold uppercase tracking-wider text-indigo-600">Selected Project</p>
            <p class="text-lg font-extrabold text-slate-900 mt-0.5">{{ $project->name }}</p>
            @if($project->location)
                <p class="text-xs text-slate-600 mt-0.5 flex items-center">
                    <svg class="w-3.5 h-3.5 text-indigo-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>{{ $project->location }}</span>
                </p>
            @endif
        </div>

        <form action="{{ route('forms-qr.generate-inquiry-qr') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Custom ON / OFF Toggle Switch Card matching design -->
            <div class="bg-slate-50 border-2 border-indigo-200/80 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-extrabold text-slate-900">Show Stacking Chart & Live Units Map</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-indigo-100 text-indigo-700">Display Setting</span>
                    </div>
                    <p class="text-xs text-slate-600">
                        When turned <strong>ON</strong>, buyers scanning this QR code will see the interactive floor stacking chart & unit availability map on the inquiry form. When turned <strong>OFF</strong>, the unit map will be hidden.
                    </p>
                </div>

                <div class="flex items-center shrink-0">
                    <label id="stacking-chart-toggle-label" class="relative inline-flex items-center cursor-pointer select-none shrink-0 w-20 h-9" title="Toggle Stacking Chart ON/OFF">
                        <input type="checkbox" id="show_stacking_chart_input" name="show_stacking_chart" value="1" class="sr-only peer" {{ old('show_stacking_chart', $project->show_stacking_chart ?? true) ? 'checked' : '' }} onchange="updateStackingChartUI(this)">
                        
                        <!-- Track background (Green when checked ON, Red when unchecked OFF) -->
                        <div class="absolute inset-0 bg-rose-500 peer-checked:bg-emerald-500 rounded-full transition-colors duration-300 ease-in-out shadow-inner pointer-events-none"></div>
                        
                        <!-- ON Text (Left side inside pill) -->
                        <span class="absolute left-3.5 text-[11px] font-black text-white uppercase tracking-wider opacity-0 peer-checked:opacity-100 transition-opacity duration-200 pointer-events-none z-10">ON</span>
                        
                        <!-- OFF Text (Right side inside pill) -->
                        <span class="absolute right-3 text-[11px] font-black text-white uppercase tracking-wider opacity-100 peer-checked:opacity-0 transition-opacity duration-200 pointer-events-none z-10">OFF</span>
                        
                        <!-- White Circular Slider Knob -->
                        <div class="absolute left-1 top-1 w-7 h-7 bg-white rounded-full shadow-md transform transition-transform duration-300 ease-in-out pointer-events-none z-20 peer-checked:translate-x-[44px]"></div>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                <a href="{{ route('forms-qr.index') }}" class="text-sm font-bold text-slate-600 hover:text-slate-900 transition">
                    ← Back to Forms & QR Codes
                </a>
                <button type="submit" class="btn-primary px-8 py-3 font-bold text-sm shadow-lg shadow-indigo-600/30">
                    Generate QR Code →
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function updateStackingChartUI(input) {
    const badge = document.getElementById('stacking-chart-status-badge');
    if (!badge) return;
    if (input.checked) {
        badge.textContent = '● ON (Showing)';
        badge.className = 'text-xs font-extrabold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800';
    } else {
        badge.textContent = '○ OFF (Hidden)';
        badge.className = 'text-xs font-extrabold px-3 py-1 rounded-full bg-rose-100 text-rose-800';
    }
}
</script>
@endsection
