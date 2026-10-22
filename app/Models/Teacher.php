<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'nuptk',
        'name',
        'subject',
        'employment_status',
        'education',
        'class_duty',
        'gender',
        'phone',
        'address',
        'birth_place',
        'birth_date',
        'email',
        'photo',
        'face_registered_at',
        'face_data',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'birth_date' => 'date',
        'face_registered_at' => 'datetime',
    ];

    public function getIsFaceRegisteredAttribute(): bool
    {
        return !empty($this->photo) && !empty($this->face_data);
    }

    public function getFaceDescriptorArrayAttribute(): ?array
    {
        if (empty($this->face_data)) {
            return null;
        }
        $decoded = json_decode($this->face_data, true);
        return is_array($decoded) ? $decoded : null;
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function todayAttendance()
    {
        return $this->hasOne(Attendance::class)->where('date', now()->toDateString());
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function lessonSchedules()
    {
        return $this->hasMany(LessonSchedule::class);
    }

    public function teachingJournals()
    {
        return $this->hasMany(TeachingJournal::class);
    }

    public function homeroomClasses()
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            if (file_exists(public_path($this->photo))) {
                return asset($this->photo) . '?v=' . filemtime(public_path($this->photo));
            }
            if (file_exists(public_path('storage/' . $this->photo))) {
                return asset('storage/' . $this->photo) . '?v=' . filemtime(public_path('storage/' . $this->photo));
            }
        }
        return null;
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', trim(preg_replace('/^(Drs\.|Dra\.|H\.|Hj\.|Ust\.)\s*/i', '', $this->name)));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $w) {
            if (!empty($w)) {
                $initials .= strtoupper(substr($w, 0, 1));
            }
        }
        return $initials ?: 'GU';
    }
}
