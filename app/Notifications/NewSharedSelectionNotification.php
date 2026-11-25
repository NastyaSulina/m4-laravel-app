<?php

namespace App\Notifications;

use App\Models\Selection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSharedSelectionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Selection $selection) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You’re invited to '.$this->selection->title)
            ->greeting("Hi {$notifiable->name}!")
            ->line($this->selection->owner->name.' shared '.$this->selection->title.' with you.')
            ->line('You can view and edit this selection.')
            ->action('Open selection', route('userzone.selections.show', $this->selection))
            ->salutation('Happy planning, Staylist');
    }
}
