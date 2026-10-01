<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public string $password
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $companyName = $this->user->company->name ?? config('app.name', 'PropDrip');
        $fromEmail = config('mail.from.address', 'noreply@propdrip.com');
        $fromName = config('mail.from.name', "{$companyName} CRM");

        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($fromEmail, $fromName),
            subject: "🎉 Welcome to {$companyName} - Your Account Credentials",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $company = $this->user->company ?? \App\Models\Company::default();
        $companyName = $company->name ?? config('app.name', 'PropDrip');
        $roleName = $this->user->role->name ?? 'Team Member';
        $assignedProjects = $this->user->projects()->pluck('name')->toArray();

        return new Content(
            view: 'emails.new-user-welcome',
            with: [
                'user' => $this->user,
                'userName' => $this->user->name,
                'userEmail' => $this->user->email,
                'password' => $this->password,
                'roleName' => $roleName,
                'companyName' => $companyName,
                'companyLogo' => $company?->logo ? asset('storage/' . $company->logo) : null,
                'assignedProjects' => $assignedProjects,
                'loginUrl' => route('login'),
            ],
        );
    }
}