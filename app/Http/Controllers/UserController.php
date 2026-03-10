<?php

namespace App\Http\Controllers;

use App\Contracts\UserServiceInterface;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserLookupRequest;

class UserController extends Controller
{
    protected UserServiceInterface $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = $this->userService->updateProfile($request->user(), $request->only('name'));

        return response()->json($user);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $this->userService->updatePassword($request->user(), $request->password);

        return response()->json(['message' => 'Password updated successfully']);
    }

    /**
     * Lookup user by exact email (Privacy-safe)
     */
    public function lookupByEmail(UserLookupRequest $request)
    {
        $currentUserId = $request->user('sanctum')?->id;

        $user = $this->userService->lookupByEmail(
            $request->input('email'),
            $currentUserId
        );

        if (!$user) {
            return response()->json([
                'found' => false,
                'message' => '找不到此 Email 對應的使用者',
            ]);
        }

        return response()->json([
            'found' => true,
            'user' => $user,
        ]);
    }
}