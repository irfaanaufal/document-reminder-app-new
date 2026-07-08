<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['name'] = $data['nama'];
        unset($data['nama']);

        $request->user()->fill($data);

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Upload / update avatar photo.
     */
    public function updateAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:3000'],
        ]);

        $user = $request->user();

        // Delete old avatar if exists
        if ($user->avatar_path) {
            $oldFile = public_path($user->avatar_path);
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        // Save to public/profile-photos/
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $request->file('avatar')->getClientOriginalExtension();
        $request->file('avatar')->move(public_path('profile-photos'), $filename);
        $path = 'profile-photos/' . $filename;

        $user->avatar_path = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'avatar_url' => asset($path),
        ]);
    }
}
