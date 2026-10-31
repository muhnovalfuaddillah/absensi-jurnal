<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'nip',
        'nuptk',
        'name',
        'subject',
        'employment_status',
        'teaches_in_foundation',
        'foundation_name',
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

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'school_teacher')
            ->withPivot('is_primary', 'subject', 'class_duty')
            ->withTimestamps();
    }

    public function hasSchool($schoolId): bool
    {
        if ((int) $this->school_id === (int) $schoolId) {
            return true;
        }
        return $this->schools()->where('schools.id', $schoolId)->exists();
    }

    public function getAssignedSchoolsAttribute()
    {
        $pivotSchools = $this->schools()->where('schools.is_active', true)->get();
        if ($this->school_id && !$pivotSchools->contains('id', $this->school_id)) {
            $mainSchool = $this->school;
            if ($mainSchool) {
                $pivotSchools->prepend($mainSchool);
            }
        }
        return $pivotSchools;
    }

    public function isMultiSchool(): bool
    {
        return $this->assigned_schools->count() > 1;
    }

    public function getFoundationNameAttribute($value): ?string
    {
        if (!empty($value)) {
            return $value;
        }
        // Fallback jika mengajar di yayasan
        if ($this->attributes['teaches_in_foundation'] ?? true) {
            if ($this->school && !empty($this->school->foundation_name)) {
                return $this->school->foundation_name;
            }
            $setting = AttendanceSetting::getGlobalSetting();
            return $setting->foundation_name ?? 'Yayasan Bahrul Ulum';
        }
        return null;
    }

    public function getFoundationStatusLabelAttribute(): string
    {
        if ($this->teaches_in_foundation) {
            $name = $this->foundation_name ?: 'Yayasan';
            return "Mengajar di Yayasan ({$name})";
        }
        return 'Non-Yayasan';
    }

    protected $casts = [
        'is_active' => 'boolean',
        'teaches_in_foundation' => 'boolean',
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
