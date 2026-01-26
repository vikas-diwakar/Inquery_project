<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show dashboard - displays projects list initially
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        $selectedProjectId = session('selected_project_id');

        // If no project is selected, show only projects list
        if (!$selectedProjectId) {
            $query = Project::where('company_id', $companyId);

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

            $projects = $query->withCount(['inquiries', 'brochures'])
                ->latest()
                ->get();

            return view('dashboard.projects-list', compact('projects'));
        }

        // If project is selected, show project-specific dashboard
        $project = Project::where('id', $selectedProjectId)
            ->where('company_id', $companyId)
            ->firstOrFail();

        // Get project-specific statistics
        $totalInquiries = Inquiry::where('company_id', $companyId)
            ->where('project_id', $project->id)
            ->count();
        
        $newInquiries = Inquiry::where('company_id', $companyId)
            ->where('project_id', $project->id)
            ->where('status', 'new')
            ->count();
        
        $bookedInquiries = Inquiry::where('company_id', $companyId)
            ->where('project_id', $project->id)
            ->where('status', 'booked')
            ->count();

        // Get recent inquiries for this project
        $recentInquiries = Inquiry::where('company_id', $companyId)
            ->where('project_id', $project->id)
            ->with(['assignedUser'])
            ->latest()
            ->take(10)
            ->get();

        // Get brochure count
        $totalBrochures = $project->brochures()->count();

        return view('dashboard.project-dashboard', compact(
            'project',
            'totalInquiries',
            'newInquiries',
            'bookedInquiries',
            'recentInquiries',
            'totalBrochures'
        ));
    }
}
