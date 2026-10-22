<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'day',
        'class_id',
        'subject_id',
        'teacher_id',
        'start_time',
        'end_time',
        'total_hours',
        'room',
        'notes',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function journals()
    {
        return $this->hasMany(TeachingJournal::class, 'lesson_schedule_id');
    }

    public function getFormattedTimeAttribute()
    {
        return substr($this->start_time, 0, 5) . ' - ' . substr($this->end_time, 0, 5);
    }

    /**
     * Hitung durasi jam pelajaran (JP).
     * Jika total_hours terisi di DB, prioritaskan itu.
     * Jika null, hitung otomatis dari selisih start_time dan end_time (asumsi 1 JP = 40-45 menit).
     */
    public function getEstimatedHoursAttribute(): int
    {
        if ($this->total_hours && $this->total_hours > 0) {
            return (int) $this->total_hours;
        }

        if ($this->start_time && $this->end_time) {
            $start = \Carbon\Carbon::parse($this->start_time);
            $end = \Carbon\Carbon::parse($this->end_time);
            $diffMinutes = $start->diffInMinutes($end);

            if ($diffMinutes <= 0) {
                return 1;
            }

            // Contoh: <= 55 menit = 1 JP, 56-105 menit = 2 JP, 106-150 menit = 3 JP, dst.
            $hours = (int) round($diffMinutes / 45);
            return max(1, $hours);
        }

        return 1;
    }

    /**
     * Label durasi lengkap: contoh "2 Jam Pelajaran" atau "1 Jam Pelajaran"
     */
    public function getDurationLabelAttribute(): string
    {
        return $this->estimated_hours . ' Jam Mata Pelajaran';
    }

    /**
     * Label durasi jam pelajaran: contoh "2 Jam Pelajaran"
     */
    public function getDurationPelajaranAttribute(): string
    {
        return $this->estimated_hours . ' Jam Pelajaran';
    }

    /**
     * Label singkat: contoh "2 JP"
     */
    public function getDurationShortLabelAttribute(): string
    {
        return $this->estimated_hours . ' JP';
    }
}
