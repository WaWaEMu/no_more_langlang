<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Contracts\AdoptionApplicationInterface;

class AdoptionApplicationController extends Controller
{
    protected AdoptionApplicationInterface $adoptionApplicationService;

    public function __construct(
        AdoptionApplicationInterface $adoptionApplicationService
    ) {
        $this->adoptionApplicationService = $adoptionApplicationService;
    }

    /**
     * Get all adoption applications for the current user's pets, grouped by pet.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $result = $this->adoptionApplicationService->getReceivedGroupedByPet($user->id);

        return response()->json(['data' => $result]);
    }

    /**
     * Get all adoption applications submitted by the current user.
     */
    public function sent(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $applications = $this->adoptionApplicationService->getSentByUser($user->id);

        $result = $applications->map(function ($app) {
            return [
                'id' => $app->id,
                'pet' => [
                    'id' => $app->pet->id,
                    'name' => $app->pet->name,
                    'image' => $app->pet->images->first()?->path,
                ],
                'name' => $app->name,
                'phone' => $app->phone,
                'line_id' => $app->line_id,
                'housing_type' => $app->housing_type,
                'experience' => $app->experience,
                'family_agreement' => $app->family_agreement,
                'message' => $app->message,
                'owner_message' => $app->owner_message,
                'status' => $app->status,
                'created_at' => $app->created_at,
                'updated_at' => $app->updated_at,
            ];
        });

        return response()->json(['data' => $result]);
    }

    /**
     * Update the status of an adoption application.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,approved,rejected',
            'owner_message' => 'nullable|string|max:1000',
        ]);

        try {
            $application = $this->adoptionApplicationService->updateStatus(
                $id,
                $validated['status'],
                $validated['owner_message'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => __('Application status updated'),
                'data' => $application
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Update failed:') . $e->getMessage()
            ], 500);
        }
    }
}
