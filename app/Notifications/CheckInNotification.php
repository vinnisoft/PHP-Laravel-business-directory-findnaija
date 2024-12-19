<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\BusinessImage;
use Illuminate\Support\Facades\Log;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Auth;

class CheckInNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($senderId, $reciverId, $businessId)
    {
        $this->senderId = $senderId;
        $this->reciverId = $reciverId;
        $this->businessId = $businessId;
    }

    public function via(object $notifiable): array
    {
        return userNotification($this->reciverId, 'Check Ins');
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello ' . getUserNameById($this->reciverId))
            ->line(getUserNameById($this->senderId).' has joined '.getBusinessNameById($this->businessId))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'data' => getUserNameById($this->senderId).' has joined '.getBusinessNameById($this->businessId),
            'sender_id' => $this->senderId
        ];
    }

    public function toFcm($notifiable)
    {
        $serviceAccountPath = base_path(env('FCM_SERVICE_ACCOUNT_PATH'));
        if (!file_exists($serviceAccountPath)) {
            Log::error('Not Found', ['response' => "File \"$serviceAccountPath\" does not exist"]);
        }
        if (!is_readable($serviceAccountPath)) {
            Log::error('Not readable', ['response' => "File \"$serviceAccountPath\" is not readable"]);
        }

        $recipientToken = User::where('id', $this->reciverId)->pluck('fcm_token')->first();
        $notificationData = [
            'title' => 'New Check in',
            'body' => getUserNameById($this->senderId).' has joined '.getBusinessNameById($this->businessId),
        ];
        $data = [
            'message' => [
                'token' => $recipientToken,
                'notification' => $notificationData,
                'data' => [
                    'id' => (string)$this->reciverId,
                    'user_id' => (string)$this->senderId,
                    'room_id' => (string)$this->businessId,
                    'user_name' => getBusinessNameById($this->businessId),
                    'user_image' => BusinessImage::where('business_id', $this->businessId)->pluck('image')->first(),
                    'type' => 'group',
                ],
            ],
        ];

        $client = new GoogleClient();
        $client->setAuthConfig($serviceAccountPath);
        $client->addScope(env('FCM_MESSAGING_URL'));
        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ])->post(env('FCM_API_URL'), $data);

        if ($response->failed()) {
            Log::error('FCM push failed', ['response' => $response->json()]);
        }
        return $response;
    }
}
