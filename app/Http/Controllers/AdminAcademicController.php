<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSetting;
use App\Models\LessonSchedule;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingJournal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminAcademicController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 1. MANAJEMEN JADWAL PELAJARAN (LESSON SCHEDULES)
    |--------------------------------------------------------------------------
    */
    public function schedulesIndex(Request $request)
    {
        $day = $request->input('day');
        $classId = $request->input('class_id');
        $teacherId = $request->input('teacher_id');
        $schoolId = current_school_id();

        $query = LessonSchedule::where('school_id', $schoolId)
            ->with(['schoolClass', 'subject', 'teacher'])
            ->orderByRaw("FIELD(day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('start_time');

        if ($day) {
            $query->where('day', $day);
        }
        if ($classId) {
            $query->where('class_id', $classId);
        }
        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        $schedules = $query->paginate(20)->withQueryString();

        $classes = SchoolClass::where('school_id', $schoolId)->where('is_active', true)->orderBy('name')->get();
        $teachers = Teacher::where('is_active', true)
            ->where(function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)
                  ->orWhereHas('schools', fn($s) => $s->where('schools.id', $schoolId));
            })
            ->orderBy('name')
            ->get();
        $subjects = Subject::where('school_id', $schoolId)->orderBy('name')->get();

        return view('admin.academic.schedules_index', compact('schedules', 'classes', 'teachers', 'subjects', 'day', 'classId', 'teacherId'));
    }

    public function schedulesStore(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'total_hours' => 'nullable|integer|min:1|max:12',
            'room' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        $validated['school_id'] = current_school_id();
        LessonSchedule::create($validated);

        return back()->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function schedulesUpdate(Request $request, $id)
    {
        $schedule = LessonSchedule::findOrFail($id);

        $validated = $request->validate([
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'total_hours' => 'nullable|integer|min:1|max:12',
            'room' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:255',
        ]);

        $schedule->update($validated);

        return back()->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function schedulesDestroy($id)
    {
        $schedule = LessonSchedule::findOrFail($id);
        $schedule->delete();

        return back()->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | 2. MANAJEMEN DATA KELAS & SISWA
    |--------------------------------------------------------------------------
    */
    public function classesIndex(Request $request)
    {
        $schoolId = current_school_id();

        $classes = SchoolClass::where('school_id', $schoolId)
            ->with(['homeroomTeacher'])
            ->withCount('students')
            ->orderBy('name')
            ->get();

        $teachers = Teacher::where('is_active', true)
            ->where(function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId)
                  ->orWhereHas('schools', fn($s) => $s->where('schools.id', $schoolId));
            })
            ->orderBy('name')
            ->get();

        return view('admin.academic.classes_index', compact('classes', 'teachers'));
    }

    public function classesStore(Request $request)
    {
        $schoolId = current_school_id();

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'grade_level' => 'required|string|max:20',
            'academic_year' => 'required|string|max:20',
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $validated['school_id'] = $schoolId;
        SchoolClass::create($validated);

        return back()->with('success', "Kelas {$validated['name']} berhasil dibuat.");
    }

    public function classesUpdate(Request $request, $id)
    {
        $class = SchoolClass::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'grade_level' => 'required|string|max:20',
            'academic_year' => 'required|string|max:20',
            'homeroom_teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $class->update($validated);

        return back()->with('success', "Data kelas {$class->name} berhasil diperbarui.");
    }

    public function classesDestroy($id)
    {
        $class = SchoolClass::findOrFail($id);
        $name = $class->name;
        $class->delete();

        return back()->with('success', "Kelas {$name} beserta siswa dan jadwalnya berhasil dihapus.");
    }

    public function printClassQr($id)
    {
        $class = SchoolClass::with('homeroomTeacher')->findOrFail($id);
        $setting = \App\Models\AttendanceSetting::getSettings(current_school_id());

        return view('admin.academic.class_qr_print', compact('class', 'setting'));
    }

    public function studentsIndex(Request $request)
    {
        $classId = $request->input('class_id');
        $search = $request->input('search');
        $schoolId = current_school_id();

        $query = Student::where('school_id', $schoolId)->with('schoolClass')->orderBy('name');

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(25)->withQueryString();
        $classes = SchoolClass::where('school_id', $schoolId)->where('is_active', true)->orderBy('name')->get();

        return view('admin.academic.students_index', compact('students', 'classes', 'classId', 'search'));
    }

    public function studentsStore(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => 'required|string|max:150',
            'nis' => 'nullable|string|max:30',
            'nisn' => 'nullable|string|max:30',
            'gender' => 'required|in:L,P',
            'parent_name' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:30',
            'status' => 'nullable|in:aktif,lulus,mutasi,keluar',
        ]);

        $validated['status'] = $validated['status'] ?? 'aktif';
        $validated['school_id'] = current_school_id();

        Student::create($validated);

        return back()->with('success', "Siswa {$validated['name']} berhasil didaftarkan.");
    }

    public function studentsUpdate(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'name' => 'required|string|max:150',
            'nis' => 'nullable|string|max:30',
            'nisn' => 'nullable|string|max:30',
            'gender' => 'required|in:L,P',
            'parent_name' => 'nullable|string|max:100',
            'parent_phone' => 'nullable|string|max:30',
            'status' => 'nullable|in:aktif,lulus,mutasi,keluar',
        ]);

        $student->update($validated);

        return back()->with('success', "Data siswa {$student->name} berhasil diperbarui.");
    }

    /**
     * Fitur Kenaikan Kelas & Mutasi / Kelulusan Massal
     */
    public function studentsPromote(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:promote,graduate',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'target_class_id' => 'nullable|required_if:action,promote|exists:classes,id',
        ], [
            'student_ids.required' => 'Pilih minimal satu siswa untuk diproses.',
            'target_class_id.required_if' => 'Pilih kelas tujuan kenaikan kelas.',
        ]);

        $count = count($validated['student_ids']);

        if ($validated['action'] === 'promote') {
            $targetClass = SchoolClass::findOrFail($validated['target_class_id']);

            Student::whereIn('id', $validated['student_ids'])->update([
                'class_id' => $targetClass->id,
                'status' => 'aktif',
                'is_active' => true,
            ]);

            return back()->with('success', "Berhasil menaikkan / memindahkan {$count} siswa ke {$targetClass->name} ({$targetClass->academic_year}).");
        } elseif ($validated['action'] === 'graduate') {
            Student::whereIn('id', $validated['student_ids'])->update([
                'status' => 'lulus',
                'is_active' => false,
            ]);

            return back()->with('success', "Berhasil memperbarui status {$count} siswa menjadi LULUS / Alumni.");
        }

        return back()->with('error', 'Aksi tidak valid.');
    }

    public function studentsDestroy($id)
    {
        $student = Student::findOrFail($id);
        $name = $student->name;
        $student->delete();

        return back()->with('success', "Data siswa {$name} berhasil dihapus.");
    }

    /*
    |--------------------------------------------------------------------------
    | 3. MONITORING JURNAL GURU (TEACHING JOURNALS)
    |--------------------------------------------------------------------------
    */
    public function journalsIndex(Request $request)
    {
        $date = $request->input('date');
        $teacherId = $request->input('teacher_id');
        $classId = $request->input('class_id');
        $search = $request->input('search');
        $schoolId = current_school_id();

        $query = TeachingJournal::where('school_id', $schoolId)
            ->with(['teacher', 'schoolClass', 'subject', 'studentAttendances', 'lessonSchedule'])
            ->orderByDesc('date')
            ->orderByDesc('start_time');

        if ($date) {
            $query->where('date', $date);
        }
        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }
        if ($classId) {
            $query->where('class_id', $classId);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('materi', 'like', "%{$search}%")
                  ->orWhere('kegiatan', 'like', "%{$search}%");
            });
        }

        // Ambil data untuk akumulasi jumlah jam dan sesi
        $allFiltered = (clone $query)->get();
        $totalSessions = $allFiltered->count();
        $totalHoursTaught = $allFiltered->sum('estimated_hours');
        $uniqueTeachersCount = $allFiltered->pluck('teacher_id')->unique()->count();

        // Rekapitulasi jam yang telah digunakan per guru
        $teacherStats = $allFiltered->groupBy('teacher_id')->map(function ($group) {
            $teacher = $group->first()->teacher;
            return [
                'teacher' => $teacher,
                'sessions_count' => $group->count(),
                'total_hours' => $group->sum('estimated_hours'),
            ];
        })->sortByDesc('total_hours')->values();

        $selectedTeacher = $teacherId ? Teacher::find($teacherId) : null;

        $journals = $query->paginate(15)->withQueryString();
        $teachers = Teacher::where('school_id', $schoolId)->where('is_active', true)->orderBy('name')->get();
        $classes = SchoolClass::where('school_id', $schoolId)->where('is_active', true)->orderBy('name')->get();

        return view('admin.academic.journals_index', compact(
            'journals',
            'teachers',
            'classes',
            'date',
            'teacherId',
            'classId',
            'search',
            'totalSessions',
            'totalHoursTaught',
            'uniqueTeachersCount',
            'teacherStats',
            'selectedTeacher'
        ));
    }

    public function journalsShow($id)
    {
        $journal = TeachingJournal::with(['teacher', 'schoolClass', 'subject', 'lessonSchedule', 'studentAttendances.student'])
            ->findOrFail($id);

        return view('admin.academic.journals_show', compact('journal'));
    }

    /*
    |--------------------------------------------------------------------------
    | 4. MONITORING PRESENSI SISWA (STUDENT ATTENDANCES)
    |--------------------------------------------------------------------------
    */
    public function studentAttendanceRecap(Request $request)
    {
        $schoolId = current_school_id();
        $classes = SchoolClass::where('school_id', $schoolId)->where('is_active', true)->orderBy('name')->get();
        $selectedClassId = $request->input('class_id', $classes->first()?->id);
        $month = $request->input('month', now()->format('Y-m'));

        $selectedClass = $selectedClassId ? SchoolClass::find($selectedClassId) : null;
        $students = $selectedClass 
            ? Student::where('class_id', $selectedClass->id)->where('is_active', true)->orderBy('name')->get()
            : collect();

        $stats = [];
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

                $stats[$student->id] = [
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpa' => $alpa,
                    'total' => $total,
                    'persentase' => $pct,
                ];
            }
        }

        $setting = AttendanceSetting::getSettings($schoolId);

        return view('admin.academic.student_attendance_recap', compact('classes', 'selectedClass', 'selectedClassId', 'month', 'students', 'stats', 'setting'));
    }

    /**
     * Export Rekapitulasi Presensi Siswa per Kelas ke Excel (.xls)
     */
    public function studentAttendanceExportExcel(Request $request)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $selectedClassId = $request->input('class_id', $classes->first()?->id);
        $month = $request->input('month', now()->format('Y-m'));

        $selectedClass = $selectedClassId ? SchoolClass::find($selectedClassId) : null;
        $students = $selectedClass 
            ? Student::where('class_id', $selectedClass->id)->where('is_active', true)->orderBy('name')->get()
            : collect();

        $stats = [];
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

                $stats[$student->id] = [
                    'hadir' => $hadir,
                    'sakit' => $sakit,
                    'izin' => $izin,
                    'alpa' => $alpa,
                    'total' => $total,
                    'persentase' => $pct,
                ];
            }
        }

        $setting = AttendanceSetting::getSettings();
        $monthLabel = \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y');
        $className = $selectedClass ? preg_replace('/[^A-Za-z0-9_-]/', '_', $selectedClass->name) : 'Kelas';
        $filename = "Rekap_Presensi_Siswa_{$className}_{$month}.xls";

        return response()->view('admin.academic.student_attendance_export_excel', compact(
            'selectedClass',
            'students',
            'stats',
            'setting',
            'month',
            'monthLabel'
        ))->withHeaders([
            'Content-Type' => 'application/vnd.ms-excel; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
            'Pragma' => 'public',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | MANAJEMEN MATA PELAJARAN (SUBJECTS)
    |--------------------------------------------------------------------------
    */
    public function subjectsIndex(Request $request)
    {
        $search = $request->input('search');
        $schoolId = current_school_id();

        $query = Subject::where('school_id', $schoolId)
            ->withCount(['lessonSchedules', 'teachingJournals'])
            ->orderBy('name');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $subjects = $query->paginate(15)->withQueryString();

        $totalSubjects = Subject::where('school_id', $schoolId)->count();
        $totalSchedules = LessonSchedule::where('school_id', $schoolId)->count();
        $totalJournals = TeachingJournal::where('school_id', $schoolId)->count();

        return view('admin.academic.subjects_index', compact('subjects', 'search', 'totalSubjects', 'totalSchedules', 'totalJournals'));
    }

    public function subjectsStore(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ], [
            'code.required' => 'Kode mata pelajaran wajib diisi.',
            'name.required' => 'Nama mata pelajaran wajib diisi.',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['school_id'] = current_school_id();

        Subject::create($validated);

        return back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function subjectsUpdate(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:subjects,code,' . $id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ], [
            'code.required' => 'Kode mata pelajaran wajib diisi.',
            'code.unique' => 'Kode mata pelajaran sudah digunakan.',
            'name.required' => 'Nama mata pelajaran wajib diisi.',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));

        $subject->update($validated);

        return back()->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function subjectsDestroy($id)
    {
        $subject = Subject::withCount(['lessonSchedules', 'teachingJournals'])->findOrFail($id);

        if ($subject->lesson_schedules_count > 0 || $subject->teaching_journals_count > 0) {
            return back()->with('error', "Mata pelajaran '{$subject->name}' tidak dapat dihapus karena masih terhubung dengan {$subject->lesson_schedules_count} jadwal dan {$subject->teaching_journals_count} jurnal mengajar.");
        }

        $subject->delete();

        return back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
