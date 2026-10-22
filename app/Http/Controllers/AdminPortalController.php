<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Teacher;
use Illuminate\Http\Request;

class AdminPortalController extends Controller
{
    /**
     * Dashboard Utama Administrator
     */
    public function dashboard()
    {
        $today = now()->toDateString();
        $setting = AttendanceSetting::getSettings();

        // 1. Statistik Keseluruhan
        $totalTeachers = Teacher::active()->count();
        $todayAttendances = Attendance::with('teacher')->where('date', $today)->get();

        $hadirCount = $todayAttendances->where('status', 'hadir')->count();
        $terlambatCount = $todayAttendances->where('status', 'terlambat')->count();
        $izinCount = $todayAttendances->where('status', 'izin')->count();
        $sakitCount = $todayAttendances->where('status', 'sakit')->count();
        $alpaCount = $todayAttendances->where('status', 'alpa')->count();
        $totalChecked = $todayAttendances->count();
        $belumHadirCount = max(0, $totalTeachers - $totalChecked);

        // Persentase Kehadiran
        $attendanceRate = $totalTeachers > 0 ? round((($hadirCount + $terlambatCount) / $totalTeachers) * 100, 1) : 0;

        // 2. Daftar Semua Guru dengan Presensi Hari Ini (untuk tabel monitoring live)
        $teachers = Teacher::active()
            ->with(['attendances' => function ($q) use ($today) {
                $q->where('date', $today);
            }])
            ->orderBy('name')
            ->get();

        // 3. Daftar Guru yang BELUM HADIR hari ini (untuk monitoring & tindak lanjut piket)
        $attendedTeacherIds = $todayAttendances->pluck('teacher_id')->toArray();
        $unattendedTeachers = Teacher::active()
            ->whereNotIn('id', $attendedTeacherIds)
            ->orderBy('name')
            ->get();

        // 4. Log Presensi Masuk & Pulang Terbaru Hari Ini
        $recentAttendances = Attendance::with('teacher')
            ->where('date', $today)
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        // 5. Permohonan Izin / Sakit Terbaru Hari Ini
        $urgentLeave = Attendance::with('teacher')
            ->where('date', $today)
            ->whereIn('status', ['izin', 'sakit'])
            ->latest()
            ->first();

        // PNS / Tetap (memiliki NIP) vs Honorer
        $guruTetapCount = Teacher::active()->whereNotNull('nip')->where('nip', '!=', '')->count();
        $guruHonorerCount = max(0, $totalTeachers - $guruTetapCount);

        // 6. Data Grafik: Distribusi Waktu Masuk Guru Hari Ini
        $timeBuckets = [
            'early' => 0,       // < 06:30
            'on_time' => 0,     // 06:30 - 07:00
            'near_limit' => 0,  // 07:00 - 07:15
            'tolerance' => 0,   // 07:15 - 07:30
            'late' => 0,        // > 07:30
        ];
        foreach ($todayAttendances as $att) {
            if ($att->time_in) {
                $t = substr($att->time_in, 0, 5);
                if ($t < '06:30') {
                    $timeBuckets['early']++;
                } elseif ($t <= '07:00') {
                    $timeBuckets['on_time']++;
                } elseif ($t <= '07:15') {
                    $timeBuckets['near_limit']++;
                } elseif ($t <= '07:30') {
                    $timeBuckets['tolerance']++;
                } else {
                    $timeBuckets['late']++;
                }
            }
        }

        // 7. Data Grafik: Tren Kehadiran 7 Hari Terakhir
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i);
            $dateStr = $d->toDateString();
            $label = $d->translatedFormat('D, d M');
            $dayAtts = Attendance::where('date', $dateStr)->get();
            $last7Days->push([
                'date' => $dateStr,
                'label' => $label,
                'hadir' => $dayAtts->where('status', 'hadir')->count(),
                'terlambat' => $dayAtts->where('status', 'terlambat')->count(),
                'izin_sakit' => $dayAtts->whereIn('status', ['izin', 'sakit'])->count(),
                'alpa' => $dayAtts->where('status', 'alpa')->count(),
                'total' => $dayAtts->count(),
            ]);
        }

        return view('admin.dashboard', compact(
            'setting',
            'today',
            'totalTeachers',
            'hadirCount',
            'terlambatCount',
            'izinCount',
            'sakitCount',
            'alpaCount',
            'belumHadirCount',
            'attendanceRate',
            'teachers',
            'unattendedTeachers',
            'recentAttendances',
            'urgentLeave',
            'guruTetapCount',
            'guruHonorerCount',
            'totalChecked',
            'timeBuckets',
            'last7Days'
        ));
    }

    /**
     * Halaman Profil & Akun Administrator / Tim IT
     */
    public function profile()
    {
        $user = auth()->user();
        $setting = AttendanceSetting::getSettings();

        // Format nomor untuk preview WA
        $cleanPhone = preg_replace('/[^0-9]/', '', $setting->school_phone ?? '');
        $waFormatted = $cleanPhone;
        if (str_starts_with($waFormatted, '0')) {
            $waFormatted = '62' . substr($waFormatted, 1);
        }

        return view('admin.profile', compact('user', 'setting', 'cleanPhone', 'waFormatted'));
    }

    /**
     * Perbarui Profil Admin & Nomor WhatsApp Tim IT Aplikasi
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:50|unique:users,username,' . $user->id,
            'phone' => 'required|string|min:9|max:25',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama administrator wajib diisi.',
            'email.required' => 'Email login wajib diisi.',
            'phone.required' => 'Nomor WhatsApp Tim IT wajib diisi.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            'new_password.min' => 'Kata sandi baru minimal 6 karakter.',
        ]);

        // Ganti password jika diisi
        if ($request->filled('new_password')) {
            if (!$request->filled('current_password')) {
                return back()->with('error', 'Silakan masukkan kata sandi saat ini untuk mengubah kata sandi baru!');
            }
            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Kata sandi saat ini tidak cocok!');
            }
            $user->password = \Illuminate\Support\Facades\Hash::make($request->new_password);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('username')) {
            $user->username = $request->username;
        }
        $user->save();

        // Sinkronisasi nomor HP Admin ke AttendanceSetting->school_phone
        // sehingga LANGSUNG COCOK ke tombol Bantuan IT di dashboard seluruh guru!
        $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone);
        $setting = AttendanceSetting::getSettings();
        $setting->school_phone = $cleanPhone;
        $setting->save();

        return back()->with('success', 'Profil Administrator & Nomor WhatsApp Tim IT (' . $cleanPhone . ') berhasil diperbarui dan tersinkronisasi ke seluruh aplikasi!');
    }
}
