<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'nis',
        'nisn',
        'name',
        'gender',
        'parent_name',
        'parent_phone',
        'status',
        'is_active',
    ];

    public function getFormattedParentPhoneAttribute(): ?string
    {
        if (!$this->parent_phone) {
            return null;
        }

        // Hapus karakter non-digit
        $cleaned = preg_replace('/[^0-9]/', '', $this->parent_phone);
        
        // Ubah awalan 0 menjadi 62
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        }

        return $cleaned;
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class, 'student_id');
    }
}
