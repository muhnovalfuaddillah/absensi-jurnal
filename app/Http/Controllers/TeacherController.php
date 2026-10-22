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
        $schoolId = current_school_id();

        $query = Teacher::where('school_id', $schoolId)->with('user')->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nuptk', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('class_duty', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', (bool) $status);
        }

        if ($employmentStatus) {
            $query->where('employment_status', $employmentStatus);
        }

        $teachers = $query->paginate(15)->withQueryString();
        
        $totalTeachers = Teacher::where('school_id', $schoolId)->count();
        $activeTeachers = Teacher::where('school_id', $schoolId)->where('is_active', true)->count();
        $tetapCount = Teacher::where('school_id', $schoolId)->where('employment_status', 'like', '%Tetap%')->count();
        $honorerCount = Teacher::where('school_id', $schoolId)->where(function($q) {
            $q->where('employment_status', 'like', '%Honorer%')
              ->orWhere('employment_status', 'like', '%GTT%');
        })->count();

        return view('teachers.index', compact(
            'teachers',
            'search',
            'status',
            'employmentStatus',
            'totalTeachers',
            'activeTeachers',
            'tetapCount',
            'honorerCount'
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

        $teacher = Teacher::create([
            'school_id' => $schoolId,
            'nip' => $request->nip,
            'nuptk' => $request->nuptk,
            'name' => $request->name,
            'subject' => $request->subject,
            'employment_status' => $request->employment_status,
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
        $request->validate([
            'nip' => 'nullable|string|max:50|unique:teachers,nip,' . $teacher->id,
            'nuptk' => 'nullable|string|max:50',
            'name' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'employment_status' => 'required|string|max:50',
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

        $teacher->update([
            'nip' => $request->nip,
            'nuptk' => $request->nuptk,
            'name' => $request->name,
            'subject' => $request->subject,
            'employment_status' => $request->employment_status,
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

    public function destroy(Teacher $teacher)
    {
        $name = $teacher->name;

        // Hapus foto jika ada
        if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
            Storage::disk('public')->delete($teacher->photo);
        }

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
