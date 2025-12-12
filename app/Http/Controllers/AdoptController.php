<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\PetCreatorInterface;
use Illuminate\Support\Facades\Log;
use App\Models\Pet;
use Illuminate\Support\Facades\Auth;

class AdoptController extends Controller
{
    protected PetCreatorInterface $petCreatorInterface;

    public function __construct(PetCreatorInterface $petCreatorInterface) {
        $this->petCreatorInterface = $petCreatorInterface;
    }

    public function index() {
        $userId = Auth::id();
        $pets = Pet::with(['images', 'detail', 'user'])
            ->withExists([
                'favoritedBy as is_favorite' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->get();
        return response()->json($pets);
    }

    public function store(Request $request) {
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
        } catch(Throwable $error) {
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
}
