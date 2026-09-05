<?php

namespace App\Jobs;

use App\Mail\NewLeadNotificationMail;
use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewLeadNotificationEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry) {}

    public function handle(): void
    {
        $recipients = [];

        // Add assigned user email if available
        if ($this->inquiry->assignedUser && $this->inquiry->assignedUser->email) {
            $recipients[] = $this->inquiry->assignedUser->email;
        }

        // Add company admin email if available
        if ($this->inquiry->company && $this->inquiry->company->email) {
            $recipients[] = $this->inquiry->company->email;
        }

        $recipients = array_unique(array_filter($recipients));

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->send(new NewLeadNotificationMail($this->inquiry));
        }
    }
}
