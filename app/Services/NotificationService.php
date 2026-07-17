<?php

namespace App\Services;

use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;

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
        return (bool) $notification->delete();
    }

    public function sendNotification(Notification $notification): array
    {
        if (! $notification->canBeSent()) {
            return [
                'success' => false,
                'message' => 'Notification cannot be sent',
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

        SendNotificationJob::dispatch($notification);

        return [
            'success' => true,
            'message' => 'Notification is being sent in the background.',
        ];
    }
}
