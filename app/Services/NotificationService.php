<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationLog;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Throwable;

class NotificationService
{
    private const STUDENT_CHUNK_SIZE = 1000;

    private const FCM_MULTICAST_LIMIT = 500;

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
        return (bool) $notification->delete();
    }

    /**
     * Send notification synchronously and wait until FCM finishes.
     */
    public function sendNotification(Notification $notification): array
    {
        if (! $notification->canBeSent()) {
            return [
                'success' => false,
                'message' => trans('main_trans.Notification_cannot_be_sent'),
            ];
        }

        $notification->logs()->delete();

        $notification->update([
            'status' => 'processing',
            'successful_sends' => 0,
            'failed_sends' => 0,
            'total_recipients' => 0,
            'sent_at' => null,
        ]);

        try {
            $result = $this->deliverNotification($notification->fresh());

            return [
                'success' => true,
                'message' => trans('main_trans.Notification_sent_successfully', [
                    'success' => $result['successful_sends'],
                    'failed' => $result['failed_sends'],
                    'total' => $result['total_recipients'],
                ]),
                'data' => $result,
            ];
        } catch (Throwable $e) {
            Log::error('Notification send failed', [
                'notification_id' => $notification->id,
                'error' => $e->getMessage(),
            ]);

            $notification->update([
                'status' => 'failed',
            ]);

            return [
                'success' => false,
                'message' => trans('main_trans.Notification_send_failed') . ': ' . $e->getMessage(),
            ];
        }
    }

    /**
     * @return array{total_recipients:int,successful_sends:int,failed_sends:int}
     */
    private function deliverNotification(Notification $notification): array
    {
        $successfulSends = 0;
        $failedSends = 0;
        $totalRecipients = 0;

        $messaging = Firebase::messaging();

        $notification->getTargetStudentsQuery()
            ->with(['studentDevices.device:id,fcm_token'])
            ->orderBy('id')
            ->chunkById(self::STUDENT_CHUNK_SIZE, function (SupportCollection $students) use (
                $notification,
                $messaging,
                &$successfulSends,
                &$failedSends,
                &$totalRecipients
            ) {
                $totalRecipients += $students->count();

                $tokenMap = $this->collectTokenStudentMap($students);
                $studentIdsWithTokens = $tokenMap->values()->unique();
                $studentsWithoutTokens = $students->whereNotIn('id', $studentIdsWithTokens->all());

                if ($studentsWithoutTokens->isNotEmpty()) {
                    $failedSends += $studentsWithoutTokens->count();
                    $this->logStudentsWithoutTokens($notification, $studentsWithoutTokens);
                }

                if ($tokenMap->isEmpty()) {
                    return;
                }

                $successfulStudentIds = collect();
                $failedStudentIds = collect();

                foreach ($tokenMap->keys()->chunk(self::FCM_MULTICAST_LIMIT) as $tokenChunk) {
                    $chunkResult = $this->sendMulticastChunk(
                        $messaging,
                        $notification,
                        $tokenChunk->values()->all(),
                        $tokenMap
                    );

                    $successfulStudentIds = $successfulStudentIds->merge($chunkResult['successful_student_ids']);
                    $failedStudentIds = $failedStudentIds->merge($chunkResult['failed_student_ids']);
                }

                $successfulStudentIds = $successfulStudentIds->unique();
                $failedStudentIds = $failedStudentIds
                    ->unique()
                    ->diff($successfulStudentIds);

                $successfulSends += $successfulStudentIds->count();
                $failedSends += $failedStudentIds->count();
            });

        $notification->update([
            'status' => 'sent',
            'sent_at' => now(),
            'total_recipients' => $totalRecipients,
            'successful_sends' => $successfulSends,
            'failed_sends' => $failedSends,
        ]);

        return [
            'total_recipients' => $totalRecipients,
            'successful_sends' => $successfulSends,
            'failed_sends' => $failedSends,
        ];
    }

    /**
     * @return SupportCollection<string, int> token => student_id
     */
    private function collectTokenStudentMap(SupportCollection $students): SupportCollection
    {
        $map = collect();

        foreach ($students as $student) {
            $tokens = $student->studentDevices
                ->map(fn ($studentDevice) => $studentDevice->device?->fcm_token)
                ->filter()
                ->unique();

            foreach ($tokens as $token) {
                $map->put($token, $student->id);
            }
        }

        return $map;
    }

    private function sendMulticastChunk(
        Messaging $messaging,
        Notification $notification,
        array $tokens,
        SupportCollection $tokenMap
    ): array {
        $successfulStudentIds = collect();
        $failedStudentIds = collect();

        try {
            $message = CloudMessage::new()
                ->withNotification(FirebaseNotification::create(
                    $notification->title,
                    $notification->body
                ))
                ->withData([
                    'notification_id' => (string) $notification->id,
                    'type' => (string) $notification->type,
                ]);

            $report = $messaging->sendMulticast($message, $tokens);

            foreach ($report->successes()->getItems() as $success) {
                $token = $success->target()->value();
                $studentId = $tokenMap->get($token);
                if ($studentId) {
                    $successfulStudentIds->push($studentId);
                }
            }

            foreach ($report->failures()->getItems() as $failure) {
                $token = $failure->target()->value();
                $error = $failure->error()?->getMessage() ?? 'Unknown FCM error';
                $studentId = $tokenMap->get($token);

                Log::error('FCM multicast token failed', [
                    'notification_id' => $notification->id,
                    'student_id' => $studentId,
                    'token' => substr((string) $token, 0, 12) . '...',
                    'error' => $error,
                ]);

                if ($studentId) {
                    $failedStudentIds->push($studentId);
                    $this->createFailureLog($notification->id, $studentId, $error);
                }
            }
        } catch (MessagingException|FirebaseException|Exception $e) {
            Log::error('FCM multicast chunk failed', [
                'notification_id' => $notification->id,
                'token_count' => count($tokens),
                'error' => $e->getMessage(),
            ]);

            foreach ($tokens as $token) {
                $studentId = $tokenMap->get($token);
                if ($studentId) {
                    $failedStudentIds->push($studentId);
                    $this->createFailureLog($notification->id, $studentId, $e->getMessage());
                }
            }
        }

        return [
            'successful_student_ids' => $successfulStudentIds,
            'failed_student_ids' => $failedStudentIds,
        ];
    }

    private function logStudentsWithoutTokens(Notification $notification, SupportCollection $students): void
    {
        foreach ($students as $student) {
            $this->createFailureLog(
                $notification->id,
                $student->id,
                'No FCM tokens found for student'
            );
        }
    }

    private function createFailureLog(int $notificationId, int $studentId, string $errorMessage): void
    {
        try {
            NotificationLog::create([
                'notification_id' => $notificationId,
                'student_id' => $studentId,
                'status' => 'failed',
                'error_message' => mb_substr($errorMessage, 0, 1000),
            ]);
        } catch (Exception $e) {
            Log::warning('Failed to write notification_log', [
                'notification_id' => $notificationId,
                'student_id' => $studentId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
