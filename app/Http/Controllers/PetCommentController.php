<?php

namespace App\Http\Controllers;

use App\Models\PetComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetCommentController extends Controller
{
    public function index($petId)
    {
        $comments = PetComment::with('user:id,name')
            ->where('pet_id', $petId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request, $petId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = PetComment::create([
            'pet_id' => $petId,
            'user_id' => Auth::id(),
            'content' => $request->input('content'),
        ]);

        return response()->json($comment->load('user:id,name'));
    }
}
