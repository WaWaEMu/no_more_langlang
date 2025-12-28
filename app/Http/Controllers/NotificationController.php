<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Get all notifications for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(50) // Limit to most recent 50 notifications
            ->get();

        return response()->json($notifications);
    }

    /**
     * Get unread notifications count.
     */
    public function unreadCount(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $count = $this->notificationService->getUnreadCount($user->id);

        return response()->json(['count' => $count]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $success = $this->notificationService->markAsRead($id, $user->id);

        if ($success) {
            return response()->json(['message' => '已標記為已讀']);
        }

        return response()->json(['message' => '標記失敗'], 404);
    }

    /**
     * Mark all notifications as read for the authenticated user.
     */
    public function markAllAsRead(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $success = $this->notificationService->markAllAsRead($user->id);

        if ($success) {
            return response()->json(['message' => '已全部標記為已讀']);
        }

        return response()->json(['message' => '標記失敗'], 500);
    }
}
