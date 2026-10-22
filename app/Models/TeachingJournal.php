<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TeachingJournal extends Model
{
    use HasFactory;

    protected $fillable = [
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
}
