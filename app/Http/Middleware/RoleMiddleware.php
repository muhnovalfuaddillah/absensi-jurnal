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

        if (!in_array($user->role, $roles)) {
            if ($user->role === 'guru') {
                return redirect()->route('guru.dashboard')->with('error', 'Akses ditolak! Anda hanya diizinkan mengakses Portal Guru.');
            } else {
                return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak! Halaman ini khusus untuk Portal Guru.');
            }
        }

        return $next($request);
    }
}
