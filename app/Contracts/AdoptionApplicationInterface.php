<?php

namespace App\Contracts;

use App\Models\AdoptionApplication;

interface AdoptionApplicationInterface
{
    /**
     * Create Adoption Application
     *
     * @param array $data front-end form data
     * @param int $userId authenticated user ID
     * @param int $petId pet ID to adopt
     * @return AdoptionApplication
     */
    public function create(array $data, int $userId, int $petId): AdoptionApplication;
}
