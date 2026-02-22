<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Services\FollowUpService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function __construct(protected FollowUpService $followUpService) {}

    /**
     * Display follow-up dashboard with reminders
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;
        
        // Get statistics
        $stats = $this->followUpService->getFollowUpStats($companyId);
        
        // Get different follow-up lists
        $overdue = $this->followUpService->getOverdueFollowUps($companyId);
        $today = $this->followUpService->getTodayFollowUps($companyId);
        $upcoming = $this->followUpService->getUpcomingFollowUps($companyId);

        return view('follow-ups.index', compact('stats', 'overdue', 'today', 'upcoming'));
    }

    /**
     * Schedule a follow-up for an inquiry
     */
    public function store(Request $request, Inquiry $inquiry)
    {
        $this->authorize('update', $inquiry);

        $validated = $request->validate([
            'follow_up_date' => 'required|date|after:now',
            'notes' => 'nullable|string|max:1000',
            'type' => 'nullable|in:call,email,sms,visit,message',
        ]);

        $followUpDate = Carbon::parse($validated['follow_up_date']);

        $followUp = $this->followUpService->scheduleFollowUp(
            $inquiry,
            $followUpDate,
            $validated['notes'] ?? null,
            auth()->id()
        );

        return redirect()->back()->with('success', 'Follow-up scheduled successfully!');
    }

    /**
     * Complete a follow-up
     */
    public function complete(Request $request, Inquiry $inquiry)
    {
        $this->authorize('update', $inquiry);

        $validated = $request->validate([
            'outcome' => 'required|in:interested,not_interested,no_response,callback_requested,other',
            'notes' => 'nullable|string|max:1000',
            'next_follow_up_date' => 'nullable|date|after:now',
        ]);

        $lastFollowUp = $inquiry->followUps()->latest()->first();
        
        if (!$lastFollowUp) {
            return redirect()->back()->with('error', 'No pending follow-up found.');
        }

        $nextFollowUpDate = $validated['next_follow_up_date'] 
            ? Carbon::parse($validated['next_follow_up_date'])
            : null;

        $this->followUpService->completeFollowUp(
            $lastFollowUp,
            $validated['outcome'],
            $validated['notes'],
            $nextFollowUpDate
        );

        return redirect()->back()->with('success', 'Follow-up completed successfully!');
    }

    /**
     * Bulk schedule follow-ups
     */
    public function bulkSchedule(Request $request)
    {
        $validated = $request->validate([
            'inquiry_ids' => 'required|array',
            'inquiry_ids.*' => 'integer|exists:inquiries,id',
            'follow_up_date' => 'required|date|after:now',
        ]);

        $followUpDate = Carbon::parse($validated['follow_up_date']);
        
        $this->followUpService->bulkScheduleFollowUp(
            $validated['inquiry_ids'],
            $followUpDate
        );

        return redirect()->back()->with('success', 'Follow-ups scheduled for selected inquiries!');
    }

    /**
     * Get follow-up stats for dashboard widget
     */
    public function getStats()
    {
        $companyId = auth()->user()->company_id;
        $stats = $this->followUpService->getFollowUpStats($companyId);

        return response()->json($stats);
    }
}
