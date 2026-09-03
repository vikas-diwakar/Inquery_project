<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUnit;
use Illuminate\Http\Request;

class ProjectUnitController extends Controller
{
    /**
     * Display unit stacking chart & inventory management for a project
     */
    public function index(Project $project)
    {
        // Group units by Tower and Floor
        $units = $project->units()->get();
        $groupedUnits = $units->groupBy('tower_name')->map(function ($towerUnits) {
            return $towerUnits->groupBy('floor_number');
        });

        $stats = [
            'total' => $units->count(),
            'available' => $units->where('status', 'available')->count(),
            'on_hold' => $units->where('status', 'on_hold')->count(),
            'sold' => $units->where('status', 'sold')->count(),
        ];

        return view('projects.units', compact('project', 'units', 'groupedUnits', 'stats'));
    }

    /**
     * Store new unit(s) for a project
     */
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'tower_name' => 'required|string|max:100',
            'unit_number' => 'required|string|max:50',
            'floor_number' => 'required|integer|min:0',
            'unit_type' => 'nullable|string|max:50',
            'status' => 'required|in:available,on_hold,sold',
            'price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        ProjectUnit::create([
            'company_id' => $project->company_id,
            'project_id' => $project->id,
            'tower_name' => $validated['tower_name'],
            'unit_number' => $validated['unit_number'],
            'floor_number' => $validated['floor_number'],
            'unit_type' => $validated['unit_type'] ?? null,
            'status' => $validated['status'],
            'price' => $validated['price'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->back()
            ->with('success', "Unit {$validated['unit_number']} created successfully!");
    }

    /**
     * Bulk generate default units for a tower
     */
    public function generateBatch(Request $request, Project $project)
    {
        $validated = $request->validate([
            'tower_name' => 'required|string|max:100',
            'floors_count' => 'required|integer|min:1|max:50',
            'units_per_floor' => 'required|integer|min:1|max:20',
            'unit_type' => 'nullable|string|max:50',
            'base_price' => 'nullable|numeric|min:0',
        ]);

        $createdCount = 0;
        // Extract meaningful prefix: "Tower B" -> "B", "Tower A" -> "A", "Block C" -> "C"
        $words = preg_split('/\s+/', trim($validated['tower_name']));
        $lastWord = end($words);
        $prefix = (strlen($lastWord) <= 3) ? strtoupper($lastWord) : strtoupper(substr($validated['tower_name'], 0, 2));

        for ($floor = 1; $floor <= $validated['floors_count']; $floor++) {
            for ($unitIndex = 1; $unitIndex <= $validated['units_per_floor']; $unitIndex++) {
                $unitNum = sprintf('%s-%d%02d', $prefix, $floor, $unitIndex);
                
                ProjectUnit::firstOrCreate(
                    [
                        'company_id' => $project->company_id,
                        'project_id' => $project->id,
                        'tower_name' => $validated['tower_name'],
                        'unit_number' => $unitNum,
                    ],
                    [
                        'floor_number' => $floor,
                        'unit_type' => $validated['unit_type'] ?? '2 BHK',
                        'status' => 'available',
                        'price' => $validated['base_price'] ?? null,
                    ]
                );
                $createdCount++;
            }
        }

        return redirect()->back()
            ->with('success', "Generated {$createdCount} inventory units for {$validated['tower_name']}!");
    }

    /**
     * Quick status update for a unit (Available -> On Hold -> Sold)
     */
    public function updateStatus(Request $request, ProjectUnit $unit)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,on_hold,sold',
        ]);

        $unit->update([
            'status' => $validated['status'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unit_id' => $unit->id,
                'status' => $unit->status,
                'badge_class' => $unit->status_badge,
            ]);
        }

        return redirect()->back()
            ->with('success', "Unit {$unit->unit_number} status updated to " . strtoupper(str_replace('_', ' ', $unit->status)));
    }

    /**
     * Delete a unit
     */
    public function destroy(ProjectUnit $unit)
    {
        $unit->delete();

        return redirect()->back()
            ->with('success', 'Unit deleted successfully!');
    }
}
