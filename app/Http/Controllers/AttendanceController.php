<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Halaman Kiosk / Absen Cepat (Real-time Quick Check-in/out)
     */
    public function kiosk(Request $request)
    {
        $today = now()->toDateString();
        $schoolId = current_school_id();
        $setting = AttendanceSetting::getSettings($schoolId);

        $search = $request->input('search');

        $teachersQuery = Teacher::where('school_id', $schoolId)
            ->active()
            ->with(['attendances' => function ($q) use ($today) {
                $q->where('date', $today);
            }])
            ->orderBy('name');

        if ($search) {
            $teachersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $teachers = $teachersQuery->get();

        // Hitung statistik hari ini
        $totalTeachers = Teacher::where('school_id', $schoolId)->active()->count();
        $todayAttendances = Attendance::where('school_id', $schoolId)->where('date', $today)->get();
        $hadirCount = $todayAttendances->where('status', 'hadir')->count();
        $terlambatCount = $todayAttendances->where('status', 'terlambat')->count();
        $izinSakitCount = $todayAttendances->whereIn('status', ['izin', 'sakit'])->count();
        $totalCheckedIn = $todayAttendances->count();
        $belumAbsenCount = max(0, $totalTeachers - $totalCheckedIn);

        return view('attendance.kiosk', compact(
            'teachers',
            'setting',
            'today',
            'totalTeachers',
            'hadirCount',
            'terlambatCount',
            'izinSakitCount',
            'belumAbsenCount',
            'search'
        ));
    }

    /**
     * Proses Absen Masuk Cepat
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'notes' => 'nullable|string|max:255',
        ]);

        $teacher = Teacher::findOrFail($request->teacher_id);
        $today = now()->toDateString();
        $now = now();

        // Cek apakah sudah absen hari ini
        $existing = Attendance::where('teacher_id', $teacher->id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return back()->with('error', "{$teacher->name} sudah tercatat absen hari ini (Status: {$existing->status_label})!");
        }

        $setting = AttendanceSetting::getSettings();

        // Validasi: Hari Libur Sekolah
        if ($setting->is_holiday_today) {
            return back()->with('error', 'Hari ini adalah hari libur sekolah. Presensi tidak dapat dilakukan.');
        }

        $standardTime = Carbon::parse($today . ' ' . $setting->time_in);

        // Validasi: Sebelum Jadwal Masuk Dimulai
        if ($now->lt($standardTime)) {
            $timeInFormatted = substr($setting->time_in, 0, 5);
            $currentTimeFormatted = $now->format('H:i');
            return back()->with('error', "Presensi belum dibuka! Jadwal absen masuk hari ini dimulai pada pukul {$timeInFormatted} WIB (saat ini masih pukul {$currentTimeFormatted} WIB).");
        }

        // Validasi: Setelah Jadwal Kepulangan Berakhir
        $standardOut = Carbon::parse($today . ' ' . $setting->time_out);
        if ($now->gt($standardOut)) {
            $timeOutFormatted = substr($setting->time_out, 0, 5);
            $currentTimeFormatted = $now->format('H:i');
            return back()->with('error', "Presensi masuk hari ini sudah ditutup! Jam kepulangan sekolah telah berakhir pada pukul {$timeOutFormatted} WIB (saat ini pukul {$currentTimeFormatted} WIB). Guru yang belum absen masuk tidak dapat lagi melakukan presensi.");
        }

        $thresholdTime = Carbon::parse($today . ' ' . $setting->time_in_late_threshold);

        $lateMinutes = 0;
        $status = 'hadir';

        if ($now->gt($thresholdTime)) {
            $status = 'terlambat';
            // Hitung menit keterlambatan dari jam masuk resmi
            $lateMinutes = (int) $standardTime->diffInMinutes($now, false);
            if ($lateMinutes < 1) {
                $lateMinutes = 1;
            }
        }

        Attendance::create([
            'school_id' => $teacher->school_id ?: current_school_id(),
            'teacher_id' => $teacher->id,
            'date' => $today,
            'time_in' => $now->format('H:i:s'),
            'status' => $status,
            'late_minutes' => $lateMinutes,
            'notes' => $request->notes,
        ]);

        if ($status === 'terlambat') {
            return back()->with('warning', "Absen Masuk Berhasil! {$teacher->name} tercatat TERLAMBAT {$lateMinutes} menit (Masuk jam {$now->format('H:i')}).");
        }

        return back()->with('success', "Absen Masuk Berhasil! {$teacher->name} hadir TEPAT WAKTU pada jam {$now->format('H:i')}.");
    }

    /**
     * Proses Absen Pulang Cepat
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'notes' => 'nullable|string|max:255',
        ]);

        $teacher = Teacher::findOrFail($request->teacher_id);
        $today = now()->toDateString();
        $now = now();

        $attendance = Attendance::where('teacher_id', $teacher->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', "{$teacher->name} belum melakukan absen masuk hari ini!");
        }

        if ($attendance->time_out) {
            return back()->with('info', "{$teacher->name} sudah absen pulang sebelumnya pada jam " . substr($attendance->time_out, 0, 5) . ".");
        }

        $setting = AttendanceSetting::getSettings();
        $standardOut = Carbon::parse($today . ' ' . $setting->time_out);

        // Hitung menit pulang lebih awal bila pulang sebelum jam operasional berakhir
        $earlyLeaveMinutes = 0;
        if ($now->lt($standardOut)) {
            $earlyLeaveMinutes = (int) $now->diffInMinutes($standardOut, false);
        }

        $notes = $attendance->notes;
        if ($request->filled('notes')) {
            $notes = ($notes ? $notes . ' | ' : '') . 'Pulang: ' . $request->notes;
        }

        $attendance->update([
            'time_out' => $now->format('H:i:s'),
            'early_leave_minutes' => $earlyLeaveMinutes,
            'notes' => $notes,
        ]);

        $message = "Absen Pulang Berhasil! {$teacher->name} tercatat pulang pada jam {$now->format('H:i')}. Total durasi: {$attendance->duration}.";
        if ($earlyLeaveMinutes > 0) {
            $message .= " (Pulang lebih awal {$earlyLeaveMinutes} menit sebelum jam {$setting->time_out}).";
        }

        return back()->with('success', $message);
    }

    /**
     * Dashboard & Riwayat Absensi
     */
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', now()->toDateString());
        $statusFilter = $request->input('status');
        $search = $request->input('search');
        $schoolId = current_school_id();

        $query = Attendance::where('school_id', $schoolId)
            ->with('teacher')
            ->where('date', $selectedDate)
            ->orderBy('time_in', 'asc');

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        if ($search) {
            $query->whereHas('teacher', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $attendances = $query->paginate(20)->withQueryString();

        // Statistik pada tanggal terpilih
        $totalTeachers = Teacher::where('school_id', $schoolId)->active()->count();
        $dateAttendances = Attendance::where('school_id', $schoolId)->where('date', $selectedDate)->get();
        $hadirCount = $dateAttendances->where('status', 'hadir')->count();
        $terlambatCount = $dateAttendances->where('status', 'terlambat')->count();
        $izinCount = $dateAttendances->where('status', 'izin')->count();
        $sakitCount = $dateAttendances->where('status', 'sakit')->count();
        $alpaCount = $dateAttendances->where('status', 'alpa')->count();
        $totalHadir = $hadirCount + $terlambatCount;
        $belumAbsen = max(0, $totalTeachers - $dateAttendances->count());

        $teachers = Teacher::where('school_id', $schoolId)->active()->orderBy('name')->get();
        $setting = AttendanceSetting::getSettings($schoolId);

        return view('attendance.index', compact(
            'attendances',
            'teachers',
            'setting',
            'selectedDate',
            'statusFilter',
            'search',
            'totalTeachers',
            'hadirCount',
            'terlambatCount',
            'izinCount',
            'sakitCount',
            'alpaCount',
            'totalHadir',
            'belumAbsen'
        ));
    }

    /**
     * Input Manual Absensi (Izin, Sakit, Alpa, atau Koreksi Masuk/Pulang)
     */
    public function storeManual(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'date' => 'required|date',
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpa',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        $schoolId = current_school_id();
        $setting = AttendanceSetting::getSettings($schoolId);
        $lateMinutes = 0;

        if (in_array($request->status, ['hadir', 'terlambat']) && $request->filled('time_in')) {
            $standardTime = Carbon::parse($request->date . ' ' . $setting->time_in);
            $inputTime = Carbon::parse($request->date . ' ' . $request->time_in);
            $threshold = Carbon::parse($request->date . ' ' . $setting->time_in_late_threshold);

            if ($inputTime->gt($threshold)) {
                $lateMinutes = (int) $standardTime->diffInMinutes($inputTime, false);
                $request->merge(['status' => 'terlambat']);
            }
        }

        Attendance::updateOrCreate(
            [
                'teacher_id' => $request->teacher_id,
                'date' => $request->date,
            ],
            [
                'school_id' => $schoolId,
                'time_in' => $request->filled('time_in') ? $request->time_in . ':00' : null,
                'time_out' => $request->filled('time_out') ? $request->time_out . ':00' : null,
                'status' => $request->status,
                'late_minutes' => max(0, $lateMinutes),
                'notes' => $request->notes,
            ]
        );

        return back()->with('success', 'Data absensi manual berhasil disimpan!');
    }

    /**
     * Update data absensi
     */
    public function update(Request $request, Attendance $attendance)
    {
        $request->validate([
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpa',
            'time_in' => 'nullable',
            'time_out' => 'nullable',
            'late_minutes' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $request->status,
            'time_in' => $request->filled('time_in') ? (strlen($request->time_in) === 5 ? $request->time_in . ':00' : $request->time_in) : null,
            'time_out' => $request->filled('time_out') ? (strlen($request->time_out) === 5 ? $request->time_out . ':00' : $request->time_out) : null,
            'late_minutes' => $request->input('late_minutes', 0),
            'notes' => $request->notes,
        ]);

        return back()->with('success', 'Data absensi berhasil diperbarui!');
    }

    /**
     * Hapus data absensi
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return back()->with('success', 'Data absensi berhasil dihapus!');
    }

    /**
     * Helper Pengambilan Data Rekapitulasi Presensi
     */
    private function getRecapData($month)
    {
        $startDate = Carbon::parse($month . '-01')->startOfMonth()->toDateString();
        $endDate = Carbon::parse($month . '-01')->endOfMonth()->toDateString();
        $schoolId = current_school_id();

        $teachers = Teacher::where('school_id', $schoolId)->active()->orderBy('name')->get();
        $setting = AttendanceSetting::getSettings($schoolId);

        // Ambil data absensi di bulan ini
        $attendances = Attendance::where('school_id', $schoolId)->whereBetween('date', [$startDate, $endDate])->get();

        // Rekap per guru
        $recapData = $teachers->map(function ($teacher) use ($attendances) {
            $teacherAttendances = $attendances->where('teacher_id', $teacher->id);

            $hadir = $teacherAttendances->where('status', 'hadir')->count();
            $terlambat = $teacherAttendances->where('status', 'terlambat')->count();
            $totalLateMinutes = $teacherAttendances->where('status', 'terlambat')->sum('late_minutes');
            $izin = $teacherAttendances->where('status', 'izin')->count();
            $sakit = $teacherAttendances->where('status', 'sakit')->count();
            $alpa = $teacherAttendances->where('status', 'alpa')->count();
            $totalKehadiran = $hadir + $terlambat;

            return (object) [
                'teacher' => $teacher,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'total_late_minutes' => $totalLateMinutes,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'total_kehadiran' => $totalKehadiran,
                'total_record' => $teacherAttendances->count(),
            ];
        });

        return [
            'recapData' => $recapData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'setting' => $setting,
        ];
    }

    /**
     * Laporan Rekapitulasi Absensi & Mode Cetak
     */
    public function recap(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $data = $this->getRecapData($month);

        $recapData = $data['recapData'];
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];
        $setting = $data['setting'];
        $isPrint = $request->boolean('print');

        return view('attendance.recap', compact(
            'recapData',
            'month',
            'startDate',
            'endDate',
            'setting',
            'isPrint'
        ));
    }

    /**
     * Export Rekapitulasi ke Excel (.xls)
     */
    public function exportExcel(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $data = $this->getRecapData($month);

        $recapData = $data['recapData'];
        $setting = $data['setting'];
        $startDate = $data['startDate'];
        $endDate = $data['endDate'];
        $monthLabel = Carbon::parse($month . '-01')->translatedFormat('F Y');

        $cleanMonth = str_replace('-', '_', $month);
        $filename = "Rekap_Presensi_Guru_{$cleanMonth}.xls";

        return response()->view('attendance.export_excel', compact(
            'recapData',
            'setting',
            'month',
            'monthLabel',
            'startDate',
            'endDate'
        ))->withHeaders([
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ]);
    }

    /**
     * Export Rekapitulasi ke CSV (.csv dengan UTF-8 BOM)
     */
    public function exportCsv(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $data = $this->getRecapData($month);
        $recapData = $data['recapData'];

        $cleanMonth = str_replace('-', '_', $month);
        $filename = "Rekap_Presensi_Guru_{$cleanMonth}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () use ($recapData) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header Kolom
            fputcsv($file, [
                'No',
                'Nama Guru',
                'NIP',
                'NUPTK',
                'Mata Pelajaran',
                'Status Kepegawaian',
                'Tepat Waktu (Hari)',
                'Terlambat (Hari)',
                'Total Menit Telat',
                'Izin (Hari)',
                'Sakit (Hari)',
                'Alpa (Hari)',
                'Total Kehadiran (Hari)'
            ]);

            foreach ($recapData as $idx => $item) {
                fputcsv($file, [
                    $idx + 1,
                    $item->teacher->name,
                    $item->teacher->nip ?: '-',
                    $item->teacher->nuptk ?: '-',
                    $item->teacher->subject ?: '-',
                    $item->teacher->employment_status ?: '-',
                    $item->hadir,
                    $item->terlambat,
                    $item->total_late_minutes,
                    $item->izin,
                    $item->sakit,
                    $item->alpa,
                    $item->total_kehadiran,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
