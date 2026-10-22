<?php

use App\Models\School;
use Illuminate\Support\Facades\Auth;

if (!function_exists('current_school_id')) {
    /**
     * Dapatkan ID sekolah yang sedang aktif/dipilih
     */
    function current_school_id(): ?int
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->isSuperAdmin()) {
                $sessionSchoolId = session('active_school_id');
                if ($sessionSchoolId && School::where('id', $sessionSchoolId)->exists()) {
                    return (int) $sessionSchoolId;
                }
                $firstSchool = School::where('is_active', true)->first() ?: School::first();
                return $firstSchool ? $firstSchool->id : 1;
            }

            if ($user->school_id) {
                return (int) $user->school_id;
            }

            if ($user->teacher && $user->teacher->school_id) {
                return (int) $user->teacher->school_id;
            }
        }

        $defaultSchool = School::where('is_active', true)->first() ?: School::first();
        return $defaultSchool ? $defaultSchool->id : 1;
    }
}

if (!function_exists('current_school')) {
    /**
     * Dapatkan instance Model School yang sedang aktif
     */
    function current_school(): ?School
    {
        $id = current_school_id();
        if (!$id) {
            return School::where('is_active', true)->first() ?: School::first();
        }

        return School::find($id) ?: (School::where('is_active', true)->first() ?: School::first());
    }
}

if (!function_exists('all_schools')) {
    /**
     * Dapatkan daftar seluruh sekolah (untuk dropdown switcher Superadmin)
     */
    function all_schools($onlyActive = false)
    {
        $query = School::orderBy('name');
        if ($onlyActive) {
            $query->where('is_active', true);
        }
        return $query->get();
    }
}

if (!function_exists('is_superadmin')) {
    /**
     * Cek apakah user yang login saat ini adalah Superadmin
     */
    function is_superadmin(): bool
    {
        return Auth::check() && Auth::user()->isSuperAdmin();
    }
}
