<?php

namespace App\Console\Commands;

use App\Models\Inquiry;
use App\Models\User;
use App\Notifications\FollowUpReminder;
use App\Notifications\OverdueFollowUpAlert;
use Illuminate\Console\Command;

class SendFollowUpReminders extends Command
{
    protected $signature = 'app:send-follow-up-reminders
                           {--overdue : Send reminders only for overdue follow-ups}
                           {--upcoming : Send reminders only for upcoming follow-ups (today)}
                           {--all : Send all reminders}';

    protected $description = 'Send follow-up reminders to users with scheduled follow-ups';

    public function handle()
    {
        $overdue = $this->option('overdue');
        $upcoming = $this->option('upcoming');
        $all = $this->option('all');
        dd($overdue, $upcoming, $all);
        // If no specific option, default to sending all
        if (!$overdue && !$upcoming && !$all) {
            $all = true;
        }

        $totalSent = 0;

        if ($all || $overdue) {
            $totalSent += $this->sendOverdueReminders();
        }

        if ($all || $upcoming) {
            $totalSent += $this->sendUpcomingReminders();
        }

        $this->info("Total notifications sent: {$totalSent}");
    }

    protected function sendOverdueReminders()
    {
        $inquiries = Inquiry::overdueFollowUps()
            ->whereNotNull('assigned_to')
            ->with('assignedUser')
            ->get();

        $sent = 0;
        foreach ($inquiries as $inquiry) {
            if ($inquiry->assignedUser) {
                $inquiry->assignedUser->notify(new OverdueFollowUpAlert($inquiry));
                $sent++;
            }
        }

        if ($sent > 0) {
            $this->info("Sent {$sent} overdue follow-up alerts");
        }

        return $sent;
    }

    protected function sendUpcomingReminders()
    {
        // Send reminders for follow-ups today
        $inquiries = Inquiry::where('company_id', auth()->user()->company_id ?? null)
            ->whereNotNull('next_follow_up_date')
            ->whereBetween('next_follow_up_date', [
                now()->startOfDay(),
                now()->endOfDay(),
            ])
            ->where('next_follow_up_date', '>', now()->subHours(1)) // Don't resend if already notified
            ->whereNotNull('assigned_to')
            ->whereNotIn('status', ['booked', 'rejected'])
            ->with('assignedUser')
            ->get();

        $sent = 0;
        foreach ($inquiries as $inquiry) {
            if ($inquiry->assignedUser) {
                $inquiry->assignedUser->notify(new FollowUpReminder($inquiry));
                $sent++;
            }
        }

        if ($sent > 0) {
            $this->info("Sent {$sent} follow-up reminders for today");
        }

        return $sent;
    }
}
