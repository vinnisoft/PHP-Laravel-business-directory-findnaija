<?php

namespace App\Broadcasting;

use Illuminate\Notifications\Notification;
use App\Models\User;

class FcmChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toFcm($notifiable);
    }
}
