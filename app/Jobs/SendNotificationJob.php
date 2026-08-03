<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Optional queue path. Dashboard create/send uses NotificationService synchronously.
 */
class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 600;

    public int $backoff = 30;

    public function __construct(
        public Notification $notification
    ) {
    }

    public function handle(NotificationService $notificationService): void
    {
        $result = $notificationService->sendNotification($this->notification->fresh());

        if (! $result['success']) {
            throw new \RuntimeException($result['message']);
        }
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
}
