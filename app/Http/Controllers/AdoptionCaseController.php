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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'adopter_id' => 'required|exists:users,id',
            'application_id' => 'nullable|exists:adoption_applications,id',
            'tracking_config' => 'nullable|array',
        ]);

        try {
            $case = $this->adoptionCaseService->createCase($validated, Auth::user());

            return response()->json([
                'success' => true,
                'message' => 'Adoption finalized and case created.',
                'data' => $case
            ], 201);

        } catch (\Exception $e) {
            $statusCode = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
            return response()->json([
                'success' => false,
                'message' => 'Failed to finalize adoption: ' . $e->getMessage()
            ], $statusCode);
        }
    }
}
