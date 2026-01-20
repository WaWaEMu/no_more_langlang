<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Pet;
use App\Services\AdoptionApplicationService;

class AdoptionController extends Controller
{
    protected AdoptionApplicationService $adoptionApplicationService;

    public function __construct(
        AdoptionApplicationService $adoptionApplicationService
    ) {
        $this->adoptionApplicationService = $adoptionApplicationService;
    }

    /**
     * Store a new adoption application.
     */
    public function store(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Basic validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'line_id' => 'nullable|string|max:50',
            'housing_type' => 'required|string',
            'experience' => 'required|string',
            'family_agreement' => 'required|boolean',
            'message' => 'required|string|max:1000',
        ]);

        try {
            // Use service to create adoption application
            $application = $this->adoptionApplicationService->create(
                $validated,
                Auth::id(),
                $id
            );

            Log::info("Adoption application created with ID: {$application->id} for pet ID: {$id} from user ID: " . Auth::id());

            return response()->json([
                'success' => true,
                'message' => __('Application submitted successfully'),
                'data' => $application
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to submit adoption application: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Application submission failed')
            ], 500);
        }
    }
}

