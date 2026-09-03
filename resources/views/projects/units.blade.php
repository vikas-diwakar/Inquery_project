@extends('layouts.app')

@section('title', 'Unit Stacking Chart & Inventory - ' . $project->name)

@section('content')
<div class="max-w-7xl mx-auto py-4 space-y-6">
    <!-- Navigation Back Link & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="space-y-1">
            <a href="{{ route('projects.index') }}" class="inline-flex items-center space-x-2 text-sm font-semibold text-indigo-600 hover:text-indigo-700 transition-colors mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Projects</span>
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Interactive Unit Stacking Chart</h1>
            <p class="text-xs sm:text-sm text-slate-500">Manage floor plans, tower inventory, and live unit availability for <span class="font-bold text-slate-800">{{ $project->name }}</span>.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="button" onclick="openModal('batchGeneratorModal')" class="btn-secondary text-xs space-x-2">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span>Batch Generator</span>
            </button>
            <button type="button" onclick="openModal('addUnitModal')" class="btn-primary text-xs space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Add Unit</span>
            </button>
        </div>
    </div>

    <!-- Inventory Metric Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center space-x-4">
            <div class="h-12 w-12 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-extrabold text-lg">
                {{ $stats['total'] }}
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Units</p>
                <p class="text-sm font-bold text-slate-900">All Inventory</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-emerald-200/80 shadow-sm flex items-center space-x-4">
            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center font-extrabold text-lg">
                {{ $stats['available'] }}
            </div>
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Available 🟩</p>
                <p class="text-sm font-bold text-slate-900">Ready to Sell</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-amber-200/80 shadow-sm flex items-center space-x-4">
            <div class="h-12 w-12 rounded-xl bg-amber-50 text-amber-700 border border-amber-200 flex items-center justify-center font-extrabold text-lg">
                {{ $stats['on_hold'] }}
            </div>
            <div>
                <p class="text-xs font-semibold text-amber-600 uppercase tracking-wider">On Hold 🟨</p>
                <p class="text-sm font-bold text-slate-900">In Discussion</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-rose-200/80 shadow-sm flex items-center space-x-4">
            <div class="h-12 w-12 rounded-xl bg-rose-50 text-rose-700 border border-rose-200 flex items-center justify-center font-extrabold text-lg">
                {{ $stats['sold'] }}
            </div>
            <div>
                <p class="text-xs font-semibold text-rose-600 uppercase tracking-wider">Sold 🟥</p>
                <p class="text-sm font-bold text-slate-900">Booked Units</p>
            </div>
        </div>
    </div>

    <!-- Main Interactive Stacking Chart View -->
    @if($groupedUnits->isEmpty())
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200/80 space-y-4 max-w-xl mx-auto shadow-sm">
            <div class="h-16 w-16 bg-indigo-50 text-indigo-600 rounded-3xl flex items-center justify-center mx-auto">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">No Inventory Units Created Yet</h3>
                <p class="text-xs text-slate-500 mt-1">Use the Batch Generator to quickly generate floors & unit numbers for your towers.</p>
            </div>
            <button type="button" onclick="document.getElementById('batchGeneratorModal').classList.remove('hidden')" class="btn-primary text-xs space-x-2">
                <span>Generate Tower Inventory</span>
            </button>
        </div>
    @else
        <!-- Multi-Tower Filter Selector Tabs -->
        <div class="flex items-center space-x-2 overflow-x-auto pb-2">
            <button type="button" onclick="filterTower('all')" id="tab-all" class="tower-tab px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-md shadow-indigo-500/20 cursor-pointer shrink-0">
                🏢 All Towers ({{ $groupedUnits->count() }})
            </button>
            @foreach($groupedUnits as $towerName => $floors)
                @php
                    $tUnits = $floors->flatten();
                    $tAvail = $tUnits->where('status', 'available')->count();
                    $tHold = $tUnits->where('status', 'on_hold')->count();
                    $tSold = $tUnits->where('status', 'sold')->count();
                @endphp
                <button type="button" onclick="filterTower('{{ Str::slug($towerName) }}')" id="tab-{{ Str::slug($towerName) }}" class="tower-tab px-4 py-2.5 rounded-xl text-xs font-bold bg-white text-slate-700 hover:text-slate-900 border border-slate-200/80 hover:bg-slate-50 cursor-pointer transition-all shrink-0 flex items-center space-x-2">
                    <span>🏢 {{ $towerName }}</span>
                    <span class="text-[10px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200">
                        <span class="text-emerald-700 font-bold">{{ $tAvail }} Avail</span> • 
                        <span class="text-amber-700 font-bold">{{ $tHold }} Hold</span> • 
                        <span class="text-rose-700 font-bold">{{ $tSold }} Sold</span>
                    </span>
                </button>
            @endforeach
        </div>

        <div class="space-y-8">
            @foreach($groupedUnits as $towerName => $floors)
                @php
                    $tUnits = $floors->flatten();
                    $tAvail = $tUnits->where('status', 'available')->count();
                    $tHold = $tUnits->where('status', 'on_hold')->count();
                    $tSold = $tUnits->where('status', 'sold')->count();
                @endphp
                <div id="tower-card-{{ Str::slug($towerName) }}" class="tower-card bg-white rounded-3xl shadow-sm border border-slate-200/80 p-6 sm:p-8 space-y-6">
                    <!-- Tower Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4 gap-3">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-2xl bg-indigo-600 text-white font-extrabold flex items-center justify-center text-sm shadow-md shadow-indigo-500/20 shrink-0">
                                {{ strtoupper(substr($towerName, 0, 1)) }}
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900">{{ $towerName }}</h2>
                                <p class="text-xs text-slate-500">{{ $floors->count() }} Floors • {{ $tUnits->count() }} Total Units</p>
                            </div>
                        </div>

                        <!-- Tower-Wise Availability Metric Badges -->
                        <div class="flex items-center space-x-2 text-xs font-bold">
                            <span class="inline-flex items-center px-3 py-1 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200/80 shadow-2xs">
                                <span class="h-2 w-2 rounded-full bg-emerald-500 mr-1.5"></span>
                                <span>{{ $tAvail }} Available</span>
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/80 shadow-2xs">
                                <span class="h-2 w-2 rounded-full bg-amber-500 mr-1.5"></span>
                                <span>{{ $tHold }} On Hold</span>
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-xl bg-rose-50 text-rose-700 border border-rose-200/80 shadow-2xs">
                                <span class="h-2 w-2 rounded-full bg-rose-500 mr-1.5"></span>
                                <span>{{ $tSold }} Sold</span>
                            </span>
                        </div>
                    </div>

                    <!-- Stacking Floors Grid (Floor 5 at top down to Floor 1) -->
                    <div class="space-y-3">
                        @foreach($floors->sortKeysDesc() as $floorNum => $unitsOnFloor)
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-3 rounded-2xl bg-slate-50/70 border border-slate-200/60">
                                <!-- Floor Badge -->
                                <div class="sm:w-28 shrink-0 flex items-center space-x-2">
                                    <span class="text-xs font-extrabold text-slate-500 bg-white px-2.5 py-1 rounded-lg border border-slate-200">Floor {{ $floorNum }}</span>
                                </div>

                                <!-- Unit Pills Grid -->
                                <div class="flex flex-wrap gap-2.5 flex-1">
                                    @foreach($unitsOnFloor as $unit)
                                        <div class="relative group">
                                            <button type="button" onclick="openStatusModal({{ $unit->id }}, '{{ addslashes($unit->unit_number) }}', '{{ $unit->status }}')" 
                                                class="px-3.5 py-2 rounded-xl text-xs font-bold border transition-all cursor-pointer flex flex-col items-center min-w-[70px] shadow-2xs {{ $unit->status_badge }}">
                                                <span>{{ $unit->unit_number }}</span>
                                                <span class="text-[9px] opacity-75 font-medium mt-0.5">{{ $unit->unit_type ?? 'Unit' }}</span>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Modal: Quick Unit Status Update -->
