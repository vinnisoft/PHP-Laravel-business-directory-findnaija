<?php

namespace App\Notifications;

use App\Models\ChatRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SendMessage extends Notification
{
    use Queueable;

    protected $data;

    public function __construct($senderId, $reciverId, $message, $roomId)
    {
        $this->senderId = $senderId;
        $this->reciverId = $reciverId;
        $this->message = $message;
        $this->roomId = $roomId;
    }

    public function via(object $notifiable): array
    {
        return userNotification($this->reciverId, 'Direct Messages');
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello ' . getUserNameById($this->reciverId))
            ->line('New message recived from '.getUserNameById($this->senderId))
            ->line($this->message)
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'data' => 'New message recived from '.getUserNameById($this->senderId),
            'message' => $this->message,
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
        $chatRoom = ChatRequest::where('id', $this->roomId)->first();

        $notificationData = [
            'title' => 'New Message',
            'body' => 'New message received from ' . getUserNameById(Auth::id()),
        ];

        $data = [
            'message' => [
                'token' => $recipientToken,
                'notification' => $notificationData,
                'data' => [
                    'id' => (string)$chatRoom->id,
                    'user_id' => (string)Auth::id(),
                    'room_id' => (string)$chatRoom->id,
                    'user_name' => getUserNameById(Auth::id()),
                    'user_image' => getUserImageById(Auth::id()),
                    'type' => 'one_to_one',
                    'accepted_status' => (string)$chatRoom->status,
                    'requested_by' => (string)$chatRoom->requested_by,
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
