<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiry Form - {{ $project->name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-mesh-light min-h-screen py-8 px-4 sm:px-6 lg:px-8 text-slate-800 font-sans antialiased">
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Project Hero Card -->
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
            <!-- Header Banner -->
            <div class="bg-slate-900 p-6 sm:p-8 text-white relative overflow-hidden flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="absolute -top-12 -right-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-2xl pointer-events-none"></div>
                <div class="space-y-1 relative z-10">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-indigo-400 bg-indigo-950/60 px-2.5 py-1 rounded-full border border-indigo-800/40">Official Inquiry Form</span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-1">{{ $project->name }}</h1>
                    @if($project->location)
                        <p class="text-xs sm:text-sm text-slate-300 flex items-center">
                            <svg class="w-4 h-4 text-indigo-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>{{ $project->location }}</span>
                        </p>
                    @endif
                </div>

                @if($project->logo)
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($project->logo) }}" alt="{{ $project->name }}" class="h-16 w-16 rounded-2xl object-cover border-2 border-white/20 shadow-lg relative z-10 shrink-0">
                @endif
            </div>

            <!-- 360° Virtual Tour Banner (If available) -->
            @if($project->virtual_tour_url)
                <div class="p-6 bg-gradient-to-r from-purple-900 to-indigo-900 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-indigo-800/40">
                    <div class="space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-purple-300">360° Walkthrough</span>
                        <h3 class="text-base font-bold">Explore {{ $project->name }} in 360° Virtual Reality</h3>
                        <p class="text-xs text-purple-200">Take an interactive virtual walkthrough tour of the project amenities and sample units.</p>
                    </div>
                    <a href="{{ $project->virtual_tour_url }}" target="_blank" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-600 text-white font-bold text-xs transition-all shadow-lg shadow-purple-500/30 shrink-0 space-x-2">
                        <span>🕶️ Open 360° Virtual Tour</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>
            @endif

            <!-- Unit Availability Stacking Chart Section -->
            @php
                $projectUnits = $project->units;
            @endphp

            @if($projectUnits->isNotEmpty())
                <div class="p-6 sm:p-8 bg-slate-50/70 border-b border-slate-200/80 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Live Unit Availability Map</h3>
                            <p class="text-xs text-slate-500">Real-time inventory status across project floors & towers.</p>
                        </div>
                        <div class="flex items-center space-x-3 text-[11px] font-bold">
                            <span class="flex items-center space-x-1 text-emerald-700"><span class="h-2 w-2 rounded-full bg-emerald-500"></span><span>Available</span></span>
                            <span class="flex items-center space-x-1 text-amber-700"><span class="h-2 w-2 rounded-full bg-amber-500"></span><span>On Hold</span></span>
                            <span class="flex items-center space-x-1 text-rose-700"><span class="h-2 w-2 rounded-full bg-rose-500"></span><span>Sold</span></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-3 max-h-64 overflow-y-auto pr-1">
                        @foreach($projectUnits->groupBy('tower_name') as $towerName => $towerUnits)
                            @php
                                $puAvail = $towerUnits->where('status', 'available')->count();
                                $puHold = $towerUnits->where('status', 'on_hold')->count();
                                $puSold = $towerUnits->where('status', 'sold')->count();
                            @endphp
                            <div class="p-3 bg-white rounded-2xl border border-slate-200/80 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">{{ $towerName }}</span>
                                    <div class="flex items-center space-x-2 text-[10px] font-bold">
                                        <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">{{ $puAvail }} Avail</span>
                                        <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">{{ $puHold }} Hold</span>
                                        <span class="text-rose-700 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200">{{ $puSold }} Sold</span>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($towerUnits as $unit)
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $unit->status_badge }}">
                                            {{ $unit->unit_number }} ({{ $unit->unit_type }})
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Form Content -->
            <div class="p-6 sm:p-8 space-y-6">
                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center space-x-3">
                        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
                        <p class="font-bold">Please correct the following errors:</p>
                        <ul class="list-disc list-inside text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('public.inquiry.store', $project) }}" method="POST" class="space-y-5">
                    @csrf
                    
                    <div class="space-y-4">
                        <!-- Name -->
                        <div class="space-y-1.5">
                            <label for="customer_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Full Name <span class="text-rose-500">*</span></label>
                            <input id="customer_name" name="customer_name" type="text" required value="{{ old('customer_name') }}" class="input-field" placeholder="Enter your full name">
                        </div>

                        <!-- Phone -->
                        <div class="space-y-1.5">
                            <label for="phone" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">WhatsApp Phone Number <span class="text-rose-500">*</span></label>
                            <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}" class="input-field" placeholder="e.g. +1 234 567 8900">
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Email Address (Optional)</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" class="input-field" placeholder="your.email@domain.com">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Budget -->
                            <div class="space-y-1.5">
                                <label for="budget" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Estimated Budget ($)</label>
                                <input id="budget" name="budget" type="number" step="0.01" value="{{ old('budget') }}" class="input-field" placeholder="e.g. 250000">
                            </div>

                            <!-- Unit Option -->
                            <div class="space-y-1.5">
                                <label for="selected_unit_option_id" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Preferred Flat / Unit Type</label>
                                <select id="selected_unit_option_id" name="selected_unit_option_id" class="input-field cursor-pointer">
                                    <option value="">-- Select Preferred Unit --</option>
                                    @foreach($project->enabledUnitOptions as $option)
                                        <option value="{{ $option->id }}" {{ old('selected_unit_option_id') == $option->id ? 'selected' : '' }}>
                                            {{ $option->option_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="space-y-1.5">
                            <label for="message" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Message / Requirements</label>
                            <textarea id="message" name="message" rows="3" class="input-field" placeholder="Specify preferred floor, possession date, financing options, etc.">{{ old('message') }}</textarea>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="btn-primary w-full space-x-2 py-3.5 text-base">
                            <span>Submit Inquiry & Receive Brochure</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

