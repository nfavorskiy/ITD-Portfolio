<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the user's profile.
     */
    public function index(Request $request): View
    {
        $this->authorize('view', $request->user());

        return view('profile.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Display the user's profile settings form.
     */
    public function settings(Request $request): View
    {
        $this->authorize('update', $request->user());

        return view('profile.settings', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $this->authorize('update', $request->user());

        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.settings')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->authorize('delete', $request->user());

        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        // Soft delete - mark as deleted instead of actually deleting
        $user->update([
            'is_deleted' => true,
            'email' => $user->email . '_deleted_' . time(),
            'name' => $user->name . '_deleted_' . time(),
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}