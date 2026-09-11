<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class CompanyVerifyEmailNotification extends VerifyEmail
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        $companyName = $notifiable->company->name ?? 'Real Estate Portal';
        $fromEmail = config('mail.from.address');

        return (new MailMessage)
            ->from($fromEmail, "{$companyName} Portal")
            ->subject("{$companyName} - Verify Email Address")
            ->greeting("Hello {$notifiable->name},")
            ->line("Please click the button below to verify your email address for your {$companyName} workspace account.")
            ->action('Verify Email Address', $verificationUrl)
            ->line('If you did not create an account, no further action is required.')
            ->salutation("Best regards,\n{$companyName}");
    }
}
