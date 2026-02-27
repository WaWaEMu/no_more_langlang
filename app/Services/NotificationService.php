<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Pet;
use App\Models\PetComment;
use App\Mail\TrackingReportSubmittedMail;
use App\Mail\ReportDueReminderMail;
use App\Mail\ReportOverdueAlertMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
                        'message' => __("Notification: New comment", ['petName' => $petName]),
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
                            'message' => __("Notification: Comment reply", ['petName' => $petName]),
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
                            'message' => __("Notification: Comment reply on pet", ['petName' => $petName]),
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
                'message' => __("Notification: New application", ['petName' => $petName]),
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
    public function getAll(int $userId)
    {
        return Notification::getAll($userId);
    }

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
            $statusText = $status === 'approved' ? __('Approved') : __('Rejected');

            $message = __("Notification: Application status", ['petName' => $petName, 'status' => $statusText]);
            if ($ownerMessage) {
                $message .= __("Notification: Owner message", ['message' => $ownerMessage]);
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

    /**
     * Create a notification + send email when an adopter submits a tracking report.
     * Notifies the pet owner (sender).
     *
     * @param int $caseId
     * @param int $reporterId
     * @param string $reportContent
     * @return Notification|null
     */
    public function notifyTrackingReport(
        int $caseId,
        int $reporterId,
        string $reportContent = ''
    ): ?Notification {
        try {
            $case = \App\Models\AdoptionCase::with(['pet', 'owner'])->findOrFail($caseId);
            $ownerId = $case->owner_id;
            $petName = $case->pet->name;

            // Don't notify if somehow the owner is the reporter
            if ($ownerId === $reporterId) {
                return null;
            }

            // 1. In-app notification
            $notification = Notification::store([
                'user_id' => $ownerId,
                'type' => 'tracking_report_submitted',
                'message' => __("Notification: Tracking report submitted", ['petName' => $petName]),
                'data' => [
                    'pet_id' => $case->pet_id,
                    'pet_name' => $petName,
                    'adoption_case_id' => $caseId,
                ],
            ]);

            // 2. Email notification to owner
            if ($case->owner && $case->owner->email) {
                Mail::to($case->owner->email)
                    ->send(new TrackingReportSubmittedMail($case, $reportContent));
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to create tracking report notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a reminder notification + send email for an adopter when a report is due.
     *
     * @param \App\Models\AdoptionCase $case
     * @param int $daysUntilDue
     * @return Notification|null
     */
    public function notifyReportDueReminder(
        \App\Models\AdoptionCase $case,
        int $daysUntilDue = 2
    ): ?Notification {
        try {
            $petName = $case->pet->name ?? '毛孩';

            // 1. In-app notification
            $notification = Notification::store([
                'user_id' => $case->adopter_id,
                'type' => 'tracking_report_reminder',
                'message' => __("Notification: Report due reminder", ['petName' => $petName]),
                'data' => [
                    'pet_id' => $case->pet_id,
                    'pet_name' => $petName,
                    'adoption_case_id' => $case->id,
                ],
            ]);

            // 2. Email notification to adopter
            $adopter = \App\Models\User::find($case->adopter_id);
            if ($adopter && $adopter->email) {
                Mail::to($adopter->email)
                    ->send(new ReportDueReminderMail($case, $daysUntilDue));
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to create report reminder notification: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create an overdue alert notification + send email to the pet owner.
     * Triggered at day +1, +3, +7 after the report due date.
     *
     * @param \App\Models\AdoptionCase $case
     * @param int $daysOverdue
     * @return Notification|null
     */
    public function notifyReportOverdueToOwner(
        \App\Models\AdoptionCase $case,
        int $daysOverdue
    ): ?Notification {
        try {
            $petName = $case->pet->name ?? '毛孩';

            // 1. In-app notification
            $notification = Notification::store([
                'user_id' => $case->owner_id,
                'type' => 'tracking_report_overdue',
                'message' => __("Notification: Report overdue alert", ['petName' => $petName, 'days' => $daysOverdue]),
                'data' => [
                    'pet_id' => $case->pet_id,
                    'pet_name' => $petName,
                    'adoption_case_id' => $case->id,
                    'days_overdue' => $daysOverdue,
                ],
            ]);

            // 2. Email notification to owner
            $owner = \App\Models\User::find($case->owner_id);
            if ($owner && $owner->email) {
                Mail::to($owner->email)
                    ->send(new ReportOverdueAlertMail($case, $daysOverdue));
            }

            return $notification;
        } catch (\Exception $e) {
            Log::error('Failed to create overdue report notification: ' . $e->getMessage());
            return null;
        }
    }
}
