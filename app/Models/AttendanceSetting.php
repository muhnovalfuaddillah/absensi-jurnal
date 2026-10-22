<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_name',
        'foundation_name',
        'npsn',
        'school_address',
        'school_phone',
        'school_email',
        'headmaster_name',
        'headmaster_nip',
        'website',
        'logo_path',
        'latitude',
        'longitude',
        'radius_meters',
        'is_geofence_active',
        'time_in',
        'time_in_late_threshold',
        'time_out',
        'work_days',
        'wa_gateway_token',
        'wa_gateway_url',
    ];

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

    protected $casts = [
        'is_geofence_active' => 'boolean',
        'radius_meters' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    /**
     * Properti runtime (bukan kolom database)
     */
    public bool $is_holiday_today = false;
    public ?DailySchedule $today_schedule = null;

    public function dailySchedules()
    {
        return $this->hasMany(DailySchedule::class, 'attendance_setting_id')->orderBy('day_of_week');
    }

    /**
     * Dapatkan instance AttendanceSetting lengkap dengan jadwal harian hari ini
     */
    public static function getSettings()
    {
        $setting = static::firstOrCreate([], [
            'school_name' => 'Yayasan Barul Ulum',
            'time_in' => '07:00:00',
            'time_in_late_threshold' => '07:15:00',
            'time_out' => '14:30:00',
            'work_days' => 'Senin - Jumat',
        ]);

        // Pastikan 7 hari jadwal (Senin s/d Minggu) sudah terinisialisasi
        $setting->ensureDailySchedulesInitialized();

        // Cari jadwal spesifik untuk hari ini
        $todaySchedule = $setting->getScheduleForDate(now());

        $setting->today_schedule = $todaySchedule;
        $setting->is_holiday_today = !($todaySchedule && $todaySchedule->is_active);

        // Pastikan state model bersih dari dirty attributes
        $setting->syncOriginal();

        return $setting;
    }

    /**
     * Accessor dinamis jam masuk: ikuti jadwal hari ini jika aktif
     */
    public function getTimeInAttribute($value)
    {
        if (isset($this->today_schedule) && $this->today_schedule && $this->today_schedule->is_active) {
            return $this->today_schedule->time_in;
        }
        return $value;
    }

    /**
     * Accessor dinamis batas toleransi: ikuti jadwal hari ini jika aktif
     */
    public function getTimeInLateThresholdAttribute($value)
    {
        if (isset($this->today_schedule) && $this->today_schedule && $this->today_schedule->is_active) {
            return $this->today_schedule->time_in_late_threshold;
        }
        return $value;
    }

    /**
     * Accessor dinamis jam pulang: ikuti jadwal hari ini jika aktif
     */
    public function getTimeOutAttribute($value)
    {
        if (isset($this->today_schedule) && $this->today_schedule && $this->today_schedule->is_active) {
            return $this->today_schedule->time_out;
        }
        return $value;
    }

    /**
     * Dapatkan jadwal spesifik berdasarkan tanggal tertentu
     */
    public function getScheduleForDate($date = null)
    {
        $carbon = $date ? Carbon::parse($date) : now();
        $dayIso = $carbon->dayOfWeekIso; // 1 = Senin, ..., 7 = Minggu

        return $this->dailySchedules()->where('day_of_week', $dayIso)->first();
    }

    /**
     * Pastikan 7 hari jadwal kerja default tersedia
     */
    public function ensureDailySchedulesInitialized()
    {
        $count = $this->dailySchedules()->count();
        if ($count === 7) {
            return;
        }

        $defaultDays = [
            1 => ['name' => 'Senin',  'active' => true,  'in' => '06:45:00', 'late' => '07:00:00', 'out' => '14:30:00', 'note' => 'Upacara Bendera Pagi'],
            2 => ['name' => 'Selasa', 'active' => true,  'in' => '07:00:00', 'late' => '07:15:00', 'out' => '14:30:00', 'note' => 'KBM Reguler'],
            3 => ['name' => 'Rabu',   'active' => true,  'in' => '07:00:00', 'late' => '07:15:00', 'out' => '14:30:00', 'note' => 'KBM Reguler'],
            4 => ['name' => 'Kamis',  'active' => true,  'in' => '07:00:00', 'late' => '07:15:00', 'out' => '14:30:00', 'note' => 'KBM Reguler'],
            5 => ['name' => 'Jumat',  'active' => true,  'in' => '07:00:00', 'late' => '07:15:00', 'out' => '11:30:00', 'note' => 'Jumat Berkah / Sholat Jumat'],
            6 => ['name' => 'Sabtu',  'active' => false, 'in' => '07:00:00', 'late' => '07:15:00', 'out' => '13:00:00', 'note' => 'Libur / Ekstrakurikuler'],
            7 => ['name' => 'Minggu', 'active' => false, 'in' => '07:00:00', 'late' => '07:15:00', 'out' => '14:00:00', 'note' => 'Libur Akhir Pekan'],
        ];

        foreach ($defaultDays as $dayNumber => $dayData) {
            DailySchedule::firstOrCreate(
                [
                    'attendance_setting_id' => $this->id,
                    'day_of_week' => $dayNumber,
                ],
                [
                    'day_name' => $dayData['name'],
                    'is_active' => $dayData['active'],
                    'time_in' => $dayData['in'],
                    'time_in_late_threshold' => $dayData['late'],
                    'time_out' => $dayData['out'],
                    'note' => $dayData['note'],
                ]
            );
        }
    }

    /**
     * Hitung jarak Haversine (dalam meter) antara koordinat guru dan koordinat sekolah
     */
    public function calculateDistanceTo($latitude, $longitude): float
    {
        if (!$this->latitude || !$this->longitude || !$latitude || !$longitude) {
            return 0.0;
        }

        $earthRadius = 6371000; // Radius bumi dalam meter
        $latFrom = deg2rad((float)$this->latitude);
        $lonFrom = deg2rad((float)$this->longitude);
        $latTo = deg2rad((float)$latitude);
        $lonTo = deg2rad((float)$longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return round($angle * $earthRadius, 1);
    }

    /**
     * Cek apakah koordinat guru berada dalam radius geofence yang ditentukan admin
     */
    public function isWithinGeofence($latitude, $longitude): array
    {
        // Jika geofence dinonaktifkan atau titik sekolah belum diisi
        if (!$this->is_geofence_active || !$this->latitude || !$this->longitude) {
            return ['allowed' => true, 'distance' => 0, 'radius' => $this->radius_meters ?? 150];
        }

        if (empty($latitude) || empty($longitude)) {
            return [
                'allowed' => false,
                'distance' => null,
                'radius' => (float)($this->radius_meters ?? 150),
                'message' => 'Titik lokasi GPS Anda tidak terdeteksi. Harap aktifkan izin lokasi/GPS pada perangkat Anda agar sistem dapat memvalidasi kehadiran di sekolah.'
            ];
        }

        $distance = $this->calculateDistanceTo($latitude, $longitude);
        $allowedRadius = (float)($this->radius_meters ?? 150);

        if ($distance > $allowedRadius) {
            return [
                'allowed' => false,
                'distance' => round($distance),
                'radius' => $allowedRadius,
                'message' => "Posisi Anda berada di luar radius sekolah ({$distance} meter). Admin menetapkan batas radius maksimal {$allowedRadius} meter dari {$this->school_name}."
            ];
        }

        return [
            'allowed' => true,
            'distance' => round($distance),
            'radius' => $allowedRadius
        ];
    }
}
