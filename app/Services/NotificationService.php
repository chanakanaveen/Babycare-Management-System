<?php

namespace App\Services;

use App\Events\NewNotification;
use App\Jobs\SendVaccineScheduledEmail;
use App\Models\BabyVaccination;
use App\Models\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notify a parent that a vaccination has been scheduled for their baby.
     */
    public function notifyParentVaccineScheduled(BabyVaccination $record): void
    {
        try {
            $record->load(['baby.parentUser', 'baby.midwife', 'vaccine']);

            $baby = $record->baby;
            $parent = $baby->parentUser;
            $midwife = $baby->midwife;
            $vaccine = $record->vaccine;

            if (!$parent) {
                Log::warning('NotificationService: No parent found for baby', ['baby_id' => $baby->baby_id]);
                return;
            }

            $title = 'Vaccination Scheduled';
            $message = "A {$vaccine->vaccine_name} vaccination has been scheduled for {$baby->full_name} on {$record->scheduled_date->format('M d, Y')} by Midwife {$midwife->name}.";

            // Create DB notification
            $notification = Notification::create([
                'recipient_type' => 'parent',
                'recipient_id' => $parent->id,
                'type' => 'vaccine_scheduled',
                'title' => $title,
                'message' => $message,
                'data' => [
                    'baby_id' => $baby->baby_id,
                    'record_id' => $record->record_id,
                    'vaccine_name' => $vaccine->vaccine_name,
                    'scheduled_date' => $record->scheduled_date->format('Y-m-d'),
                ],
            ]);

            // Update vaccination record
            $record->update([
                'notification_sent_at' => now(),
                'parent_notified' => true,
            ]);

            // Dispatch email job
            SendVaccineScheduledEmail::dispatch(
                $parent->email,
                $baby->full_name,
                $vaccine->vaccine_name,
                $record->scheduled_date->format('M d, Y'),
                $midwife->name
            );

            // Broadcast (will be logged if BROADCAST_DRIVER=log)
            try {
                event(new NewNotification($notification->toArray(), 'parent', $parent->id));
            } catch (\Exception $e) {
                Log::info('Broadcast not sent (driver may be log)', ['error' => $e->getMessage()]);
            }

        } catch (\Exception $e) {
            Log::error('NotificationService: Failed to notify parent', [
                'record_id' => $record->record_id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create a notification for a midwife.
     */
    public function notifyMidwife(int $midwifeId, string $type, string $title, string $message, array $data = []): ?Notification
    {
        try {
            $notification = Notification::create([
                'recipient_type' => 'midwife',
                'recipient_id' => $midwifeId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);

            try {
                event(new NewNotification($notification->toArray(), 'midwife', $midwifeId));
            } catch (\Exception $e) {
                Log::info('Broadcast not sent', ['error' => $e->getMessage()]);
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error('NotificationService: Failed to notify midwife', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Create a notification for a parent.
     */
    public function notifyParent(int $parentId, string $type, string $title, string $message, array $data = []): ?Notification
    {
        try {
            $notification = Notification::create([
                'recipient_type' => 'parent',
                'recipient_id' => $parentId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
            ]);

            try {
                event(new NewNotification($notification->toArray(), 'parent', $parentId));
            } catch (\Exception $e) {
                Log::info('Broadcast not sent', ['error' => $e->getMessage()]);
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error('NotificationService: Failed to notify parent', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(int $notificationId, string $recipientType, int $recipientId): bool
    {
        return Notification::where('id', $notificationId)
            ->where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]) > 0;
    }

    /**
     * Mark all notifications as read for a recipient.
     */
    public function markAllRead(string $recipientType, int $recipientId): int
    {
        return Notification::where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get unread notification count.
     */
    public function getUnreadCount(string $recipientType, int $recipientId): int
    {
        return Notification::forRecipient($recipientType, $recipientId)
            ->unread()
            ->count();
    }

    /**
     * Get notifications for a recipient.
     */
    public function getNotifications(string $recipientType, int $recipientId, int $limit = 20): Collection
    {
        return Notification::forRecipient($recipientType, $recipientId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
