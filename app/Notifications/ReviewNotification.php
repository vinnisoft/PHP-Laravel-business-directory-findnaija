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

class ReviewNotification extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($senderId, $reciverId, $businessId, $review)
    {
        $this->senderId = $senderId;
        $this->reciverId = $reciverId;
        $this->businessId = $businessId;
        $this->review = $review;
    }

    public function via(object $notifiable): array
    {
        return userNotification($this->reciverId, 'Reviews');
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello ' . getUserNameById($this->reciverId))
            ->line('New review found in '.getBusinessNameById($this->businessId))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'data' => 'New review found in '.getBusinessNameById($this->businessId),
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
            'title' => 'New Review',
            'body' => 'New review found in '.getBusinessNameById($this->businessId),
        ];
        $data = [
            'message' => [
                'token' => $recipientToken,
                'notification' => $notificationData,
                'data' => [
                    'id' => (string)$this->reciverId,
                    'business_id' => (string)$this->businessId,
                    'business_image' => BusinessImage::where('business_id', $this->businessId)->pluck('image')->first(),
                    'type' => 'review',
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
