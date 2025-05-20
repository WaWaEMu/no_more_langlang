<?php

namespace App\Services;

use App\Models\Pet;
use App\Contracts\PetCreatorInterface;
use Illuminate\Support\Arr;

class PetCreationService implements PetCreatorInterface {
    protected Pet $pet;

    public function __construct(
        Pet $pet,
    ) {
        $this->pet = $pet;
    }

    public function create(array $data, array $blobs = []):Pet {
        $cities = $data['sendable_city'];
        $details = Arr::only($data, [
            'adoption_description',
            'health_description',
            'adoption_condition',
        ]);

        $pet = $this->pet->create($data);

        // Attach cities where the pet can be adopted
        $pet->attachSendableCities($cities);

        // Store upload images related to the pet
        $pet->storeImages($blobs);

        // Save details description of the pet
        $pet->storeDetail($details);

        return $pet;
    }
}
