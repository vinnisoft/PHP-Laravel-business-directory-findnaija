<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmail extends Notification
{
    use Queueable;
   
    public function __construct($user)
    {
        $this->userName = $user->first_name.' '.$user->last_name;
        $this->otp = $user->otp;
    }
    
    public function via(object $notifiable): array
    {
        return ['mail'];
    }
    
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello '.$this->userName)
            ->line('Your account has been successfully created please verify your account')            
            ->line('OTP:- '.$this->otp);
    }
   
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
