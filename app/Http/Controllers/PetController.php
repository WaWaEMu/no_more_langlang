<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\PetCreatorInterface;
use Illuminate\Support\Facades\Log;
use App\Models\Pet;
use Illuminate\Support\Facades\Auth;
use Throwable;

use App\Contracts\PetServiceInterface;

class PetController extends Controller
{
    protected PetCreatorInterface $petCreatorInterface;
    protected PetServiceInterface $petService;

    public function __construct(
        PetCreatorInterface $petCreatorInterface,
        PetServiceInterface $petService
    ) {
        $this->petCreatorInterface = $petCreatorInterface;
        $this->petService = $petService;
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'type',
            'city',
            'color',
            'fur_type',
            'gender',
            'age',
            'is_neuter',
            'keyword',
            'status'
        ]);

        $pets = $this->petService->getAvailablePets($filters, 12);
        return response()->json($pets);
    }

    /**
     * Get all pets owned by the authenticated user.
     */
    public function getUserPets(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $pets = $this->petService->getUserPets($user->id);
        return response()->json($pets);
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $data = $request->all();
        $blobs = $request->file('blobs', []);
        $data['user_id'] = $user->id;

        try {
            $this->petCreatorInterface->create($data, $blobs);

            return response()->json([
                'success' => true
            ]);
        } catch (Throwable $error) {
            \Log::error(__('Add pet failed') . '：' . $error->getMessage());

            return response()->json([
                'success' => false
            ]);
        }
    }

    public function show($id)
    {
        $userId = Auth::id();
        $pet = Pet::with(['images', 'detail', 'user'])
            ->withExists([
                'favoritedBy as is_favorite' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->find($id);
        return response()->json($pet);
    }

    public function toggleFavorite($id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $pet = Pet::findOrFail($id);

        // Toggle the favorite status
        $attached = $user->favorites()->toggle($id);

        // Determine if currently favorited based on toggle result
        $isFavorited = count($attached['attached']) > 0;

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorited
        ]);
    }

    public function destroy($id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $pet = Pet::findOrFail($id);

        // Check if user is the owner
        if ($pet->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        try {
            $pet->delete();
            return response()->json(['success' => true, 'message' => 'Pet deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to delete pet: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete pet'], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = Auth::user();
        $pet = Pet::findOrFail($id);

        // Check if user is the owner
        if ($pet->user_id !== $user->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:available,paused,pending,adopted'
        ]);

        try {
            $pet->status = $validated['status'];
            $pet->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'status' => $pet->status
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update status: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update status'], 500);
        }
    }
}
