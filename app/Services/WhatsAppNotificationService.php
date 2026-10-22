<?php

namespace App\Services;

use App\Models\AttendanceSetting;
use App\Models\Student;
use App\Models\TeachingJournal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppNotificationService
{
    /**
     * Format template pesan resmi presensi siswa untuk orang tua
     */
    public static function formatAbsenceMessage(Student $student, TeachingJournal $journal, string $status, ?string $notes = null): string
    {
        $setting = AttendanceSetting::getSettings();
        $schoolName = $setting->school_name ?: 'Sekolah';
        $parentGreeting = $student->parent_name ? "Bapak/Ibu {$student->parent_name}" : "Bapak/Ibu Orang Tua/Wali";
        
        $statusLabels = [
            'alpa' => '⚠️ ALPA (Tidak Masuk Tanpa Keterangan)',
            'sakit' => '🏥 SAKIT',
            'izin' => '✉️ IZIN',
            'hadir' => '✅ HADIR',
        ];

        $statusText = $statusLabels[$status] ?? strtoupper($status);
        
        // Format tanggal Indonesia
        $dateFormatted = Carbon::parse($journal->date)->locale('id')->isoFormat('dddd, D MMMM Y');
        $subjectName = $journal->subject ? $journal->subject->name : 'Pelajaran';
        $className = $journal->schoolClass ? $journal->schoolClass->name : ($student->schoolClass ? $student->schoolClass->name : '-');
        $teacherName = $journal->teacher ? $journal->teacher->name : 'Guru Pengajar';

        if ($journal->is_substitute && $journal->substitutedTeacher) {
            $teacherName .= " (Guru Pengganti untuk {$journal->substitutedTeacher->name})";
        }

        $msg = "*PEMBERITAHUAN KEHADIRAN SISWA*\n";
        $msg .= "*{$schoolName}*\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━━\n\n";
        $msg .= "Yth. {$parentGreeting},\n\n";
        $msg .= "Kami menginformasikan data kehadiran putra/putri Bapak/Ibu pada sesi pembelajaran berikut:\n\n";
        $msg .= "👤 *Nama Siswa:* {$student->name}\n";
        if ($student->nis) {
            $msg .= "🔢 *NIS:* {$student->nis}\n";
        }
        $msg .= "🏫 *Kelas:* {$className}\n";
        $msg .= "📅 *Hari, Tanggal:* {$dateFormatted}\n";
        $msg .= "📖 *Mata Pelajaran:* {$subjectName}\n";
        $msg .= "⏰ *Jam:* " . substr($journal->start_time, 0, 5) . " - " . substr($journal->end_time, 0, 5) . " WIB\n";
        $msg .= "👨‍🏫 *Guru Pengajar:* {$teacherName}\n\n";
        $msg .= "📋 *Status Kehadiran:* \n*{$statusText}*\n";

        if ($notes) {
            $msg .= "\n💬 *Catatan Guru:* {$notes}\n";
        }

        if ($status === 'alpa') {
            $msg .= "\n⚠️ _Mohon perhatian Bapak/Ibu untuk segera mengonfirmasi alasan ketidakhadiran siswa kepada pihak sekolah/wali kelas._\n";
        }

        $msg .= "\nTerima kasih atas kerja sama dan perhatian Bapak/Ibu.\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "_{$schoolName} - Sistem Presensi Digital HadirGuru_";

        return $msg;
    }

    /**
     * Kirim notifikasi WhatsApp otomatis ke orang tua jika nomor tersedia
     * Menggunakan WhatsApp Gateway (Fonnte / Webhook) jika dikonfigurasi,
     * serta selalu menyediakan tautan wa.me untuk manual / fallback.
     */
    public static function notifyParent(Student $student, TeachingJournal $journal, string $status, ?string $notes = null): array
    {
        $phone = $student->formatted_parent_phone;
        if (!$phone) {
            return [
                'sent' => false,
                'method' => 'none',
                'message' => 'Nomor WhatsApp orang tua belum terdaftar.',
                'wa_url' => null,
            ];
        }

        $messageText = self::formatAbsenceMessage($student, $journal, $status, $notes);
        $waUrl = "https://wa.me/{$phone}?text=" . urlencode($messageText);

        $setting = AttendanceSetting::getSettings();
        $token = $setting->wa_gateway_token ?: env('FONNTE_TOKEN');
        $endpoint = $setting->wa_gateway_url ?: env('FONNTE_URL', 'https://api.fonnte.com/send');

        $isApiSent = false;
        $apiResponse = null;

        if (!empty($token)) {
            try {
                $response = Http::timeout(6)
                    ->withHeaders([
                        'Authorization' => $token,
                    ])
                    ->post($endpoint, [
                        'target' => $phone,
                        'message' => $messageText,
                        'countryCode' => '62',
                    ]);

                if ($response->successful()) {
                    $isApiSent = true;
                    $apiResponse = $response->json();
                    Log::info("WhatsApp presensi terkirim ke orang tua {$student->name} ({$phone}) via API Gateway.");
                } else {
                    Log::warning("Gagal kirim WhatsApp presensi ke {$phone}: " . $response->body());
                }
            } catch (\Throwable $e) {
                Log::error("Exception saat kirim WhatsApp Gateway: " . $e->getMessage());
            }
        }

        return [
            'sent' => $isApiSent,
            'method' => $isApiSent ? 'api_gateway' : 'direct_link',
            'phone' => $phone,
            'parent_name' => $student->parent_name,
            'message_text' => $messageText,
            'wa_url' => $waUrl,
            'api_response' => $apiResponse,
        ];
    }
}
