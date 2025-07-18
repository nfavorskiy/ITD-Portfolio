<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

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
        try {
            // Store original email for potential redisplay
            $originalEmail = $request->email;

            // Convert email to lowercase before validation
            $request->merge([
                'email' => strtolower($request->email)
            ]);

            $request->validate([
                'name' => [
                    'required', 
                    'string', 
                    'max:255', 
                    'min:1',  // Allow single character names
                    'unique:'.User::class
                ],
                'email' => [
                    'required', 
                    'string', 
                    'lowercase', 
                    'email:rfc', 
                    'regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/', // Require domain with TLD
                    'max:255', 
                    'unique:'.User::class
                ],
                'password' => [
                    'required', 
                    'confirmed', 
                    'max:255',
                    'string',
                    function ($attribute, $value, $fail) {
                        $passwordRule = Rules\Password::defaults();
                        $validator = validator([$attribute => $value], [$attribute => $passwordRule]);
                        
                        if ($validator->fails()) {
                            $fail('Password must be at least 8 characters long, with numbers, special characters, uppercase and lowercase letters.');
                        }
                    }
                ],
            ], [
                // Custom error messages
                'name.unique' => 'This name is already taken. Please choose a different name.',
                'name.max' => 'Username is too long. Please choose a username with 255 characters or fewer.',
                'email.unique' => 'This email is already registered. Please use a different email address.',
                'name.required' => 'Please enter your username.',
                'email.required' => 'Please enter your email address.',
                'email.email' => 'Please enter a valid email address.',
                'email.regex' => 'Please enter a valid email address.',
                'password.confirmed' => 'Password confirmation does not match.',
                'password.max' => 'Password must not exceed 255 characters.',
            ]);
            
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                // Passwords are hashed before being stored
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            Auth::login($user);

            return redirect(route('verification.notice', absolute: false));
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors gracefully - use original email case for redisplay
            $inputData = $request->except('password', 'password_confirmation');
            $inputData['email'] = $originalEmail; // Restore original email case
            
            return back()
                ->withErrors($e->errors())
                ->withInput($inputData)
                ->with('registration_failed', true);
        }
    }
}