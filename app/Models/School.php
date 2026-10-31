<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'npsn',
        'foundation_name',
        'address',
        'phone',
        'email',
        'headmaster_name',
        'headmaster_nip',
        'website',
        'logo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($school) {
            if (empty($school->slug)) {
                $school->slug = Str::slug($school->name) ?: 'sekolah-' . Str::random(5);
            }
        });

        // Saat sekolah baru dibuat, otomatis buatkan AttendanceSetting & 7 hari DailySchedule
        static::created(function ($school) {
            $setting = AttendanceSetting::firstOrCreate(
                ['school_id' => $school->id],
                [
                    'school_name' => $school->name,
                    'foundation_name' => $school->foundation_name,
                    'npsn' => $school->npsn,
                    'school_address' => $school->address,
                    'school_phone' => $school->phone,
                    'school_email' => $school->email,
                    'headmaster_name' => $school->headmaster_name,
                    'headmaster_nip' => $school->headmaster_nip,
                    'website' => $school->website,
                    'logo_path' => $school->logo_path,
                    'time_in' => '07:00:00',
                    'time_in_late_threshold' => '07:15:00',
                    'time_out' => '14:30:00',
                    'work_days' => 'Senin - Jumat',
                    'radius_meters' => 150,
                    'is_geofence_active' => true,
                ]
            );

            $setting->ensureDailySchedulesInitialized();
        });
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path) {
            if (file_exists(public_path($this->logo_path))) {
                return asset($this->logo_path) . '?v=' . filemtime(public_path($this->logo_path));
            }
            if (file_exists(public_path('storage/' . $this->logo_path))) {
                return asset('storage/' . $this->logo_path) . '?v=' . filemtime(public_path('storage/' . $this->logo_path));
            }
        }
        if (file_exists(public_path('logo.jpg'))) {
            return asset('logo.jpg') . '?v=' . filemtime(public_path('logo.jpg'));
        }
        return asset('logo.jpg');
    }

    public function setting()
    {
        return $this->hasOne(AttendanceSetting::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function assignedTeachers()
    {
        return $this->belongsToMany(Teacher::class, 'school_teacher')
            ->withPivot('is_primary', 'subject', 'class_duty')
            ->withTimestamps();
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function announcements()
    {
        return $this->hasMany(SchoolAnnouncement::class);
    }

    public function lessonSchedules()
    {
        return $this->hasMany(LessonSchedule::class);
    }

    public function teachingJournals()
    {
        return $this->hasMany(TeachingJournal::class);
    }
}
