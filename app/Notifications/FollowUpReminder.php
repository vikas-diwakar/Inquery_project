<?php

namespace App\Notifications;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FollowUpReminder extends Notification implements ShouldQueue
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
        return (new MailMessage)
            ->subject('Follow-up Reminder: ' . $this->inquiry->customer_name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have a scheduled follow-up for the following inquiry:')
            ->line('**Customer Name:** ' . $this->inquiry->customer_name)
            ->line('**Phone:** ' . $this->inquiry->phone)
            ->line('**Email:** ' . $this->inquiry->email)
            ->line('**Project:** ' . $this->inquiry->project->name)
            ->line('**Status:** ' . ucfirst($this->inquiry->status))
            ->line('**Scheduled Follow-up Date:** ' . $this->inquiry->next_follow_up_date->format('M d, Y H:i A'))
            ->action('View Inquiry', route('inquiries.show', $this->inquiry->id))
            ->line('Please ensure to follow up with the customer as scheduled.')
            ->line('Thank you for your diligence in customer follow-ups!');
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
            'message' => 'Follow-up reminder for ' . $this->inquiry->customer_name,
        ];
    }
}
