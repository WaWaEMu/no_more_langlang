<?php

namespace App\Services;

use App\Models\Pet;

class PetDetailsService {
    public function storeDetail(Pet $pet, array $details) {
        $pet->detail()->create($details);
    }
}