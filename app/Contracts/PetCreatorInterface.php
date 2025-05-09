<?php

namespace App\Contracts;

use App\Models\Pet;

interface PetCreatorInterface {
    /**
     * Create Pet Profile
     *
     * @param array $data front-end form data
     * @param array $blobs uploaded image files
     * @return Pet
     */
    public function create(array $data, array $blobs = []): Pet;
}
