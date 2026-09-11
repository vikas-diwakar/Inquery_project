<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class CompanyResetPasswordNotification extends ResetPassword
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $company = $notifiable->company;
        $companyName = $company->name ?? 'Real Estate Portal';
        $fromEmail = config('mail.from.address');

        if ($company && $company->subdomain) {
            $resetUrl = $company->workspace_url . '/reset-password/' . $this->token . '?email=' . urlencode($notifiable->getEmailForPasswordReset());
        } else {
            $resetUrl = route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ]);
        }

        return (new MailMessage)
            ->from($fromEmail, "{$companyName} Portal")
            ->subject("{$companyName} - Reset Password Notification")
            ->greeting("Hello {$notifiable->name},")
            ->line("You are receiving this email because we received a password reset request for your {$companyName} account.")
            ->action('Reset Password', $resetUrl)
            ->line('This password reset link will expire in ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60) . ' minutes.')
            ->line('If you did not request a password reset, no further action is required.')
            ->salutation("Best regards,\n{$companyName}");
    }
}
