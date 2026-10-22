<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();

        // Superadmin memiliki wewenang penuh pada portal admin dan superadmin
        if ($user->isSuperAdmin()) {
            if (in_array('guru', $roles) && !in_array('admin', $roles) && !in_array('superadmin', $roles)) {
                return redirect()->route('admin.dashboard')->with('info', 'Superadmin diarahkan ke Dashboard Manajemen.');
            }
            return $next($request);
        }

        if (!in_array($user->role, $roles)) {
            if ($user->role === 'guru') {
                return redirect()->route('guru.dashboard')->with('error', 'Akses ditolak! Anda hanya diizinkan mengakses Portal Guru.');
            } else {
                return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak!');
            }
        }

        return $next($request);
    }
}
