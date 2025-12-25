<?php

namespace App\Services;

use App\Models\AdoptionApplication;
use App\Contracts\AdoptionApplicationInterface;

class AdoptionApplicationService implements AdoptionApplicationInterface
{
    protected AdoptionApplication $adoptionApplication;

    public function __construct(
        AdoptionApplication $adoptionApplication,
    ) {
        $this->adoptionApplication = $adoptionApplication;
    }

    public function create(array $data, int $userId, int $petId): AdoptionApplication
    {
        // Merge user_id and pet_id into data array
        $data['user_id'] = $userId;
        $data['pet_id'] = $petId;

        // Create the adoption application with status 'pending'
        $application = $this->adoptionApplication->create($data);

        return $application;
    }
}
