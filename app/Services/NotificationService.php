<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Student;
use Exception;
use Google\Client as GoogleClient;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function getAllNotifications(): Collection
    {
        return Notification::with('creator')
            ->latest()
            ->get();
    }

    public function createNotification(array $data): Notification
    {
        return Notification::create($data);
    }

    public function updateNotification(Notification $notification, array $data): Notification
    {
        $notification->update($data);
        return $notification->fresh();
    }

    public function deleteNotification(Notification $notification): bool
    {
        return $notification->delete();
    }

    public function sendNotification(Notification $notification): array
    {
        if (!$notification->canBeSent()) {
            return ['success' => false, 'message' => 'Notification cannot be sent'];
        }

        $students = $notification->getTargetStudentsQuery()->get();
        $results = ['successful' => 0, 'failed' => 0, 'details' => []];

        foreach ($students as $student) {
            try {
                $this->sendFcmToStudent($student, $notification);
                $results['successful']++;
                $results['details'][] = [
                    'student_id' => $student->id,
                    'status' => 'success',
                    'sent_at' => now()
                ];
            } catch (Exception $e) {
                $results['failed']++;
                $results['details'][] = [
                    'student_id' => $student->id,
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                    'attempted_at' => now()
                ];
                Log::error('Failed to send notification to student', [
                    'student_id' => $student->id,
                    'notification_id' => $notification->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $notification->update([
            'status' => 'sent',
            'sent_at' => now(),
            'total_recipients' => $students->count(),
            'successful_sends' => $results['successful'],
            'failed_sends' => $results['failed'],
            'send_results' => $results['details']
        ]);

        return [
            'success' => true,
            'message' => "Notification sent to {$results['successful']} students",
            'results' => $results
        ];
    }

    /**
     * @throws Exception
     */
    private function sendFcmToStudent(Student $student, Notification $notification): void
    {
        $fcmTokens = $student->studentDevices()
            ->whereHas('device', function($q) {
                $q->whereNotNull('fcm_token');
            })
            ->with('device')
            ->get()
            ->pluck('device.fcm_token')
            ->filter()
            ->unique();

        if ($fcmTokens->isEmpty()) {
            throw new Exception('No FCM tokens found for student');
        }

        $this->sendFcmNotification($fcmTokens->toArray(), $notification);
    }

    /**
     * @throws Exception
     */
    private function sendFcmNotification(array $fcmTokens, Notification $notification): void
    {
        $projectId = config('services.fcm.project_id');
        $credentialsFilePath = storage_path('app/json/file.json');

        try {
            $client = new GoogleClient();
            $client->setAuthConfig($credentialsFilePath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $token = $client->getAccessToken();

            $accessToken = $token['access_token'];

            foreach ($fcmTokens as $fcmToken) {
                $this->sendSingleFcmMessage($fcmToken, $notification, $accessToken, $projectId);
            }
        } catch (Exception $e) {
            Log::error('FCM Authentication failed: ' . $e->getMessage());
            throw new Exception('FCM service unavailable: ' . $e->getMessage());
        }
    }

    /**
     * @throws ConnectionException
     */
    private function sendSingleFcmMessage(string $fcmToken, Notification $notification, string $accessToken, string $projectId): void
    {
        $data = [
            "message" => [
                "token" => $fcmToken,
                "notification" => [
                    "title" => $notification->title,
                    "body" => $notification->body,
                ],
                "data" => [
                    "notification_id" => (string)$notification->id,
                    "type" => $notification->type,
                ]
            ]
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->timeout(30)->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", $data);

            if (!$response->successful()) {
                throw new Exception("FCM send failed with status: {$response->status()}, body: {$response->body()}");
            }

            Log::info('FCM message sent successfully', [
                'token' => substr($fcmToken, 0, 10) . '...',
                'notification_id' => $notification->id
            ]);

        } catch (Exception $e) {
            Log::error('FCM send failed', [
                'token' => substr($fcmToken, 0, 10) . '...',
                'error' => $e->getMessage(),
                'notification_id' => $notification->id
            ]);
            throw $e;
        }
    }
}
