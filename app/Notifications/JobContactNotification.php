<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Illuminate\Support\Facades\Http;

class JobContactNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($businessOwnerName, $userId, $businessName, $subject, $message)
    {
        $this->businessOwnerName = $businessOwnerName;
        $this->userId = $userId;
        $this->businessName = $businessName;
        $this->subject = $subject;
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello ' . $this->businessOwnerName)
            ->line(getUserNameById($this->userId) . ' contacts in ' . $this->businessName)
            ->line($this->message)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => getUserNameById($this->userId) . ' contacts in ' . $this->businessName,
            'data' => $this->message,
        ];
    }
}
