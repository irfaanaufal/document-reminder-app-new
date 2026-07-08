<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Application;
use App\Models\UserApplication;
use App\Models\LogNotifikasi;
use App\Models\Role;

echo "=== ROLES ===\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "  id={$role->id}, name={$role->name}\n";
}

echo "\n=== ALL USERS ===\n";
$allUsers = User::orderBy('role_id')->orderBy('name')->get();
foreach ($allUsers as $u) {
    echo "  id={$u->id}, name={$u->name}, username={$u->username}, role_id={$u->role_id}\n";
}

echo "\n=== NON-ADMIN USERS (role_id=3) ===\n";
$users = User::where('role_id', 3)->get();
if ($users->isEmpty()) {
    echo "  (tidak ada)\n";
} else {
    foreach ($users as $user) {
        echo "  id={$user->id}, name={$user->name}\n";
    }
}

echo "\n=== SIMULASI LOGIN ACTIVATION CHECK ===\n";
$nonAdmin = User::where('role_id', 3)->first();
if ($nonAdmin) {
    echo "User: {$nonAdmin->name} (id={$nonAdmin->id}) login...\n";

    // Simulasi AuthenticatedSessionController logic
    echo "1. Cek aplikasi Reminder...\n";
    $app = Application::where('slug', 'reminder')
        ->orWhere('name', 'Reminder')
        ->first();
    if (!$app) {
        echo "   -> Belum ada, akan dibuat firstOrCreate saat login\n";
    } else {
        echo "   -> Ditemukan: id={$app->id}\n";
    }

    if ($app) {
        echo "2. Cek UserApplication...\n";
        $userApp = UserApplication::where('user_id', $nonAdmin->id)
            ->where('application_id', $app->id)->first();
        if (!$userApp) {
            echo "   -> TIDAK ADA (user belum request akses)\n";
        } else {
            echo "   -> is_active=" . ($userApp->is_active ? 'true' : 'false') . "\n";
        }

        echo "3. Cek notifikasi login_activation_required sudah pernah?\n";
        $notifExists = LogNotifikasi::where('actor_user_id', $nonAdmin->id)
            ->where('action', 'login_activation_required')->exists();
        echo "   -> " . ($notifExists ? 'YA (skip kirim)' : 'BELUM (akan dikirim)') . "\n";

        if (!$notifExists) {
            $adminUsers = User::whereHas('role', function ($q) {
                $q->whereIn('name', ['superadmin', 'admin']);
            })->get();
            echo "4. Akan kirim notifikasi ke {$adminUsers->count()} admin:\n";
            foreach ($adminUsers as $admin) {
                echo "   => {$admin->name} (id={$admin->id})\n";
            }
            echo "\n   Data notifikasi:\n";
            echo "   actor_user_id = {$nonAdmin->id}\n";
            echo "   actor_name = {$nonAdmin->name}\n";
            echo "   recipient_type = admin\n";
            echo "   action = login_activation_required\n";
            echo "   title = Login ditolak — aktivasi diperlukan\n";
            echo "   message = {$nonAdmin->name} mencoba login tetapi belum diaktivasi untuk aplikasi Reminder.\n";
            echo "   visible_in_bell = true\n";
        }

        echo "\n5. Hasil: Login DITOLAK, redirect ke login dengan error activation_needed\n";
    }
} else {
    echo "Tidak ada user non-admin.\n";
    echo "Login activation check tidak akan terpicu (semua user admin/superadmin bypass).\n";
}

echo "\n--- DRY RUN - tidak ada data diubah ---\n";
