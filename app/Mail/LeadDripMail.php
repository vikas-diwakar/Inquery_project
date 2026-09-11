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
        $companyName = $this->inquiry->company->name ?? 'Real Estate';
        $projectName = $this->inquiry->project->name ?? 'Project';
        $subject = $this->step->subject ?? ("Information regarding {$projectName} - {$companyName}");
        $fromEmail = config('mail.from.address');

        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($fromEmail, $companyName),
            subject: "{$subject} | {$companyName}",
        );
    }

    public function content(): Content
    {
        $company = $this->inquiry->company;
        $companyName = $company->name ?? 'Real Estate';

        return new Content(
            view: 'emails.lead-drip',
            with: [
                'subjectTitle' => $this->step->subject ?? 'Project Information',
                'customerName' => $this->inquiry->customer_name,
                'projectName' => $this->inquiry->project->name ?? 'Project',
                'companyName' => $companyName,
                'companyLogo' => $company?->logo ? asset('storage/' . $company->logo) : null,
                'bodyMessage' => $this->step->message_template ?? '',
            ],
        );
    }
}
