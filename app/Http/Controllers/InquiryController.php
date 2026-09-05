<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Exports\InquiriesExport;
use Maatwebsite\Excel\Facades\Excel;

class InquiryController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of inquiries (filtered by selected project)
     */
    public function index(Request $request)
    {
        $selectedProjectId = session('selected_project_id');
        
        // Filter by selected project (required)
        $query = Inquiry::where('company_id', auth()->user()->company_id)
            ->where('project_id', $selectedProjectId)
            ->with(['assignedUser']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $inquiries = $query->latest()->paginate(20);
        $project = Project::findOrFail($selectedProjectId);

        return view('inquiries.index', compact('inquiries', 'project'));
    }

    /**
     * Show the form for creating a new inquiry (public)
     */
    public function showPublicForm(Project $project)
    {
        return view('public.inquiry-form', compact('project'));
    }

    /**
     * Show the form for creating a new inquiry (authenticated user)
     */
    public function create()
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::findOrFail($selectedProjectId);
        
        // Check user has access to this project
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->projects()->where('projects.id', $project->id)->exists()) {
            abort(403, 'Unauthorized access to this project.');
        }

        // Get users associated with this project
        $projectUsers = $project->users()->get();

        return view('inquiries.create', compact('project', 'projectUsers'));
    }

    /**
     * Store a new inquiry (public)
     */
    public function storePublic(Request $request, Project $project)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'budget' => 'nullable|numeric|min:0',
            'flat_type' => 'nullable|string|max:50',
            'message' => 'nullable|string',
            'description' => 'nullable|string',
            'selected_unit_option_id' => 'nullable|exists:project_unit_options,id',
        ]);

        $inquiry = Inquiry::create([
            'company_id' => $project->company_id,
            'project_id' => $project->id,
            'customer_name' => $validated['customer_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'flat_type' => $validated['flat_type'] ?? null,
            'message' => $validated['message'] ?? null,
            'description' => $validated['description'] ?? null,
            'selected_unit_option_id' => $validated['selected_unit_option_id'] ?? null,
            'status' => 'new',
        ]);

        // Calculate AI Intent Score, allocate via Round-Robin, send WhatsApp & enroll in drip
        app(\App\Services\LeadScoringService::class)->evaluateAndUpdate($inquiry);
        if (!$inquiry->assigned_to) {
            app(\App\Services\LeadAllocationService::class)->allocateInquiry($inquiry);
        }
        app(\App\Services\WhatsAppService::class)->sendInstantBrochure($inquiry);
        app(\App\Services\DripNurtureService::class)->enrollInquiry($inquiry);

        // Dispatch email sending queued jobs asynchronously (does not block HTTP response)
        \App\Jobs\SendInquiryConfirmationEmailJob::dispatch($inquiry);
        \App\Jobs\SendNewLeadNotificationEmailJob::dispatch($inquiry);

        return redirect()->back()
            ->with('success', 'Thank you for your inquiry! Check your WhatsApp for project details & brochure.');
    }

    /**
     * Store a new inquiry (authenticated user)
     */
    public function store(Request $request)
    {
        $selectedProjectId = session('selected_project_id');
        $project = Project::findOrFail($selectedProjectId);
        
        // Check user has access to this project
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->projects()->where('projects.id', $project->id)->exists()) {
            abort(403, 'Unauthorized access to this project.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'budget' => 'nullable|numeric|min:0',
            'flat_type' => 'nullable|string|max:50',
            'message' => 'nullable|string',
            'description' => 'nullable|string',
            'selected_unit_option_id' => 'nullable|exists:project_unit_options,id',
            'assigned_to' => 'nullable|exists:users,id',
            'next_follow_up_date' => 'nullable|date_format:Y-m-d\TH:i|after:now',
        ]);

        $followUpDate = null;
        if (isset($validated['next_follow_up_date']) && $validated['next_follow_up_date']) {
            $followUpDate = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $validated['next_follow_up_date']);
        }

        $inquiry = Inquiry::create([
            'company_id' => $project->company_id,
            'project_id' => $project->id,
            'customer_name' => $validated['customer_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'flat_type' => $validated['flat_type'] ?? null,
            'message' => $validated['message'] ?? null,
            'description' => $validated['description'] ?? null,
            'selected_unit_option_id' => $validated['selected_unit_option_id'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'next_follow_up_date' => $followUpDate,
            'status' => 'new',
        ]);

        // Calculate AI Intent Score, allocate via Round-Robin, send WhatsApp & enroll in drip
        app(\App\Services\LeadScoringService::class)->evaluateAndUpdate($inquiry);
        if (!$inquiry->assigned_to) {
            app(\App\Services\LeadAllocationService::class)->allocateInquiry($inquiry);
        }
        app(\App\Services\WhatsAppService::class)->sendInstantBrochure($inquiry);
        app(\App\Services\DripNurtureService::class)->enrollInquiry($inquiry);

        // Dispatch email sending queued jobs asynchronously
        \App\Jobs\SendInquiryConfirmationEmailJob::dispatch($inquiry);
        \App\Jobs\SendNewLeadNotificationEmailJob::dispatch($inquiry);

        return redirect()->route('inquiries.index')
            ->with('success', 'Inquiry created, auto-allocated, brochure sent & drip sequence active!');
    }

    /**
     * Resend WhatsApp Brochure manually for an inquiry
     */
    public function resendWhatsApp(Inquiry $inquiry)
    {
        $this->authorize('update', $inquiry);

        $result = app(\App\Services\WhatsAppService::class)->sendInstantBrochure($inquiry, true);

        if ($result['success']) {
            return redirect()->back()->with('success', 'WhatsApp brochure resent successfully!');
        }

        return redirect()->back()->with('error', 'Failed to resend WhatsApp: ' . $result['message']);
    }

    /**
     * Display the specified inquiry
     */
    public function show(Inquiry $inquiry)
    {
        $this->authorize('view', $inquiry);

        $inquiry->load(['project', 'assignedUser', 'company']);

        return view('inquiries.show', compact('inquiry'));
    }

    /**
     * Update the inquiry
     */
    public function update(Request $request, Inquiry $inquiry)
    {
        $this->authorize('update', $inquiry);

        $validated = $request->validate([
            'status' => 'required|in:new,contacted,interested,site_visit,booked,lost',
            'assigned_to' => 'nullable|exists:users,id',
            'project_unit_id' => 'nullable|exists:project_units,id',
            'description' => 'nullable|string',
        ]);

        $inquiry->update($validated);

        // Re-evaluate AI Lead Intent Score & Grade
        app(\App\Services\LeadScoringService::class)->evaluateAndUpdate($inquiry);

        // Auto-sync physical unit status if assigned
        if ($inquiry->project_unit_id) {
            $unit = \App\Models\ProjectUnit::find($inquiry->project_unit_id);
            if ($unit) {
                if ($inquiry->status === 'booked') {
                    $unit->update(['status' => 'sold']);
                } elseif (in_array($inquiry->status, ['interested', 'site_visit'])) {
                    $unit->update(['status' => 'on_hold']);
                } elseif ($inquiry->status === 'lost') {
                    $unit->update(['status' => 'available']);
                }
            }
        }

        return redirect()->route('inquiries.show', $inquiry)
            ->with('success', 'Inquiry & unit availability updated successfully!');
    }

    /**
     * Update inquiry status via AJAX/PATCH and record history
     */
    public function updateStatus(Request $request, Inquiry $inquiry)
    {
        $this->authorize('update', $inquiry);

        $validated = $request->validate([
            'status' => 'required|in:new,contacted,interested,site_visit,booked,lost',
        ]);

        $old = $inquiry->status;
        $inquiry->status = $validated['status'];
        $inquiry->save();

        // Record history
        \App\Models\InquiryStatusHistory::create([
            'inquiry_id' => $inquiry->id,
            'from_status' => $old,
            'to_status' => $validated['status'],
            'changed_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'status' => $inquiry->status]);
    }

    /**
     * Remove the inquiry
     */
    public function destroy(Inquiry $inquiry)
    {
        $this->authorize('delete', $inquiry);

        $inquiry->delete();

        return redirect()->route('inquiries.index')
            ->with('success', 'Inquiry deleted successfully!');
    }

    /**
     * Export inquiries to Excel
     */
    public function export(Request $request)
    {
        $selectedProjectId = session('selected_project_id');

        // Build the same query as the index method
        $query = Inquiry::where('company_id', auth()->user()->company_id)
            ->where('project_id', $selectedProjectId)
            ->with(['assignedUser', 'project']);

        // Apply the same filters as the index method
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $project = Project::findOrFail($selectedProjectId);
        $filename = 'inquiries_' . $project->name . '_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new InquiriesExport($query), $filename);
    }
}
