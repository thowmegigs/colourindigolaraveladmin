<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use App\Models\Notification;
use App\Helpers\FirebaseHelper;
class SendFcmNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $fcmToken, $title, $body, $image, $screen, $slug;

    public function __construct($fcmToken, $title, $body, $screen, $slug, $image)
    {
        $this->fcmToken = $fcmToken;
        $this->title = $title;
        $this->body = $body;
        $this->image = $image;
        $this->screen = $screen;
        $this->slug = $slug;
      
    }

  public function handle()
{
    $accessToken = FirebaseHelper::getAccessToken();
    $projectId = config('services.fcm.project_id');
$notificationPayload = [
    "title" => $this->title,
    "body"  => $this->body,
];
if (!empty($this->image)) {
    $notificationPayload['image'] = $this->image;
}
    $response = Http::withToken($accessToken)
        ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
            "message" => [
                "token" => $this->fcmToken,
                 "notification" => $notificationPayload,
                "data" => [
                    "screen" => $this->screen ?? '',
                    "slug" => $this->slug ?? '',
                    // Optional: "notification_id" => $this->notificationId,
                ],
            ]
        ]);

    if (!$response->successful()) {
        \Log::error('FCM send failed', [
            'token' => $this->fcmToken,
            'response' => $response->body(),
        ]);
    }
}
}
