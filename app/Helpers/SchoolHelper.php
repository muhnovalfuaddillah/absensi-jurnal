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

            if ($user->teacher) {
                $sessionGuruSchoolId = session('guru_active_school_id');
                if ($sessionGuruSchoolId && School::where('id', $sessionGuruSchoolId)->exists()) {
                    if ($user->teacher->hasSchool($sessionGuruSchoolId)) {
                        return (int) $sessionGuruSchoolId;
                    }
                }

                if ($user->teacher->school_id) {
                    return (int) $user->teacher->school_id;
                }

                $firstAssigned = $user->teacher->assigned_schools->first();
                if ($firstAssigned) {
                    return (int) $firstAssigned->id;
                }
            }

            if ($user->school_id) {
                return (int) $user->school_id;
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

if (!function_exists('teacher_schools')) {
    /**
     * Dapatkan daftar sekolah yang ditugaskan kepada guru yang sedang login
     */
    function teacher_schools()
    {
        if (Auth::check() && Auth::user()->teacher) {
            return Auth::user()->teacher->assigned_schools;
        }
        return collect();
    }
}

if (!function_exists('is_multi_school_teacher')) {
    /**
     * Cek apakah guru yang login saat ini mengajar di lebih dari 1 sekolah
     */
    function is_multi_school_teacher(): bool
    {
        return teacher_schools()->count() > 1;
    }
}

