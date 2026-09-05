<?php

namespace App\Mail;

use App\Models\Inquiry;
use App\Models\LeadDripStep;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadDripMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Inquiry $inquiry,
        public LeadDripStep $step
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->step->subject ?? ('Information regarding ' . ($this->inquiry->project->name ?? 'Project'));
        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-drip',
            with: [
                'subjectTitle' => $this->step->subject ?? 'Project Information',
                'customerName' => $this->inquiry->customer_name,
                'projectName' => $this->inquiry->project->name ?? 'Project',
                'companyName' => $this->inquiry->company->name ?? 'PropDrip',
                'bodyMessage' => $this->step->message_template ?? '',
            ],
        );
    }
}
