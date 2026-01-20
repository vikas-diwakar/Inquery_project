<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Project;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
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
        ]);

        Inquiry::create([
            'company_id' => $project->company_id,
            'project_id' => $project->id,
            'customer_name' => $validated['customer_name'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'budget' => $validated['budget'] ?? null,
            'flat_type' => $validated['flat_type'] ?? null,
            'message' => $validated['message'] ?? null,
            'status' => 'new',
        ]);

        return redirect()->back()
            ->with('success', 'Thank you for your inquiry! We will contact you soon.');
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
            'status' => 'required|in:new,contacted,qualified,booked,rejected',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $inquiry->update($validated);

        return redirect()->route('inquiries.show', $inquiry)
            ->with('success', 'Inquiry updated successfully!');
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
}
