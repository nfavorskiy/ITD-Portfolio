<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();

            // Check if the authenticated user is deleted
            if ($request->user() && $request->user()->is_deleted) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account has been deleted.',
                ])->withInput($request->only('email'));
            }
            $request->session()->regenerate();
            
            // Set login success message
            $request->session()->flash('login_success', true);

            // Check if they specifically clicked "Sign in to view Posts"
            if ($request->has('redirect_to_posts')) {
                return redirect()->intended(route('posts.index'));
            }

            return redirect()->intended(route('profile.index'));
            
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('email'));
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Login error: ' . $e->getMessage());
            
            return back()->withErrors([
                'email' => 'An error occurred during login. Please try again.',
            ])->withInput($request->only('email'));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}