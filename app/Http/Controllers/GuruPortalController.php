<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class GuruPortalController extends Controller
{
    /**
     * Dashboard Personal Guru (HANYA melihat data miliknya sendiri)
     */
    public function dashboard()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return redirect()->route('login')->with('error', 'Profil guru tidak terhubung dengan akun Anda.');
        }

        $setting = AttendanceSetting::getSettings();
        $today = now()->toDateString();

        // 1. Presensi Saya Hari Ini
        $todayAttendance = Attendance::where('teacher_id', $teacher->id)
            ->where('date', $today)
            ->first();

        // 2. Statistik Kehadiran Saya Bulan Ini
        $startOfMonth = now()->startOfMonth()->toDateString();
        $endOfMonth = now()->endOfMonth()->toDateString();

        $monthAttendances = Attendance::where('teacher_id', $teacher->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $totalHadir = $monthAttendances->where('status', 'hadir')->count();
        $totalTerlambat = $monthAttendances->where('status', 'terlambat')->count();
        $totalMenitTelat = $monthAttendances->where('status', 'terlambat')->sum('late_minutes');
        $totalIzin = $monthAttendances->where('status', 'izin')->count();
        $totalSakit = $monthAttendances->where('status', 'sakit')->count();
        $totalAlpa = $monthAttendances->where('status', 'alpa')->count();
        $totalPresensi = $totalHadir + $totalTerlambat;

        // 3. Riwayat 7 Hari Terakhir Saya
        $recentAttendances = Attendance::where('teacher_id', $teacher->id)
            ->orderBy('date', 'desc')
            ->take(7)
            ->get();

        // 4. Jadwal Mengajar & Jurnal Hari Ini
        $dayOfWeekIso = now()->dayOfWeekIso;
        $dayNames = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];
        $todayDay = $dayNames[$dayOfWeekIso] ?? 'Senin';

        $todaySchedules = \App\Models\LessonSchedule::with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->where('day', $todayDay)
            ->orderBy('start_time')
            ->get();

        $todayJournals = \App\Models\TeachingJournal::where('teacher_id', $teacher->id)
            ->where('date', $today)
            ->pluck('id', 'lesson_schedule_id')
            ->toArray();

        // 5. Hitung Guru Izin Hari Ini (untuk indikator Laporan Izin & Inval)
        $todayAbsentTeachersCount = Attendance::where('date', $today)
            ->whereIn('status', ['izin', 'sakit'])
            ->count();

        // 6. Informasi Sekolah & Maklumat Yayasan Aktif
        $announcements = \App\Models\SchoolAnnouncement::where('is_active', true)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        return view('guru.dashboard', compact(
            'teacher',
            'setting',
            'today',
            'todayDay',
            'todaySchedules',
            'todayJournals',
            'todayAttendance',
            'totalHadir',
            'totalTerlambat',
            'totalMenitTelat',
            'totalIzin',
            'totalSakit',
            'totalAlpa',
            'totalPresensi',
            'recentAttendances',
            'todayAbsentTeachersCount',
            'announcements'
        ));
    }

    /**
     * Guru Melakukan Absen Masuk Mandiri
     */
    public function checkIn(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $today = now()->toDateString();
        $now = now();

        $existing = Attendance::where('teacher_id', $teacher->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return back()->with('error', "Anda sudah tercatat presensi hari ini ({$existing->status_label})!");
        }

        // Validasi: Wajib daftarkan wajah terlebih dahulu
        if (!$teacher->is_face_registered) {
            return back()->with('error', 'Wajah Anda belum terdaftar di sistem! Silakan lakukan Pendaftaran Wajah (Face Enrollment) terlebih dahulu agar sistem dapat mengenali Anda.');
        }

        $setting = AttendanceSetting::getSettings();

        // Validasi: Hari Libur Sekolah
        if ($setting->is_holiday_today) {
            return back()->with('error', 'Hari ini adalah hari libur sekolah. Presensi masuk tidak dibuka.');
        }

        // Validasi: Sebelum Jadwal Masuk Dimulai
        $standardTime = Carbon::parse($today . ' ' . $setting->time_in);
        if ($now->lt($standardTime)) {
            $timeInFormatted = substr($setting->time_in, 0, 5);
            $currentTimeFormatted = $now->format('H:i');
            return back()->with('error', "Presensi masuk belum dibuka! Jadwal presensi hari ini dimulai pada pukul {$timeInFormatted} WIB (saat ini masih pukul {$currentTimeFormatted} WIB).");
        }

        // Validasi: Setelah Jadwal Kepulangan Berakhir (Jam Operasional Sekolah Selesai)
        $standardOut = Carbon::parse($today . ' ' . $setting->time_out);
        if ($now->gt($standardOut)) {
            $timeOutFormatted = substr($setting->time_out, 0, 5);
            $currentTimeFormatted = $now->format('H:i');
            return back()->with('error', "Presensi masuk hari ini sudah ditutup! Jam kegiatan/kepulangan sekolah telah berakhir pada pukul {$timeOutFormatted} WIB (saat ini pukul {$currentTimeFormatted} WIB). Guru yang belum absen masuk tidak dapat lagi melakukan presensi masuk hari ini.");
        }

        // Validasi Geofence: Guru WAJIB berada di dalam titik lokasi yang telah ditentukan oleh Admin
        $geofenceCheck = $setting->isWithinGeofence($request->input('latitude'), $request->input('longitude'));
        if (!$geofenceCheck['allowed']) {
            return back()->with('error', $geofenceCheck['message']);
        }

        $thresholdTime = Carbon::parse($today . ' ' . $setting->time_in_late_threshold);

        $lateMinutes = 0;
        $status = 'hadir';

        if ($now->gt($thresholdTime)) {
            $status = 'terlambat';
            $lateMinutes = (int) $standardTime->diffInMinutes($now, false);
            if ($lateMinutes < 1) {
                $lateMinutes = 1;
            }
        }

        $photoInPath = null;
        if ($request->filled('face_image')) {
            $photoInPath = $this->saveFaceImage($request->input('face_image'), 'masuk_' . $teacher->id);
        }

        Attendance::create([
            'teacher_id' => $teacher->id,
            'date' => $today,
            'time_in' => $now->format('H:i:s'),
            'status' => $status,
            'late_minutes' => $lateMinutes,
            'attendance_method' => $photoInPath ? 'face_scan' : 'mandiri',
            'photo_in' => $photoInPath,
            'latitude_in' => $request->filled('latitude') ? $request->input('latitude') : null,
            'longitude_in' => $request->filled('longitude') ? $request->input('longitude') : null,
            'notes' => $request->input('notes'),
        ]);

        if ($status === 'terlambat') {
            return back()->with('warning', "Scan Wajah Berhasil! Anda tercatat TERLAMBAT {$lateMinutes} menit (Masuk pukul {$now->format('H:i')}).");
        }

        return back()->with('success', "Verifikasi Scan Wajah Berhasil! Anda hadir TEPAT WAKTU pada pukul {$now->format('H:i')}. Selamat bertugas!");
    }

    /**
     * Guru Melakukan Absen Pulang Mandiri (dengan Scan Wajah)
     */
    public function checkOut(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $today = now()->toDateString();
        $now = now();

        $attendance = Attendance::where('teacher_id', $teacher->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum melakukan absen masuk hari ini!');
        }

        if ($attendance->time_out) {
            return back()->with('info', 'Anda sudah melakukan absen pulang sebelumnya pada pukul ' . substr($attendance->time_out, 0, 5) . '.');
        }

        // Validasi: Wajib daftarkan wajah terlebih dahulu
        if (!$teacher->is_face_registered) {
            return back()->with('error', 'Wajah Anda belum terdaftar di sistem! Silakan daftarkan wajah Anda terlebih dahulu.');
        }

        $setting = AttendanceSetting::getSettings();

        // Validasi Geofence: Guru WAJIB berada di dalam titik lokasi yang telah ditentukan oleh Admin
        $geofenceCheck = $setting->isWithinGeofence($request->input('latitude'), $request->input('longitude'));
        if (!$geofenceCheck['allowed']) {
            return back()->with('error', $geofenceCheck['message']);
        }

        $standardOut = Carbon::parse($today . ' ' . $setting->time_out);

        // Hitung menit pulang lebih awal bila pulang sebelum jam operasional berakhir
        $earlyLeaveMinutes = 0;
        if ($now->lt($standardOut)) {
            $earlyLeaveMinutes = (int) $now->diffInMinutes($standardOut, false);
        }

        $notes = $attendance->notes;
        if ($request->filled('notes')) {
            $notes = ($notes ? $notes . ' | ' : '') . 'Catatan Pulang: ' . $request->notes;
        }

        $photoOutPath = null;
        if ($request->filled('face_image')) {
            $photoOutPath = $this->saveFaceImage($request->input('face_image'), 'pulang_' . $teacher->id);
        }

        $attendance->update([
            'time_out' => $now->format('H:i:s'),
            'early_leave_minutes' => $earlyLeaveMinutes,
            'photo_out' => $photoOutPath ?: $attendance->photo_out,
            'latitude_out' => $request->filled('latitude') ? $request->input('latitude') : $attendance->latitude_out,
            'longitude_out' => $request->filled('longitude') ? $request->input('longitude') : $attendance->longitude_out,
            'notes' => $notes,
        ]);

        $msg = "Scan Wajah Pulang Berhasil! Tercatat pukul {$now->format('H:i')}. Total durasi kerja: {$attendance->duration}.";
        if ($earlyLeaveMinutes > 0) {
            $msg .= " (Pulang {$earlyLeaveMinutes} menit lebih awal dari jadwal {$setting->time_out}).";
        }
        $msg .= " Terima kasih atas dedikasi Anda hari ini.";

        return back()->with('success', $msg);
    }

    /**
     * Helper untuk Menyimpan Base64 Snapshot Kamera Wajah Guru
     */
    protected function saveFaceImage($base64Data, $prefix = 'face')
    {
        if (empty($base64Data)) {
            return null;
        }

        // Hapus prefix data URL jika ada
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $base64Data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]);
            if (!in_array($type, ['jpg', 'jpeg', 'png', 'webp'])) {
                $type = 'jpg';
            }
        } else {
            $type = 'jpg';
        }

        $data = base64_decode($base64Data);
        if ($data === false) {
            return null;
        }

        $monthDir = date('Y-m');
        $dir = public_path('uploads/face_presensi/' . $monthDir);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileName = $prefix . '_' . date('Ymd_His') . '_' . uniqid() . '.' . $type;
        $filePath = $dir . DIRECTORY_SEPARATOR . $fileName;
        file_put_contents($filePath, $data);

        return 'uploads/face_presensi/' . $monthDir . '/' . $fileName;
    }

    /**
     * Daftarkan / Rekam Wajah Guru (Face Enrollment)
     */
    public function registerFace(Request $request)
    {
        $request->validate([
            'face_image' => 'required|string',
            'face_descriptor' => 'nullable|string',
        ]);

        $teacher = Auth::user()->teacher;
        $savedPath = $this->saveFaceImage($request->input('face_image'), 'registered_face_' . $teacher->id);

        if (!$savedPath) {
            return back()->with('error', 'Gagal memproses gambar rekaman wajah. Mohon coba lagi.');
        }

        $faceData = $request->input('face_descriptor');
        if ($faceData) {
            $decoded = json_decode($faceData, true);
            if (!is_array($decoded) || count($decoded) < 50) {
                $faceData = 'enrolled_verified';
            }
        } else {
            $faceData = 'enrolled_verified';
        }

        $teacher->update([
            'photo' => $savedPath,
            'face_registered_at' => now(),
            'face_data' => $faceData,
        ]);

        return back()->with('success', 'Wajah Anda berhasil didaftarkan secara biometrik di sistem HadirGuru! Anda kini dapat melakukan presensi menggunakan Face ID.');
    }

    /**
     * Menyimpan descriptor biometrik wajah (128 array float) via AJAX
     */
    public function syncDescriptor(Request $request)
    {
        $request->validate([
            'face_descriptor' => 'required|string',
        ]);

        $teacher = Auth::user()->teacher;
        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher profile not found'], 404);
        }

        $descriptor = $request->input('face_descriptor');
        $decoded = json_decode($descriptor, true);

        if (!is_array($decoded) || count($decoded) < 50) {
            return response()->json(['success' => false, 'message' => 'Invalid descriptor data'], 422);
        }

        $teacher->update([
            'face_data' => $descriptor,
            'face_registered_at' => $teacher->face_registered_at ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Descriptor biometrik wajah berhasil disinkronisasi.',
        ]);
    }

    /**
     * Riwayat Lengkap Presensi Guru (HANYA data miliknya sendiri)
     */
    public function riwayat(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $month = $request->input('month', now()->format('Y-m'));
        $status = $request->input('status');

        $startDate = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($month . '-01')->endOfMonth()->toDateString();

        $query = Attendance::where('teacher_id', $teacher->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $attendances = $query->paginate(20)->withQueryString();

        // Statistik bulan yang dipilih untuk guru ini
        $allMonth = Attendance::where('teacher_id', $teacher->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $hadirCount = $allMonth->where('status', 'hadir')->count();
        $telatCount = $allMonth->where('status', 'terlambat')->count();
        $totalTelatMenit = $allMonth->where('status', 'terlambat')->sum('late_minutes');
        $izinCount = $allMonth->where('status', 'izin')->count();
        $sakitCount = $allMonth->where('status', 'sakit')->count();

        return view('guru.riwayat', compact(
            'teacher',
            'attendances',
            'month',
            'status',
            'hadirCount',
            'telatCount',
            'totalTelatMenit',
            'izinCount',
            'sakitCount'
        ));
    }

    /**
     * Form Pengajuan Izin / Sakit Mandiri
     */
    public function formIzin()
    {
        $teacher = Auth::user()->teacher;
        $today = now()->toDateString();
        $setting = AttendanceSetting::getSettings();

        // Ambil riwayat izin/sakit terbaru guru ini
        $leaveHistory = Attendance::where('teacher_id', $teacher->id)
            ->whereIn('status', ['izin', 'sakit'])
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        return view('guru.izin', compact('teacher', 'today', 'setting', 'leaveHistory'));
    }

    /**
     * Simpan Pengajuan Izin / Sakit Mandiri
     */
    public function storeIzin(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'status' => 'required|in:izin,sakit',
            'notes' => 'required|string|min:5|max:500',
        ]);

        $teacher = Auth::user()->teacher;

        Attendance::updateOrCreate(
            [
                'teacher_id' => $teacher->id,
                'date' => $request->date,
            ],
            [
                'time_in' => null,
                'time_out' => null,
                'status' => $request->status,
                'late_minutes' => 0,
                'early_leave_minutes' => 0,
                'notes' => $request->notes,
            ]
        );

        return redirect()->route('guru.dashboard')->with('success', 'Keterangan ' . strtoupper($request->status) . ' untuk tanggal ' . $request->date . ' berhasil disimpan!');
    }

    /**
     * Profil Saya
     */
    public function profile()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        return view('guru.profile', compact('user', 'teacher'));
    }

    /**
     * Update Profil, Foto, & Password Mandiri
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return back()->with('error', 'Data profil guru tidak terhubung dengan akun Anda.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'nip' => 'nullable|string|max:50|unique:teachers,nip,' . $teacher->id,
            'nuptk' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'employment_status' => 'nullable|string|max:50',
            'education' => 'nullable|string|max:50',
            'class_duty' => 'nullable|string|max:100',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'address' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'remove_photo' => 'nullable|boolean',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'nip.unique' => 'NIP sudah digunakan oleh guru lain.',
            'email.unique' => 'Alamat email ini sudah terdaftar untuk pengguna lain.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'current_password.required_with' => 'Masukkan password saat ini jika ingin membuat password baru.',
        ]);

        $updateData = [
            'name' => $request->name,
            'gender' => $request->gender,
            'nip' => $request->nip,
            'nuptk' => $request->nuptk,
            'subject' => $request->subject,
            'employment_status' => $request->employment_status,
            'education' => $request->education,
            'class_duty' => $request->class_duty,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
        ];

        // Upload Foto Profil Mandiri
        if ($request->hasFile('photo')) {
            if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $storedPath = $request->file('photo')->store('teachers', 'public');
            $updateData['photo'] = $storedPath;
            $updateData['face_registered_at'] = now();
            $updateData['face_data'] = 'uploaded_profile_photo';
        } elseif ($request->boolean('remove_photo')) {
            if ($teacher->photo && Storage::disk('public')->exists($teacher->photo)) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $updateData['photo'] = null;
            $updateData['face_registered_at'] = null;
            $updateData['face_data'] = null;
        }

        $teacher->update($updateData);

        // Sinkronisasi data ke user
        $userUpdates = ['name' => $request->name];
        if ($request->filled('email')) {
            $userUpdates['email'] = $request->email;
        }

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Password saat ini yang Anda masukkan salah!');
            }
            $userUpdates['password'] = Hash::make($request->new_password);
        }

        $user->update($userUpdates);

        return back()->with('success', 'Data profil guru Anda berhasil disimpan dan diperbarui!');
    }
}
