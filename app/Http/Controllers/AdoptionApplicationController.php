<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdoptionApplicationController extends Controller
{
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
                        'status' => $app->status,
                        'created_at' => $app->created_at,
                    ];
                }),
            ];
        });

        return response()->json(['data' => $result]);
    }
}
