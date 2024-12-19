<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BusinessNotification extends Notification
{
    use Queueable;

    public function __construct($user, $businessName)
    {
        $this->userName = $user->first_name.' '.$user->last_name;
        $this->businessName = $businessName;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello ' . $this->userName)
            ->subject('New Business')
            ->line('Your business ' . $this->businessName . ' has been successfully created!')
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'data' => 'Your business '.$this->businessName.' has been successfully created!'
        ];
    }
}
