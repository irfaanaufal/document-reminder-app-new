<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Application;
use App\Models\LogNotifikasi;
use App\Models\User;
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
            ?? Application::where('slug', 'reminder')
                ->orWhere('name', 'Reminder')
                ->firstOr(function () {
                    return Application::create([
                        'name' => 'Reminder',
                        'slug' => 'reminder',
                        'description' => 'Sistem pengingat dokumen.',
                    ]);
                });

        $userApp = UserApplication::where('user_id', $user->id)
            ->where('application_id', $app->id)
            ->first();

        if (!$userApp) {
            $userApp = UserApplication::create([
                'user_id' => $user->id,
                'application_id' => $app->id,
                'is_active' => false,
            ]);
        }

        if (!$userApp->is_active) {
            try {
                $adminUsers = User::where('role_id', User::ROLE_SUPER_ADMIN)->get();
                foreach ($adminUsers as $admin) {
                    $notifExists = LogNotifikasi::where('user_id', $admin->id)
                        ->where('actor_user_id', $user->id)
                        ->where('action', 'new_access_request')
                        ->exists();

                    if (!$notifExists) {
                        LogNotifikasi::create([
                            'user_id' => $admin->id,
                            'ticket_id' => null,
                            'actor_user_id' => $user->id,
                            'actor_name' => $user->name,
                            'recipient_type' => 'admin',
                            'action' => 'new_access_request',
                            'title' => 'Permintaan akses baru',
                            'message' => $user->name . ' mengajukan akses ke "' . $app->name . '".',
                            'status' => null,
                            'visible_in_bell' => true,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Gagal membuat notifikasi aktivasi reminder: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                    'error' => $e->getTraceAsString(),
                ]);
            }

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'activation_needed' => 'Akun Anda belum diaktifkan untuk aplikasi ini. Silakan hubungi tim IT.',
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
