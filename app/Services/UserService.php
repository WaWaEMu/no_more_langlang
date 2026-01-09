<?php

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Models\User;

class UserService implements UserServiceInterface
{
    public function updateProfile(User $user, array $data): User
    {
        $user->updateProfile($data);
        return $user;
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->updatePassword($password);
    }
}
