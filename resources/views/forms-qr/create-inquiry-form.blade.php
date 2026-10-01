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

                <!-- Company Custom Inquiry Fields -->
                @php
                    $activeCustomFields = $project->customFields ?? collect();
                @endphp
                @if($activeCustomFields->isNotEmpty())
                    <li class="pt-3 border-t border-slate-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">Company Added Custom Fields ({{ $activeCustomFields->count() }})</span>
                        </div>
                        <div class="space-y-2">
                            @foreach($activeCustomFields as $cField)
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-white border border-slate-200 shadow-sm">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span class="font-bold text-slate-800">{{ $cField->field_label }}</span>
                                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200">{{ strtoupper($cField->field_type) }}</span>
                                        @if($cField->is_required)
                                            <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">Required *</span>
                                        @else
                                            <span class="text-[10px] text-slate-500">Optional</span>
                                        @endif
                                        @if($cField->field_type === 'select' && is_array($cField->field_options))
                                            <span class="text-[10px] text-slate-400">({{ implode(', ', $cField->field_options) }})</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <form action="{{ route('forms-qr.custom-fields.toggle', $cField) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs font-bold px-2 py-1 rounded-lg {{ $cField->is_active ? 'text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200' : 'text-slate-400 bg-slate-100 hover:bg-slate-200' }}">
                                                {{ $cField->is_active ? 'Active' : 'Disabled' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('forms-qr.custom-fields.delete', $cField) }}" method="POST" class="inline" onsubmit="return confirm('Delete custom field \'{{ $cField->field_label }}\'?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-slate-400 hover:text-rose-600 p-1" title="Delete Field">✕</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </li>
                @endif
            </ul>

            <!-- Add New Custom Field Section -->
            <div class="mt-4 pt-4 border-t border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Want to collect more details from leads?</h4>
                        <p class="text-[11px] text-slate-500">Add custom questions like Occupation, Financing Needed, Current City, etc.</p>
                    </div>
                    <button type="button" onclick="document.getElementById('add-custom-field-modal').classList.toggle('hidden')" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs shadow-sm transition-all flex items-center space-x-1">
                        <span>+ Add New Field</span>
                    </button>
                </div>

                <!-- Inline Add Field Form / Modal Card -->
                <div id="add-custom-field-modal" class="hidden mt-4 p-4 bg-white border-2 border-indigo-200 rounded-2xl shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <span class="text-xs font-extrabold uppercase text-indigo-700">Add Custom Field to Inquiry Form</span>
                        <button type="button" onclick="document.getElementById('add-custom-field-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-xs">✕ Close</button>
                    </div>

                    <form action="{{ route('forms-qr.custom-fields.store') }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Field Label *</label>
                                <input type="text" name="field_label" required placeholder="e.g. Occupation, City, Loan Required" class="input-field border px-3 py-2 text-xs w-full rounded-lg">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Field Input Type *</label>
                                <select name="field_type" id="new_field_type_select" required class="input-field border px-3 py-2 text-xs w-full rounded-lg" onchange="toggleFieldOptionsInput(this.value)">
                                    <option value="text">Short Text (e.g. City, Occupation)</option>
                                    <option value="number">Number (e.g. Expected Downpayment)</option>
                                    <option value="select">Dropdown Select (e.g. Yes/No, Salaried/Business)</option>
                                    <option value="textarea">Long Text Area</option>
                                </select>
                            </div>
                        </div>

                        <div id="field_options_container" class="hidden">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Dropdown Choices (comma-separated)</label>
                            <input type="text" name="field_options_raw" placeholder="e.g. Salaried, Self-Employed, Business, NRI" class="input-field border px-3 py-2 text-xs w-full rounded-lg">
                            <span class="text-[10px] text-slate-400">Separate each dropdown choice with a comma.</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Placeholder Text (Optional)</label>
                            <input type="text" name="placeholder" placeholder="e.g. Enter your current occupation" class="input-field border px-3 py-2 text-xs w-full rounded-lg">
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="is_required" value="1" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="text-xs font-semibold text-slate-700">Make this field Required for buyers</span>
                            </label>
                            <button type="submit" class="btn-primary text-xs px-4 py-2 rounded-xl">
                                <span>Save Field to Form</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
        function toggleFieldOptionsInput(val) {
            const container = document.getElementById('field_options_container');
            if (val === 'select') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
        </script>

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
