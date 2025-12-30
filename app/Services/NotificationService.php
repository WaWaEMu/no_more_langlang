<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Pet;
use App\Models\PetComment;
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
     * @return array Array of created notifications
     */
    public function notifyComment(
        int $petId,
        int $commentId,
        int $commenterId,
        ?int $parentCommentId = null
    ): array {
        $notifications = [];

        try {
            $pet = Pet::findOrFail($petId);
            $ownerId = $pet->user_id;
            $petName = $pet->name;

            // Case 1: New comment (no parent) - notify pet owner
            if (!$parentCommentId) {
                // Don't notify if the commenter is the pet owner
                if ($ownerId !== $commenterId) {
                    $notifications[] = Notification::store([
                        'user_id' => $ownerId,
                        'type' => 'new_comment',
                        'message' => "有人在「{$petName}」的頁面留言",
                        'data' => [
                            'pet_id' => $petId,
                            'pet_name' => $petName,
                            'comment_id' => $commentId,
                        ],
                    ]);
                }
            }
            // Case 2: Reply to a comment - notify the parent comment author
            else {
                $parentComment = PetComment::find($parentCommentId);

                if ($parentComment) {
                    $parentAuthorId = $parentComment->user_id;

                    // Notify the parent comment author (if not replying to themselves)
                    if ($parentAuthorId !== $commenterId) {
                        $notifications[] = Notification::store([
                            'user_id' => $parentAuthorId,
                            'type' => 'comment_reply',
                            'message' => "有人回覆了您在「{$petName}」的留言",
                            'data' => [
                                'pet_id' => $petId,
                                'pet_name' => $petName,
                                'comment_id' => $commentId,
                                'parent_comment_id' => $parentCommentId,
                            ],
                        ]);
                    }

                    // Also notify pet owner if they're not the commenter or parent author
                    if ($ownerId !== $commenterId && $ownerId !== $parentAuthorId) {
                        $notifications[] = Notification::store([
                            'user_id' => $ownerId,
                            'type' => 'comment_reply',
                            'message' => "有人在「{$petName}」的頁面回覆了留言",
                            'data' => [
                                'pet_id' => $petId,
                                'pet_name' => $petName,
                                'comment_id' => $commentId,
                                'parent_comment_id' => $parentCommentId,
                            ],
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to create comment notification: ' . $e->getMessage());
        }

        return $notifications;
    }

    /**
     * Create a notification for new adoption application.
     *
     * @param int $petId
     * @param int $applicationId
     * @param int $applicantId
     * @return Notification|null
     */
    public function notifyNewApplication(
        int $petId,
        int $applicationId,
        int $applicantId
    ): ?Notification {
        try {
            $pet = Pet::findOrFail($petId);
            $ownerId = $pet->user_id;
            $petName = $pet->name;

            // Don't notify if the applicant is the pet owner
            if ($ownerId === $applicantId) {
                return null;
            }

            return Notification::store([
                'user_id' => $ownerId,
                'type' => 'new_adoption_application',
                'message' => "有人提交了「{$petName}」的領養申請",
                'data' => [
                    'pet_id' => $petId,
                    'pet_name' => $petName,
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
            Notification::markAllAsRead($userId);
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
        return Notification::getUnreadCount($userId);
    }

    /**
     * Create a notification for adoption application status update.
     *
     * @param int $applicationId
     * @param string $status
     * @param string|null $ownerMessage
     * @return Notification|null
     */
    public function notifyApplicationStatus(
        int $applicationId,
        string $status,
        ?string $ownerMessage = null
    ): ?Notification {
        try {
            $application = \App\Models\AdoptionApplication::with('pet')->findOrFail($applicationId);
            $applicantId = $application->user_id;
            $petName = $application->pet->name;
            $statusText = $status === 'approved' ? '已通過' : '已婉拒';

            $message = "您對「{$petName}」的領養申請{$statusText}";
            if ($ownerMessage) {
                $message .= "。送養人留言：{$ownerMessage}";
            }

            return Notification::store([
                'user_id' => $applicantId,
                'type' => 'adoption_application_status',
                'message' => $message,
                'data' => [
                    'pet_id' => $application->pet_id,
                    'pet_name' => $petName,
                    'application_id' => $applicationId,
                    'status' => $status,
                    'owner_message' => $ownerMessage,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create adoption application status notification: ' . $e->getMessage());
            return null;
        }
    }
}
