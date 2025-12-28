<?php

namespace App\Http\Controllers;

use App\Models\PetComment;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetCommentController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index($petId)
    {
        $comments = PetComment::with(['user:id,name', 'replies.user:id,name'])
            ->where('pet_id', $petId)
            ->whereNull('parent_id') // Only fetch top-level comments
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request, $petId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:pet_comments,id',
        ]);

        // If parent_id is provided, validate it belongs to the same pet
        if ($request->has('parent_id')) {
            $parentComment = PetComment::find($request->parent_id);
            if ($parentComment && $parentComment->pet_id != $petId) {
                return response()->json(['message' => '無效的父留言'], 400);
            }
        }

        $comment = PetComment::create([
            'pet_id' => $petId,
            'user_id' => Auth::id(),
            'parent_id' => $request->input('parent_id'),
            'content' => $request->input('content'),
        ]);

        // Create notification for pet owner
        $this->notificationService->createCommentNotification(
            $petId,
            $comment->id,
            Auth::id(),
            $request->input('parent_id')
        );

        return response()->json($comment->load('user:id,name'));
    }
}
