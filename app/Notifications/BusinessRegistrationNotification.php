<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Auth;

class BusinessRegistrationNotification extends Notification
{
    use Queueable;

    public function __construct($userName, $businessName)
    {
        $this->userName = $userName;
        $this->businessName = $businessName;
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'fcm'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Hello ' . $this->userName)
            ->subject('Expire Business Registration')
            ->line('Your business registration of ' . $this->businessName . ' has been expired!')
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'data' => 'Your business registration of ' . $this->businessName . ' has been expired!'
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

        $recipientToken = Auth::user()->fcm_token;
        $notificationData = [
            'title' => 'Business Registration',
            'body' => 'Your business registration of ' . $this->businessName . ' has been expired!',
        ];
        $data = [
            'message' => [
                'token' => $recipientToken,
                'notification' => $notificationData,
                'data' => [
                    'id' => '',
                    'user_id' => (string)Auth::id(),
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
