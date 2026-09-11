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
        $companyName = $this->inquiry->company->name ?? 'Real Estate';
        $fromEmail = config('mail.from.address');

        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($fromEmail, $companyName),
            subject: "Inquiry Confirmation: {$projectName} - {$companyName}",
        );
    }

    public function content(): Content
    {
        $company = $this->inquiry->company;
        $companyName = $company->name ?? 'Real Estate';

        return new Content(
            view: 'emails.inquiry-confirmation',
            with: [
                'customerName' => $this->inquiry->customer_name,
                'projectName' => $this->inquiry->project->name ?? 'Property',
                'companyName' => $companyName,
                'companyLogo' => $company?->logo ? asset('storage/' . $company->logo) : null,
                'location' => $this->inquiry->project->location ?? null,
                'phone' => $this->inquiry->phone,
            ],
        );
    }
}
