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
            <button type="button" onclick="openModal('batchGeneratorModal')" class="btn-primary text-xs space-x-2">
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
                                <div class="flex flex-wrap gap-3 flex-1">
                                    @foreach($unitsOnFloor as $unit)
                                        @php
                                            $unitData = [
                                                'id' => $unit->id,
                                                'unit_number' => $unit->unit_number,
                                                'tower_name' => $unit->tower_name,
                                                'floor_number' => $unit->floor_number,
                                                'unit_type' => $unit->unit_type ?? '',
                                                'status' => $unit->status,
                                                'price' => $unit->price,
                                                'notes' => $unit->notes ?? '',
                                            ];
                                        @endphp
                                        <div class="relative group/unit">
                                            <button type="button" onclick='openEditUnitModal(@json($unitData))' 
                                                class="px-3.5 py-2.5 rounded-xl text-xs font-bold border transition-all cursor-pointer flex flex-col items-center min-w-[76px] shadow-2xs hover:shadow-md hover:-translate-y-0.5 active:translate-y-0 {{ $unit->status_badge }}">
                                                <span class="font-extrabold tracking-tight">{{ $unit->unit_number }}</span>
                                                <span class="text-[9px] opacity-75 font-medium mt-0.5 truncate max-w-[72px]">{{ $unit->unit_type ?: 'Unit' }}</span>
                                                @if($unit->price)
                                                    <span class="text-[8px] opacity-85 font-semibold mt-0.5 text-slate-600">₹{{ number_format($unit->price) }}</span>
                                                @endif
                                            </button>

                                            <!-- Hover Action Buttons: Edit & Delete -->
                                            <div class="absolute -top-2.5 -right-2 hidden group-hover/unit:flex items-center space-x-0.5 bg-white rounded-lg shadow-md border border-slate-200/90 p-0.5 z-20">
                                                <button type="button" onclick='event.stopPropagation(); openEditUnitModal(@json($unitData))' 
                                                    title="Edit Unit" 
                                                    class="p-1 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                </button>
                                                <button type="button" onclick="event.stopPropagation(); confirmDeleteUnit({{ $unit->id }}, '{{ addslashes($unit->unit_number) }}')" 
                                                    title="Delete Unit" 
                                                    class="p-1 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
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

<!-- Modal: Edit Unit & Inventory Management -->
<div id="editUnitModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden overflow-y-auto" style="display: none;">
    <div class="min-h-full flex items-center justify-center p-4 text-center">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-md w-full p-6 sm:p-8 space-y-5 text-left relative m-auto transform transition-all">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 flex items-center space-x-2">
                        <span>Edit Unit</span>
                        <span id="edit_modal_title_unit" class="text-indigo-600 font-extrabold"></span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Modify unit specifications, pricing, or live status.</p>
                </div>
                <button type="button" onclick="closeModal('editUnitModal')" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="editUnitForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Live Status Selection -->
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Live Inventory Status</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="status" id="edit_status_available" value="available" class="sr-only peer">
                            <div class="p-2.5 rounded-xl border text-center text-xs font-bold transition-all peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 peer-checked:shadow-sm bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100">
                                🟩 Available
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status" id="edit_status_on_hold" value="on_hold" class="sr-only peer">
                            <div class="p-2.5 rounded-xl border text-center text-xs font-bold transition-all peer-checked:bg-amber-600 peer-checked:text-white peer-checked:border-amber-600 peer-checked:shadow-sm bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100">
                                🟨 On Hold
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="status" id="edit_status_sold" value="sold" class="sr-only peer">
                            <div class="p-2.5 rounded-xl border text-center text-xs font-bold transition-all peer-checked:bg-rose-600 peer-checked:text-white peer-checked:border-rose-600 peer-checked:shadow-sm bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100">
                                🟥 Sold
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label for="edit_tower_name" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Tower / Block</label>
                        <input type="text" name="tower_name" id="edit_tower_name" required class="input-field text-sm">
                    </div>
                    <div class="space-y-1">
                        <label for="edit_unit_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Unit Number</label>
                        <input type="text" name="unit_number" id="edit_unit_number" required class="input-field text-sm font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label for="edit_floor_number" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Floor Number</label>
                        <input type="number" name="floor_number" id="edit_floor_number" required min="0" class="input-field text-sm">
                    </div>
                    <div class="space-y-1">
                        <label for="edit_unit_type" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Configuration</label>
                        <input type="text" name="unit_type" id="edit_unit_type" placeholder="e.g. 2 BHK, 3 BHK, Penthouse" class="input-field text-sm">
                    </div>
                </div>

                <div class="space-y-1">
                    <label for="edit_price" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Price (₹)</label>
                    <input type="number" name="price" id="edit_price" step="0.01" min="0" placeholder="Optional unit price" class="input-field text-sm">
                </div>

                <div class="space-y-1">
                    <label for="edit_notes" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Notes & Remarks</label>
                    <textarea name="notes" id="edit_notes" rows="2" placeholder="Optional notes (e.g. corner unit, garden view, reserved by buyer)" class="input-field text-sm resize-none"></textarea>
                </div>

                <!-- Modal Action Footer -->
                <div class="pt-4 border-t border-slate-200 flex items-center justify-between gap-3">
                    <button type="button" onclick="deleteCurrentUnitFromModal()" class="btn-danger text-xs space-x-1.5 inline-flex items-center py-2 px-3">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Delete Unit</span>
                    </button>

                    <div class="flex items-center space-x-2">
                        <button type="button" onclick="closeModal('editUnitModal')" class="btn-secondary text-xs py-2 px-3">Cancel</button>
                        <button type="submit" class="btn-primary text-xs py-2 px-4 font-bold">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Unit Form -->
