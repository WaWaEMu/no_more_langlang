<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use App\Models\PetComment;

interface PetCommentServiceInterface
{
    /**
     * Get top-level comments for a pet.
     *
     * @param int $petId
     * @return Collection
     */
    public function getCommentsByPetId(int $petId): Collection;

    /**
     * Create a new comment.
     *
     * @param array $data
     * @param int $userId
     * @param int $petId
     * @return PetComment
     */
    public function createComment(array $data, int $userId, int $petId): PetComment;
}
