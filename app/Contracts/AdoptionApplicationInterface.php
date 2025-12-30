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

    /**
     * Update Adoption Application status
     *
     * @param int $id application ID
     * @param string $status new status
     * @param string|null $ownerMessage message from the pet owner
     * @return AdoptionApplication
     */
    public function updateStatus(int $id, string $status, ?string $ownerMessage = null): AdoptionApplication;

    /**
     * Get applications received for the user's pets, grouped by pet.
     *
     * @param int $userId
     * @return \Illuminate\Support\Collection
     */
    public function getReceivedGroupedByPet(int $userId): \Illuminate\Support\Collection;

    /**
     * Get applications sent by the user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSentByUser(int $userId): \Illuminate\Database\Eloquent\Collection;
}
