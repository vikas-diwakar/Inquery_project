<?php

namespace App\Services;

use App\Models\FollowUp;
use App\Models\Inquiry;
use Carbon\Carbon;

class FollowUpService
{
    /**
     * Schedule a new follow-up for an inquiry
     */
    public function scheduleFollowUp(
        Inquiry $inquiry,
        ?Carbon $followUpDate = null,
        ?string $notes = null,
        ?int $followUpByUserId = null
    ): FollowUp {
        // Default to 3 days from now if not specified
        $followUpDate = $followUpDate ?? now()->addDays(3);

        // Use currently authenticated user if not specified
        $followUpByUserId = $followUpByUserId ?? auth()->id();

        // Create the follow-up record
        $followUp = FollowUp::create([
            'inquiry_id' => $inquiry->id,
            'company_id' => $inquiry->company_id,
            'follow_up_by' => $followUpByUserId,
            'type' => 'call', // Default type
            'notes' => $notes,
            'scheduled_date' => $followUpDate,
        ]);

        // Update the inquiry's next follow-up date
        $inquiry->update([
            'next_follow_up_date' => $followUpDate,
        ]);

        return $followUp;
    }

    /**
     * Complete a follow-up
     */
    public function completeFollowUp(
        FollowUp $followUp,
        string $outcome,
        ?string $notes = null,
        ?Carbon $nextFollowUpDate = null
    ): void {
        // Update the follow-up with outcome and notes
        $followUp->update([
            'outcome' => $outcome,
            'notes' => $notes ?? $followUp->notes,
        ]);

        // Update the inquiry's last follow-up date
        $followUp->inquiry->update([
            'last_follow_up_date' => now(),
        ]);

        // Schedule next follow-up if outcome suggests it
        if ($nextFollowUpDate) {
            $this->scheduleFollowUp(
                $followUp->inquiry,
                $nextFollowUpDate,
                $notes
            );
        } elseif ($outcome === 'no_response') {
            // Auto-schedule next follow-up in 2 days for no response
            $this->scheduleFollowUp(
                $followUp->inquiry,
                now()->addDays(2),
                'Auto-scheduled due to no response'
            );
        } elseif ($outcome === 'callback_requested') {
            // Prompt user to set follow-up date if callback requested
            // In a real app, this would be handled by the controller
        }
    }

    /**
     * Get overdue follow-ups for a company
     */
    public function getOverdueFollowUps($companyId)
    {
        return Inquiry::where('company_id', $companyId)
            ->overdueFollowUps()
            ->with(['company', 'project', 'assignedUser', 'followUps'])
            ->orderBy('next_follow_up_date', 'asc')
            ->get();
    }

    /**
     * Get upcoming follow-ups for a company (next 7 days)
     */
    public function getUpcomingFollowUps($companyId)
    {
        return Inquiry::where('company_id', $companyId)
            ->upcomingFollowUps()
            ->with(['company', 'project', 'assignedUser', 'followUps'])
            ->orderBy('next_follow_up_date', 'asc')
            ->get();
    }

    /**
     * Get follow-ups that need attention today
     */
    public function getTodayFollowUps($companyId)
    {
        return Inquiry::where('company_id', $companyId)
            ->whereNotNull('next_follow_up_date')
            ->whereBetween('next_follow_up_date', [
                now()->startOfDay(),
                now()->endOfDay(),
            ])
            ->whereNotIn('status', ['booked', 'rejected'])
            ->with(['company', 'project', 'assignedUser', 'followUps'])
            ->orderBy('next_follow_up_date', 'asc')
            ->get();
    }

    /**
     * Get follow-up statistics for dashboard
     */
    public function getFollowUpStats($companyId)
    {
        $overdueCount = Inquiry::where('company_id', $companyId)
            ->overdueFollowUps()
            ->count();

        $todayCount = $this->getTodayFollowUps($companyId)->count();

        $upcomingCount = Inquiry::where('company_id', $companyId)
            ->upcomingFollowUps()
            ->count();

        return [
            'overdue' => $overdueCount,
            'today' => $todayCount,
            'upcoming' => $upcomingCount,
            'total_pending' => $overdueCount + $todayCount + $upcomingCount,
        ];
    }

    /**
     * Bulk update follow-up dates for inquiries
     */
    public function bulkScheduleFollowUp(array $inquiryIds, Carbon $followUpDate)
    {
        Inquiry::whereIn('id', $inquiryIds)->update([
            'next_follow_up_date' => $followUpDate,
        ]);
    }
}
