<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Application;
use App\Models\UserApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = $request->user();

        $app = Application::find(config('app.application_id'))
            ?? Application::firstOrCreate(
                ['slug' => 'reminder'],
                ['name' => 'Reminder', 'description' => 'Sistem pengingat dokumen.']
            );

        $userApp = UserApplication::where('user_id', $user->id)
            ->where('application_id', $app->id)
            ->first();

        if (!$userApp) {
            // Baris belum ada (akun lama) → buat inactive + notifikasi admin.
            // Aturan seragam: TANPA auto-aktif — semua akun (termasuk admin)
            // harus diaktifkan via Kelola Permintaan di it-system.
            $userApp = UserApplication::create([
                'user_id' => $user->id,
                'application_id' => $app->id,
                'is_active' => false,
            ]);
            $user->sendAccessRequestNotifications();
        }

        if (!$userApp->is_active) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'activation_needed' => 'Akun belum diaktifkan. Hubungi tim IT',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
