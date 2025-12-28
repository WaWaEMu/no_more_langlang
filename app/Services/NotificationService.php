<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Pet;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Create a notification for new pet comment.
     *
     * @param int $petId
     * @param int $commentId
     * @param int $commenterId
     * @param int|null $parentCommentId
     * @return Notification|null
     */
    public function createCommentNotification(
        int $petId,
        int $commentId,
        int $commenterId,
        ?int $parentCommentId = null
    ): ?Notification {
        try {
            $pet = Pet::findOrFail($petId);
            $ownerId = $pet->user_id;

            // Don't notify if the commenter is the pet owner
            if ($ownerId === $commenterId) {
                return null;
            }

            $type = $parentCommentId ? 'comment_reply' : 'new_comment';
            $message = $parentCommentId
                ? '有人回覆了您寵物的留言'
                : '有人在您的寵物頁面留言';

            return Notification::create([
                'user_id' => $ownerId,
                'type' => $type,
                'message' => $message,
                'data' => [
                    'pet_id' => $petId,
                    'comment_id' => $commentId,
                    'parent_comment_id' => $parentCommentId,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create comment notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a notification for new adoption application.
     *
     * @param int $petId
     * @param int $applicationId
     * @param int $applicantId
     * @return Notification|null
     */
    public function createAdoptionApplicationNotification(
        int $petId,
        int $applicationId,
        int $applicantId
    ): ?Notification {
        try {
            $pet = Pet::findOrFail($petId);
            $ownerId = $pet->user_id;

            // Don't notify if the applicant is the pet owner
            if ($ownerId === $applicantId) {
                return null;
            }

            return Notification::create([
                'user_id' => $ownerId,
                'type' => 'new_adoption_application',
                'message' => '有人提交了領養申請',
                'data' => [
                    'pet_id' => $petId,
                    'application_id' => $applicationId,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create adoption application notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Mark notification as read.
     *
     * @param int $notificationId
     * @param int $userId
     * @return bool
     */
    public function markAsRead(int $notificationId, int $userId): bool
    {
        try {
            $notification = Notification::where('id', $notificationId)
                ->where('user_id', $userId)
                ->firstOrFail();

            $notification->markAsRead();
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead(int $userId): bool
    {
        try {
            Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark all notifications as read: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get unread notifications count for a user.
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }
}
