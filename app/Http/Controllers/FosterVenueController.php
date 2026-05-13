<?php

namespace App\Http\Controllers;

use App\Contracts\FosterVenueServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FosterVenueController extends Controller
{
    public function __construct(
        private readonly FosterVenueServiceInterface $venueService
    ) {}

    /**
     * Display a listing of the foster venues.
     */
    public function index(Request $request): JsonResponse
    {
        $venues = $this->venueService->getAllVenues($request->all());

        return response()->json([
            'success' => true,
            'data' => $venues
        ]);
    }

    /**
     * Display the specified foster venue.
     */
    public function show(string $uuid): JsonResponse
    {
        $venue = $this->venueService->getVenueByUuid($uuid);

        return response()->json([
            'success' => true,
            'data' => $venue
        ]);
    }
}
