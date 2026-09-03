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

        $recentLogs = InquiryDripLog::with(['inquiry.project', 'step'])
            ->where('company_id', $company->id)
            ->latest()
            ->take(25)
            ->get();

        $stats = [
            'total_steps' => $steps->count(),
            'active_steps' => $steps->where('is_active', true)->count(),
            'pending_drips' => InquiryDripLog::where('company_id', $company->id)->where('status', 'pending')->count(),
            'sent_drips' => InquiryDripLog::where('company_id', $company->id)->where('status', 'sent')->count(),
        ];

        return view('settings.drip', compact('steps', 'recentLogs', 'stats'));
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
     * Manual trigger to process due drips now (with option for immediate test dispatch)
     */
    public function processNow(Request $request, DripNurtureService $service)
    {
        $company = auth()->user()->company;
        $forceNow = $request->has('force') || $request->input('mode') === 'test';
        $result = $service->processPendingDrips($company->id, $forceNow);

        return redirect()->back()->with('success', "Processed {$result['total_processed']} pending drip logs ({$result['sent']} sent successfully).");
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
