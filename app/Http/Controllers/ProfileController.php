<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();
        $userApp = \App\Models\UserApplication::where('user_id', $user->id)
            ->where('application_id', $this->resolveApplicationId())
            ->first();

        $today = now()->startOfDay()->toDateString();

        return view('profile.edit', [
            'user' => $user,
            'userApp' => $userApp,
            'isAppActive' => (bool) $userApp?->is_active,
            'stats' => [
                'total' => \App\Models\DocumentReminder::count(),
                'segera_habis' => \App\Models\DocumentReminder::whereNotNull('tanggal_expired')
                    ->whereBetween('tanggal_expired', [$today, now()->startOfDay()->addDays(30)->toDateString()])
                    ->count(),
                'kadaluarsa' => \App\Models\DocumentReminder::whereNotNull('tanggal_expired')
                    ->where('tanggal_expired', '<', $today)
                    ->count(),
                'tanpa_expiry' => \App\Models\DocumentReminder::whereNull('tanggal_expired')->count(),
            ],
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

    private function resolveApplicationId(): int
    {
        $configured = (int) config('app.application_id');
        if ($configured > 0) {
            return $configured;
        }

        $app = \App\Models\Application::find($configured)
            ?? \App\Models\Application::firstOrCreate(
                ['slug' => 'reminder'],
                ['name' => 'Reminder', 'description' => 'Sistem pengingat dokumen.']
            );

        return (int) $app->id;
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
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $request->file('avatar')->store('profile-photos', 'public');

        $user->avatar_path = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'avatar_url' => Storage::disk('public')->url($path),
        ]);
    }
}
