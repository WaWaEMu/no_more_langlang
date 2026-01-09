<?php

namespace App\Services;

use App\Contracts\PetServiceInterface;
use App\Models\Pet;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class PetService implements PetServiceInterface
{
    public function getAvailablePets(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return Pet::getAvailablePets($filters, $perPage);
    }
}
