<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface FosterVenueServiceInterface
{
    /**
     * Get all foster venues with optional filters
     *
     * @param array $filters
     * @return Collection
     */
    public function getAllVenues(array $filters = []): Collection;

    /**
     * Get a single foster venue by UUID
     *
     * @param string $uuid
     * @return mixed
     */
    public function getVenueByUuid(string $uuid): mixed;
}
