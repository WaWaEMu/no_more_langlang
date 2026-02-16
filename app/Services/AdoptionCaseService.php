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

            // Validate ownership
            if ($pet->user_id !== $owner->id) {
                throw new \Exception('無權限：您不是此寵物的所有者', 403);
            }

            // Validate pet is not already adopted
            if ($pet->status === Pet::STATUS_ADOPTED) {
                throw new \Exception('此寵物已被領養', 400);
            }

            // Validate application status if provided
            if (!empty($data['application_id'])) {
                $application = AdoptionApplication::find($data['application_id']);
                if (!$application) {
                    throw new \Exception('申請不存在', 404);
                }
                if ($application->status !== 'approved') {
                    throw new \Exception('申請狀態必須為已核准才能結案', 400);
                }
            }

            return AdoptionCase::finalize($data, $owner);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \Exception('寵物不存在', 404);
        } catch (\Exception $e) {
            // Re-throw the exception to be handled by the controller's catch block
            throw $e;
        }
    }
}
