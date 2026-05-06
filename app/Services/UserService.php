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

    public function lookupByEmail(string $email, ?int $excludeId = null): ?User
    {
        return User::when($excludeId, function ($q) use ($excludeId) {
            return $q->where('id', '!=', $excludeId);
        })
            ->where('email', $email)
            ->select('id', 'name', 'email')
            ->first();
    }
    public function getProfile(User $user, ?int $viewerId = null): User
    {
        // If the viewer is not the owner, mask the email
        if ($viewerId !== $user->id) {
            $user->email = $this->maskEmail($user->email);
        }

        return $user;
    }

    /**
     * Mask email address (e.g., wawaemu@gmail.com -> w*****u@gmail.com)
     */
    private function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];

        $len = strlen($name);
        if ($len <= 2) {
            return substr($name, 0, 1) . '***@' . $domain;
        }

        // Keep first and last char, mask the middle
        return substr($name, 0, 1) . str_repeat('*', $len - 2) . substr($name, -1) . '@' . $domain;
    }
}
