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
        $this->notificationService->notifyNewApplication(
            $petId,
            $application->id,
            $userId
        );

        return $application;
    }

    public function updateStatus(int $id, string $status, ?string $ownerMessage = null): AdoptionApplication
    {
        $application = $this->adoptionApplication->findOrFail($id);
        $application->update([
            'status' => $status,
            'owner_message' => $ownerMessage
        ]);

        // Create notification for applicant
        $this->notificationService->notifyApplicationStatus(
            $id,
            $status,
            $ownerMessage
        );

        return $application;
    }

    public function getReceivedGroupedByPet(int $userId): \Illuminate\Support\Collection
    {
        return AdoptionApplication::getReceivedGroupedByPet($userId);
    }

    public function getSentByUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return AdoptionApplication::getSentByUser($userId);
    }
}
