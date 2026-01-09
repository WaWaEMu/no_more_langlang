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
}
