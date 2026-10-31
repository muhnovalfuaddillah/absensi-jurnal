<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SuperadminController extends Controller
{
    /**
     * Dashboard Global Superadmin
     */
    public function dashboard()
    {
        $today = now()->toDateString();

        $schools = School::withCount(['teachers', 'students', 'classes', 'users'])
            ->orderBy('name')
            ->get();

        $totalSchools = $schools->count();
        $activeSchools = $schools->where('is_active', true)->count();
        $totalTeachers = Teacher::count();
        $totalStudents = Student::count();
        $totalClasses = SchoolClass::count();
        $totalAdmins = User::where('role', 'admin')->count();

        // Presensi guru hari ini secara keseluruhan
        $todayAttendances = Attendance::where('date', $today)->get();
        $totalHadirToday = $todayAttendances->whereIn('status', ['hadir', 'terlambat'])->count();

        // Statistik ringkas per sekolah
        $schoolsStats = $schools->map(function ($sch) use ($today) {
            $schTeachersCount = $sch->teachers_count;
            $schAttendances = Attendance::where('school_id', $sch->id)
                ->where('date', $today)
                ->get();
            $schHadir = $schAttendances->whereIn('status', ['hadir', 'terlambat'])->count();

            return [
                'school' => $sch,
                'teachers_count' => $schTeachersCount,
                'students_count' => $sch->students_count,
                'classes_count' => $sch->classes_count,
                'hadir_today' => $schHadir,
                'hadir_percentage' => $schTeachersCount > 0 ? round(($schHadir / $schTeachersCount) * 100, 1) : 0,
            ];
        });

        return view('superadmin.dashboard', compact(
            'schools',
            'schoolsStats',
            'totalSchools',
            'activeSchools',
            'totalTeachers',
            'totalStudents',
            'totalClasses',
            'totalAdmins',
            'totalHadirToday'
        ));
    }

    /**
     * Beralih Konteks Sekolah Aktif (Switch School)
     */
    public function switchSchool(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
        ]);

        $school = School::findOrFail($request->school_id);
        session(['active_school_id' => $school->id]);

        return back()->with('success', "Berhasil beralih ke sekolah: {$school->name}");
    }

    /**
     * Manajemen Daftar Sekolah
     */
    public function schoolsIndex(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = School::withCount(['teachers', 'students', 'classes', 'users'])->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('npsn', 'like', "%{$search}%")
                  ->orWhere('foundation_name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('headmaster_name', 'like', "%{$search}%");
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('is_active', (bool) $status);
        }

        $schools = $query->paginate(12)->withQueryString();

        $totalSchools = School::count();
        $activeSchools = School::where('is_active', true)->count();

        return view('superadmin.schools_index', compact(
            'schools',
            'search',
            'status',
            'totalSchools',
            'activeSchools'
        ));
    }

    /**
     * Simpan Sekolah Baru (Termasuk Opsional Buat Akun Admin Sekolah Langsung)
     */
    public function schoolsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:50',
            'foundation_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'headmaster_name' => 'nullable|string|max:255',
            'headmaster_nip' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            // Akun admin opsional
            'admin_name' => 'nullable|string|max:255',
            'admin_username' => 'nullable|string|max:100|unique:users,username',
            'admin_email' => 'nullable|email|max:255|unique:users,email',
            'admin_password' => 'nullable|string|min:6',
        ], [
            'name.required' => 'Nama sekolah wajib diisi.',
            'admin_username.unique' => 'Username admin sudah digunakan.',
            'admin_email.unique' => 'Email admin sudah digunakan.',
            'admin_password.min' => 'Password admin minimal 6 karakter.',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('uploads/logo', 'public');
        }

        $school = School::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . Str::random(4),
            'npsn' => $request->npsn,
            'foundation_name' => $request->foundation_name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'headmaster_name' => $request->headmaster_name,
            'headmaster_nip' => $request->headmaster_nip,
            'website' => $request->website,
            'logo_path' => $logoPath,
            'is_active' => true,
        ]);

        // Buat akun admin jika diisi
        if ($request->filled('admin_username') && $request->filled('admin_password')) {
            User::create([
                'school_id' => $school->id,
                'name' => $request->admin_name ?: 'Admin ' . $school->name,
                'username' => $request->admin_username,
                'email' => $request->admin_email ?: strtolower($request->admin_username) . '@sekolah.sch.id',
                'password' => Hash::make($request->admin_password),
                'role' => 'admin',
            ]);
        }

        return redirect()->route('superadmin.schools.index')->with('success', "Sekolah \"{$school->name}\" berhasil ditambahkan!");
    }

    /**
     * Update Informasi Sekolah
     */
    public function schoolsUpdate(Request $request, $id)
    {
        $school = School::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:50',
            'foundation_name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'headmaster_name' => 'nullable|string|max:255',
            'headmaster_nip' => 'nullable|string|max:50',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'is_active' => 'nullable|boolean',
        ]);

        $updateData = [
            'name' => $request->name,
            'npsn' => $request->npsn,
            'foundation_name' => $request->foundation_name,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'headmaster_name' => $request->headmaster_name,
            'headmaster_nip' => $request->headmaster_nip,
            'website' => $request->website,
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $school->is_active,
        ];

        if ($request->hasFile('logo')) {
            if ($school->logo_path && Storage::disk('public')->exists($school->logo_path)) {
                Storage::disk('public')->delete($school->logo_path);
            }
            $updateData['logo_path'] = $request->file('logo')->store('uploads/logo', 'public');
        }

        $school->update($updateData);

        // Sinkronkan ke attendance_setting sekolah jika ada
        if ($school->setting) {
            $school->setting->update([
                'school_name' => $school->name,
                'foundation_name' => $school->foundation_name,
                'npsn' => $school->npsn,
                'school_address' => $school->address,
                'school_phone' => $school->phone,
                'school_email' => $school->email,
                'headmaster_name' => $school->headmaster_name,
                'headmaster_nip' => $school->headmaster_nip,
                'website' => $school->website,
                'logo_path' => $school->logo_path,
            ]);
        }

        return back()->with('success', "Data sekolah \"{$school->name}\" berhasil diperbarui!");
    }

    /**
     * Toggle Status Aktif Sekolah
     */
    public function schoolsToggle($id)
    {
        $school = School::findOrFail($id);
        $school->is_active = !$school->is_active;
        $school->save();

        $statusLabel = $school->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Sekolah \"{$school->name}\" berhasil {$statusLabel}!");
    }

    /**
     * Hapus Sekolah
     */
    public function schoolsDestroy($id)
    {
        $school = School::findOrFail($id);

        if (School::count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus sekolah utama atau satu-satunya sekolah di aplikasi!');
        }

        $schoolName = $school->name;
        $school->delete();

        // Reset session jika sekolah yang dihapus sedang aktif
        if (session('active_school_id') == $id) {
            session()->forget('active_school_id');
        }

        return redirect()->route('superadmin.schools.index')->with('success', "Sekolah \"{$schoolName}\" beserta data terkait berhasil dihapus.");
    }

    /**
     * Kelola Seluruh Akun Admin Sekolah
     */
    public function adminsIndex(Request $request)
    {
        $search = $request->input('search');
        $schoolFilter = $request->input('school_id');

        $query = User::with('school')->where('role', 'admin')->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($schoolFilter) {
            $query->where('school_id', $schoolFilter);
        }

        $admins = $query->paginate(15)->withQueryString();
        $schools = School::orderBy('name')->get();

        return view('superadmin.admins_index', compact('admins', 'schools', 'search', 'schoolFilter'));
    }

    /**
     * Buat Akun Admin Sekolah Baru
     */
    public function adminsStore(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
        ], [
            'school_id.required' => 'Pilih sekolah untuk admin ini.',
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh user lain.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        User::create([
            'school_id' => $request->school_id,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return back()->with('success', "Akun admin \"{$request->name}\" berhasil dibuat!");
    }

    /**
     * Update Akun Admin Sekolah
     */
    public function adminsUpdate(Request $request, $id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        $request->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username,' . $admin->id,
            'email' => 'required|email|max:255|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:6',
        ]);

        $updateData = [
            'school_id' => $request->school_id,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $admin->update($updateData);

        return back()->with('success', "Akun admin \"{$admin->name}\" berhasil diperbarui!");
    }

    /**
     * Hapus Akun Admin Sekolah
     */
    public function adminsDestroy($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);
        $name = $admin->name;
        $admin->delete();

        return back()->with('success', "Akun admin \"{$name}\" berhasil dihapus.");
    }

    /**
     * Halaman Pengaturan Identitas Yayasan & Logo Login Portal
     */
    public function settingsIndex()
    {
        $setting = AttendanceSetting::getGlobalSetting();
        $schools = School::withCount(['teachers', 'students'])->orderBy('name')->get();

        return view('superadmin.settings', compact('setting', 'schools'));
    }

    /**
     * Simpan Pengaturan Identitas Yayasan & Logo Login Portal
     */
    public function settingsUpdate(Request $request)
    {
        $setting = AttendanceSetting::getGlobalSetting();

        $request->validate([
            'school_name' => 'required|string|max:150',
            'foundation_name' => 'required|string|max:150',
            'school_phone' => 'nullable|string|max:50',
            'school_email' => 'nullable|email|max:100',
            'website' => 'nullable|string|max:150',
            'school_address' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:4096',
        ]);

        $updateData = [
            'school_name' => $request->school_name,
            'foundation_name' => $request->foundation_name,
            'school_phone' => $request->school_phone,
            'school_email' => $request->school_email,
            'website' => $request->website,
            'school_address' => $request->school_address,
        ];

        // Handle upload logo baru
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $dir = public_path('uploads/logo');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'login_portal_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $updateData['logo_path'] = 'uploads/logo/' . $filename;

            // Sinkronkan juga ke public/logo.jpg
            $targetPath = $dir . DIRECTORY_SEPARATOR . $filename;
            @copy($targetPath, public_path('logo.jpg'));

            // Generate icon PWA (192 & 512) jika GD aktif
            if (extension_loaded('gd')) {
                try {
                    $ext = strtolower($file->getClientOriginalExtension());
                    $src = null;
                    if ($ext === 'png') {
                        $src = @imagecreatefrompng($targetPath);
                    } elseif (in_array($ext, ['jpg', 'jpeg'])) {
                        $src = @imagecreatefromjpeg($targetPath);
                    } elseif ($ext === 'webp') {
                        $src = @imagecreatefromwebp($targetPath);
                    }
                    if ($src) {
                        imagealphablending($src, true);
                        imagesavealpha($src, true);
                        foreach ([192, 512] as $size) {
                            $dst = imagecreatetruecolor($size, $size);
                            imagealphablending($dst, false);
                            imagesavealpha($dst, true);
                            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
                            imagefilledrectangle($dst, 0, 0, $size, $size, $transparent);
                            imagecopyresampled($dst, $src, 0, 0, 0, 0, $size, $size, imagesx($src), imagesy($src));
                            imagepng($dst, public_path('icon-' . $size . '.png'));
                            imagedestroy($dst);
                        }
                        imagedestroy($src);
                    }
                } catch (\Throwable $e) {}
            }
        } elseif ($request->boolean('reset_logo')) {
            $updateData['logo_path'] = 'logo.jpg';
        }

        $setting->update($updateData);

        return back()->with('success', 'Pengaturan Logo Halaman Login dan Identitas Yayasan berhasil disimpan!');
    }
}
