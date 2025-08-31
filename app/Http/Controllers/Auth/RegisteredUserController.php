<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Anhskohbo\NoCaptcha\Facades\NoCaptcha;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Rules\Password::defaults()],
            'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                if (!NoCaptcha::verifyResponse($value)) {
                    $fail(__('The reCAPTCHA verification failed. Please try again.'));
                }
            }],
        ]);

        // Get default role for new users
        $defaultRole = Role::where('name', 'user')->first();
        if (!$defaultRole) {
            $defaultRole = Role::whereNotIn('name', ['admin', 'superadmin'])->first();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $defaultRole ? $defaultRole->id : null,
        ]);

        try {
            event(new Registered($user));
        } catch (\Exception $e) {
            // Log the email error but don't stop registration
            \Log::error('Email verification sending failed during registration', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Optionally show a warning to the user
            session()->flash('warning', 'Account created successfully, but we couldn\'t send the verification email. Please contact support if needed.');
        }

        Auth::login($user);

        return redirect(route('home', absolute: false));
    }
}