<div id="unitStatusModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-sm w-full p-6 space-y-4 my-auto relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-base font-bold text-slate-900">Update Unit Status</h3>
            <button type="button" onclick="closeModal('unitStatusModal')" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>
        <p class="text-xs text-slate-500">Unit Number: <strong id="modalUnitNumber" class="text-slate-900"></strong></p>

        <form id="unitStatusForm" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="space-y-2">
                <button type="submit" name="status" value="available" class="w-full text-left p-3 rounded-xl border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold flex items-center justify-between transition-colors">
                    <span>🟩 Available (Ready to Sell)</span>
                </button>
                <button type="submit" name="status" value="on_hold" class="w-full text-left p-3 rounded-xl border border-amber-200 bg-amber-50 hover:bg-amber-100 text-amber-800 text-xs font-bold flex items-center justify-between transition-colors">
                    <span>🟨 On Hold (Buyer Interested)</span>
                </button>
                <button type="submit" name="status" value="sold" class="w-full text-left p-3 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-800 text-xs font-bold flex items-center justify-between transition-colors">
                    <span>🟥 Sold (Booked / Complete)</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Batch Unit Generator -->
<div id="batchGeneratorModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-md w-full p-6 sm:p-8 space-y-5 my-auto relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-lg font-bold text-slate-900">Batch Inventory Generator</h3>
            <button type="button" onclick="closeModal('batchGeneratorModal')" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>
        <p class="text-xs text-slate-500">Quickly generate floor-by-floor unit numbers for a tower or block.</p>

        <form action="{{ route('projects.units.batch', $project) }}" method="POST" class="space-y-4">
            @csrf
            <div class="space-y-1.5">
                <label for="batch_tower_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Tower / Block Name</label>
                <input type="text" name="tower_name" id="batch_tower_name" required value="Tower A" class="input-field" placeholder="e.g. Tower A">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="floors_count" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Number of Floors</label>
                    <input type="number" name="floors_count" id="floors_count" required value="5" min="1" max="50" class="input-field">
                </div>
                <div class="space-y-1.5">
                    <label for="units_per_floor" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Units Per Floor</label>
                    <input type="number" name="units_per_floor" id="units_per_floor" required value="4" min="1" max="20" class="input-field">
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="batch_unit_type" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Default Unit Type</label>
                <input type="text" name="unit_type" id="batch_unit_type" value="2 BHK" class="input-field" placeholder="e.g. 2 BHK">
            </div>

            <div class="pt-3 border-t border-slate-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('batchGeneratorModal')" class="btn-secondary text-xs">Cancel</button>
                <button type="submit" class="btn-primary text-xs">Generate Batch Units</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Single Unit -->
