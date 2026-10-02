<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if ($user) {
            // Generate 6-digit OTP
            $otp = sprintf('%06d', random_int(0, 999999));

            $user->forceFill([
                'reset_otp' => $otp,
                'reset_otp_expires_at' => now()->addMinutes(15),
            ])->save();

            try {
                \Illuminate\Support\Facades\Mail::send('emails.reset-otp', [
                    'otp' => $otp,
                    'app_name' => config('app.name'),
                ], function ($message) use ($user, $otp) {
                    $message->to($user->email)
                        ->subject('Kode OTP Reset Password — ' . config('app.name'));
                });
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send reset OTP email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Put email in session to be verified (same response whether user exists or not)
        $request->session()->put('reset_password_email', $request->email);

        return redirect()->route('password.otp.show')
            ->with('status', 'Jika email terdaftar, kode OTP telah dikirim ke email Anda.');
    }

    /**
     * Display the OTP verification form.
     */
    public function showOtpForm(Request $request): View
    {
        $email = $request->session()->get('reset_password_email') ?? $request->email;

        if (! $email) {
            return view('auth.forgot-password')->withErrors(['email' => 'Silakan masukkan email terlebih dahulu.']);
        }

        return view('auth.verify-otp', ['email' => $email]);
    }

    /**
     * Verify the submitted OTP code.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user || ! $user->reset_otp || ! hash_equals($user->reset_otp, $request->otp) || now()->gt($user->reset_otp_expires_at)) {
            return back()->withInput($request->only('email', 'otp'))
                ->withErrors(['otp' => 'Kode OTP tidak valid atau telah kedaluwarsa. Silakan minta ulang atau hubungi Super Admin.']);
        }

        // OTP is correct! Clear it from database
        $user->forceFill([
            'reset_otp' => null,
            'reset_otp_expires_at' => null,
        ])->save();

        // Store email verified state in session for reset-password
        $request->session()->put('otp_verified_email', $request->email);

        return redirect()->route('password.reset', [
            'token' => 'otp',
            'email' => $request->email
        ]);
    }
}
