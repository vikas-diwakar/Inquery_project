<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Get statistics
        $totalProjects = Project::where('company_id', $companyId)->count();
        $totalInquiries = Inquiry::where('company_id', $companyId)->count();
        $newInquiries = Inquiry::where('company_id', $companyId)
            ->where('status', 'new')
            ->count();
        $bookedInquiries = Inquiry::where('company_id', $companyId)
            ->where('status', 'booked')
            ->count();

        // Get recent inquiries
        $recentInquiries = Inquiry::where('company_id', $companyId)
            ->with(['project', 'assignedUser'])
            ->latest()
            ->take(10)
            ->get();

        // Get project-wise statistics
        $projectStats = Project::where('company_id', $companyId)
            ->withCount(['inquiries', 'inquiries as new_inquiries_count' => function ($query) {
                $query->where('status', 'new');
            }, 'inquiries as booked_inquiries_count' => function ($query) {
                $query->where('status', 'booked');
            }])
            ->get();

        return view('dashboard.index', compact(
            'totalProjects',
            'totalInquiries',
            'newInquiries',
            'bookedInquiries',
            'recentInquiries',
            'projectStats'
        ));
    }
}
