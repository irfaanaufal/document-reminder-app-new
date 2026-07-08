<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\LogNotifikasi;
use App\Models\User;
use App\Models\UserApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    public function index(): View
    {
        $applications = Application::all()->map(function ($app) {
            $userApp = UserApplication::where('user_id', Auth::id())
                ->where('application_id', $app->id)
                ->first();

            return [
                'id' => $app->id,
                'name' => $app->name,
                'slug' => $app->slug,
                'description' => $app->description,
                'is_active' => $userApp?->is_active ?? false,
                'has_requested' => $userApp !== null,
            ];
        });

        return view('applications.index', compact('applications'));
    }

    public function requestAccess(Request $request): RedirectResponse
    {
        $request->validate([
            'application_id' => ['required', 'exists:applications,id'],
        ]);

        $user = $request->user();
        $app = Application::findOrFail($request->application_id);

        UserApplication::updateOrCreate(
            [
                'user_id' => $user->id,
                'application_id' => $app->id,
            ],
            ['is_active' => false]
        );

        $adminUsers = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['superadmin', 'admin']);
        })->get();

        $adminUsers->each(function ($admin) use ($user, $app) {
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
        });

        return back()->with('success', 'Permintaan akses telah dikirim ke admin.');
    }
}
