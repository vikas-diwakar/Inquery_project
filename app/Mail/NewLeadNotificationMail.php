<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLeadNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry) {}

    public function envelope(): Envelope
    {
        $projectName = $this->inquiry->project->name ?? 'Project';
        return new Envelope(
            subject: "🔥 New Lead Captured: {$this->inquiry->customer_name} ({$projectName})",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-lead-notification',
            with: [
                'customerName' => $this->inquiry->customer_name,
                'projectName' => $this->inquiry->project->name ?? 'Project',
                'phone' => $this->inquiry->phone,
                'email' => $this->inquiry->email,
                'budget' => $this->inquiry->budget,
                'preferredType' => $this->inquiry->selectedUnitOption->option_name ?? null,
                'userMessage' => $this->inquiry->message ?? $this->inquiry->description,
                'viewUrl' => route('inquiries.show', $this->inquiry->id),
            ],
        );
    }
}
