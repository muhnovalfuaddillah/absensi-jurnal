<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            if (Auth::user()->isSuperAdmin()) {
                return redirect()->route('superadmin.dashboard');
            }
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('guru.dashboard');
        }

        $setting = \App\Models\AttendanceSetting::getGlobalSetting();
        $schools = \App\Models\School::where('is_active', true)->orderBy('name')->get();

        return view('auth.login', compact('setting', 'schools'));
    }

    /**
     * Proses autentikasi login (bisa pakai Email, Username, atau NIP Guru)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        $loginInput = trim($credentials['login']);
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        $user = null;

        // 1. Cek jika input email
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginInput)->first();
        }

        // 2. Jika belum ketemu, cek username
        if (!$user) {
            $user = User::where('username', $loginInput)->first();
        }

        // 3. Jika belum ketemu dan ada kemungkinan NIP guru
        if (!$user) {
            $teacher = Teacher::where('nip', $loginInput)->first();
            if ($teacher && $teacher->user) {
                $user = $teacher->user;
            }
        }

        // 4. Verifikasi user dan password
        if ($user && Hash::check($password, $user->password)) {
            // Cek apakah guru berstatus aktif
            if ($user->isTeacher() && $user->teacher && !$user->teacher->is_active) {
                return back()->with('error', 'Akun guru Anda saat ini dinonaktifkan. Silakan hubungi Administrator.')->withInput();
            }

            Auth::login($user, $remember);
            $request->session()->regenerate();

            if ($user->isSuperAdmin()) {
                return redirect()->route('superadmin.dashboard')->with('success', "Selamat datang di Panel Pusat, {$user->name}!");
            }

            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('success', "Selamat datang kembali, {$user->name} (Administrator)!");
            }

            return redirect()->route('guru.dashboard')->with('success', "Selamat datang, {$user->name}! Silakan lakukan absensi hari ini.");
        }

        return back()->with('error', 'Email / NIP / Username atau password tidak valid!')->withInput();
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem presensi.');
    }
}
