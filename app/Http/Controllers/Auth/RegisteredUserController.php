<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Models\User;
use App\Models\Application;
use App\Models\UserApplication;
use App\Models\LogNotifikasi;
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
            'fid' => ['required', 'string', 'max:255'],
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
            'role_id' => User::ROLE_USER,
            'email_verified_at' => now(),
        ]);

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

        if ($app) {
            UserApplication::updateOrCreate(
                ['user_id' => $user->id, 'application_id' => $app->id],
                ['is_active' => false]
            );

            try {
                $adminUsers = User::where('role_id', User::ROLE_SUPER_ADMIN)->get();
                foreach ($adminUsers as $admin) {
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
            } catch (\Exception $e) {
                \Log::error('Gagal membuat notifikasi registrasi reminder: ' . $e->getMessage());
            }
        }

        event(new Registered($user));

        return redirect(route('login'))->with('status', 'Registrasi berhasil. Silakan hubungi admin untuk aktivasi akun.');
    }
}
