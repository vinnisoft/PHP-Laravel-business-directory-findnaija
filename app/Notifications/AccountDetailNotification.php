<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountDetailNotification extends Notification
{
    use Queueable;

    public function __construct($user, $password)
    {
        $this->fullName = $user->first_name.' '.$user->last_name;
        $this->email = $user->email;
        $this->password = $password;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello ' . $this->fullName)
            ->subject('Find Naija Account Credentials')
            ->line('Your account has been successfully created!')
            ->line('Email: '.$this->email)
            ->line('Password: '.$this->password)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'data' => ''
        ];
    }
}