<form id="deleteUnitForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

<!-- Modal: Batch Unit Generator -->
<div id="batchGeneratorModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden overflow-y-auto" style="display: none;">
    <div class="min-h-full flex items-center justify-center p-4 text-center">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-md w-full p-6 sm:p-8 space-y-5 text-left relative m-auto transform transition-all">
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
</div>

<!-- Modal: Add Single Unit -->
<div id="addUnitModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden overflow-y-auto" style="display: none;">
    <div class="min-h-full flex items-center justify-center p-4 text-center">
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/80 max-w-md w-full p-6 sm:p-8 space-y-5 text-left relative m-auto transform transition-all">
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

                <div class="space-y-1.5">
                    <label for="price" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Price (₹)</label>
                    <input type="number" name="price" id="price" step="0.01" min="0" placeholder="Optional unit pricing" class="input-field">
                </div>

                <div class="space-y-1.5">
                    <label for="notes" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Notes & Remarks</label>
                    <textarea name="notes" id="notes" rows="2" placeholder="Optional notes" class="input-field resize-none"></textarea>
                </div>

                <div class="pt-3 border-t border-slate-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('addUnitModal')" class="btn-secondary text-xs">Cancel</button>
                    <button type="submit" class="btn-primary text-xs">Create Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentEditingUnit = null;

    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'block';
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    function openEditUnitModal(unit) {
        currentEditingUnit = unit;
        document.getElementById('edit_modal_title_unit').textContent = unit.unit_number;
        document.getElementById('editUnitForm').action = '/units/' + unit.id;
        document.getElementById('edit_tower_name').value = unit.tower_name || '';
        document.getElementById('edit_unit_number').value = unit.unit_number || '';
        document.getElementById('edit_floor_number').value = unit.floor_number ?? 1;
        document.getElementById('edit_unit_type').value = unit.unit_type || '';
        document.getElementById('edit_price').value = unit.price ?? '';
        document.getElementById('edit_notes').value = unit.notes || '';

        // Check the matching status radio button
        const statusRadio = document.getElementById('edit_status_' + unit.status);
        if (statusRadio) {
            statusRadio.checked = true;
        }

        openModal('editUnitModal');
    }

    // Backward compatibility for any external triggers
    function openStatusModal(unitId, unitNum, currentStatus) {
        openEditUnitModal({
            id: unitId,
            unit_number: unitNum,
            status: currentStatus,
            tower_name: '',
            floor_number: 1,
            unit_type: '',
            price: '',
            notes: ''
        });
    }

    function deleteCurrentUnitFromModal() {
        if (!currentEditingUnit) return;
        const unitId = currentEditingUnit.id;
        const unitNumber = currentEditingUnit.unit_number;
        closeModal('editUnitModal');
        confirmDeleteUnit(unitId, unitNumber);
    }

    function confirmDeleteUnit(unitId, unitNumber) {
        showConfirmationModal(
            'Delete Inventory Unit',
            'Are you sure you want to delete unit "' + unitNumber + '"? This will permanently remove it from the tower stacking chart.',
            function() {
                const form = document.getElementById('deleteUnitForm');
                form.action = '/units/' + unitId;
                form.submit();
            },
            {
                confirmText: 'Yes, Delete Unit',
                btnClass: 'btn-danger'
            }
        );
    }

    // Close modal on click outside (backdrop)
    ['editUnitModal', 'batchGeneratorModal', 'addUnitModal'].forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this || e.target.classList.contains('min-h-full')) {
                    closeModal(modalId);
                }
            });
        }
    });

    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal('editUnitModal');
            closeModal('batchGeneratorModal');
            closeModal('addUnitModal');
        }
    });

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
