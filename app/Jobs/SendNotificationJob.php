<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\NotificationLog;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Throwable;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 600;

    public int $backoff = 30;

    private const STUDENT_CHUNK_SIZE = 1000;

    private const FCM_MULTICAST_LIMIT = 500;

    public function __construct(
        public Notification $notification
    ) {
    }

    public function handle(): void
    {
        $notification = $this->notification->fresh();

        if (! $notification || $notification->status !== 'processing') {
            Log::warning('SendNotificationJob skipped: invalid notification state', [
                'notification_id' => $this->notification->id,
                'status' => $notification?->status,
            ]);

            return;
        }

        $successfulSends = 0;
        $failedSends = 0;
        $totalRecipients = 0;

        $messaging = Firebase::messaging();

        $notification->getTargetStudentsQuery()
            ->with(['studentDevices.device:id,fcm_token'])
            ->orderBy('id')
            ->chunkById(self::STUDENT_CHUNK_SIZE, function (Collection $students) use (
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
                    $result = $this->sendMulticastChunk(
                        $messaging,
                        $notification,
                        $tokenChunk->values()->all(),
                        $tokenMap
                    );

                    $successfulStudentIds = $successfulStudentIds->merge($result['successful_student_ids']);
                    $failedStudentIds = $failedStudentIds->merge($result['failed_student_ids']);
                }

                $successfulStudentIds = $successfulStudentIds->unique();
                // Student is successful if at least one token succeeded
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

        Log::info('Notification send completed', [
            'notification_id' => $notification->id,
            'total_recipients' => $totalRecipients,
            'successful_sends' => $successfulSends,
            'failed_sends' => $failedSends,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('SendNotificationJob failed', [
            'notification_id' => $this->notification->id,
            'error' => $exception?->getMessage(),
        ]);

        $this->notification->update([
            'status' => 'failed',
        ]);
    }

    /**
     * @return Collection<string, int> token => student_id
     */
    private function collectTokenStudentMap(Collection $students): Collection
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
        Collection $tokenMap
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

    private function logStudentsWithoutTokens(Notification $notification, Collection $students): void
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
