<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use \App\Models\PushNotification;
use App\Helpers\FirebaseHelper;
class PushNotificationController extends Controller
{
    /**
     * Send Push Notification using FCM API V1
     */
    public function showNotificationForm()
    {
        $data['notifications']=\DB::table('push_notifications')->latest()->paginate(10);
        $data['collections']=\DB::table('collections')->latest()->get();
        return view('admin.push_notification',with($data));
    } 
public function handleNotification(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'body' => 'nullable|string',
            'type' => 'required|in:single,bulk',
            'fcm_token' => 'nullable|string',
           
            'slug' => 'required|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ✅ Handle image upload
        $imageUrl = null;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('push_notifications', 'public');
            $imageUrl = asset('storage/' . $path);
        } else {
            $imageUrl = $request->image ?? null;
        }

        $request->merge(['image' => $imageUrl]);
          PushNotification::create([
                'user_id' => auth()->id(),
                'title' => $request->title,
                'body' => $request->body,
                'image_url' => $request->image,
                'screen' => $request->screen,
                'slug' => $request->slug,
                'fcm_token' => $request->fcm_token,
                // 'type' => 'single',
                // 'status' => $response->successful() ? 'sent' : 'failed',
                // 'error_message' => $response->successful() ? null : $response->body(),
            ]);

        if ($request->type === 'single') {
            return $this->sendSingle($request);
        } else {
            return $this->sendBulk($request);
        }
    }

    /**
     * Send single-user notification and store in DB (only once)
     */
    private function sendSingle(Request $request)
    {
        try {
            $response = $this->sendFcmNotification(
                $request->fcm_token,
                $request->title,
                $request->body,
                $request->image,
                $request->screen,
                $request->slug
            );

            // ✅ Store single notification record
          

            return back()->with('success', 'Notification sent successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed: ' . $e->getMessage());
        }
    }

    /**
     * Send bulk notification to all users but store only one DB record
     */
    private function sendBulk(Request $request)
    {
       \App\Models\User::whereNotNull('device_token')
        ->chunkById(200, function ($users) use ($request) {
        foreach ($users as $user) {
            dispatch(new \App\Jobs\SendFcmNotificationJob(
                $user->device_token,
                $request->title,
                $request->body,
               
                $request->screen,
                $request->slug,
                 $request->image,
              
            ));
        }
    });

        
        

        return back()->with('success', 'Bulk notification sent & logged!');
    }

    /**
     * Firebase FCM V1 API Sender
     */
    private function sendFcmNotification($fcmToken, $title, $body, $image = null, $screen = null, $slug = null)
    {
       $accessToken = FirebaseHelper::getAccessToken();
       $projectId = config('services.fcm.project_id');
        $notificationPayload = [
            "title" => $title,
            "body"  => $body,
        ];
        if (!empty($image)) {
            $notificationPayload['image'] = $image;
        }

        return Http::withToken($accessToken)
        ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                "message" => [
                    "token" => $fcmToken,
                    "notification" =>$notificationPayload,
                    "data" => [
                        "screen" => $screen ?? '',
                        "slug" => $slug ?? '',
                      
                    ]
                ]
            ]);
    }
    public function destroy($id)
{
    try {
        $notification = PushNotification::findOrFail($id);
        $notification->delete();

        return back()->with('success', 'Notification deleted successfully.');
    } catch (\Exception $e) {
        return back()->with('error', 'Failed to delete notification: ' . $e->getMessage());
    }
}

}
