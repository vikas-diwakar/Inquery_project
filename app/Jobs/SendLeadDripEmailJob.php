<?php

namespace App\Jobs;

use App\Mail\LeadDripMail;
use App\Models\Inquiry;
use App\Models\LeadDripStep;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendLeadDripEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Inquiry $inquiry,
        public LeadDripStep $step
    ) {}

    public function handle(): void
    {
        if ($this->inquiry->email) {
            Mail::to($this->inquiry->email)->send(new LeadDripMail($this->inquiry, $this->step));
        }
    }
}
