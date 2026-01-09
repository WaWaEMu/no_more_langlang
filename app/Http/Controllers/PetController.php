<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\PetCreatorInterface;
use Illuminate\Support\Facades\Log;
use App\Models\Pet;
use Illuminate\Support\Facades\Auth;
use Throwable;

class PetController extends Controller
{
    protected PetCreatorInterface $petCreatorInterface;

    public function __construct(PetCreatorInterface $petCreatorInterface)
    {
        $this->petCreatorInterface = $petCreatorInterface;
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Pet::with(['images', 'detail', 'user'])
            ->withExists([
                'favoritedBy as is_favorite' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ]);

        // Filtering
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('city')) {
            $query->where('city', $request->city);
        }
        if ($request->has('color')) {
            $query->where('color', $request->color);
        }
        if ($request->has('fur_type')) {
            $query->where('fur_type', $request->fur_type);
        }
        if ($request->has('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->has('age')) {
            $query->where('age', $request->age);
        }
        if ($request->has('is_neuter')) {
            $query->where('is_neuter', $request->is_neuter === 'true' || $request->is_neuter === '1');
        }

        // Keyword search
        if ($request->has('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%")
                    ->orWhere('city', 'like', "%{$keyword}%")
                    ->orWhere('town', 'like', "%{$keyword}%")
                    ->orWhereHas('detail', function ($dq) use ($keyword) {
                        $dq->where('adoption_description', 'like', "%{$keyword}%")
                            ->orWhere('health_description', 'like', "%{$keyword}%")
                            ->orWhere('adoption_condition', 'like', "%{$keyword}%");
                    });
            });
        }

        $pets = $query->latest()->paginate(12);
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
            \Log::error('新增寵物失敗：' . $error->getMessage());

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
}
