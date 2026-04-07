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

    public function getUserPets(int $userId)
    {
        return Pet::getUserPets($userId);
    }

    public function getUserFavorites(int $userId)
    {
        return Pet::getUserFavorites($userId);
    }

    public function replaceImage($pet, $imageId, $imageFile)
    {
        $petImage = $pet->images()->where('id', $imageId)->firstOrFail();

        // Delete old file from storage
        if ($petImage->path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($petImage->path);
        }

        // Store new image
        $date = now()->format('Y_m_d');
        $extension = $imageFile->guessExtension();
        $filename = $pet->id . '_' . $petImage->index . '_' . time() . '.' . $extension;
        $path = $imageFile->storeAs("images/{$date}", $filename, 'public');

        // Update DB record
        $petImage->update(['path' => $path]);

        return $path;
    }

    public function addImage($pet, $imageFile)
    {
        $existingIndices = $pet->images()->pluck('index')->toArray();
        $nextIndex = 0;
        for ($i = 0; $i < 3; $i++) {
            if (!in_array($i, $existingIndices)) {
                $nextIndex = $i;
                break;
            }
        }

        // Store new image
        $date = now()->format('Y_m_d');
        $extension = $imageFile->guessExtension();
        $filename = $pet->id . '_' . $nextIndex . '_' . time() . '.' . $extension;
        $path = $imageFile->storeAs("images/{$date}", $filename, 'public');

        // Create DB record
        return $pet->images()->create([
            'type' => 'preview',
            'index' => $nextIndex,
            'path' => $path,
        ]);
    }
}
