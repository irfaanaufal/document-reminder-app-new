<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Application;
use App\Models\UserApplication;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function checkKaryawan($fid): JsonResponse
    {
        $karyawan = Karyawan::where('fid', $fid)->first();

        if (!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'FID tidak ditemukan. Silakan hubungi admin.',
            ], 404);
        }

        $linked = User::where('fid', $fid)->exists();
        if ($linked) {
            return response()->json([
                'success' => false,
                'message' => 'FID ini sudah terdaftar. Silakan login.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'karyawan' => [
                'fid' => $karyawan->fid,
                'nama_karyawan' => $karyawan->nama_karyawan,
                'divisi' => $karyawan->divisi ?? 'Umum',
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'fid' => ['required', 'string', 'max:255', 'exists:karyawans,fid', 'unique:users,fid'],
            'nama' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'lowercase', 'max:255', 'unique:' . User::class, 'alpha_dash'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'no_telpon' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'fid' => $request->fid,
            'name' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'no_telpon' => $request->no_telpon,
            'password' => Hash::make($request->password),
            'role_id' => 10,
            // Email verification intentionally bypassed.
            // Access control is handled by UserApplication.is_active (admin approval).
            'email_verified_at' => now(),
        ]);

        $app = Application::find(config('app.application_id'))
            ?? Application::firstOrCreate(
                ['slug' => 'reminder'],
                ['name' => 'Reminder', 'description' => 'Sistem pengingat dokumen.']
            );

        if ($app) {
            UserApplication::updateOrCreate(
                ['user_id' => $user->id, 'application_id' => $app->id],
                ['is_active' => false]
            );
        }

        // Aturan seragam lintas aplikasi: notifikasi "Permintaan akses baru"
        // ke pemegang hak akses Kelola Permintaan (level 1,2,3,4,7).
        $user->sendAccessRequestNotifications();

        event(new Registered($user));

        return redirect(route('login'))->with('status', 'Registrasi berhasil. Silakan hubungi admin untuk aktivasi akun.');
    }
}
