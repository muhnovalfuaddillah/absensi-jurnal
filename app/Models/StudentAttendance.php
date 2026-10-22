<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'teaching_journal_id',
        'class_id',
        'student_id',
        'date',
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function teachingJournal()
    {
        return $this->belongsTo(TeachingJournal::class, 'teaching_journal_id');
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'hadir' => ['label' => 'Hadir', 'color' => 'bg-emerald-100 text-emerald-800 border-emerald-300'],
            'sakit' => ['label' => 'Sakit', 'color' => 'bg-amber-100 text-amber-800 border-amber-300'],
            'izin'  => ['label' => 'Izin', 'color' => 'bg-blue-100 text-blue-800 border-blue-300'],
            'alpa'  => ['label' => 'Alpa', 'color' => 'bg-rose-100 text-rose-800 border-rose-300'],
            default => ['label' => ucfirst($this->status), 'color' => 'bg-slate-100 text-slate-800 border-slate-300'],
        };
    }
}