<div id="addUnitModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-md w-full p-6 sm:p-8 space-y-5 my-auto relative">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h3 class="text-lg font-bold text-slate-900">Add Inventory Unit</h3>
            <button type="button" onclick="closeModal('addUnitModal')" class="text-slate-400 hover:text-slate-600 p-1">✕</button>
        </div>

        <form action="{{ route('projects.units.store', $project) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="tower_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Tower Name</label>
                    <input type="text" name="tower_name" id="tower_name" required value="Tower A" class="input-field">
                </div>
                <div class="space-y-1.5">
                    <label for="unit_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Unit Number</label>
                    <input type="text" name="unit_number" id="unit_number" required placeholder="e.g. A-101" class="input-field">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label for="floor_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Floor Number</label>
                    <input type="number" name="floor_number" id="floor_number" required value="1" min="0" class="input-field">
                </div>
                <div class="space-y-1.5">
                    <label for="status" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Initial Status</label>
                    <select name="status" id="status" class="input-field cursor-pointer">
                        <option value="available">Available 🟩</option>
                        <option value="on_hold">On Hold 🟨</option>
                        <option value="sold">Sold 🟥</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label for="unit_type" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Unit Configuration</label>
                <input type="text" name="unit_type" id="unit_type" placeholder="e.g. 3 BHK Luxury" class="input-field">
            </div>

            <div class="pt-3 border-t border-slate-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal('addUnitModal')" class="btn-secondary text-xs">Cancel</button>
                <button type="submit" class="btn-primary text-xs">Create Unit</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    function openStatusModal(unitId, unitNum, currentStatus) {
        document.getElementById('modalUnitNumber').innerText = unitNum;
        document.getElementById('unitStatusForm').action = '/units/' + unitId + '/status';
        openModal('unitStatusModal');
    }

    function filterTower(slug) {
        // Toggle tab styles
        document.querySelectorAll('.tower-tab').forEach(tab => {
            tab.className = 'tower-tab px-4 py-2.5 rounded-xl text-xs font-bold bg-white text-slate-700 hover:text-slate-900 border border-slate-200/80 hover:bg-slate-50 cursor-pointer transition-all shrink-0 flex items-center space-x-2';
        });

        const activeTab = document.getElementById('tab-' + slug);
        if (activeTab) {
            activeTab.className = 'tower-tab px-4 py-2.5 rounded-xl text-xs font-bold bg-indigo-600 text-white shadow-md shadow-indigo-500/20 cursor-pointer shrink-0 flex items-center space-x-2';
        }

        // Show/hide tower cards
        document.querySelectorAll('.tower-card').forEach(card => {
            if (slug === 'all' || card.id === 'tower-card-' + slug) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
</script>
@endsection
