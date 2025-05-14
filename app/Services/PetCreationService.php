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
        $cities = $data['sendableCity'];

        $pet = $this->pet->create([
            'title' => $data['title'],
            'city' => $data['city'],
            'town' => $data['town'],
            'is_stray' => $data['isStray'],
            'type' => $data['type'],
            'color' => $data['color'],
            'fur_type' => $data['furType'],
            'name' => $data['name'],
            'gender' => $data['gender'],
            'age' => $data['age'],
            'is_neuter' => $data['isNeuter'],
            'description' => $data['description'],
            'health_description' => $data['healthDescription'],
            'condition' => $data['condition']
        ]);

        $this->cityService->attachToPet($pet, $cities);
        $this->imageService->storeImage($pet, $blobs);

        return $pet;
    }
}
