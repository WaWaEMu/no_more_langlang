<?php

namespace App\Services;

use App\Models\Pet;

class PetImageService {
    public function storeImage(Pet $pet, array $images): void {
        $index = 0;
        $date = now()->format('Y_m_d');

        foreach ($images as $image) {
            $extension = $image->guessExtension();
            $filename = $pet->id . '_' . $index . '.' . $extension;
            $path = $image->storeAs("images/{$date}", $filename, 'public');
            $pet->images()->create([
                'type' => 'preview',
                'index' => $index,
                'path' => $path ?: null
            ]);

            $index++;
        }
    }
}