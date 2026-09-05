<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry) {}

    public function envelope(): Envelope
    {
        $projectName = $this->inquiry->project->name ?? 'Property Inquiry';
        return new Envelope(
            subject: "Inquiry Confirmation: {$projectName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inquiry-confirmation',
            with: [
                'customerName' => $this->inquiry->customer_name,
                'projectName' => $this->inquiry->project->name ?? 'Property',
                'companyName' => $this->inquiry->company->name ?? 'PropDrip Partner',
                'location' => $this->inquiry->project->location ?? null,
                'phone' => $this->inquiry->phone,
            ],
        );
    }
}
