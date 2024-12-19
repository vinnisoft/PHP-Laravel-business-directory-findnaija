<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessRecommendationNotification extends Notification
{
    use Queueable;

    public function __construct($user, $notification)
    {
        $this->userName = $user->first_name.' '.$user->last_name;
        $this->notification = $notification;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello ' . $this->userName)
            ->subject('Recomended Business')
            ->line($this->notification)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'data' => $this->notification
        ];
    }
}
