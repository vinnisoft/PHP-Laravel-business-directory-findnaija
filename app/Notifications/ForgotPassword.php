<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ForgotPassword extends Notification
{
    use Queueable;
   
    public function __construct($name, $otp)
    {
        $this->name = $name;
        $this->otp = $otp;
    }
    
    public function via(object $notifiable): array
    {
        return ['mail'];
    }
    
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello '.$this->name)
            ->line('Here is the otp for forget password')            
            ->line('OTP:- '.$this->otp);
    }
   
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
