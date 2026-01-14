<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

class SocialAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // 1. Check if the social account already exists
            $socialAccount = \App\Models\SocialAccount::where('provider_name', 'google')
                ->where('provider_id', $googleUser->getId())
                ->first();

            if ($socialAccount) {
                // If exists, login the associated user
                $user = $socialAccount->user;
                Auth::login($user);
            } else {
                // 2. If not, check if a user with the same email exists
                $user = User::where('email', $googleUser->getEmail())->first();

                if (!$user) {
                    // 3. If user doesn't exist, create a new user
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'password' => null, // Password is null for social login users
                        'email_verified_at' => now(),
                    ]);
                }

                // 4. Create the social account link
                $user->socialAccounts()->create([
                    'provider_name' => 'google',
                    'provider_id' => $googleUser->getId(),
                    'token' => $googleUser->token,
                ]);

                Auth::login($user);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return redirect(config('app.frontend_url') . '/auth/callback?token=' . $token);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Login Error: ' . $e->getMessage());
            return redirect(config('app.frontend_url') . '/auth/login?error=auth.google_failed');
        }
    }
}
