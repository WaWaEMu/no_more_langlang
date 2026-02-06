<?php

namespace App\Http\Controllers;

use App\Models\AdoptionApplication;
use App\Models\AdoptionCase;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AdoptionCaseServiceInterface;

class AdoptionCaseController extends Controller
{
    protected $adoptionCaseService;

    public function __construct(AdoptionCaseServiceInterface $adoptionCaseService)
    {
        $this->adoptionCaseService = $adoptionCaseService;
    }

    /**
     * Get list of adoption cases for the user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $role = $request->query('role', 'owner');

        $cases = $this->adoptionCaseService->getUserCases($user, $role);

        return response()->json([
            'success' => true,
            'data' => $cases
        ]);
    }

    /**
     * Store a new adoption case.
     * This is triggered when an owner finalizes an adoption.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'adopter_id' => 'required|exists:users,id',
            'application_id' => 'nullable|exists:adoption_applications,id',
            'tracking_config' => 'nullable|array',
        ]);

        $pet = Pet::findOrFail($validated['pet_id']);

        // Ensure the current user is the owner of the pet
        if ($pet->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $case = DB::transaction(function () use ($validated, $pet) {
                // 1. Create the Adoption Case
                $case = AdoptionCase::create([
                    'pet_id' => $validated['pet_id'],
                    'adopter_id' => $validated['adopter_id'],
                    'owner_id' => Auth::id(),
                    'application_id' => $validated['application_id'] ?? null,
                    'status' => 'active',
                    'tracking_config' => $validated['tracking_config'],
                    'started_at' => now(),
                    'next_report_due_at' => $this->calculateNextReportDate($validated['tracking_config']),
                ]);

                // 2. Update Pet Status to Adopted
                $pet->update(['status' => Pet::STATUS_ADOPTED]);

                // 3. Update Adoption Application Status to Approved (if provided)
                if (isset($validated['application_id'])) {
                    AdoptionApplication::where('id', $validated['application_id'])
                        ->update(['status' => 'approved']);
                }

                return $case;
            });

            return response()->json([
                'success' => true,
                'message' => 'Adoption finalized and case created.',
                'data' => $case
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to finalize adoption: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate the next report due date based on tracking config.
     */
    private function calculateNextReportDate(?array $config)
    {
        if (!$config || !isset($config['frequency'])) {
            return null;
        }

        return match ($config['frequency']) {
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            default => null,
        };
    }
}
