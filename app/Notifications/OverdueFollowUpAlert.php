<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OverdueFollowUpAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Inquiry $inquiry) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daysOverdue = now()->diffInDays($this->inquiry->next_follow_up_date);

        return (new MailMessage)
            ->subject('URGENT: Overdue Follow-up - ' . $this->inquiry->customer_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('⚠️ **URGENT:** You have an overdue follow-up that requires immediate attention!')
            ->line('**Customer Name:** ' . $this->inquiry->customer_name)
            ->line('**Phone:** ' . $this->inquiry->phone)
            ->line('**Email:** ' . $this->inquiry->email)
            ->line('**Project:** ' . $this->inquiry->project->name)
            ->line('**Due Date:** ' . $this->inquiry->next_follow_up_date->format('M d, Y H:i A'))
            ->line('**Overdue by:** ' . $daysOverdue . ' day(s)')
            ->action('Update Follow-up', route('inquiries.show', $this->inquiry->id))
            ->line('Please prioritize this follow-up to prevent losing the inquiry.')
            ->line('Thank you!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'inquiry_id' => $this->inquiry->id,
            'customer_name' => $this->inquiry->customer_name,
            'phone' => $this->inquiry->phone,
            'email' => $this->inquiry->email,
            'project_name' => $this->inquiry->project->name,
            'next_follow_up_date' => $this->inquiry->next_follow_up_date,
            'type' => 'overdue',
            'message' => '⚠️ URGENT: Overdue follow-up for ' . $this->inquiry->customer_name,
        ];
    }
}
