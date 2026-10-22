<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'qr_code_token',
        'grade_level',
        'homeroom_teacher_id',
        'academic_year',
        'is_active',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->qr_code_token)) {
                $model->qr_code_token = 'CLS-' . strtoupper(\Illuminate\Support\Str::slug($model->name)) . '-' . strtoupper(\Illuminate\Support\Str::random(8));
            }
        });
    }

    public function getQrCodeImageUrlAttribute(): string
    {
        $payload = urlencode($this->qr_code_token);
        return "https://api.qrserver.com/v1/create-qr-code/?size=350x350&margin=15&data={$payload}";
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function homeroomTeacher()
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id')->orderBy('name');
    }

    public function lessonSchedules()
    {
        return $this->hasMany(LessonSchedule::class, 'class_id');
    }

    public function teachingJournals()
    {
        return $this->hasMany(TeachingJournal::class, 'class_id');
    }
}
