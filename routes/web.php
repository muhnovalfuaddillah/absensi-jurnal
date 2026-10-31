<?php

use App\Http\Controllers\AdminAcademicController;
use App\Http\Controllers\AdminPortalController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GuruAcademicController;
use App\Http\Controllers\GuruPortalController;
use App\Http\Controllers\SchoolAnnouncementController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SuperadminController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect Root ke Dashboard masing-masing atau Login
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->isSuperAdmin()) {
            return redirect()->route('superadmin.dashboard');
        }
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('guru.dashboard');
    }
    return redirect()->route('login');
});

// Autentikasi (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| PORTAL KHUSUS GURU (Hanya bisa melihat data miliknya sendiri!)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    // Dashboard Pribadi Guru
    Route::get('/dashboard', [GuruPortalController::class, 'dashboard'])->name('dashboard');

    // Switcher Sekolah (Bagi guru yang mengajar di >1 sekolah, misal MTs & MA)
    Route::post('/switch-school', [GuruPortalController::class, 'switchSchool'])->name('switch_school');

    // Presensi Mandiri dari HP / Perangkat Guru
    Route::post('/absen/masuk', [GuruPortalController::class, 'checkIn'])->name('checkin');
    Route::post('/absen/pulang', [GuruPortalController::class, 'checkOut'])->name('checkout');

    // Riwayat Presensi Pribadi (Tidak tercampur guru lain)
    Route::get('/riwayat', [GuruPortalController::class, 'riwayat'])->name('riwayat');

    // Form Pengajuan Izin / Sakit Mandiri
    Route::get('/izin', [GuruPortalController::class, 'formIzin'])->name('izin');
    Route::post('/izin', [GuruPortalController::class, 'storeIzin'])->name('izin.store');

    // Jadwal Mengajar Guru
    Route::get('/jadwal', [GuruAcademicController::class, 'jadwal'])->name('jadwal');
    Route::get('/laporan-izin-guru', [GuruAcademicController::class, 'laporanIzinGuru'])->name('laporan_izin');

    // Jurnal Mengajar & Pembelajaran Guru
    Route::get('/jurnal', [GuruAcademicController::class, 'jurnalIndex'])->name('jurnal.index');
    Route::get('/jurnal/tambah', [GuruAcademicController::class, 'jurnalCreate'])->name('jurnal.create');
    Route::post('/jurnal', [GuruAcademicController::class, 'jurnalStore'])->name('jurnal.store');
    Route::get('/jurnal/{id}', [GuruAcademicController::class, 'jurnalShow'])->name('jurnal.show');
    Route::delete('/jurnal/{id}', [GuruAcademicController::class, 'jurnalDestroy'])->name('jurnal.destroy');

    // Presensi & Rekap Kehadiran Siswa
    Route::get('/absen-siswa', [GuruAcademicController::class, 'absenSiswaIndex'])->name('absen_siswa.index');
    Route::get('/api/kelas/{classId}/siswa', [GuruAcademicController::class, 'apiClassStudents'])->name('api.class_students');

    // Scan QR Code Kelas Sebelum Isi Jurnal
    Route::get('/scan-kelas', [GuruAcademicController::class, 'scanKelasPage'])->name('scan_kelas');
    Route::post('/api/verify-class-qr', [GuruAcademicController::class, 'verifyClassQr'])->name('api.verify_class_qr');

    // Profil & Ubah Password Saya
    Route::get('/profil', [GuruPortalController::class, 'profile'])->name('profile');
    Route::put('/profil', [GuruPortalController::class, 'updateProfile'])->name('profile.update');

    // Pendaftaran Wajah Guru (Face ID Enrollment)
    Route::post('/face/register', [GuruPortalController::class, 'registerFace'])->name('face.register');
    Route::post('/face/sync-descriptor', [GuruPortalController::class, 'syncDescriptor'])->name('face.sync_descriptor');
});

