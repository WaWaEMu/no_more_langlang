<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Services\AdoptionApplicationService;

class AdoptionApplicationController extends Controller
{
    protected AdoptionApplicationService $adoptionApplicationService;

    public function __construct(
        AdoptionApplicationService $adoptionApplicationService
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

        // Get all pets belonging to the user with their applications
        $pets = Pet::where('user_id', $user->id)
            ->with([
                'images' => function ($query) {
                    $query->limit(1);
                }
            ])
            ->withCount('adoptionApplications')
            ->having('adoption_applications_count', '>', 0)
            ->get();

        // Build response with grouped applications
        $result = $pets->map(function ($pet) {
            $applications = $pet->adoptionApplications()
                ->with('user:id,name')
                ->orderBy('created_at', 'desc')
                ->get();

            return [
                'pet' => [
                    'id' => $pet->id,
                    'name' => $pet->name,
                    'image' => $pet->images->first()?->path,
                ],
                'applications' => $applications->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'applicant_name' => $app->name,
                        'applicant_user' => $app->user ? [
                            'id' => $app->user->id,
                            'name' => $app->user->name,
                        ] : null,
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
                }),
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
                'message' => '申請狀態已更新',
                'data' => $application
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '更新失敗：' . $e->getMessage()
            ], 500);
        }
    }
}
