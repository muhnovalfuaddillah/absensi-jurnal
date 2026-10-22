<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'teacher_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'attendance_method',
        'photo_in',
        'photo_out',
        'latitude_in',
        'longitude_in',
        'latitude_out',
        'longitude_out',
        'late_minutes',
        'early_leave_minutes',
        'notes',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function getPhotoInUrlAttribute(): ?string
    {
        if ($this->photo_in) {
            if (str_starts_with($this->photo_in, 'data:image')) {
                return $this->photo_in;
            }
            if (file_exists(public_path($this->photo_in))) {
                return asset($this->photo_in);
            }
            if (file_exists(public_path('storage/' . $this->photo_in))) {
                return asset('storage/' . $this->photo_in);
            }
        }
        return null;
    }

    public function getPhotoOutUrlAttribute(): ?string
    {
        if ($this->photo_out) {
            if (str_starts_with($this->photo_out, 'data:image')) {
                return $this->photo_out;
            }
            if (file_exists(public_path($this->photo_out))) {
                return asset($this->photo_out);
            }
            if (file_exists(public_path('storage/' . $this->photo_out))) {
                return asset('storage/' . $this->photo_out);
            }
        }
        return null;
    }

    protected $casts = [
        'date' => 'date',
        'late_minutes' => 'integer',
        'early_leave_minutes' => 'integer',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function getDurationAttribute()
    {
        if ($this->time_in && $this->time_out) {
            $in = Carbon::parse($this->time_in);
            $out = Carbon::parse($this->time_out);
            $diffMinutes = $in->diffInMinutes($out);
            $hours = floor($diffMinutes / 60);
            $minutes = $diffMinutes % 60;
            return "{$hours} jam {$minutes} mnt";
        }
        return '-';
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'hadir' => 'Tepat Waktu',
            'terlambat' => 'Terlambat (' . $this->late_minutes . ' mnt)',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            'alpa' => 'Alpa / Tanpa Ket.',
            default => ucfirst($this->status),
        };
    }
}