/*
|--------------------------------------------------------------------------
| PORTAL KHUSUS ADMINISTRATOR (Monitoring & Pengelolaan Seluruh Sekolah)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Analitik Admin
    Route::get('/dashboard', [AdminPortalController::class, 'dashboard'])->name('dashboard');

    // Kiosk / Presensi Bersama di Meja Piket
    Route::get('/kiosk', [AttendanceController::class, 'kiosk'])->name('kiosk');
    Route::post('/absen/masuk', [AttendanceController::class, 'checkIn'])->name('checkin');
    Route::post('/absen/pulang', [AttendanceController::class, 'checkOut'])->name('checkout');

    // Riwayat Absensi Harian Seluruh Guru
    Route::get('/riwayat', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/riwayat/manual', [AttendanceController::class, 'storeManual'])->name('attendance.manual');
    Route::put('/riwayat/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/riwayat/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

    // Rekapitulasi Presensi Bulanan & Export (PDF / Excel / CSV / Cetak)
    Route::get('/rekap', [AttendanceController::class, 'recap'])->name('attendance.recap');
    Route::get('/rekap/excel', [AttendanceController::class, 'exportExcel'])->name('attendance.recap.excel');
    Route::get('/rekap/csv', [AttendanceController::class, 'exportCsv'])->name('attendance.recap.csv');

    // Manajemen Data Guru & Akun Login
    Route::get('/guru', [TeacherController::class, 'index'])->name('teachers.index');
    Route::post('/guru', [TeacherController::class, 'store'])->name('teachers.store');
    Route::post('/guru/link-existing', [TeacherController::class, 'linkExisting'])->name('teachers.link_existing');
    Route::put('/guru/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::patch('/guru/{teacher}/toggle', [TeacherController::class, 'toggleStatus'])->name('teachers.toggle');
    Route::patch('/guru/{teacher}/reset-face', [TeacherController::class, 'resetFace'])->name('teachers.reset_face');
    Route::post('/guru/{teacher}/reset-password', [TeacherController::class, 'resetPassword'])->name('teachers.reset_password');
    Route::delete('/guru/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');

    // Manajemen Jadwal Pelajaran Sekolah
    Route::get('/jadwal-pelajaran', [AdminAcademicController::class, 'schedulesIndex'])->name('schedules.index');
    Route::post('/jadwal-pelajaran', [AdminAcademicController::class, 'schedulesStore'])->name('schedules.store');
    Route::put('/jadwal-pelajaran/{id}', [AdminAcademicController::class, 'schedulesUpdate'])->name('schedules.update');
    Route::delete('/jadwal-pelajaran/{id}', [AdminAcademicController::class, 'schedulesDestroy'])->name('schedules.destroy');

    // Manajemen Data Mata Pelajaran
    Route::get('/mata-pelajaran', [AdminAcademicController::class, 'subjectsIndex'])->name('subjects.index');
    Route::post('/mata-pelajaran', [AdminAcademicController::class, 'subjectsStore'])->name('subjects.store');
    Route::put('/mata-pelajaran/{id}', [AdminAcademicController::class, 'subjectsUpdate'])->name('subjects.update');
    Route::delete('/mata-pelajaran/{id}', [AdminAcademicController::class, 'subjectsDestroy'])->name('subjects.destroy');

    // Manajemen Data Kelas & Siswa
    Route::get('/kelas', [AdminAcademicController::class, 'classesIndex'])->name('classes.index');
    Route::post('/kelas', [AdminAcademicController::class, 'classesStore'])->name('classes.store');
    Route::put('/kelas/{id}', [AdminAcademicController::class, 'classesUpdate'])->name('classes.update');
    Route::delete('/kelas/{id}', [AdminAcademicController::class, 'classesDestroy'])->name('classes.destroy');
    Route::get('/kelas/{id}/cetak-qr', [AdminAcademicController::class, 'printClassQr'])->name('classes.print_qr');

    Route::get('/siswa', [AdminAcademicController::class, 'studentsIndex'])->name('students.index');
    Route::post('/siswa', [AdminAcademicController::class, 'studentsStore'])->name('students.store');
    Route::post('/siswa/kenaikan-kelas', [AdminAcademicController::class, 'studentsPromote'])->name('students.promote');
    Route::put('/siswa/{id}', [AdminAcademicController::class, 'studentsUpdate'])->name('students.update');
    Route::delete('/siswa/{id}', [AdminAcademicController::class, 'studentsDestroy'])->name('students.destroy');

    // Monitoring Jurnal Guru & Pembelajaran
    Route::get('/jurnal-guru', [AdminAcademicController::class, 'journalsIndex'])->name('journals.index');
    Route::get('/jurnal-guru/{id}', [AdminAcademicController::class, 'journalsShow'])->name('journals.show');

    // Rekapitulasi Presensi Siswa
    Route::get('/presensi-siswa', [AdminAcademicController::class, 'studentAttendanceRecap'])->name('students.attendance_recap');
    Route::get('/presensi-siswa/excel', [AdminAcademicController::class, 'studentAttendanceExportExcel'])->name('students.attendance_recap.excel');

    // Pengaturan Jam Kerja & Jadwal Harian (Senin - Minggu)
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
    Route::post('/pengaturan/upload', [SettingController::class, 'uploadCsv'])->name('settings.upload');
    Route::get('/pengaturan/template', [SettingController::class, 'downloadTemplate'])->name('settings.template');

    // Profil Administrator & Kontak Tim IT Aplikasi
    Route::get('/profil', [AdminPortalController::class, 'profile'])->name('profile');
    Route::put('/profil', [AdminPortalController::class, 'updateProfile'])->name('profile.update');

    // Informasi Sekolah & Maklumat Yayasan
    Route::get('/informasi-sekolah', [SchoolAnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/informasi-sekolah', [SchoolAnnouncementController::class, 'store'])->name('announcements.store');
    Route::put('/informasi-sekolah/{id}', [SchoolAnnouncementController::class, 'update'])->name('announcements.update');
    Route::patch('/informasi-sekolah/{id}/toggle', [SchoolAnnouncementController::class, 'toggleStatus'])->name('announcements.toggle');
    Route::delete('/informasi-sekolah/{id}', [SchoolAnnouncementController::class, 'destroy'])->name('announcements.destroy');
});

/*
|--------------------------------------------------------------------------
| PORTAL SUPERADMINISTRATOR (Pusat Kendali Aplikasi Multi-Sekolah)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    // Dashboard Utama Global
    Route::get('/dashboard', [SuperadminController::class, 'dashboard'])->name('dashboard');

    // Switcher Sekolah Aktif (Session Switch)
    Route::post('/switch-school', [SuperadminController::class, 'switchSchool'])->name('switch_school');

    // Manajemen Seluruh Sekolah
    Route::get('/sekolah', [SuperadminController::class, 'schoolsIndex'])->name('schools.index');
    Route::post('/sekolah', [SuperadminController::class, 'schoolsStore'])->name('schools.store');
    Route::put('/sekolah/{id}', [SuperadminController::class, 'schoolsUpdate'])->name('schools.update');
    Route::patch('/sekolah/{id}/toggle', [SuperadminController::class, 'schoolsToggle'])->name('schools.toggle');
    Route::delete('/sekolah/{id}', [SuperadminController::class, 'schoolsDestroy'])->name('schools.destroy');

    // Manajemen Admin Sekolah
    Route::get('/admin-sekolah', [SuperadminController::class, 'adminsIndex'])->name('admins.index');
    Route::post('/admin-sekolah', [SuperadminController::class, 'adminsStore'])->name('admins.store');
    Route::put('/admin-sekolah/{id}', [SuperadminController::class, 'adminsUpdate'])->name('admins.update');
    Route::delete('/admin-sekolah/{id}', [SuperadminController::class, 'adminsDestroy'])->name('admins.destroy');

    // Pengaturan Identitas Yayasan & Logo Halaman Login
    Route::get('/pengaturan', [SuperadminController::class, 'settingsIndex'])->name('settings.index');
    Route::post('/pengaturan', [SuperadminController::class, 'settingsUpdate'])->name('settings.update');
});

