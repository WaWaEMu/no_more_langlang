<?php

namespace App\Contracts;

use App\Models\User;

interface UserServiceInterface
{
    /**
     * Update user profile.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateProfile(User $user, array $data): User;

    /**
     * Update user password.
     *
     * @param User $user
     * @param string $password
     * @return void
     */
    public function updatePassword(User $user, string $password): void;

    /**
     * Lookup user by exact email.
     *
     * @param string $email
     * @param int|null $excludeId
     * @return User|null
     */
    public function lookupByEmail(string $email, ?int $excludeId = null): ?User;
}
