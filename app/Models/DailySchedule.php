<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailySchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_setting_id',
        'day_of_week',
        'day_name',
        'is_active',
        'time_in',
        'time_in_late_threshold',
        'time_out',
        'note',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'day_of_week' => 'integer',
    ];

    public function setting()
    {
        return $this->belongsTo(AttendanceSetting::class, 'attendance_setting_id');
    }

    public function getTimeInFormattedAttribute()
    {
        return substr($this->time_in, 0, 5);
    }

    public function getLateThresholdFormattedAttribute()
    {
        return substr($this->time_in_late_threshold, 0, 5);
    }

    public function getTimeOutFormattedAttribute()
    {
        return substr($this->time_out, 0, 5);
    }
}
