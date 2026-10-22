<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TeachingJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'teacher_id',
        'is_substitute',
        'substituted_teacher_id',
        'lesson_schedule_id',
        'class_id',
        'subject_id',
        'date',
        'start_time',
        'end_time',
        'materi',
        'kegiatan',
        'catatan',
        'attachment',
        'status',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    protected $casts = [
        'date' => 'date',
        'is_substitute' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function substitutedTeacher()
    {
        return $this->belongsTo(Teacher::class, 'substituted_teacher_id');
    }

    public function lessonSchedule()
    {
        return $this->belongsTo(LessonSchedule::class, 'lesson_schedule_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function studentAttendances()
    {
        return $this->hasMany(StudentAttendance::class, 'teaching_journal_id');
    }

    public function getAttachmentUrlAttribute()
    {
        if (!$this->attachment) {
            return null;
        }

        if (str_starts_with($this->attachment, 'http')) {
            return $this->attachment;
        }

        return asset('storage/' . $this->attachment);
    }

    // Hitung ringkasan absensi siswa dalam sesi jurnal ini
    public function getAttendanceSummaryAttribute()
    {
        $attendances = $this->studentAttendances;
        return [
            'total' => $attendances->count(),
            'hadir' => $attendances->where('status', 'hadir')->count(),
            'sakit' => $attendances->where('status', 'sakit')->count(),
            'izin'  => $attendances->where('status', 'izin')->count(),
            'alpa'  => $attendances->where('status', 'alpa')->count(),
        ];
    }

    /**
     * Hitung durasi jam pelajaran (JP) untuk jurnal mengajar ini.
     * Jika terhubung dengan jadwal pelajaran (lessonSchedule), prioritaskan nilai JP dari jadwal.
     * Jika tidak, hitung otomatis dari selisih start_time dan end_time (asumsi 1 JP = 40-45 menit).
     */
    public function getEstimatedHoursAttribute(): int
    {
        if ($this->lessonSchedule && $this->lessonSchedule->estimated_hours) {
            return (int) $this->lessonSchedule->estimated_hours;
        }

        if ($this->start_time && $this->end_time) {
            $start = \Carbon\Carbon::parse($this->start_time);
            $end = \Carbon\Carbon::parse($this->end_time);
            $diffMinutes = $start->diffInMinutes($end);

            if ($diffMinutes <= 0) {
                return 1;
            }

            $hours = (int) round($diffMinutes / 45);
            return max(1, $hours);
        }

        return 1;
    }

    /**
     * Label durasi lengkap
     * Contoh: "2 Jam Mata Pelajaran"
     */
    public function getDurationLabelAttribute(): string
    {
        return $this->estimated_hours . ' Jam Mata Pelajaran';
    }

    /**
     * Label jam pelajaran
     * Contoh: "2 Jam Pelajaran"
     */
    public function getDurationPelajaranAttribute(): string
    {
        return $this->estimated_hours . ' Jam Pelajaran';
    }

    /**
     * Label singkat
     * Contoh: "2 JP"
     */
    public function getDurationShortLabelAttribute(): string
    {
        return $this->estimated_hours . ' JP';
    }
}
