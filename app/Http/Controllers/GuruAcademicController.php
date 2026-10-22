<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LessonSchedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingJournal;
use App\Services\WhatsAppNotificationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GuruAcademicController extends Controller
{
    private function getIndonesianDayName($date = null): string
    {
        $dayOfWeek = ($date ? Carbon::parse($date) : now())->dayOfWeekIso; // 1 = Senin, 7 = Minggu
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
        return $days[$dayOfWeek] ?? 'Senin';
    }

    /**
     * Jadwal Mengajar Guru
     */
    public function jadwal(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $todayDay = $this->getIndonesianDayName();
        $todayDate = now()->toDateString();

        // Jadwal Hari Ini
        $todaySchedules = LessonSchedule::with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->where('day', $todayDay)
            ->orderBy('start_time')
            ->get();

        // Cek jurnal yang sudah dibuat hari ini untuk jadwal terkait
        $todayJournals = TeachingJournal::where('teacher_id', $teacher->id)
            ->where('date', $todayDate)
            ->pluck('id', 'lesson_schedule_id')
            ->toArray();

        // Seluruh Jadwal Mingguan
        $allSchedules = LessonSchedule::with(['schoolClass', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day');

        return view('guru.academic.jadwal', compact('teacher', 'todayDay', 'todaySchedules', 'todayJournals', 'allSchedules'));
    }

    /**
     * Laporan Guru Berhalangan Hadir (Izin / Sakit) & Jadwal Kelas Pengganti (Inval)
     */
    public function laporanIzinGuru(Request $request)
    {
        $currentTeacher = Auth::user()->teacher;
        $date = $request->input('date', now()->toDateString());
        $carbonDate = Carbon::parse($date);
        $dayName = $this->getIndonesianDayName($date);

        // Ambil semua presensi guru dengan status izin atau sakit pada tanggal terpilih
        $absences = Attendance::with('teacher')
            ->where('date', $date)
            ->whereIn('status', ['izin', 'sakit'])
            ->get();

        $absentTeacherIds = $absences->pluck('teacher_id')->unique();

        // Ambil seluruh jadwal mengajar guru yang izin/sakit pada hari tersebut
        $schedules = LessonSchedule::with(['subject', 'schoolClass', 'teacher'])
            ->whereIn('teacher_id', $absentTeacherIds)
            ->where('day', $dayName)
            ->orderBy('start_time')
            ->get();

        // Ambil jurnal yang sudah terisi untuk jadwal-jadwal tersebut pada tanggal ini
        $scheduleIds = $schedules->pluck('id');
        $filledJournals = TeachingJournal::with(['teacher', 'substitutedTeacher'])
            ->whereIn('lesson_schedule_id', $scheduleIds)
            ->where('date', $date)
            ->get()
            ->keyBy('lesson_schedule_id');

        // Susun laporan per guru yang berhalangan hadir
        $reports = $absences->map(function ($att) use ($schedules, $filledJournals) {
            $teacherSchedules = $schedules->where('teacher_id', $att->teacher_id);
            return [
                'attendance' => $att,
                'teacher' => $att->teacher,
                'schedules' => $teacherSchedules,
            ];
        });

        // Metrik hitungan
        $totalAbsentTeachers = $absences->count();
        $totalClassesNeedSub = $schedules->count();
        $filledClassesCount = $filledJournals->count();
        $emptyClassesCount = max(0, $totalClassesNeedSub - $filledClassesCount);

        return view('guru.academic.laporan_izin', compact(
            'currentTeacher',
            'date',
            'carbonDate',
            'dayName',
            'reports',
            'filledJournals',
            'totalAbsentTeachers',
            'totalClassesNeedSub',
            'filledClassesCount',
            'emptyClassesCount'
        ));
    }

    /**
     * Daftar Riwayat Jurnal Mengajar Guru
     */
    public function jurnalIndex(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $search = $request->input('search');
        $classId = $request->input('class_id');
        $month = $request->input('month', now()->format('Y-m'));

        $query = TeachingJournal::with(['schoolClass', 'subject', 'studentAttendances', 'teacher', 'substitutedTeacher', 'lessonSchedule'])
            ->where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)
                  ->orWhere('substituted_teacher_id', $teacher->id);
            })
            ->orderByDesc('date')
            ->orderByDesc('start_time');

        if ($month) {
            $query->where('date', 'like', "{$month}%");
        }

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('materi', 'like', "%{$search}%")
                  ->orWhere('kegiatan', 'like', "%{$search}%")
                  ->orWhereHas('subject', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $journals = $query->paginate(12)->withQueryString();
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();

        // Statistik ringkas pertemuan dan total jam mengajar yang telah digunakan bulan ini
        $monthJournals = TeachingJournal::with('lessonSchedule')
            ->where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)
                  ->orWhere('substituted_teacher_id', $teacher->id);
            })
            ->where('date', 'like', "{$month}%")
            ->get();

        $totalJournalsMonth = $monthJournals->count();
        $totalHoursMonth = $monthJournals->sum('estimated_hours');

        return view('guru.academic.jurnal_index', compact('teacher', 'journals', 'classes', 'month', 'classId', 'search', 'totalJournalsMonth', 'totalHoursMonth'));
    }

    /**
     * Halaman Dedicated Scan QR Kelas
     */
    public function scanKelasPage(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $scheduleId = $request->input('schedule_id');
        $targetSchedule = null;

        if ($scheduleId) {
            $targetSchedule = LessonSchedule::with(['schoolClass', 'subject'])->find($scheduleId);
        }

        return view('guru.academic.scan_kelas', compact('teacher', 'targetSchedule'));
    }

    /**
     * API AJAX Verifikasi QR Code Kelas
     */
    public function verifyClassQr(Request $request)
    {
        $token = trim($request->input('qr_token'));
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Kode QR kosong!'], 422);
        }

        $class = SchoolClass::where('qr_code_token', $token)->first();
        if (!$class) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau bukan QR kelas terdaftar!'], 404);
        }

        // Simpan sesi verifikasi kelas
        session(['verified_class_token_' . $class->id => $class->qr_code_token]);

        $scheduleId = $request->input('schedule_id');
        $teacher = Auth::user()->teacher;
        $todayDay = $this->getIndonesianDayName();
        $matchedSchedule = null;

        if ($scheduleId) {
            $matchedSchedule = LessonSchedule::with(['subject', 'schoolClass'])->find($scheduleId);
        }

        // Jika belum ada jadwal terpilih eksplisit dari URL, otomatis cari jadwal di kelas ini pada jam saat ini!
        if (!$matchedSchedule) {
            // A. Cari jadwal guru ini di kelas ini yang sedang berlangsung saat ini (+/- 25 menit toleransi)
            $matchedSchedule = LessonSchedule::with(['subject', 'schoolClass'])
                ->where('class_id', $class->id)
                ->where('day', $todayDay)
                ->where('teacher_id', $teacher->id)
                ->where('start_time', '<=', Carbon::now()->addMinutes(25)->format('H:i:s'))
                ->where('end_time', '>=', Carbon::now()->subMinutes(25)->format('H:i:s'))
                ->first();

            // B. Jika tidak ada jadwal guru ini, cek apakah ada jadwal kelas ini pada jam sekarang (misal guru inval/pengganti)
            if (!$matchedSchedule) {
                $matchedSchedule = LessonSchedule::with(['subject', 'schoolClass'])
                    ->where('class_id', $class->id)
                    ->where('day', $todayDay)
                    ->where('start_time', '<=', Carbon::now()->addMinutes(25)->format('H:i:s'))
                    ->where('end_time', '>=', Carbon::now()->subMinutes(25)->format('H:i:s'))
                    ->first();
            }

            // C. Jika di luar jam aktif, ambil jadwal terdekat hari ini milik guru ini di kelas ini
            if (!$matchedSchedule) {
                $matchedSchedule = LessonSchedule::with(['subject', 'schoolClass'])
                    ->where('class_id', $class->id)
                    ->where('day', $todayDay)
                    ->where('teacher_id', $teacher->id)
                    ->orderBy('start_time')
                    ->first();
            }

            // D. Jika masih belum ada, ambil jadwal terdekat hari ini di kelas ini
            if (!$matchedSchedule) {
                $matchedSchedule = LessonSchedule::with(['subject', 'schoolClass'])
                    ->where('class_id', $class->id)
                    ->where('day', $todayDay)
                    ->orderBy('start_time')
                    ->first();
            }
        }

        if ($matchedSchedule) {
            $scheduleId = $matchedSchedule->id;
        }

        $redirectUrl = route('guru.jurnal.create', [
            'class_id' => $class->id,
            'qr_token' => $class->qr_code_token,
            'schedule_id' => $scheduleId ?: null,
        ]);

        $message = "Verifikasi Berhasil! Ruang {$class->name} terkonfirmasi.";
        if ($matchedSchedule && $matchedSchedule->subject) {
            $message = "Terverifikasi! Sesi: {$matchedSchedule->subject->name} (" . substr($matchedSchedule->start_time, 0, 5) . " - " . substr($matchedSchedule->end_time, 0, 5) . " WIB).";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'class' => [
                'id' => $class->id,
                'name' => $class->name,
                'grade_level' => $class->grade_level,
            ],
            'qr_token' => $class->qr_code_token,
            'redirect_url' => $redirectUrl,
        ]);
    }

    /**
     * Form Isi Jurnal Baru & Presensi Siswa (Wajib Terverifikasi QR Kelas)
     */
    public function jurnalCreate(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $scheduleId = $request->input('schedule_id');
        $todayDate = now()->toDateString();

        $selectedSchedule = null;
        $selectedClassId = null;
        $selectedSubjectId = null;
        $defaultStartTime = now()->format('H:i');
        $defaultEndTime = now()->addMinutes(90)->format('H:i');

        $qrToken = $request->input('qr_token');
        $scannedClass = null;

        // Jika ada qr_token dari scan barcode kelas, kunci kelas target ke hasil scan
        if ($qrToken) {
            $scannedClass = SchoolClass::where('qr_code_token', $qrToken)->first();
            if ($scannedClass) {
                $selectedClassId = $scannedClass->id;
            }
        }

        if ($scheduleId) {
            $selectedSchedule = LessonSchedule::with(['schoolClass', 'subject'])
                ->find($scheduleId);

            if ($selectedSchedule) {
                if (!$selectedClassId) {
                    $selectedClassId = $selectedSchedule->class_id;
                }
                $selectedSubjectId = $selectedSchedule->subject_id;
                $defaultStartTime = substr($selectedSchedule->start_time, 0, 5);
                $defaultEndTime = substr($selectedSchedule->end_time, 0, 5);
            }
        }

        if (!$selectedClassId && $request->filled('class_id')) {
            $selectedClassId = $request->input('class_id');
        }

        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        // Default kelas pertama jika belum dipilih
        if (!$selectedClassId && $classes->isNotEmpty()) {
            $selectedClassId = $classes->first()->id;
        }

        $targetClass = $scannedClass ?: ($selectedClassId ? SchoolClass::find($selectedClassId) : null);

        // Cek Verifikasi QR Code Kelas
        $sessionKey = $selectedClassId ? 'verified_class_token_' . $selectedClassId : 'none';
        $isQrVerified = false;

        if ($targetClass) {
            if ($qrToken && $qrToken === $targetClass->qr_code_token) {
                session([$sessionKey => $qrToken]);
                $isQrVerified = true;
            } elseif (session($sessionKey) === $targetClass->qr_code_token) {
                $isQrVerified = true;
                $qrToken = $targetClass->qr_code_token;
            }
        }

        // WAJIB SCAN BARCODE: Jika belum scan QR kelas, langsung arahkan ke pemindai kamera!
        if (!$isQrVerified) {
            return redirect()->route('guru.scan_kelas', [
                'schedule_id' => $scheduleId ?: null,
            ])->with('warning', 'Wajib Scan Barcode / QR Code Kelas! Silakan pindai barcode yang terpasang di kelas terlebih dahulu sebelum mengisi jurnal.');
        }

        $students = $selectedClassId 
            ? Student::where('class_id', $selectedClassId)->where('is_active', true)->orderBy('name')->get()
            : collect();

        // Daftar guru lain untuk pilihan Guru Pengganti (Inval)
        $otherTeachers = Teacher::where('is_active', true)->where('id', '!=', $teacher->id)->orderBy('name')->get();

        $todayDay = $this->getIndonesianDayName();

        // Ambil jadwal pelajaran untuk kelas ini (sesuai jam pelajaran)
        $todayClassSchedules = collect();
        $otherDayClassSchedules = collect();

        if ($selectedClassId) {
            // 1. Jadwal di kelas ini hari ini
            $todayClassSchedules = LessonSchedule::with(['subject', 'teacher', 'schoolClass'])
                ->where('class_id', $selectedClassId)
                ->where('day', $todayDay)
                ->orderBy('start_time')
                ->get();

            // Urutkan: jadwal milik guru yang sedang login berada di paling atas
            $todayClassSchedules = $todayClassSchedules->sortByDesc(function($sch) use ($teacher) {
                return $sch->teacher_id == $teacher->id ? 1 : 0;
            })->values();

            // 2. Jadwal di kelas ini pada hari lain (milik guru ini)
            $otherDayClassSchedules = LessonSchedule::with(['subject', 'teacher', 'schoolClass'])
                ->where('class_id', $selectedClassId)
                ->where('day', '!=', $todayDay)
                ->where('teacher_id', $teacher->id)
                ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
                ->orderBy('start_time')
                ->get();

            // Jika belum ada jadwal terpilih eksplisit dari URL, otomatis cocokkan dengan jam pelajaran saat ini!
            if (!$selectedSchedule) {
                $nowTime = Carbon::now()->format('H:i:s');

                // A. Jadwal milik guru ini pada jam pelajaran saat ini (+/- 25 menit toleransi)
                $matchedSchedule = $todayClassSchedules->first(function($sch) use ($teacher, $nowTime) {
                    return $sch->teacher_id == $teacher->id 
                        && $nowTime >= Carbon::parse($sch->start_time)->subMinutes(25)->format('H:i:s')
                        && $nowTime <= Carbon::parse($sch->end_time)->addMinutes(25)->format('H:i:s');
                });

                // B. Jadwal jam saat ini di kelas ini (untuk guru inval / pengganti)
                if (!$matchedSchedule) {
                    $matchedSchedule = $todayClassSchedules->first(function($sch) use ($nowTime) {
                        return $nowTime >= Carbon::parse($sch->start_time)->subMinutes(25)->format('H:i:s')
                            && $nowTime <= Carbon::parse($sch->end_time)->addMinutes(25)->format('H:i:s');
                    });
                }

                // C. Jadwal guru ini hari ini di kelas ini
                if (!$matchedSchedule) {
                    $matchedSchedule = $todayClassSchedules->firstWhere('teacher_id', $teacher->id);
                }

                // D. Jadwal pertama hari ini di kelas ini
                if (!$matchedSchedule) {
                    $matchedSchedule = $todayClassSchedules->first();
                }

                // E. Jadwal hari lain
                if (!$matchedSchedule && $otherDayClassSchedules->isNotEmpty()) {
                    $matchedSchedule = $otherDayClassSchedules->first();
                }

                if ($matchedSchedule) {
                    $selectedSchedule = $matchedSchedule;
                    $selectedSubjectId = $matchedSchedule->subject_id;
                    $defaultStartTime = substr($matchedSchedule->start_time, 0, 5);
                    $defaultEndTime = substr($matchedSchedule->end_time, 0, 5);
                }
            }
        }

        if (!$selectedSubjectId && $subjects->isNotEmpty()) {
            $selectedSubjectId = $subjects->first()->id;
        }

        $isSubstituteAuto = ($selectedSchedule && $selectedSchedule->teacher_id != $teacher->id);
        $substituteForTeacherId = $isSubstituteAuto ? $selectedSchedule->teacher_id : null;

        return view('guru.academic.jurnal_create', compact(
            'teacher',
            'otherTeachers',
            'classes',
            'subjects',
            'selectedSchedule',
            'selectedClassId',
            'selectedSubjectId',
            'todayDay',
            'todayClassSchedules',
            'otherDayClassSchedules',
            'targetClass',
            'defaultStartTime',
            'defaultEndTime',
            'todayDate',
            'students',
            'isQrVerified',
            'qrToken',
            'isSubstituteAuto',
            'substituteForTeacherId'
        ));
    }

    /**
     * Simpan Jurnal & Presensi Siswa Secara Atomik
     */
    public function jurnalStore(Request $request)
    {
        $teacher = Auth::user()->teacher;

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'lesson_schedule_id' => 'nullable|exists:lesson_schedules,id',
            'is_substitute' => 'nullable',
            'substituted_teacher_id' => 'nullable|exists:teachers,id',
            'qr_token' => 'required|string',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'materi' => 'required|string|max:255',
            'kegiatan' => 'required|string',
            'catatan' => 'nullable|string',
            'status' => 'required|in:terlaksana,tugas,daring,tidak_terlaksana',
            'attachment' => 'nullable|image|max:3072',
            'attendances' => 'nullable|array',
            'attendances.*' => 'in:hadir,sakit,izin,alpa',
            'attendance_notes' => 'nullable|array',
        ]);

        // Verifikasi Ketat QR Token Kelas
        $class = SchoolClass::findOrFail($validated['class_id']);
        $sessionKey = 'verified_class_token_' . $class->id;
        if ($validated['qr_token'] !== $class->qr_code_token && session($sessionKey) !== $class->qr_code_token) {
            return back()->withInput()->with('error', "Verifikasi Ditolak! Anda belum melakukan pemindaian (Scan) QR Code di dalam {$class->name}. Silakan pindai QR Code yang terpasang di dinding kelas terlebih dahulu.");
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('journals', 'public');
        }

        $isSubstitute = $request->boolean('is_substitute');
        $substitutedTeacherId = $isSubstitute ? $request->input('substituted_teacher_id') : null;

        DB::beginTransaction();
        try {
            // 1. Simpan Jurnal (Dapat sebagai Guru Pengampu Utama atau Guru Pengganti / Inval)
            $journal = TeachingJournal::create([
                'teacher_id' => $teacher->id,
                'is_substitute' => $isSubstitute,
                'substituted_teacher_id' => $substitutedTeacherId,
                'lesson_schedule_id' => $validated['lesson_schedule_id'] ?? null,
                'class_id' => $validated['class_id'],
                'subject_id' => $validated['subject_id'],
                'date' => $validated['date'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'materi' => $validated['materi'],
                'kegiatan' => $validated['kegiatan'],
                'catatan' => $validated['catatan'] ?? null,
                'attachment' => $attachmentPath,
                'status' => $validated['status'],
            ]);

            // 2. Simpan Presensi Siswa
            $attendancesData = $request->input('attendances', []);
            $attendanceNotes = $request->input('attendance_notes', []);

            // Ambil semua siswa di kelas ini
            $students = Student::where('class_id', $validated['class_id'])->where('is_active', true)->get();

            $waNotifications = [];

            foreach ($students as $student) {
                $status = $attendancesData[$student->id] ?? 'hadir';
                $note = $attendanceNotes[$student->id] ?? null;

                StudentAttendance::create([
                    'teaching_journal_id' => $journal->id,
                    'class_id' => $validated['class_id'],
                    'student_id' => $student->id,
                    'date' => $validated['date'],
                    'status' => $status,
                    'notes' => $note,
                ]);

                // Notifikasi Otomatis ke Orang Tua jika siswa tidak masuk (Alpa / Sakit / Izin)
                if ($status !== 'hadir') {
                    $notifResult = WhatsAppNotificationService::notifyParent($student, $journal, $status, $note);
                    $waNotifications[] = array_merge($notifResult, [
                        'student_name' => $student->name,
                        'status' => $status,
                    ]);
                }
            }

            DB::commit();

            if (!empty($waNotifications)) {
                session()->flash('wa_absent_alerts', $waNotifications);
            }

            $successMsg = 'Jurnal mengajar dan presensi siswa berhasil disimpan!';
            if (!empty($waNotifications)) {
                $alpaCount = collect($waNotifications)->where('status', 'alpa')->count();
                $successMsg .= " Terdeteksi {$alpaCount} siswa Alpa. Notifikasi WhatsApp orang tua telah disiapkan/dikirim.";
            }

            return redirect()->route('guru.jurnal.show', $journal->id)
                ->with('success', $successMsg);
        } catch (\Exception $e) {
            DB::rollBack();
            if ($attachmentPath) {
                Storage::disk('public')->delete($attachmentPath);
            }
            return back()->withInput()->with('error', 'Gagal menyimpan jurnal: ' . $e->getMessage());
        }
    }

    /**
     * Detail Jurnal Mengajar & Kehadiran Siswa
     */
    public function jurnalShow($id)
    {
        $teacher = Auth::user()->teacher;
        $journal = TeachingJournal::with(['schoolClass', 'subject', 'lessonSchedule', 'studentAttendances.student', 'teacher', 'substitutedTeacher'])
            ->where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)
                  ->orWhere('substituted_teacher_id', $teacher->id);
            })
            ->findOrFail($id);

        return view('guru.academic.jurnal_show', compact('teacher', 'journal'));
    }

    /**
     * Hapus Jurnal Mengajar
     */
    public function jurnalDestroy($id)
    {
        $teacher = Auth::user()->teacher;
        $journal = TeachingJournal::where('teacher_id', $teacher->id)->findOrFail($id);

        if ($journal->attachment) {
            Storage::disk('public')->delete($journal->attachment);
        }

        $journal->delete();

        return redirect()->route('guru.jurnal.index')
            ->with('success', 'Jurnal mengajar berhasil dihapus.');
    }

    /**
     * Rekap Presensi Siswa per Kelas
     */
    public function absenSiswaIndex(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $selectedClassId = $request->input('class_id', $classes->first()?->id);
        $month = $request->input('month', now()->format('Y-m'));

        $selectedClass = $selectedClassId ? SchoolClass::find($selectedClassId) : null;
        $students = $selectedClass 
            ? Student::where('class_id', $selectedClass->id)->where('is_active', true)->orderBy('name')->get()
            : collect();

        // Hitung akumulasi kehadiran per siswa untuk bulan yang dipilih
        $studentStats = [];
        if ($selectedClass) {
            foreach ($students as $student) {
                $records = StudentAttendance::where('student_id', $student->id)
                    ->where('date', 'like', "{$month}%")
                    ->get();

                $total = $records->count();
                $hadir = $records->where('status', 'hadir')->count();
                $sakit = $records->where('status', 'sakit')->count();
                $izin = $records->where('status', 'izin')->count();
                $alpa = $records->where('status', 'alpa')->count();
                $pct = $total > 0 ? round(($hadir / $total) * 100) : 100;

                $studentStats[$student->id] = [
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpa' => $alpa,
                    'total' => $total,
                    'persentase' => $pct,
                ];
            }
        }

        return view('guru.academic.absen_siswa', compact(
            'teacher',
            'classes',
            'selectedClass',
            'selectedClassId',
            'month',
            'students',
            'studentStats'
        ));
    }

    /**
     * API AJAX: Mengambil daftar siswa berdasarkan kelas
     */
    public function apiClassStudents($classId)
    {
        $students = Student::where('class_id', $classId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'nis', 'gender']);

        return response()->json($students);
    }
}
