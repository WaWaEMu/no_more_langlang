<?php

namespace App\Services;

use App\Contracts\FosterVenueServiceInterface;
use App\Models\FosterVenue;
use Illuminate\Support\Collection;

class FosterVenueService implements FosterVenueServiceInterface
{
    /**
     * @inheritDoc
     */
    public function getAllVenues(array $filters = []): Collection
    {
        return FosterVenue::query()
            ->filter($filters)
            ->where('status', FosterVenue::STATUS_ACTIVE)
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function getVenueByUuid(string $uuid): mixed
    {
        return FosterVenue::where('uuid', $uuid)->firstOrFail();
    }
}
