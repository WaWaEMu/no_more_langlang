<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use App\Models\AdoptionCase;

interface AdoptionCaseServiceInterface
{
    /**
     * Get list of adoption cases for the user based on role.
     *
     * @param User $user
     * @param string $role 'adopter' or 'owner'
     * @return Collection
     */
    public function getUserCases(User $user, string $role): Collection;

    /**
     * Create a new adoption case.
     *
     * @param array $data
     * @param User $owner
     * @return AdoptionCase
     */
    public function createCase(array $data, User $owner): AdoptionCase;
}
