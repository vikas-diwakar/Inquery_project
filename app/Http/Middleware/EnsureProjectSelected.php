<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProjectSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access to project selection and dashboard
        if ($request->routeIs('dashboard') || $request->routeIs('projects.*')) {
            return $next($request);
        }

        // Check if a project is selected in session
        if (!session()->has('selected_project_id')) {
            return redirect()->route('dashboard')
                ->with('error', 'Please select a project first to access this feature.');
        }

        // Verify the selected project belongs to the logged-in company
        $selectedProjectId = session('selected_project_id');
        $user = auth()->user();
        
        $project = \App\Models\Project::where('id', $selectedProjectId)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$project) {
            session()->forget('selected_project_id');
            return redirect()->route('dashboard')
                ->with('error', 'Selected project not found. Please select a project again.');
        }

        // Add project to request for easy access
        $request->merge(['selected_project' => $project]);

        return $next($request);
    }
}
