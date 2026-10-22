<?php

namespace App\Http\Controllers;

use App\Models\AttendanceSetting;
use App\Models\DailySchedule;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SettingController extends Controller
{
    public function index()
    {
        $setting = AttendanceSetting::getSettings();
        $schedules = $setting->dailySchedules()->orderBy('day_of_week')->get();

        return view('settings.index', compact('setting', 'schedules'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'foundation_name' => 'nullable|string|max:255',
            'npsn' => 'nullable|string|max:50',
            'school_address' => 'nullable|string|max:500',
            'school_phone' => 'nullable|string|max:50',
            'school_email' => 'nullable|email|max:100',
            'headmaster_name' => 'nullable|string|max:255',
            'headmaster_nip' => 'nullable|string|max:50',
            'website' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius_meters' => 'nullable|integer|min:10|max:5000',
            'wa_gateway_token' => 'nullable|string|max:255',
            'wa_gateway_url' => 'nullable|string|max:255',
            'schedules' => 'nullable|array',
        ]);

        $setting = AttendanceSetting::getSettings();

        $updateData = [
            'school_name' => $request->school_name,
            'foundation_name' => $request->foundation_name,
            'npsn' => $request->npsn,
            'school_address' => $request->school_address,
            'school_phone' => $request->school_phone,
            'school_email' => $request->school_email,
            'headmaster_name' => $request->headmaster_name,
            'headmaster_nip' => $request->headmaster_nip,
            'website' => $request->website,
            'wa_gateway_token' => $request->wa_gateway_token,
            'wa_gateway_url' => $request->wa_gateway_url,
            'latitude' => $request->filled('latitude') ? $request->latitude : $setting->latitude,
            'longitude' => $request->filled('longitude') ? $request->longitude : $setting->longitude,
            'radius_meters' => $request->filled('radius_meters') ? (int) $request->radius_meters : $setting->radius_meters,
            'is_geofence_active' => $request->has('is_geofence_active'),
        ];

        // Handle Logo Upload
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $dir = public_path('uploads/logo');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $updateData['logo_path'] = 'uploads/logo/' . $filename;

            // Sinkronkan juga ke public/logo.jpg dan icon PWA
            $targetPath = $dir . DIRECTORY_SEPARATOR . $filename;
            @copy($targetPath, public_path('logo.jpg'));

            if (extension_loaded('gd')) {
                try {
                    $ext = strtolower($file->getClientOriginalExtension());
                    $src = null;
                    if ($ext === 'png') {
                        $src = @imagecreatefrompng($targetPath);
                    } elseif (in_array($ext, ['jpg', 'jpeg'])) {
                        $src = @imagecreatefromjpeg($targetPath);
                    } elseif ($ext === 'webp') {
                        $src = @imagecreatefromwebp($targetPath);
                    }
                    if ($src) {
                        imagealphablending($src, true);
                        imagesavealpha($src, true);
                        foreach ([192, 512] as $size) {
                            $dst = imagecreatetruecolor($size, $size);
                            imagealphablending($dst, false);
                            imagesavealpha($dst, true);
                            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
                            imagefilledrectangle($dst, 0, 0, $size, $size, $transparent);
                            imagecopyresampled($dst, $src, 0, 0, 0, 0, $size, $size, imagesx($src), imagesy($src));
                            imagepng($dst, public_path('icon-' . $size . '.png'));
                            imagedestroy($dst);
                        }
                        imagedestroy($src);
                    }
                } catch (\Throwable $e) {}
            }
        }

        AttendanceSetting::where('id', $setting->id)->update($updateData);

        // Perbarui jadwal per hari jika ada input
        if ($request->has('schedules')) {
            $activeDays = [];

            foreach ($request->schedules as $scheduleId => $data) {
                $schedule = DailySchedule::where('attendance_setting_id', $setting->id)
                    ->where('id', $scheduleId)
                    ->first();

                if ($schedule) {
                    $isActive = isset($data['is_active']) && ($data['is_active'] == '1' || $data['is_active'] == 'on');

                    $timeIn = !empty($data['time_in']) ? (strlen($data['time_in']) === 5 ? $data['time_in'] . ':00' : $data['time_in']) : $schedule->time_in;
                    $lateThreshold = !empty($data['time_in_late_threshold']) ? (strlen($data['time_in_late_threshold']) === 5 ? $data['time_in_late_threshold'] . ':00' : $data['time_in_late_threshold']) : $schedule->time_in_late_threshold;
                    $timeOut = !empty($data['time_out']) ? (strlen($data['time_out']) === 5 ? $data['time_out'] . ':00' : $data['time_out']) : $schedule->time_out;

                    $schedule->update([
                        'is_active' => $isActive,
                        'time_in' => $timeIn,
                        'time_in_late_threshold' => $lateThreshold,
                        'time_out' => $timeOut,
                        'note' => $data['note'] ?? null,
                    ]);

                    if ($isActive) {
                        $activeDays[] = $schedule->day_name;
                    }
                }
            }

            // Update label hari kerja di setting utama
            if (!empty($activeDays)) {
                $firstDay = reset($activeDays);
                $lastDay = end($activeDays);
                $workDaysLabel = count($activeDays) > 1 ? "{$firstDay} - {$lastDay}" : $firstDay;
                AttendanceSetting::where('id', $setting->id)->update([
                    'work_days' => $workDaysLabel,
                ]);
            }
        }

        return back()->with('success', 'Data profil sekolah, yayasan, logo, dan jadwal presensi berhasil diperbarui!');
    }

    /**
     * Upload & Import File Jadwal (CSV)
     */
    public function uploadCsv(Request $request)
    {
        $request->validate([
            'file_schedule' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file_schedule');
        $handle = fopen($file->getRealPath(), 'r');

        if (!$handle) {
            return back()->with('error', 'Gagal membuka file CSV!');
        }

        $setting = AttendanceSetting::getSettings();
        $rowNumber = 0;
        $updatedCount = 0;

        $dayMap = [
            'senin' => 1,
            'selasa' => 2,
            'rabu' => 3,
            'kamis' => 4,
            'jumat' => 5,
            'sabtu' => 6,
            'minggu' => 7,
        ];

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $rowNumber++;

            // Skip header baris pertama
            if ($rowNumber === 1 && (strtolower(trim($row[0])) === 'hari' || strtolower(trim($row[0])) === 'day')) {
                continue;
            }

            if (empty($row[0])) {
                continue;
            }

            $dayKey = strtolower(trim($row[0]));
            if (!isset($dayMap[$dayKey])) {
                continue;
            }

            $dayNumber = $dayMap[$dayKey];
            $timeIn = !empty($row[1]) ? trim($row[1]) : '07:00';
            $lateThreshold = !empty($row[2]) ? trim($row[2]) : '07:15';
            $timeOut = !empty($row[3]) ? trim($row[3]) : '14:30';
            $statusStr = !empty($row[4]) ? strtolower(trim($row[4])) : 'masuk';
            $note = !empty($row[5]) ? trim($row[5]) : null;

            $isActive = !in_array($statusStr, ['libur', 'off', '0', 'false', 'tidak']);

            $timeInFormatted = strlen($timeIn) === 5 ? $timeIn . ':00' : $timeIn;
            $lateThresholdFormatted = strlen($lateThreshold) === 5 ? $lateThreshold . ':00' : $lateThreshold;
            $timeOutFormatted = strlen($timeOut) === 5 ? $timeOut . ':00' : $timeOut;

            DailySchedule::updateOrCreate(
                [
                    'attendance_setting_id' => $setting->id,
                    'day_of_week' => $dayNumber,
                ],
                [
                    'day_name' => ucfirst($dayKey),
                    'is_active' => $isActive,
                    'time_in' => $timeInFormatted,
                    'time_in_late_threshold' => $lateThresholdFormatted,
                    'time_out' => $timeOutFormatted,
                    'note' => $note,
                ]
            );

            $updatedCount++;
        }

        fclose($handle);

        return back()->with('success', "Berhasil mengimpor dan memperbarui {$updatedCount} jadwal hari dari file CSV!");
    }

    /**
     * Download Template CSV untuk Jadwal
     */
    public function downloadTemplate(): StreamedResponse
    {
        $setting = AttendanceSetting::getSettings();
        $schedules = $setting->dailySchedules()->orderBy('day_of_week')->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="template_jadwal_presensi_sekolah.csv"',
        ];

        return response()->stream(function () use ($schedules) {
            $handle = fopen('php://output', 'w');

            // Tambahkan BOM untuk UTF-8 (agar dibuka di Excel tanpa error karakter)
            fputs($handle, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($handle, ['Hari', 'Jam_Masuk', 'Batas_Telat', 'Jam_Pulang', 'Status_Hari', 'Keterangan']);

            foreach ($schedules as $s) {
                fputcsv($handle, [
                    $s->day_name,
                    substr($s->time_in, 0, 5),
                    substr($s->time_in_late_threshold, 0, 5),
                    substr($s->time_out, 0, 5),
                    $s->is_active ? 'Masuk' : 'Libur',
                    $s->note ?? '',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
