<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $verifiedEmail = $request->session()->get('otp_verified_email');

        if (! $verifiedEmail || $verifiedEmail !== $request->email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi verifikasi OTP kedaluwarsa atau tidak valid. Silakan ulangi proses.']);
        }

        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $verifiedEmail = $request->session()->get('otp_verified_email');

        if (! $verifiedEmail || $verifiedEmail !== $request->email) {
            throw ValidationException::withMessages([
                'email' => 'Sesi verifikasi OTP kedaluwarsa atau tidak valid. Silakan ulangi proses.',
            ]);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'User tidak ditemukan.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        // Clear the session
        $request->session()->forget('otp_verified_email');

        return redirect()->route('login')->with('success', 'Kata sandi Anda berhasil diubah. Silakan masuk.');
    }
}
