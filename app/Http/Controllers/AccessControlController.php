<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAccessChangeLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccessControlController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q')->toString());
        $roleFilter = $request->string('role')->toString();
        $statusFilter = $request->string('status')->toString();

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(in_array($roleFilter, ['1', '2', '3'], true), function ($query) use ($roleFilter) {
                $query->where('role', (int) $roleFilter);
            })
            ->when(in_array($statusFilter, ['1', '0'], true), function ($query) use ($statusFilter) {
                $query->where('is_active', $statusFilter === '1');
            })
            ->orderByRaw('role ASC')
            ->orderBy('nama')
            ->paginate(12)
            ->withQueryString();

        $recentLogs = UserAccessChangeLog::query()
            ->with(['actor', 'target'])
            ->latest()
            ->limit(20)
            ->get();

        $totalUsers = User::query()->count();
        $activeUsers = User::query()->where('is_active', true)->count();
        $inactiveUsers = User::query()->where('is_active', false)->count();
        $adminUsers = User::query()->whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_ADMIN])->count();

        return view('access-control.index', [
            'users' => $users,
            'roleOptions' => User::roleOptions(),
            'recentLogs' => $recentLogs,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'adminUsers' => $adminUsers,
            'filters' => [
                'q' => $search,
                'role' => $roleFilter,
                'status' => $statusFilter,
            ],
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'integer', 'in:1,2,3'],
            'is_active' => ['required', 'boolean'],
        ]);

        $actor = $request->user();

        if ((int) $user->id === (int) $actor->id) {
            if ((int) $validated['role'] !== User::ROLE_SUPER_ADMIN) {
                return back()->with('error', 'Super admin tidak bisa menurunkan hak akses akunnya sendiri.');
            }

            if (! (bool) $validated['is_active']) {
                return back()->with('error', 'Super admin tidak bisa menonaktifkan akunnya sendiri.');
            }
        }

        $oldRole = (int) $user->role;
        $oldIsActive = (bool) $user->is_active;
        $newRole = (int) $validated['role'];
        $newIsActive = (bool) $validated['is_active'];

        if ($oldRole === $newRole && $oldIsActive === $newIsActive) {
            return back()->with('success', 'Tidak ada perubahan hak akses.');
        }

        $user->update([
            'role' => $newRole,
            'is_active' => $newIsActive,
        ]);

        UserAccessChangeLog::create([
            'actor_user_id' => $actor->id,
            'target_user_id' => $user->id,
            'old_role' => $oldRole,
            'new_role' => $newRole,
            'old_is_active' => $oldIsActive,
            'new_is_active' => $newIsActive,
        ]);

        return back()->with('success', 'Hak akses user berhasil diperbarui.');
    }
}
