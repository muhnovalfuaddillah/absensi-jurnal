<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $employmentStatus = $request->input('employment_status');
        $foundationStatus = $request->input('foundation_status');
        $schoolId = current_school_id();

        $currentSchool = current_school();
        $defaultFoundationName = $currentSchool->foundation_name ?? \App\Models\AttendanceSetting::getGlobalSetting()->foundation_name ?? 'Yayasan Bahrul Ulum';

        $query = Teacher::where(function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId)
              ->orWhereHas('schools', fn($s) => $s->where('schools.id', $schoolId));
        })->with(['user', 'schools'])->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('class_duty', 'like', "%{$search}%")
                  ->orWhere('foundation_name', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', (bool) $status);
        }

        if ($employmentStatus) {
            $query->where('employment_status', $employmentStatus);
        }

        if ($foundationStatus !== null && $foundationStatus !== '') {
            $query->where('teaches_in_foundation', (bool) $foundationStatus);
        }

        $teachers = $query->paginate(15)->withQueryString();
        
        $totalTeachers = Teacher::where(function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId)
              ->orWhereHas('schools', fn($s) => $s->where('schools.id', $schoolId));
        })->count();

        $activeTeachers = Teacher::where(function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId)
              ->orWhereHas('schools', fn($s) => $s->where('schools.id', $schoolId));
        })->where('is_active', true)->count();

        $tetapCount = Teacher::where(function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId)
              ->orWhereHas('schools', fn($s) => $s->where('schools.id', $schoolId));
        })->where('employment_status', 'like', '%Tetap%')->count();

        $honorerCount = Teacher::where(function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId)
              ->orWhereHas('schools', fn($s) => $s->where('schools.id', $schoolId));
        })->where(function($q) {
            $q->where('employment_status', 'like', '%Honorer%')
              ->orWhere('employment_status', 'like', '%GTT%');
        })->count();

        $allSchools = \App\Models\School::where('is_active', true)->orderBy('name')->get();

        // Guru dari sekolah lain yang belum terhubung ke sekolah ini (untuk fitur cepat tautkan guru)
        $unlinkedTeachers = Teacher::where('school_id', '!=', $schoolId)
            ->whereDoesntHave('schools', fn($s) => $s->where('schools.id', $schoolId))
            ->where('is_active', true)
            ->with('school')
            ->orderBy('name')
            ->get();

        return view('teachers.index', compact(
            'teachers',
            'search',
            'status',
            'employmentStatus',
            'foundationStatus',
            'defaultFoundationName',
            'totalTeachers',
            'activeTeachers',
            'tetapCount',
            'honorerCount',
            'allSchools',
            'unlinkedTeachers'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'nullable|string|max:50|unique:teachers,nip',
            'nuptk' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'employment_status' => 'required|string|max:50',
            'teaches_in_foundation' => 'nullable|boolean',
            'foundation_name' => 'nullable|string|max:150',
            'education' => 'nullable|string|max:50',
            'class_duty' => 'nullable|string|max:100',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'email' => 'nullable|email|max:255|unique:users,email',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            // Akun Login
            'username' => 'nullable|string|max:100|unique:users,username',
            'password' => 'nullable|string|min:6',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('teachers', 'public');
        }

        $schoolId = current_school_id();
        $currentSchool = current_school();
        $defaultFoundationName = $currentSchool->foundation_name ?? \App\Models\AttendanceSetting::getGlobalSetting()->foundation_name ?? 'Yayasan Bahrul Ulum';
        $teachesInFoundation = $request->boolean('teaches_in_foundation', true);
        $foundationName = $teachesInFoundation ? ($request->input('foundation_name') ?: $defaultFoundationName) : null;

        $teacher = Teacher::create([
            'school_id' => $schoolId,
            'nip' => $request->nip,
            'nuptk' => $request->nuptk,
            'name' => $request->name,
            'subject' => $request->subject,
            'employment_status' => $request->employment_status,
            'teaches_in_foundation' => $teachesInFoundation,
            'foundation_name' => $foundationName,
            'education' => $request->education,
            'class_duty' => $request->class_duty,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'email' => $request->email,
            'photo' => $photoPath,
            'is_active' => true,
        ]);

        // Hubungkan ke sekolah-sekolah penugasan (Multi-Sekolah)
        $schoolIds = $request->input('school_ids', []);
        if (!in_array($schoolId, $schoolIds)) {
            $schoolIds[] = $schoolId;
        }
        $teacher->schools()->sync($schoolIds);

        // Buat Akun Login Guru
        $cleanNameSlug = Str::slug(preg_replace('/^(Drs\.|Dra\.|H\.|Hj\.|Ust\.)\s*/i', '', $teacher->name), '.');
        $email = $request->filled('email') ? $request->email : ($cleanNameSlug . '@sekolah.sch.id');
        $username = $request->filled('username') ? $request->username : ($request->nip ?: Str::slug($teacher->name, ''));
        $password = $request->filled('password') ? $request->password : 'guru123';

        User::create([
            'school_id' => $schoolId,
            'name' => $teacher->name,
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            'role' => 'guru',
            'teacher_id' => $teacher->id,
        ]);

        return back()->with('success', "Data profil guru '{$teacher->name}' dan akun login berhasil dibuat! (Username: {$username} | Password: {$password})");
    }

    public function update(Request $request, Teacher $teacher)
    {
        $schoolId = current_school_id();
        $currentSchool = current_school();
        $defaultFoundationName = $currentSchool->foundation_name ?? \App\Models\AttendanceSetting::getGlobalSetting()->foundation_name ?? 'Yayasan Bahrul Ulum';

        $request->validate([
            'nip' => 'nullable|string|max:50|unique:teachers,nip,' . $teacher->id,
            'nuptk' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'employment_status' => 'required|string|max:50',
            'teaches_in_foundation' => 'nullable|boolean',
            'foundation_name' => 'nullable|string|max:150',
            'education' => 'nullable|string|max:50',
            'class_duty' => 'nullable|string|max:100',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'is_active' => 'required|boolean',
            'school_ids' => 'nullable|array',
            // Akun Login Update
            'username' => 'nullable|string|max:100|unique:users,username,' . ($teacher->user->id ?? 0),
            'password' => 'nullable|string|min:6',
        ]);

        $photoPath = $teacher->photo;
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }
            $photoPath = $request->file('photo')->store('teachers', 'public');
        }

        $teachesInFoundation = $request->boolean('teaches_in_foundation', false);
        $foundationName = $teachesInFoundation ? ($request->input('foundation_name') ?: $defaultFoundationName) : null;

        $teacher->update([
            'nip' => $request->nip,
            'nuptk' => $request->nuptk,
            'name' => $request->name,
            'subject' => $request->subject,
            'employment_status' => $request->employment_status,
            'teaches_in_foundation' => $teachesInFoundation,
            'foundation_name' => $foundationName,
            'education' => $request->education,
            'class_duty' => $request->class_duty,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'address' => $request->address,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'email' => $request->email,
            'photo' => $photoPath,
            'is_active' => $request->is_active,
        ]);

        // Sinkronisasi Multi-Sekolah
        if ($request->has('school_ids')) {
            $schoolIds = $request->input('school_ids', []);
            if (!in_array($schoolId, $schoolIds) && !is_superadmin()) {
                $schoolIds[] = $schoolId;
            }
            $teacher->schools()->sync($schoolIds);
        }

        // Sinkronisasi data ke tabel users jika ada
        if ($teacher->user) {
            $userUpdates = [
                'name' => $teacher->name,
                'email' => $request->email ?: $teacher->user->email,
            ];

            if ($request->filled('username')) {
                $userUpdates['username'] = $request->username;
            }

            if ($request->filled('password')) {
                $userUpdates['password'] = Hash::make($request->password);
            }

            $teacher->user->update($userUpdates);
        } else {
            // Jika guru belum memiliki akun user, buatkan otomatis
            $cleanNameSlug = Str::slug(preg_replace('/^(Drs\.|Dra\.|H\.|Hj\.|Ust\.)\s*/i', '', $teacher->name), '.');
            $email = $request->filled('email') ? $request->email : ($cleanNameSlug . '@sekolah.sch.id');
            $username = $request->filled('username') ? $request->username : ($request->nip ?: Str::slug($teacher->name, ''));
            $password = $request->filled('password') ? $request->password : 'guru123';

            User::create([
                'school_id' => $schoolId,
                'name' => $teacher->name,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($password),
                'role' => 'guru',
                'teacher_id' => $teacher->id,
            ]);
        }

        return back()->with('success', "Data profil guru '{$teacher->name}' berhasil diperbarui!");
    }

    /**
     * Tautkan Guru yang Sudah Terdaftar di Sekolah Lain ke Sekolah Ini
     */
    public function linkExisting(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $schoolId = current_school_id();
        $teacher = Teacher::findOrFail($request->teacher_id);

        $teacher->schools()->syncWithoutDetaching([$schoolId => [
            'is_primary' => false,
            'subject' => $teacher->subject,
            'class_duty' => $teacher->class_duty,
            'created_at' => now(),
            'updated_at' => now(),
        ]]);

        $school = \App\Models\School::find($schoolId);

        return back()->with('success', "Guru '{$teacher->name}' berhasil ditautkan ke {$school->name}! Guru ini kini dapat mengajar dan presensi di kedua sekolah.");
    }

    public function destroy(Teacher $teacher)
    {
        $schoolId = current_school_id();
        $name = $teacher->name;

        // Jika guru terdaftar di lebih dari 1 sekolah dan yang menghapus adalah admin sekolah (bukan superadmin)
        if ($teacher->schools()->count() > 1 && !is_superadmin()) {
            $teacher->schools()->detach($schoolId);
            return back()->with('success', "Penugasan guru '{$name}' di sekolah ini telah dilepas. (Profil dan penugasan di sekolah lainnya tetap aktif).");
        }

        // Hapus foto jika ada
        if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
            Storage::disk('public')->delete($teacher->photo);
        }

        // Hapus relasi pivot sekolah
        $teacher->schools()->detach();

        // Hapus akun user
        if ($teacher->user) {
            $teacher->user->delete();
        }

        $teacher->delete();
        return back()->with('success', "Data Guru '{$name}' dan akun loginnya telah berhasil dihapus!");
    }

    public function toggleStatus(Teacher $teacher)
    {
        $teacher->is_active = !$teacher->is_active;
        $teacher->save();

        $statusStr = $teacher->is_active ? 'diaktifkan kembali' : 'dinonaktifkan (cuti/keluar)';
        return back()->with('success', "Status Guru '{$teacher->name}' berhasil {$statusStr}!");
    }

    public function resetPassword(Request $request, Teacher $teacher)
    {
        $newPassword = $request->input('new_password', 'guru123');

        if (!$teacher->user) {
            $cleanNameSlug = Str::slug(preg_replace('/^(Drs\.|Dra\.|H\.|Hj\.|Ust\.)\s*/i', '', $teacher->name), '.');
            $email = $teacher->email ?: ($cleanNameSlug . '@sekolah.sch.id');
            $username = $teacher->nip ?: Str::slug($teacher->name, '');
            User::create([
                'name' => $teacher->name,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make($newPassword),
                'role' => 'guru',
                'teacher_id' => $teacher->id,
            ]);
        } else {
            $teacher->user->update([
                'password' => Hash::make($newPassword),
            ]);
        }

        return back()->with('success', "Password akun login untuk '{$teacher->name}' telah di-reset ke: {$newPassword}");
    }

    public function resetFace(Teacher $teacher)
    {
        $teacher->face_registered_at = null;
        $teacher->face_data = null;
        $teacher->save();

        return back()->with('success', "Data Face ID / Wajah terdaftar untuk '{$teacher->name}' berhasil di-reset. Guru dapat mendaftarkan wajah ulang.");
    }
}
