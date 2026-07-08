<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckITWorkflowAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        \Illuminate\Support\Facades\Log::info('MIDDLEWARE_HIT', [
            'user' => $user ? $user->id : null,
            'is_admin' => $user ? $user->isAdmin() : false
        ]);
        if (!$user) return $next($request);

        $appId = config('app.application_id');
        $hasAccess = $user->userApplications()
            ->where(function ($query) use ($appId) {
                if ($appId) {
                    $query->where('application_id', $appId);
                } else {
                    $query->whereHas('application', fn($q) => $q->where('slug', 'reminder'));
                }
            })
            ->where('is_active', true)
            ->exists();

        if (!$hasAccess) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'activation_needed' => 'Akun Anda belum memiliki akses ke aplikasi ini. Silakan hubungi tim IT.',
            ]);
        }

        return $next($request);
    }
}
