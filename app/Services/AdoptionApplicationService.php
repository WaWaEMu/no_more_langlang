<?php

namespace App\Services;

use App\Models\AdoptionApplication;
use App\Contracts\AdoptionApplicationInterface;

class AdoptionApplicationService implements AdoptionApplicationInterface
{
    protected NotificationService $notificationService;

    public function __construct(
        NotificationService $notificationService
    ) {
        $this->notificationService = $notificationService;
    }

    public function create(array $data, int $userId, int $petId): AdoptionApplication
    {
        // Merge user_id and pet_id into data array
        $data['user_id'] = $userId;
        $data['pet_id'] = $petId;

        // Create the adoption application with status 'pending'
        $application = AdoptionApplication::store($data);

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
        $application = AdoptionApplication::updateStatus($id, $status, $ownerMessage);

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
