<?php

namespace App\Services;

use App\Models\AdoptionCase;
use App\Models\AdoptionApplication;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class AdoptionCaseService implements AdoptionCaseServiceInterface
{
    /**
     * Get list of adoption cases for the user based on role.
     *
     * @param User $user
     * @param string $role 'adopter' or 'owner'
     * @return Collection
     */
    public function getUserCases(User $user, string $role): Collection
    {
        if ($role === 'adopter') {
            return AdoptionCase::forAdopter($user->id)
                ->latest('started_at')
                ->get();
        }

        return AdoptionCase::forOwner($user->id)
            ->latest('started_at')
            ->get();
    }

    public function createCase(array $data, User $owner): AdoptionCase
    {
        try {
            $pet = Pet::findOrFail($data['pet_id']);

            if ($pet->user_id !== $owner->id) {
                throw new \Exception('Unauthorized: User is not the owner of this pet.', 403);
            }

            return AdoptionCase::finalize($data, $owner);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception('Pet not found.', 404);
        } catch (\Exception $e) {
            // Re-throw the exception to be handled by the controller's catch block
            throw $e;
        }
    }
}
