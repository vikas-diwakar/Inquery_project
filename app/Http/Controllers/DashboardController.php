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

        // Today's follow-ups for this project
        $todayFollowUps = Inquiry::where('company_id', $companyId)
            ->where('project_id', $project->id)
            ->whereNotNull('next_follow_up_date')
            ->whereDate('next_follow_up_date', now()->toDateString())
            ->count();

        // Analytics: most demanded unit options (join to project_unit_options for accuracy)
        $cacheKey = "project_{$project->id}_unit_stats";
        [$topUnits, $mostPopularUnitName, $mostPopularUnitPercent] = \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($companyId, $project, $totalInquiries) {
            $rows = \App\Models\ProjectUnitOption::where('project_unit_options.project_id', $project->id)
                ->leftJoin('inquiries', function ($join) use ($project) {
                    $join->on('project_unit_options.id', '=', 'inquiries.selected_unit_option_id')
                        ->where('inquiries.project_id', '=', $project->id);
                })
                ->selectRaw('project_unit_options.id, project_unit_options.option_name, COUNT(inquiries.id) as cnt')
                ->groupBy('project_unit_options.id', 'project_unit_options.option_name')
                ->orderByDesc('cnt')
                ->get();

            $top = $rows->take(3)->map(function ($r) use ($totalInquiries) {
                $percent = $totalInquiries > 0 ? round(($r->cnt / $totalInquiries) * 100) : 0;
                return [
                    'id' => $r->id,
                    'name' => $r->option_name,
                    'count' => (int) $r->cnt,
                    'percent' => $percent,
                ];
            })->toArray();

            $most = $rows->first();
            $mostName = $most ? $most->option_name : null;
            $mostPercent = $most && $totalInquiries > 0 ? round(($most->cnt / $totalInquiries) * 100) : 0;

            return [$top, $mostName, $mostPercent];
        });

        // Inquiry trend - last 30 days
        $days = 30;
        $trendData = [];
        $labels = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $labels[] = date('M d', strtotime($date));
            $trendData[] = Inquiry::where('company_id', $companyId)
                ->where('project_id', $project->id)
                ->whereDate('created_at', $date)
                ->count();
        }

        // Conversion rate = booked / total
        $conversionRate = $totalInquiries > 0 ? round(($bookedInquiries / $totalInquiries) * 100, 1) : 0;

        return view('dashboard.project-dashboard', compact(
            'project',
            'totalInquiries',
            'newInquiries',
            'bookedInquiries',
            'recentInquiries',
            'totalBrochures',
            'topUnits',
            'mostPopularUnitName',
            'mostPopularUnitPercent',
            'labels',
            'trendData',
            'conversionRate',
            'todayFollowUps'
        ));
    }
}
