<?php

namespace App\Services;

use App\Contracts\PetCommentServiceInterface;
use App\Models\PetComment;
use Illuminate\Database\Eloquent\Collection;

class PetCommentService implements PetCommentServiceInterface
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getCommentsByPetId(int $petId): Collection
    {
        return PetComment::getByPetId($petId);
    }

    public function createComment(array $data, int $userId, int $petId): PetComment
    {
        // If parent_id is provided, validate it belongs to the same pet
        if (isset($data['parent_id'])) {
            $parentComment = PetComment::find($data['parent_id']);
            if ($parentComment && $parentComment->pet_id != $petId) {
                throw new \InvalidArgumentException('無效的父留言');
            }
        }

        $comment = PetComment::create([
            'pet_id' => $petId,
            'user_id' => $userId,
            'parent_id' => $data['parent_id'] ?? null,
            'content' => $data['content'],
        ]);

        // Create notification for pet owner
        $this->notificationService->notifyComment(
            $petId,
            $comment->id,
            $userId,
            $data['parent_id'] ?? null
        );

        return $comment->load('user:id,name');
    }
}
