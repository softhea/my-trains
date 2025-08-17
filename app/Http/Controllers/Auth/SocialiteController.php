<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Redirect to Apple OAuth
     */
    public function redirectToApple()
    {
        return Socialite::driver('apple')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists with this Google ID
            $user = User::where('google_id', $googleUser->id)->first();
            
            if ($user) {
                // User exists with Google ID, just login
                Auth::login($user);
                return redirect()->route('home')->with('success', __('Welcome back, :name!', ['name' => $user->name]));
            }
            
            // Check if user exists with same email
            $existingUser = User::where('email', $googleUser->email)->first();
            
            if ($existingUser) {
                // Link Google account to existing user
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'avatar' => $googleUser->avatar,
                ]);
                
                Auth::login($existingUser);
                return redirect()->route('home')->with('success', __('Google account linked successfully! Welcome back, :name!', ['name' => $existingUser->name]));
            }
            
            // Create new user
            $newUser = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
                'password' => null, // No password for OAuth users
                'role_id' => $this->getDefaultRoleId(),
                'email_verified_at' => now(), // Google accounts are pre-verified
            ]);
            
            Auth::login($newUser);
            
            return redirect()->route('home')->with('success', __('Account created successfully! Welcome, :name!', ['name' => $newUser->name]));
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', __('There was an error signing in with Google. Please try again.'));
        }
    }

    /**
     * Handle Apple OAuth callback
     */
    public function handleAppleCallback()
    {
        try {
            $appleUser = Socialite::driver('apple')->user();
            
            // Check if user already exists with this Apple ID
            $user = User::where('apple_id', $appleUser->id)->first();
            
            if ($user) {
                // User exists with Apple ID, just login
                Auth::login($user);
                return redirect()->route('home')->with('success', __('Welcome back, :name!', ['name' => $user->name]));
            }
            
            // Check if user exists with same email
            $existingUser = User::where('email', $appleUser->email)->first();
            
            if ($existingUser) {
                // Link Apple account to existing user
                $existingUser->update([
                    'apple_id' => $appleUser->id,
                    // Note: Apple doesn't always provide avatar
                    'avatar' => $existingUser->avatar ?: ($appleUser->avatar ?? null),
                ]);
                
                Auth::login($existingUser);
                return redirect()->route('home')->with('success', __('Apple account linked successfully! Welcome back, :name!', ['name' => $existingUser->name]));
            }
            
            // Create new user
            $newUser = User::create([
                'name' => $appleUser->name ?: $appleUser->nickname ?: 'Apple User',
                'email' => $appleUser->email,
                'apple_id' => $appleUser->id,
                'avatar' => $appleUser->avatar,
                'password' => null, // No password for OAuth users
                'role_id' => $this->getDefaultRoleId(),
                'email_verified_at' => now(), // Apple accounts are pre-verified
            ]);
            
            Auth::login($newUser);
            
            return redirect()->route('home')->with('success', __('Account created successfully! Welcome, :name!', ['name' => $newUser->name]));
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', __('There was an error signing in with Apple. Please try again.'));
        }
    }

    /**
     * Get default role ID for new users
     */
    private function getDefaultRoleId()
    {
        // Get the default user role (not admin)
        $defaultRole = Role::where('name', 'user')->first();
        
        if (!$defaultRole) {
            // Fallback: get any role that's not admin/superadmin
            $defaultRole = Role::whereNotIn('name', ['admin', 'superadmin'])->first();
        }
        
        return $defaultRole ? $defaultRole->id : null;
    }
}