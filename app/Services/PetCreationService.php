<?php

namespace App\Services;

use App\Models\Pet;
use App\Contracts\PetCreatorInterface;
use Illuminate\Support\Arr;

class PetCreationService implements PetCreatorInterface {
    protected SendableCityService $cityService;
    protected PetImageService $imageService;
    protected Pet $pet;
    protected PetDetailsService $detailService;

    public function __construct(
        SendableCityService $cityService,
        PetImageService $imageService,
        Pet $pet,
        PetDetailsService $detailService
    ) {
        $this->cityService = $cityService;
        $this->imageService = $imageService;
        $this->pet = $pet;
        $this->detailService = $detailService;
    }

    public function create(array $data, array $blobs = []):Pet {
        $cities = $data['sendable_city'];
        $details = Arr::only($data, [
            'adoption_description',
            'health_description',
            'adoption_condition',
        ]);

        $pet = $this->pet->create($data);

        $this->cityService->attachToPet($pet, $cities);
        $this->imageService->storeImage($pet, $blobs);
        $this->detailService->storeDetail($pet, $details);

        return $pet;
    }
}
