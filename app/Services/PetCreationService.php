<?php

namespace App\Services;

use App\Models\Pet;
use App\Contracts\PetCreatorInterface;

class PetCreationService implements PetCreatorInterface {
    protected SendableCityService $cityService;
    protected PetImageService $imageService;
    protected Pet $pet;

    public function __construct(
        SendableCityService $cityService,
        PetImageService $imageService,
        Pet $pet
    ) {
        $this->cityService = $cityService;
        $this->imageService = $imageService;
        $this->pet = $pet;
    }

    public function create(array $data, array $blobs = []):Pet {
        $cities = $data['sendable_city'];

        $pet = $this->pet->create($data);

        $this->cityService->attachToPet($pet, $cities);
        $this->imageService->storeImage($pet, $blobs);

        return $pet;
    }
}
