<?php

namespace App\Http\Controllers;

use App\Models\InquiryDripLog;
use App\Models\LeadDripStep;
use App\Services\DripNurtureService;
use Illuminate\Http\Request;

class LeadDripController extends Controller
{
    /**
     * Display drip sequence workflow editor & logs
     */
    public function index(DripNurtureService $service)
    {
        $company = auth()->user()->company;

        // Ensure default Day 1, 3, 7, 14 steps exist
        if ($company && LeadDripStep::where('company_id', $company->id)->count() === 0) {
            $service->seedDefaultSteps($company);
        }

        $steps = LeadDripStep::where('company_id', $company->id)
            ->orderBy('day_offset')
            ->get();

        $pendingLogs = InquiryDripLog::with(['inquiry.project', 'step'])
            ->where('company_id', $company->id)
            ->where('status', 'pending')
            ->orderBy('scheduled_for')
            ->get();

        $recentLogs = InquiryDripLog::with(['inquiry.project', 'step'])
            ->where('company_id', $company->id)
            ->whereIn('status', ['pending', 'failed'])
            ->orderBy('scheduled_for')
            ->take(50)
            ->get();

        $stats = [
            'total_steps' => $steps->count(),
            'active_steps' => $steps->where('is_active', true)->count(),
            'pending_drips' => InquiryDripLog::where('company_id', $company->id)->where('status', 'pending')->count(),
            'sent_drips' => InquiryDripLog::where('company_id', $company->id)->where('status', 'sent')->count(),
        ];

        return view('settings.drip', compact('steps', 'pendingLogs', 'recentLogs', 'stats'));
    }

    /**
     * Store or update a drip sequence step
     */
    public function store(Request $request)
    {
        $company = auth()->user()->company;

        $validated = $request->validate([
            'day_offset' => 'required|integer|min:1|max:365',
            'step_title' => 'required|string|max:255',
            'channel' => 'required|in:whatsapp,email,both',
            'message_template' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        LeadDripStep::updateOrCreate(
            [
                'company_id' => $company->id,
                'day_offset' => $validated['day_offset'],
            ],
            [
                'step_title' => $validated['step_title'],
                'channel' => $validated['channel'],
                'message_template' => $validated['message_template'],
                'is_active' => $request->has('is_active'),
            ]
        );

        return redirect()->back()
            ->with('success', "Day {$validated['day_offset']} drip sequence step saved successfully!");
    }

    /**
     * Delete a drip step
     */
    public function destroy(LeadDripStep $step)
    {
        if ($step->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $step->delete();

        return redirect()->back()->with('success', 'Drip step deleted successfully!');
    }

    /**
     * Manual trigger to process due drips now via background Queue Job
     */
    public function processNow(Request $request, DripNurtureService $service)
    {
        $company = auth()->user()->company;
        $forceNow = $request->has('force') || $request->input('mode') === 'test';

        // Dispatch background Queue Job
        \App\Jobs\ProcessPendingDripsJob::dispatch($company->id, $forceNow);

        return redirect()->back()->with('success', "Dispatched pending drip workflow processing job to queue! Your queue worker will send messages in background.");
    }

    /**
     * Dispatch drip messages to specifically selected users / drip logs
     */
    public function processSelected(Request $request, DripNurtureService $service)
    {
        $company = auth()->user()->company;

        $validated = $request->validate([
            'selected_drip_ids' => 'required|array|min:1',
            'selected_drip_ids.*' => 'integer|exists:inquiry_drip_logs,id',
        ], [
            'selected_drip_ids.required' => 'Please select at least one user lead to dispatch dripping.',
            'selected_drip_ids.min' => 'Please select at least one user lead to dispatch dripping.',
        ]);

        $result = $service->processPendingDrips(
            companyId: $company->id,
            forceNow: true,
            logIds: $validated['selected_drip_ids']
        );

        $sent = $result['sent'];
        $total = $result['total_processed'];

        if ($sent > 0) {
            return redirect()->back()->with('success', "Successfully dispatched {$sent} of {$total} selected drip message(s)!");
        } elseif ($total > 0) {
            return redirect()->back()->with('warning', "Processed {$total} selected drip(s). Check activity logs for individual delivery status.");
        }

        return redirect()->back()->with('info', "No valid pending drips found for the selected IDs.");
    }

    /**
     * Dispatch a single user's drip message immediately
     */
    public function processSingle(InquiryDripLog $log, DripNurtureService $service)
    {
        if ($log->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $result = $service->processPendingDrips(
            companyId: auth()->user()->company_id,
            forceNow: true,
            logIds: [$log->id]
        );

        $customerName = $log->inquiry ? $log->inquiry->customer_name : 'Customer';

        if ($result['sent'] > 0) {
            return redirect()->back()->with('success', "Drip message successfully dispatched to {$customerName}!");
        }

        return redirect()->back()->with('warning', "Attempted dispatch to {$customerName}. Check status in logs.");
    }

    /**
     * Discard (skip/remove) a single drip log from the pending queue
     */
    public function discardSingle(InquiryDripLog $log)
    {
        if ($log->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $customerName = $log->inquiry ? $log->inquiry->customer_name : 'Customer';
        $log->update([
            'status' => 'skipped',
            'last_error' => 'Manually discarded by user',
        ]);

        return redirect()->back()->with('success', "Drip for {$customerName} discarded and removed from queue!");
    }

    /**
     * Discard (skip/remove) selected drip logs from the pending queue
     */
    public function discardSelected(Request $request)
    {
        $company = auth()->user()->company;

        $validated = $request->validate([
            'selected_drip_ids' => 'required|array|min:1',
            'selected_drip_ids.*' => 'integer|exists:inquiry_drip_logs,id',
        ], [
            'selected_drip_ids.required' => 'Please select at least one lead drip to discard.',
            'selected_drip_ids.min' => 'Please select at least one lead drip to discard.',
        ]);

        $updated = InquiryDripLog::where('company_id', $company->id)
            ->whereIn('id', $validated['selected_drip_ids'])
            ->whereIn('status', ['pending', 'failed'])
            ->update([
                'status' => 'skipped',
                'last_error' => 'Manually discarded by user',
            ]);

        return redirect()->back()->with('success', "Successfully discarded {$updated} drip item(s) from the pending queue!");
    }

    /**
     * Enroll all past existing inquiries into active drip sequences
     */
    public function enrollPastLeads(DripNurtureService $service)
    {
        $company = auth()->user()->company;
        $count = $service->enrollExistingInquiries($company);

        return redirect()->back()->with('success', "Enrolled all existing past leads into active drip sequences ({$count} pending drip logs created)!");
    }
}
