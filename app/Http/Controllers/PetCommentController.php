<?php

namespace App\Http\Controllers;

use App\Models\PetComment;
use App\Contracts\PetCommentServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetCommentController extends Controller
{
    protected PetCommentServiceInterface $petCommentService;

    public function __construct(PetCommentServiceInterface $petCommentService)
    {
        $this->petCommentService = $petCommentService;
    }

    public function index($petId)
    {
        $comments = $this->petCommentService->getCommentsByPetId($petId);
        return response()->json($comments);
    }

    public function store(Request $request, $petId)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:pet_comments,id',
        ]);

        try {
            $comment = $this->petCommentService->createComment(
                $request->only(['content', 'parent_id']),
                Auth::id(),
                $petId
            );
            return response()->json($comment);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
