<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WelcomeCompanyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        $companyName = $this->user->company->name ?? 'Real Estate Portal';
        $fromEmail = config('mail.from.address');

        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($fromEmail, "{$companyName} Portal"),
            subject: "Welcome to {$companyName} - Your Workspace Account",
        );
    }

    public function content(): Content
    {
        $company = $this->user->company;
        $companyName = $company->name ?? 'Your Company';
        $loginUrl = ($company && $company->subdomain) ? ($company->workspace_url . '/login') : route('login');

        return new Content(
            view: 'emails.welcome',
            with: [
                'userName' => $this->user->name,
                'companyName' => $companyName,
                'companyLogo' => $company?->logo ? asset('storage/' . $company->logo) : null,
                'workspaceUrl' => $company?->workspace_url ?? route('dashboard'),
                'loginUrl' => $loginUrl,
            ],
        );
    }
}
