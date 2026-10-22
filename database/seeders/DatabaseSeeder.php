<?php

namespace Database\Seeders;

use App\Models\AttendanceSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Administrator Sistem
        User::updateOrCreate(
            ['email' => 'admin@sekolah.sch.id'],
            [
                'name' => 'Administrator Presensi',
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'teacher_id' => null,
            ]
        );

        // 2. Pengaturan Jam Kerja Default
        AttendanceSetting::firstOrCreate([], [
            'school_name' => 'SMA / SMK Negeri 1 Teladan',
            'time_in' => '07:00:00',
            'time_in_late_threshold' => '07:15:00',
            'time_out' => '14:30:00',
            'work_days' => 'Senin - Jumat',
        ]);
    }
}
