<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of projects
     */
    public function index()
    {
        $query = Project::where('company_id', auth()->user()->company_id);

        // If user is not admin, only show projects they're assigned to
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $assignedProjectIds = $user->projects()->pluck('projects.id')->toArray();
            if (!empty($assignedProjectIds)) {
                $query->whereIn('id', $assignedProjectIds);
            } else {
                // If user has no assigned projects, show empty result
                $query->whereRaw('1 = 0');
            }
        }

        $projects = $query->latest()->paginate(15);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'status' => 'required|in:planning,ongoing,completed,on_hold',
            'logo' => 'nullable|image|max:2048',
            'selected_unit_options' => 'nullable|array',
            'selected_unit_options.*' => 'string|max:255',
        ]);

        $project = Project::create([
            'company_id' => auth()->user()->company_id,
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'status' => $validated['status'],
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('projects', 'public');
            $project->logo = $logoPath;
            $project->save();
        }

        // Create unit options from selected predefined options
        if ($request->has('selected_unit_options') && is_array($request->selected_unit_options)) {
            $predefinedOptions = [
                '1 BHK', '2 BHK', '3 BHK', '4 BHK', '5 BHK',
                'Studio', 'Penthouse', 'Villa', 'Shop', 'Office', 'Plot'
            ];

            foreach ($request->selected_unit_options as $index => $optionName) {
                // Validate that the option is from our predefined list
                if (in_array($optionName, $predefinedOptions)) {
                    $project->unitOptions()->create([
                        'option_name' => $optionName,
                        'is_enabled' => true, // All selected options are enabled by default
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        // Generate unique inquiry QR code URL (company-wise and project-wise unique)
        // This URL is automatically unique per company (via project.company_id)
        // and per project (via project.id), ensuring company-wise and project-wise uniqueness
        $inquiryUrl = $project->getInquiryFormUrl();
        $project->inquiry_qr_code = $inquiryUrl;
        $project->save();

        return redirect()->route('dashboard')
            ->with('success', 'Project created successfully!');
    }

    /**
     * Select a project (set in session)
     */
    public function select(Request $request, Project $project)
    {
        if (!auth()->user()->can('view', $project)) {
            abort(403, 'Unauthorized access');
        }

        // Verify project belongs to user's company
        if ($project->company_id !== auth()->user()->company_id) {
            abort(403, 'Unauthorized access');
        }

        // Set selected project in session
        session(['selected_project_id' => $project->id]);

        return redirect()->route('dashboard')
            ->with('success', "Project '{$project->name}' selected successfully!");
    }

    /**
     * Clear selected project from session
     */
    public function clearSelection()
    {
        session()->forget('selected_project_id');
        
        return redirect()->route('dashboard')
            ->with('success', 'Project selection cleared. Please select a project to continue.');
    }

    /**
     * Display the specified project
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load(['inquiries', 'brochures']);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the project
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        return view('projects.edit', compact('project'));
    }

    /**
     * Update the project
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'status' => 'required|in:planning,ongoing,completed,on_hold',
            'logo' => 'nullable|image|max:2048',
            'selected_unit_options' => 'nullable|array',
            'selected_unit_options.*' => 'required|string|max:255',
        ]);

        $project->update([
            'name' => $validated['name'],
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'status' => $validated['status'],
        ]);

        // Handle unit options - delete all existing and create new ones based on selection
        $project->unitOptions()->delete(); // Remove all existing unit options

        if ($request->has('selected_unit_options') && is_array($request->selected_unit_options)) {
            $predefinedOptions = [
                '1 BHK', '2 BHK', '3 BHK', '4 BHK', '5 BHK',
                'Studio', 'Penthouse', 'Villa', 'Shop', 'Office', 'Plot'
            ];

            foreach ($request->selected_unit_options as $index => $optionName) {
                // Validate that the option is from our predefined list
                if (in_array($optionName, $predefinedOptions)) {
                    $project->unitOptions()->create([
                        'option_name' => $optionName,
                        'is_enabled' => true, // All selected options are enabled by default
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        // Ensure QR code URL is always set (in case it wasn't set during creation)
        if (!$project->inquiry_qr_code) {
            $project->inquiry_qr_code = $project->getInquiryFormUrl();
            $project->save();
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($project->logo) {
                Storage::disk('public')->delete($project->logo);
            }
            $logoPath = $request->file('logo')->store('projects', 'public');
            $project->logo = $logoPath;
            $project->save();
        }

        return redirect()->route('dashboard')
            ->with('success', 'Project updated successfully!');
    }

    /**
     * Remove the project
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        // Clear selected project from session if this is the selected project
        if (session('selected_project_id') == $project->id) {
            session()->forget('selected_project_id');
        }

        // Delete logo if exists
        if ($project->logo) {
            Storage::disk('public')->delete($project->logo);
        }

        // Delete QR code if exists
        if ($project->inquiry_qr_code && Storage::disk('public')->exists($project->inquiry_qr_code)) {
            Storage::disk('public')->delete($project->inquiry_qr_code);
        }

        $project->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Project deleted successfully!');
    }
}
