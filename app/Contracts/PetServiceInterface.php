<?php

namespace App\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface PetServiceInterface
{
    /**
     * Get available pets with filters and pagination.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAvailablePets(array $filters, int $perPage = 12): LengthAwarePaginator;

    /**
     * Get all pets owned by a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserPets(int $userId);

    /**
     * Get all pets favorited by a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserFavorites(int $userId);
}
