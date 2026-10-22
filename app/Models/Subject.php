<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'code',
        'name',
        'description',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function lessonSchedules()
    {
        return $this->hasMany(LessonSchedule::class, 'subject_id');
    }

    public function teachingJournals()
    {
        return $this->hasMany(TeachingJournal::class, 'subject_id');
    }
}
