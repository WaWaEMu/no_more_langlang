<?php

namespace App\Services;

use App\Models\AdoptionApplication;
use App\Contracts\AdoptionApplicationInterface;

class AdoptionApplicationService implements AdoptionApplicationInterface
{
    protected AdoptionApplication $adoptionApplication;
    protected NotificationService $notificationService;

    public function __construct(
        AdoptionApplication $adoptionApplication,
        NotificationService $notificationService
    ) {
        $this->adoptionApplication = $adoptionApplication;
        $this->notificationService = $notificationService;
    }

    public function create(array $data, int $userId, int $petId): AdoptionApplication
    {
        // Merge user_id and pet_id into data array
        $data['user_id'] = $userId;
        $data['pet_id'] = $petId;

        // Create the adoption application with status 'pending'
        $application = $this->adoptionApplication->create($data);

        // Create notification for pet owner
        $this->notificationService->createAdoptionApplicationNotification(
            $petId,
            $application->id,
            $userId
        );

        return $application;
    }
}
