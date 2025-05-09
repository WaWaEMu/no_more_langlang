<?php

namespace App\Services;

use App\Models\Pet;

class SendableCityService {
    public function attachToPet(Pet $pet, array $cities): void {
        foreach ($cities as $city) {
            $pet->sendableCitites()->create([
                'city' => $city
            ]);
        }
    }
}