@extends('layouts.app')

@section('title', 'SIMPRES BU - Presensi Face ID')

@push('styles')
<style>
    @keyframes scanLine {
        0% { transform: translateY(-85px); opacity: 0.3; }
        50% { opacity: 1; }
        100% { transform: translateY(85px); opacity: 0.3; }
    }
    .animate-scan-line {
        animation: scanLine 2s ease-in-out infinite alternate;
    }
    @keyframes pulseRing {
        0% { transform: scale(0.98); opacity: 0.9; }
        50% { transform: scale(1.03); opacity: 0.5; }
        100% { transform: scale(0.98); opacity: 0.9; }
    }
    .animate-pulse-ring {
        animation: pulseRing 2.5s ease-in-out infinite;
    }
</style>
@endpush

@section('content')
@php
    $waNumber = preg_replace('/[^0-9]/', '', $setting->school_phone ?? '089629615301');
    if (str_starts_with($waNumber, '0')) {
        $waNumber = '62' . substr($waNumber, 1);
    }
@endphp

<!-- 1. Teacher Greeting & Profile Card -->
<div class="bg-white rounded-3xl p-4 shadow-xs border border-slate-100 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3.5 min-w-0">
        <div class="relative shrink-0">
            @if($teacher->photo_url)
                <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->name }}" class="w-12 h-12 rounded-full object-cover ring-2 ring-emerald-500/20 shadow-xs">
            @else
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-[#13793e] font-extrabold flex items-center justify-center text-sm shadow-xs">
                    {{ strtoupper(substr($teacher->name, 0, 2)) }}
                </div>
            @endif
            <span class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 rounded-full ring-2 ring-white"></span>
        </div>
        <div class="flex flex-col min-w-0">
            <span class="text-[11px] text-emerald-800 font-semibold leading-tight">
                Assalamu'alaikum Warahmatullah,
            </span>
            <h2 class="text-sm sm:text-base font-extrabold text-slate-900 truncate leading-tight mt-0.5">
                {{ $teacher->name }}
            </h2>
            <div class="flex items-center gap-1.5 text-[11px] text-slate-400 mt-0.5 truncate">
                <span class="font-mono">NIP: {{ $teacher->nip ?: '-' }}</span>
                <span>•</span>
                <span class="text-[#13793e] font-semibold bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60 text-[10px]">
                    {{ $teacher->subject ?: 'Guru' }} ({{ $setting->school_name ?? 'Madrasah' }})
                </span>
            </div>
        </div>
    </div>

    @if(!$teacher->is_face_registered)
        <button type="button" onclick="openEnrollFaceModal()" class="px-2.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-[11px] shadow-xs flex items-center gap-1 shrink-0">
            <i class="fa-solid fa-camera text-xs"></i>
            <span class="hidden sm:inline">Daftar Wajah</span>
        </button>
    @endif
</div>

@if($teacher->isMultiSchool())
<!-- Multi-School Active Indicator Banner -->
<div class="bg-gradient-to-r from-emerald-700 via-teal-700 to-emerald-800 text-white rounded-3xl p-3.5 shadow-sm border border-emerald-600/30 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 rounded-2xl bg-white/15 backdrop-blur-xs flex items-center justify-center shrink-0 border border-white/20">
            <i class="fa-solid fa-repeat text-white text-base"></i>
        </div>
        <div class="min-w-0">
            <span class="text-[10px] uppercase font-extrabold tracking-wider text-emerald-200">
                Mode Multi-Sekolah Aktif
            </span>
            <h3 class="text-xs sm:text-sm font-extrabold truncate">
                {{ $setting->school_name ?? 'Madrasah' }}
            </h3>
            <p class="text-[10px] text-emerald-100/80 truncate">
                Presensi & jadwal tersinkronisasi khusus madrasah ini
            </p>
        </div>
    </div>
    <button type="button" onclick="openGuruSchoolModal()"
            class="px-3 py-1.5 rounded-xl bg-white text-emerald-900 font-extrabold text-[11px] hover:bg-emerald-50 shadow-sm shrink-0 flex items-center gap-1.5 active:scale-95 transition-all">
        <span>Ganti</span>
        <i class="fa-solid fa-chevron-right text-[9px]"></i>
    </button>
</div>
@endif

<!-- 2. Live Digital Clock & Geofence Radar Card -->
<div class="bg-gradient-to-b from-[#f0f7ff] to-white border border-blue-100/80 rounded-3xl p-4 sm:p-5 shadow-xs flex flex-col gap-3.5">
    <!-- Top: Clock & Today Date -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-baseline gap-2">
                <span class="text-3xl sm:text-4xl font-black font-mono text-[#064e3b] tracking-tight" id="digital-clock">
                    {{ now()->format('H:i:s') }}
                </span>
                <span class="text-xs font-extrabold text-blue-600/80 bg-blue-50 px-2 py-0.5 rounded-md">WIB</span>
            </div>
            <p class="text-[11px] text-slate-500 font-medium mt-0.5 flex items-center gap-1.5">
                <i class="fa-regular fa-calendar text-[10px] text-emerald-600"></i>
                <span>{{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }} • 1446 H</span>
            </p>
        </div>
        <div class="w-11 h-11 rounded-2xl bg-emerald-100 text-[#13793e] flex items-center justify-center text-xl shrink-0 shadow-xs border border-emerald-200/60">
            <i class="fa-regular fa-clock"></i>
        </div>
    </div>

    <!-- Live Real-Time Geofence Radar Banner -->
    <div id="geofenceBanner" class="p-3.5 rounded-2xl bg-amber-50/80 border border-amber-200 text-amber-950 transition-all duration-300 flex flex-col gap-2.5">
        <div class="flex items-center justify-between gap-2 flex-wrap">
            <div class="flex items-center gap-2.5 min-w-0">
                <div id="geofenceIconWrapper" class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200">
                    <i id="geofenceIcon" class="fa-solid fa-satellite-dish text-xs animate-pulse"></i>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500">RADAR GEOFENCE GPS</span>
                        <span id="geofenceLiveDot" class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                    </div>
                    <h4 id="geofenceTitle" class="text-xs sm:text-sm font-black text-slate-900 leading-tight truncate">
                        Mendeteksi Koordinat GPS...
                    </h4>
                </div>
            </div>

            <div class="flex items-center gap-1.5 shrink-0">
                <span id="geofenceBadge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                    <span>Mencari Sinyal GPS</span>
                </span>
                <button type="button" onclick="refreshGeofenceLocation(true)" title="Segarkan Koordinat GPS"
                        class="w-7 h-7 rounded-xl bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 shadow-2xs flex items-center justify-center text-xs active:scale-90 transition-all">
                    <i id="geofenceRefreshIcon" class="fa-solid fa-arrows-rotate"></i>
                </button>
            </div>
        </div>

        <!-- Real-time Explanation text -->
        <div id="geofenceTextBox" class="text-[11.5px] leading-relaxed text-slate-700 font-medium">
            <span id="geofenceText">Sedang menyelaraskan posisi GPS perangkat Anda dengan titik koordinat <strong>{{ $setting->school_name ?? 'Sekolah' }}</strong>...</span>
        </div>

        <!-- Live Telemetry / Metrics Bar -->
        <div id="geofenceMetricsBar" class="flex items-center flex-wrap gap-1.5 pt-2 border-t border-slate-200/50 text-[10px] font-mono font-bold">
            <span id="metricDistance" class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-700">
                <i class="fa-solid fa-route text-[9px] text-blue-500 mr-1"></i>Jarak: Mengukur...
            </span>
            <span id="metricRadius" class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-700">
                <i class="fa-solid fa-bullseye text-[9px] text-emerald-500 mr-1"></i>Radius Maks: {{ $setting->radius_meters ?? 150 }}m
            </span>
            <span id="metricAccuracy" class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-500 hidden">
                <i class="fa-solid fa-crosshairs text-[9px] mr-1"></i>Akurasi: ±0m
            </span>
        </div>
    </div>
</div>

<!-- 3. Gerbang Presensi Harian (Dual Big Cards - Masuk & Pulang) -->
<div>
    <!-- Real-time Outside Radius Alert Banner (Muncul otomatis secara real-time saat guru berada di luar jangkauan) -->
    <div id="outsideRadiusAlertBanner" class="hidden mb-3 p-3.5 rounded-2xl bg-rose-50 border border-rose-300 text-rose-950 shadow-sm">
        <div class="flex items-start gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 mt-0.5 shadow-xs">
                <i class="fa-solid fa-triangle-exclamation text-xs"></i>
            </div>
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between gap-2">
                    <h4 class="text-xs font-black text-rose-900 leading-tight">
                        ⛔ PERHATIAN: ANDA DI LUAR AREA RADIUS SEKOLAH
                    </h4>
                    <span id="outsideRadiusPill" class="px-2 py-0.5 rounded-full text-[9px] font-black bg-rose-200 text-rose-900 uppercase shrink-0">
                        Presensi Terkunci
                    </span>
                </div>
                <p id="outsideRadiusDesc" class="text-[11px] text-rose-800 font-medium mt-1 leading-snug">
                    Posisi Anda terdeteksi berjarak <strong id="outsideDistanceVal">0 meter</strong> dari <strong>{{ $setting->school_name ?? 'Madrasah' }}</strong> (Batas radius maksimal: <strong>{{ $setting->radius_meters ?? 150 }}m</strong>). Tombol presensi ditahan hingga Anda berada di dalam area lingkungan madrasah.
                </p>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between mb-2.5 px-1">
        <h3 class="font-extrabold text-sm text-slate-900">Gerbang Presensi Harian</h3>
        <span class="text-xs font-bold text-[#13793e]">Shift Pagi</span>
    </div>

    <div class="grid grid-cols-2 gap-3">
        <!-- Card 1: Presensi Masuk -->
        @if(!$todayAttendance)
            <button type="button" id="btnCardMasuk" onclick="handleAttendanceAction('masuk')"
                    class="bg-[#13793e] hover:bg-[#0f6332] text-white rounded-3xl p-4 shadow-md shadow-emerald-800/20 active:scale-[0.98] transition-all text-left flex flex-col justify-between min-h-[145px] relative group overflow-hidden">
                <div class="flex items-center justify-between w-full">
                    <div id="btnCardMasukIconWrapper" class="w-9 h-9 rounded-2xl bg-white/20 flex items-center justify-center text-white text-base">
                        <i class="fa-solid fa-camera"></i>
                    </div>
                    <span id="badgeCardMasukStatus" class="px-2 py-0.5 rounded-full bg-white text-[#13793e] font-black text-[10px] tracking-wider shadow-xs">
                        BUKA
                    </span>
                </div>
                <div>
                    <h4 class="font-black text-base sm:text-lg text-white leading-tight">Presensi Masuk</h4>
                    <p class="text-[11px] text-emerald-100 font-medium mt-0.5">
                        {{ substr($setting->time_in, 0, 5) }} - {{ substr($setting->time_in_late_threshold, 0, 5) }} WIB
                    </p>
                    <span id="subCardMasukText" class="text-[11px] text-emerald-200 font-semibold mt-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-camera-rotate text-[10px]"></i> Tap Kamera Selfie
                    </span>
                </div>
            </button>
        @else
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-950 rounded-3xl p-4 shadow-xs flex flex-col justify-between min-h-[145px]">
                <div class="flex items-center justify-between w-full">
                    <div class="w-9 h-9 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-base">
                        <i class="fa-solid fa-check"></i>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-900 font-bold text-[10px]">
                        {{ strtoupper($todayAttendance->status) }}
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-base text-emerald-900 leading-tight">Presensi Masuk</h4>
                    <p class="text-[11px] text-emerald-700 font-medium mt-0.5">
                        Pukul {{ substr($todayAttendance->time_in, 0, 5) }} WIB
                    </p>
                    <span class="text-[11px] text-emerald-800 font-semibold mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Sudah Tercatat
                    </span>
                </div>
            </div>
        @endif

        <!-- Card 2: Presensi Pulang -->
        @if(!$todayAttendance)
            <!-- Belum masuk: Terkunci -->
            <div class="bg-[#f1f5fd] border border-blue-100 text-slate-700 rounded-3xl p-4 shadow-xs flex flex-col justify-between min-h-[145px] opacity-90">
                <div class="flex items-center justify-between w-full">
                    <div class="w-9 h-9 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-base">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-blue-100/80 text-blue-700 font-bold text-[10px] tracking-wider">
                        TERKUNCI
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-base text-slate-800 leading-tight">Presensi Pulang</h4>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                        {{ substr($setting->time_out, 0, 5) }} WIB
                    </p>
                    <span class="text-[11px] text-slate-400 font-medium mt-2 flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-[10px]"></i> Menunggu Masuk
                    </span>
                </div>
            </div>
        @elseif($todayAttendance->time_out)
            <!-- Sudah absen pulang -->
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-950 rounded-3xl p-4 shadow-xs flex flex-col justify-between min-h-[145px]">
                <div class="flex items-center justify-between w-full">
                    <div class="w-9 h-9 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-base">
                        <i class="fa-solid fa-house-chimney-user"></i>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-200 text-emerald-900 font-bold text-[10px]">
                        SELESAI
                    </span>
                </div>
                <div>
                    <h4 class="font-extrabold text-base text-emerald-900 leading-tight">Presensi Pulang</h4>
                    <p class="text-[11px] text-emerald-700 font-medium mt-0.5">
                        Pukul {{ substr($todayAttendance->time_out, 0, 5) }} WIB
                    </p>
                    <span class="text-[11px] text-emerald-800 font-semibold mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Tugas Hari Ini Usai
                    </span>
                </div>
            </div>
        @else
            <!-- Sudah masuk, siap absen pulang -->
            <button type="button" id="btnCardPulang" onclick="handleAttendanceAction('pulang')"
                    class="bg-[#13793e] hover:bg-[#0f6332] text-white rounded-3xl p-4 shadow-md shadow-emerald-800/20 active:scale-[0.98] transition-all text-left flex flex-col justify-between min-h-[145px] relative group overflow-hidden">
                <div class="flex items-center justify-between w-full">
                    <div id="btnCardPulangIconWrapper" class="w-9 h-9 rounded-2xl bg-white/20 flex items-center justify-center text-white text-base">
                        <i class="fa-solid fa-right-from-bracket"></i>
                    </div>
                    <span id="badgeCardPulangStatus" class="px-2 py-0.5 rounded-full bg-white text-[#13793e] font-black text-[10px] tracking-wider shadow-xs">
                        BUKA
                    </span>
                </div>
                <div>
                    <h4 class="font-black text-base sm:text-lg text-white leading-tight">Presensi Pulang</h4>
                    <p class="text-[11px] text-emerald-100 font-medium mt-0.5">
                        {{ substr($setting->time_out, 0, 5) }} WIB
                    </p>
                    <span id="subCardPulangText" class="text-[11px] text-emerald-200 font-semibold mt-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-camera-rotate text-[10px]"></i> Tap Kamera Selfie
                    </span>
                </div>
            </button>
        @endif
    </div>

    <!-- Alert Note GPS & Selfie -->
    <div class="mt-2.5 p-2.5 rounded-2xl bg-[#f0f7ff] border border-blue-100/70 text-slate-600 text-[11px] flex items-center gap-2">
        <i class="fa-solid fa-circle-info text-blue-600 text-xs shrink-0"></i>
        <span>Wajib menyertakan foto selfie di lokasi madrasah & memastikan GPS akurat.</span>
    </div>
</div>

<!-- 4. Statistik Bulan Ini -->
<div>
    <div class="flex items-center justify-between mb-2.5 px-1">
        <h3 class="font-extrabold text-sm text-slate-900">
            Statistik Bulan Ini ({{ \Carbon\Carbon::now()->translatedFormat('F') }})
        </h3>
        <a href="{{ route('guru.riwayat') }}" class="text-xs font-semibold text-slate-500 hover:text-[#13793e] flex items-center gap-1">
            <span>Detail</span>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
        </a>
    </div>

    <div class="grid grid-cols-4 gap-2 sm:gap-2.5">
        <!-- 1. Tepat Waktu -->
        <div class="bg-white rounded-2xl p-3 border border-slate-100 shadow-xs text-center flex flex-col items-center">
            <div class="w-9 h-9 rounded-full bg-emerald-100 text-[#13793e] flex items-center justify-center text-sm">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span class="text-base font-extrabold text-slate-900 mt-1.5 leading-none">{{ $totalHadir }}</span>
            <span class="text-[10px] text-slate-500 font-medium mt-1 leading-tight">Tepat Waktu</span>
        </div>

        <!-- 2. Terlambat -->
        <div class="bg-white rounded-2xl p-3 border border-slate-100 shadow-xs text-center flex flex-col items-center">
            <div class="w-9 h-9 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-sm">
                <i class="fa-regular fa-clock"></i>
            </div>
            <span class="text-base font-extrabold text-slate-900 mt-1.5 leading-none">{{ $totalTerlambat }}</span>
            <span class="text-[10px] text-slate-500 font-medium mt-1 leading-tight">Terlambat</span>
        </div>

        <!-- 3. Izin / Sakit -->
        <div class="bg-white rounded-2xl p-3 border border-slate-100 shadow-xs text-center flex flex-col items-center">
            <div class="w-9 h-9 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center text-sm">
                <i class="fa-solid fa-suitcase-medical"></i>
            </div>
            <span class="text-base font-extrabold text-slate-900 mt-1.5 leading-none">{{ $totalIzin + $totalSakit }}</span>
            <span class="text-[10px] text-slate-500 font-medium mt-1 leading-tight">Izin / Sakit</span>
        </div>

        <!-- 4. Alpha -->
        <div class="bg-white rounded-2xl p-3 border border-slate-100 shadow-xs text-center flex flex-col items-center">
            <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center text-sm">
                <i class="fa-solid fa-ban"></i>
            </div>
            <span class="text-base font-extrabold text-slate-900 mt-1.5 leading-none">{{ $totalAlpa }}</span>
            <span class="text-[10px] text-slate-500 font-medium mt-1 leading-tight">Alpha</span>
        </div>
    </div>
</div>

<!-- 5. Jadwal Mengajar Hari Ini -->
<div>
    <div class="flex items-center justify-between mb-2.5 px-1">
        <div class="flex items-center gap-1.5">
            <i class="fa-solid fa-graduation-cap text-[#13793e]"></i>
            <h3 class="font-extrabold text-sm text-slate-900">Jadwal Mengajar Hari Ini</h3>
        </div>
        <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-[#13793e] text-[10px] font-bold">
            {{ $todaySchedules->count() }} Agenda
        </span>
    </div>

    @if($todaySchedules->isEmpty())
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xs text-center">
            <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                <i class="fa-regular fa-calendar-xmark text-lg"></i>
            </div>
            <p class="text-xs font-bold text-slate-700">Tidak ada agenda tatap muka hari ini</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Silakan cek jadwal sepekan atau isi jurnal mengajar.</p>
        </div>
    @else
        <div class="space-y-2.5">
            @foreach($todaySchedules as $sch)
                @php
                    $isJournalFilled = isset($todayJournals[$sch->id]);
                @endphp
                <div class="bg-white rounded-3xl p-3.5 border border-slate-100 shadow-xs flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="bg-[#eaf8ef] text-[#13793e] font-bold text-xs p-2 rounded-2xl flex flex-col items-center justify-center min-w-[54px] shrink-0 font-mono">
                            <span class="leading-none">{{ substr($sch->start_time, 0, 5) }}</span>
                            <span class="text-[9px] text-slate-400 font-normal mt-1 leading-none">{{ substr($sch->end_time, 0, 5) }}</span>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 truncate">
                                    {{ $sch->subject->name ?? 'Mata Pelajaran' }}
                                </h4>
                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-[#13793e] text-[10px] font-bold shrink-0">
                                    {{ $sch->schoolClass->name ?? 'Kelas' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium flex items-center gap-1.5 mt-1 truncate">
                                <i class="fa-solid fa-building-columns text-[10px]"></i>
                                <span>Ruang {{ $sch->room ?: 'Kelas' }}</span>
                            </p>
                        </div>
                    </div>

                    <div class="shrink-0">
                        @if($isJournalFilled)
                            <a href="{{ route('guru.jurnal.show', $todayJournals[$sch->id]) }}" 
                               class="px-2.5 py-1.5 rounded-xl bg-emerald-50 text-[#13793e] font-bold text-xs border border-emerald-200 hover:bg-emerald-100 transition-colors flex items-center gap-1">
                                <span>Jurnal</span>
                                <i class="fa-solid fa-check text-[10px]"></i>
                            </a>
                        @else
                            <a href="{{ route('guru.scan_kelas', ['schedule_id' => $sch->id]) }}" 
                               class="px-3 py-1.5 rounded-xl bg-[#13793e] hover:bg-[#0f6332] text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5">
                                <i class="fa-solid fa-qrcode text-xs"></i>
                                <span>Mulai</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- 6. Layanan Akademik & Kepegawaian (6 Grid Icons) -->
<div>
    <h3 class="font-extrabold text-sm text-slate-900 mb-2.5 px-1">Layanan Akademik & Kepegawaian</h3>
    <div class="grid grid-cols-3 gap-2.5">
        <!-- 1. Ajukan Izin -->
        <a href="{{ route('guru.izin') }}" class="bg-white rounded-3xl p-3.5 border border-slate-100 shadow-xs flex flex-col items-center justify-center gap-2 hover:bg-slate-50 transition-all text-center">
            <div class="w-11 h-11 rounded-full bg-[#dcfce7] text-[#15803d] flex items-center justify-center text-lg shadow-2xs">
                <i class="fa-regular fa-calendar-plus"></i>
            </div>
            <span class="text-xs font-bold text-slate-800 leading-tight">Ajukan Izin</span>
        </a>

        <!-- 2. Jadwal Pelajaran -->
        <a href="{{ route('guru.jadwal') }}" class="bg-white rounded-3xl p-3.5 border border-slate-100 shadow-xs flex flex-col items-center justify-center gap-2 hover:bg-slate-50 transition-all text-center">
            <div class="w-11 h-11 rounded-full bg-[#e0e7ff] text-[#4338ca] flex items-center justify-center text-lg shadow-2xs">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <span class="text-xs font-bold text-slate-800 leading-tight">Jadwal Pelajaran</span>
        </a>

        <!-- 3. Riwayat Absen -->
        <a href="{{ route('guru.riwayat') }}" class="bg-white rounded-3xl p-3.5 border border-slate-100 shadow-xs flex flex-col items-center justify-center gap-2 hover:bg-slate-50 transition-all text-center">
            <div class="w-11 h-11 rounded-full bg-[#ccfbf1] text-[#0f766e] flex items-center justify-center text-lg shadow-2xs">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <span class="text-xs font-bold text-slate-800 leading-tight">Riwayat Absen</span>
        </a>

        <!-- 4. Laporan Izin Guru & Inval -->
        <a href="{{ route('guru.laporan_izin') }}" class="bg-white rounded-3xl p-3.5 border border-slate-100 shadow-xs flex flex-col items-center justify-center gap-2 hover:bg-slate-50 transition-all text-center relative group">
            <div class="w-11 h-11 rounded-full bg-[#fef3c7] text-[#b45309] flex items-center justify-center text-lg shadow-2xs group-hover:scale-105 transition-transform">
                <i class="fa-solid fa-user-clock"></i>
            </div>
            <span class="text-xs font-bold text-slate-800 leading-tight">Izin Guru & Inval</span>
            @if(isset($todayAbsentTeachersCount) && $todayAbsentTeachersCount > 0)
                <span class="absolute top-2 right-2 px-1.5 py-0.5 rounded-full text-[9px] font-black bg-rose-500 text-white animate-pulse shadow-xs">
                    {{ $todayAbsentTeachersCount }}
                </span>
            @endif
        </a>

        <!-- 5. Jurnal Guru -->
        <a href="{{ route('guru.jurnal.index') }}" class="bg-white rounded-3xl p-3.5 border border-slate-100 shadow-xs flex flex-col items-center justify-center gap-2 hover:bg-slate-50 transition-all text-center">
            <div class="w-11 h-11 rounded-full bg-[#ffedd5] text-[#c2410c] flex items-center justify-center text-lg shadow-2xs">
                <i class="fa-solid fa-book-bookmark"></i>
            </div>
            <span class="text-xs font-bold text-slate-800 leading-tight">Jurnal Guru</span>
        </a>

        <!-- 6. Bantuan IT -->
        <a href="https://wa.me/{{ $waNumber }}?text=Halo%20Admin%20IT%20Madrasah,%20saya%20memerlukan%20bantuan%20terkait%20akun%20Presensi." target="_blank"
           class="bg-white rounded-3xl p-3.5 border border-slate-100 shadow-xs flex flex-col items-center justify-center gap-2 hover:bg-slate-50 transition-all text-center">
            <div class="w-11 h-11 rounded-full bg-[#ffe4e6] text-[#e11d48] flex items-center justify-center text-lg shadow-2xs">
                <i class="fa-solid fa-headset"></i>
            </div>
            <span class="text-xs font-bold text-slate-800 leading-tight">Bantuan IT</span>
        </a>
    </div>
</div>

<!-- 7. Informasi Sekolah -->
<div>
    <div class="flex items-center justify-between mb-2.5 px-1">
        <div class="flex items-center gap-1.5">
            <i class="fa-solid fa-bullhorn text-amber-600"></i>
            <h3 class="font-extrabold text-sm text-slate-900">Informasi Sekolah</h3>
        </div>
        @if(isset($announcements) && $announcements->count() > 0)
        <span class="text-[10px] font-bold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-md">
            {{ $announcements->count() }} Informasi
        </span>
        @endif
    </div>

    @if(isset($announcements) && $announcements->count() > 0)
        <div class="space-y-3">
            @foreach($announcements as $announcement)
            @php
                $catBadge = match(strtoupper($announcement->category)) {
                    'PENTING' => 'bg-rose-600 text-white',
                    'MAKLUMAT' => 'bg-amber-600 text-white',
                    'SURAT EDARAN' => 'bg-[#13793e] text-white',
                    'AGENDA' => 'bg-blue-600 text-white',
                    default => 'bg-slate-700 text-white',
                };
            @endphp
            <div class="bg-white rounded-3xl p-4 border border-slate-100 shadow-xs transition-all hover:shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-0.5 rounded-md {{ $catBadge }} font-extrabold text-[9px] tracking-wider uppercase">
                        {{ $announcement->category }}
                    </span>
                    <span class="text-[11px] text-slate-400 font-medium">
                        {{ $announcement->date ? $announcement->date->translatedFormat('d M Y') : \Carbon\Carbon::now()->translatedFormat('d M Y') }}
                    </span>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-14 h-14 rounded-2xl bg-slate-50 overflow-hidden shrink-0 border border-slate-200 p-0.5 flex items-center justify-center">
                        <img src="{{ $setting->logo_url ?? asset('logo.jpg') }}" alt="Logo" class="w-full h-full object-contain">
                    </div>
                    <div class="min-w-0 flex-1">
                        <h4 class="font-extrabold text-xs sm:text-sm text-slate-900 leading-snug">
                            {{ $announcement->title }}
                        </h4>
                        <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5">
                            {{ $announcement->content }}
                        </p>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                    <span class="text-slate-400 font-medium flex items-center gap-1 truncate max-w-[55%]">
                        <i class="fa-regular fa-file-lines shrink-0"></i>
                        <span class="truncate">{{ $announcement->letter_number ?: ($setting->school_name ?? 'Madrasah') }}</span>
                    </span>
                    
                    <button type="button" onclick="showAnnouncementDetail({{ json_encode($announcement) }})" class="font-bold text-[#13793e] hover:underline flex items-center gap-1 shrink-0">
                        <span>Baca Lengkap</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <!-- Fallback Jika Belum Ada Informasi yang Diterbitkan Admin -->
        <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xs text-center">
            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-2">
                <i class="fa-solid fa-bell-slash text-sm"></i>
            </div>
            <h4 class="font-extrabold text-xs text-slate-800">Belum Ada Informasi Terbaru</h4>
            <p class="text-[11px] text-slate-400 mt-0.5">Seluruh informasi dan edaran resmi sekolah akan diumumkan di sini oleh pihak Admin Madrasah.</p>
        </div>
    @endif
</div>

<!-- ==================================================== -->
<!-- MODAL DETAIL MAKLUMAT YAYASAN & INFORMASI SEKOLAH -->
<!-- ==================================================== -->
<div id="announcementDetailModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100 flex flex-col gap-4 max-h-[85vh] overflow-y-auto animate-scaleUp">
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-slate-50 border border-slate-200 p-1 flex items-center justify-center shrink-0">
                    <img src="{{ $setting->logo_url ?? asset('logo.jpg') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
                <div>
                    <span id="modalAnnounceCategory" class="px-2 py-0.5 rounded-md text-[9px] font-black tracking-wider uppercase bg-amber-600 text-white">
                        MAKLUMAT
                    </span>
                    <span id="modalAnnounceDate" class="text-[11px] text-slate-400 font-medium ml-1"></span>
                </div>
            </div>
            <button type="button" onclick="closeAnnouncementModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Judul Maklumat -->
        <div>
            <h3 id="modalAnnounceTitle" class="font-black text-base text-slate-900 leading-snug"></h3>
            <p id="modalAnnounceLetter" class="text-xs text-slate-500 font-medium mt-1 flex items-center gap-1.5"></p>
        </div>

        <!-- Isi Pengumuman -->
        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 text-xs text-slate-700 leading-relaxed whitespace-pre-line" id="modalAnnounceContent">
        </div>

        <!-- Tombol Lampiran & Link (Jika Ada) -->
        <div id="modalAnnounceActionBox" class="flex flex-col gap-2 pt-1 hidden">
            <a id="modalAnnounceAttachLink" href="#" target="_blank" class="w-full py-2.5 px-4 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-[#13793e] font-bold text-xs flex items-center justify-center gap-2 transition-colors hidden">
                <i class="fa-solid fa-file-pdf text-sm"></i>
                <span>Unduh / Lihat Dokumen Lampiran</span>
            </a>

            <a id="modalAnnounceExtLink" href="#" target="_blank" class="w-full py-2.5 px-4 rounded-xl bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 font-bold text-xs flex items-center justify-center gap-2 transition-colors hidden">
                <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                <span>Buka Tautan Terkait</span>
            </a>
        </div>

        <!-- Footer Modal -->
        <div class="pt-2 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="closeAnnouncementModal()" class="w-full py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition-colors">
                Tutup Informasi
            </button>
        </div>
    </div>
</div>




<!-- ==================================================== -->
<!-- MODAL 1: PENDAFTARAN WAJAH RESMI (FACE ENROLLMENT) -->
<!-- ==================================================== -->
<div id="faceEnrollModal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-3xl max-w-sm w-full p-5 shadow-2xl border border-surface-container flex flex-col items-center gap-3 relative animate-scaleUp">
        
        <!-- Header Modal -->
        <div class="w-full flex items-center justify-between pb-2 border-b border-surface-container">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">add_a_photo</span>
                </div>
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold leading-tight">Pendaftaran Face ID</h3>
                    <span class="text-[11px] text-on-surface-variant font-medium">Rekam data biometrik wajah resmi</span>
                </div>
            </div>
            <button type="button" onclick="closeEnrollFaceModal()" class="w-8 h-8 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface-variant flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <p class="text-xs text-on-surface-variant text-center leading-normal">
            Posisikan wajah Anda tepat di dalam lingkaran dengan pencahayaan yang cukup. Wajah ini akan menjadi acuan resmi atas nama <strong>{{ $teacher->name }}</strong>.
        </p>

        <!-- Camera Scanner Viewport -->
        <div id="enrollVideoWrapper" class="relative w-64 h-64 sm:w-72 sm:h-72 rounded-full overflow-hidden border-4 border-primary shadow-xl bg-slate-900 flex items-center justify-center my-1 transition-all">
            <video id="enrollVideo" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
            <canvas id="enrollOverlayCanvas" class="absolute inset-0 w-full h-full pointer-events-none transform -scale-x-100"></canvas>
            <canvas id="enrollCanvas" class="hidden"></canvas>

            <!-- Guide Frame -->
            <div class="absolute inset-0 pointer-events-none flex flex-col items-center justify-between p-5">
                <div class="w-full flex justify-between">
                    <div class="w-7 h-7 border-t-4 border-l-4 border-emerald-400 rounded-tl-xl shadow-sm"></div>
                    <div class="w-7 h-7 border-t-4 border-r-4 border-emerald-400 rounded-tr-xl shadow-sm"></div>
                </div>
                <div class="w-full h-1 bg-gradient-to-r from-transparent via-emerald-400 to-transparent shadow-[0_0_15px_#10b981] animate-scan-line"></div>
                <div class="w-full flex justify-between">
                    <div class="w-7 h-7 border-b-4 border-l-4 border-emerald-400 rounded-bl-xl shadow-sm"></div>
                    <div class="w-7 h-7 border-b-4 border-r-4 border-emerald-400 rounded-br-xl shadow-sm"></div>
                </div>
            </div>

            <!-- Preview Image -->
            <img id="enrollCapturedPreview" alt="Snapshot Enroll" class="absolute inset-0 w-full h-full object-cover hidden rounded-full border-4 border-emerald-500 shadow-2xl" />
        </div>

        <!-- Status -->
        <div id="enrollStatusBadge" class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-low text-primary font-label-sm text-label-sm font-semibold border border-surface-container">
            <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
            <span id="enrollStatusText">Menghubungkan Kamera & Model AI Wajah...</span>
        </div>

        <!-- Form Enrollment -->
        <form id="enrollFaceForm" action="{{ route('guru.face.register') }}" method="POST" class="w-full flex flex-col gap-2 mt-1">
            @csrf
            <input type="hidden" name="face_image" id="inputEnrollFaceImage">
            <input type="hidden" name="face_descriptor" id="inputEnrollFaceDescriptor">

            <button type="button" id="btnCaptureEnroll" onclick="captureAndEnrollFace()"
                    class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold font-headline-sm text-headline-sm shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[22px]">save</span>
                <span>Daftarkan & Simpan Wajah</span>
            </button>
            
            <div id="enrollFallbackBox" class="hidden text-center mt-1">
                <label class="text-[11px] text-primary hover:underline cursor-pointer font-semibold block">
                    <span>Atau ambil foto profil wajah dari kamera HP</span>
                    <input type="file" accept="image/*" capture="user" class="hidden" onchange="handleEnrollFallback(this)">
                </label>
            </div>
        </form>

    </div>
</div>

<!-- ==================================================== -->
<!-- MODAL 2: SCAN MUKA VERIFIKASI PRESENSI (FACE SCAN) -->
<!-- ==================================================== -->
<div id="faceScanModal" class="fixed inset-0 bg-slate-950/85 backdrop-blur-md z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-3xl max-w-sm w-full p-5 shadow-2xl border border-surface-container flex flex-col items-center gap-3 relative animate-scaleUp">
        
        <!-- Header Modal -->
        <div class="w-full flex items-center justify-between pb-2 border-b border-surface-container">
            <div class="flex items-center gap-2">
                <div id="modalFaceHeaderIcon" class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">login</span>
                </div>
                <div>
                    <h3 id="modalFaceTitle" class="font-headline-sm text-headline-sm text-on-surface font-bold leading-tight">Presensi MASUK</h3>
                    <span id="modalFaceSub" class="text-[11px] text-on-surface-variant font-medium">Verifikasi Biometrik Wajah</span>
                </div>
            </div>
            <button type="button" onclick="closeFaceScanModal()" class="w-8 h-8 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface-variant flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <!-- Target Verified Profile Match Indicator -->
        <div class="w-full flex items-center justify-between bg-surface-container-low px-3 py-2 rounded-xl border border-surface-container text-xs">
            <div class="flex items-center gap-2 min-w-0">
                @if($teacher->photo_url)
                    <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->name }}" class="w-8 h-8 rounded-full object-cover border border-emerald-500 shrink-0">
                @else
                    <div class="w-8 h-8 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center text-xs shrink-0">
                        {{ strtoupper(substr($teacher->name, 0, 2)) }}
                    </div>
                @endif
                <div class="flex flex-col min-w-0">
                    <span class="font-bold text-on-surface truncate">{{ $teacher->name }}</span>
                    <span class="text-[10px] text-emerald-700 font-semibold flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[12px]">verified</span> Data Wajah Cocok
                    </span>
                </div>
            </div>
            <span class="text-[10px] font-mono text-on-surface-variant bg-surface-container px-2 py-0.5 rounded-full shrink-0">
                NIP: {{ $teacher->nip ?: '-' }}
            </span>
        </div>

        <!-- Camera Scanner Viewport with Circular Face ID Frame & Laser Line -->
        <div id="faceVideoWrapper" class="relative w-64 h-64 sm:w-72 sm:h-72 rounded-full overflow-hidden border-4 border-primary shadow-xl bg-slate-900 flex items-center justify-center my-1 transition-all">
            <!-- Video Camera Stream -->
            <video id="faceVideo" autoplay playsinline muted class="w-full h-full object-cover transform -scale-x-100"></video>
            
            <!-- Face Mesh Canvas Overlay -->
            <canvas id="faceOverlayCanvas" class="absolute inset-0 w-full h-full pointer-events-none transform -scale-x-100"></canvas>

            <!-- Snapshot Canvas (Hidden) -->
            <canvas id="faceCanvas" class="hidden"></canvas>

            <!-- Face ID Target Brackets & Laser Scan Line -->
            <div id="scannerOverlay" class="absolute inset-0 pointer-events-none flex flex-col items-center justify-between p-5">
                <!-- Top Bracket -->
                <div class="w-full flex justify-between">
                    <div class="w-7 h-7 border-t-4 border-l-4 border-cyan-400 rounded-tl-xl shadow-sm"></div>
                    <div class="w-7 h-7 border-t-4 border-r-4 border-cyan-400 rounded-tr-xl shadow-sm"></div>
                </div>

                <!-- Laser Scan Line -->
                <div class="w-full h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent shadow-[0_0_15px_#38bdf8] animate-scan-line"></div>

                <!-- Bottom Bracket -->
                <div class="w-full flex justify-between">
                    <div class="w-7 h-7 border-b-4 border-l-4 border-cyan-400 rounded-bl-xl shadow-sm"></div>
                    <div class="w-7 h-7 border-b-4 border-r-4 border-cyan-400 rounded-br-xl shadow-sm"></div>
                </div>
            </div>

            <!-- Captured Preview Snapshot -->
            <img id="faceCapturedPreview" alt="Snapshot Face ID" class="absolute inset-0 w-full h-full object-cover hidden rounded-full border-4 border-emerald-500 shadow-2xl" />
        </div>

        <!-- Status Detection Pill -->
        <div id="scanStatusBadge" class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-low text-primary font-label-sm text-label-sm font-semibold border border-surface-container">
            <span id="scanStatusDot" class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
            <span id="scanStatusText">Mempersiapkan Kamera Wajah...</span>
        </div>

        <!-- GPS Indicator -->
        <div class="flex items-center gap-1 text-[11px] text-on-surface-variant font-medium">
            <span class="material-symbols-outlined text-[14px] text-emerald-600">location_on</span>
            <span id="scanLocationText">Mendeteksi Titik GPS...</span>
        </div>

        <!-- Form Submission -->
        <form id="faceAttendanceForm" action="{{ route('guru.checkin') }}" method="POST" class="w-full flex flex-col gap-2 mt-1">
            @csrf
            <input type="hidden" name="face_image" id="inputFaceImage">
            <input type="hidden" name="latitude" id="inputLatitude">
            <input type="hidden" name="longitude" id="inputLongitude">

            <!-- Optional Note -->
            <input type="text" name="notes" id="inputNotes" placeholder="Catatan (opsional, misal: Piket / Lab Komputer)"
                   class="w-full px-3 py-2 rounded-xl bg-surface-container-low border border-surface-container text-xs text-center text-on-surface focus:outline-none focus:ring-2 focus:ring-primary">

            <!-- Primary Scan Capture Button -->
            <button type="button" id="btnCaptureFace" onclick="captureAndVerifyFace()"
                    class="w-full py-3 px-4 rounded-xl bg-primary hover:bg-primary-container text-on-primary font-bold font-headline-sm text-headline-sm shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[22px]">verified_user</span>
                <span id="btnCaptureText">Verifikasi & Absen Sekarang</span>
            </button>

            <!-- Fallback File Upload -->
            <div id="cameraFallbackBox" class="hidden text-center mt-1">
                <label class="text-[11px] text-primary hover:underline cursor-pointer font-semibold block">
                    <span>Atau ambil foto manual via kamera ponsel</span>
                    <input type="file" accept="image/*" capture="user" class="hidden" onchange="handleFallbackImage(this)">
                </label>
            </div>
        </form>

        <span class="text-[10px] text-on-surface-variant text-center leading-tight">
            Presensi biometrik otomatis memvalidasi wajah dengan profil terdaftar Anda.
        </span>

    </div>
</div>

<script src="{{ asset('js/face-api.js') }}"></script>
<script>
    // ====================================================
    // AI BIOMETRIC FACE RECOGNITION (FACE-API.JS)
    // ====================================================
    const FACE_MODELS_LOCAL_URI = "{{ asset('models/face-api') }}";
    const FACE_MODELS_CDN_URI = "https://cdn.jsdelivr.net/gh/cgarciagl/face-api.js@0.22.2/weights";
    let registeredFaceDescriptor = @json($teacher->face_descriptor_array);
    const teacherPhotoUrl = "{{ $teacher->photo_url }}";

    let isFaceApiLoaded = false;
    let isFaceApiLoading = false;

    // Load AI Models (TinyFaceDetector, FaceLandmark68, FaceRecognition)
    async function loadFaceApiModels() {
        if (isFaceApiLoaded) return true;
        if (isFaceApiLoading) {
            while (isFaceApiLoading) {
                await new Promise(r => setTimeout(r, 100));
            }
            return isFaceApiLoaded;
        }
        isFaceApiLoading = true;
        try {
            if (typeof faceapi === 'undefined') {
                console.error("face-api.js library is not available in global scope.");
                return false;
            }

            let loaded = false;
            // 1. Coba muat model dari direktori lokal aplikasi
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri(FACE_MODELS_LOCAL_URI),
                    faceapi.nets.faceLandmark68Net.loadFromUri(FACE_MODELS_LOCAL_URI),
                    faceapi.nets.faceRecognitionNet.loadFromUri(FACE_MODELS_LOCAL_URI)
                ]);
                loaded = true;
                console.log("✅ Face-API biometrik models loaded dari lokal.");
            } catch (localErr) {
                console.warn("Model lokal tidak dapat dimuat, mencoba cadangan CDN...", localErr);
                try {
                    await Promise.all([
                        faceapi.nets.tinyFaceDetector.loadFromUri(FACE_MODELS_CDN_URI),
                        faceapi.nets.faceLandmark68Net.loadFromUri(FACE_MODELS_CDN_URI),
                        faceapi.nets.faceRecognitionNet.loadFromUri(FACE_MODELS_CDN_URI)
                    ]);
                    loaded = true;
                    console.log("✅ Face-API biometrik models loaded dari CDN.");
                } catch (cdnErr) {
                    console.error("Gagal memuat model Face-API dari lokal maupun CDN:", cdnErr);
                }
            }

            if (loaded) {
                isFaceApiLoaded = true;
                ensureRegisteredDescriptorFromPhoto();

                // Trigger live detection jika modal kamera saat ini sedang terbuka
                const scanModal = document.getElementById('faceScanModal');
                if (scanModal && !scanModal.classList.contains('hidden') && videoStream) {
                    startLiveScanDetection();
                }
                const enrollModal = document.getElementById('faceEnrollModal');
                if (enrollModal && !enrollModal.classList.contains('hidden') && enrollStream) {
                    startLiveEnrollDetection();
                }
                return true;
            }
            return false;
        } catch (err) {
            console.error("Kesalahan inisialisasi Face-API:", err);
            return false;
        } finally {
            isFaceApiLoading = false;
        }
    }

    // Helper Kamera Adaptif & Tahan Error (Kompatibel dengan Laptop, Desktop USB Webcam, & Mobile)
    async function getBestCameraStream() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            const error = new Error("MEDIA_DEVICES_NOT_SUPPORTED");
            error.name = "MediaDevicesNotSupported";
            throw error;
        }

        // Percobaan 1: Kamera depan ideal untuk presensi wajah (Smartphone / Tablet)
        try {
            return await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: 'user',
                    width: { ideal: 640 },
                    height: { ideal: 480 }
                },
                audio: false
            });
        } catch (e1) {
            console.warn("Kamera depan spesifik gagal/tidak tersedia, fallback ke default video:", e1);
        }

        // Percobaan 2: Fallback ke perangkat kamera video default apapun (Desktop PC / Laptop Webcam / USB)
        return await navigator.mediaDevices.getUserMedia({
            video: true,
            audio: false
        });
    }

    // Helper Pesan Diagnostik Kamera yang Ramah Pengguna
    function getCameraErrorMessage(err) {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia || err.name === 'MediaDevicesNotSupported') {
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                return "Akses kamera diblokir browser karena koneksi jaringan tidak aman (HTTP). Silakan gunakan HTTPS atau buka via http://localhost.";
            }
            return "Perangkat atau browser Anda tidak mendukung akses streaming kamera.";
        }
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            return "Izin akses kamera belum diizinkan. Silakan klik ikon gembok/setelan di sebelah URL browser dan izinkan 'Kamera'.";
        }
        if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
            return "Perangkat kamera tidak ditemukan. Pastikan webcam telah terhubung ke komputer/laptop.";
        }
        if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
            return "Kamera sedang digunakan aplikasi lain (Zoom, Meet, atau tab lain). Silakan tutup aplikasi tersebut.";
        }
        if (err.name === 'OverconstrainedError') {
            return "Resolusi kamera tidak didukung oleh perangkat Anda.";
        }
        return "Gagal membuka kamera: " + (err.message || "Pastikan webcam aktif dan izin diaktifkan.");
    }

    // Ekstraksi descriptor biometrik dari foto profil jika belum tersimpan di kolom face_data
    async function ensureRegisteredDescriptorFromPhoto() {
        if (registeredFaceDescriptor && Array.isArray(registeredFaceDescriptor) && registeredFaceDescriptor.length > 50) {
            return registeredFaceDescriptor;
        }
        if (!teacherPhotoUrl || !isFaceApiLoaded) return null;

        try {
            const img = await faceapi.fetchImage(teacherPhotoUrl);
            const detection = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.4 }))
                .withFaceLandmarks()
                .withFaceDescriptor();

            if (detection && detection.descriptor) {
                registeredFaceDescriptor = Array.from(detection.descriptor);
                console.log("✅ Ekstraksi biometrik wajah dari foto profil guru berhasil.");

                // Simpan otomatis ke database agar menjadi acuan permanen
                fetch("{{ route('guru.face.sync_descriptor') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        face_descriptor: JSON.stringify(registeredFaceDescriptor)
                    })
                }).then(r => r.json()).then(res => {
                    if (res.success) {
                        console.log("✅ Descriptor biometrik tersimpan di database.");
                    }
                }).catch(e => console.warn("Sync descriptor error:", e));

                return registeredFaceDescriptor;
            }
        } catch (e) {
            console.warn("Tidak dapat mengekstrak descriptor dari foto profil:", e);
        }
        return null;
    }

    // Konfigurasi Sekolah & Titik Lokasi dari Administrator
    const schoolConfig = {
        name: "{{ addslashes($setting->school_name ?? 'Yayasan Barul Ulum') }}",
        lat: {{ (float)($setting->latitude ?? 0) }},
        lng: {{ (float)($setting->longitude ?? 0) }},
        radius: {{ (int)($setting->radius_meters ?? 150) }},
        isGeofenceActive: {{ $setting->is_geofence_active ? 'true' : 'false' }}
    };

    // Konfigurasi Jadwal Presensi Hari Ini
    const scheduleConfig = {
        isHoliday: {{ $setting->is_holiday_today ? 'true' : 'false' }},
        timeIn: "{{ substr($setting->time_in, 0, 5) }}",
        timeInLateThreshold: "{{ substr($setting->time_in_late_threshold, 0, 5) }}",
        timeOut: "{{ substr($setting->time_out, 0, 5) }}",
        isAttended: {{ $todayAttendance ? 'true' : 'false' }}
    };

    function isBeforeTimeInNow() {
        if (scheduleConfig.isHoliday || !scheduleConfig.timeIn) return false;
        const now = new Date();
        const parts = scheduleConfig.timeIn.split(':');
        if (parts.length < 2) return false;
        const scheduleIn = new Date();
        scheduleIn.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10), 0, 0);
        return now < scheduleIn;
    }

    function isAfterTimeOutNow() {
        if (scheduleConfig.isHoliday || !scheduleConfig.timeOut) return false;
        const now = new Date();
        const parts = scheduleConfig.timeOut.split(':');
        if (parts.length < 2) return false;
        const scheduleOut = new Date();
        scheduleOut.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10), 0, 0);
        return now > scheduleOut;
    }

    function isBeforeTimeOutNow() {
        if (scheduleConfig.isHoliday || !scheduleConfig.timeOut) return false;
        const now = new Date();
        const parts = scheduleConfig.timeOut.split(':');
        if (parts.length < 2) return false;
        const scheduleOut = new Date();
        scheduleOut.setHours(parseInt(parts[0], 10), parseInt(parts[1], 10), 0, 0);
        return now < scheduleOut;
    }

    function updateScheduleStatusUi() {
        if (scheduleConfig.isHoliday || scheduleConfig.isAttended) return;
        const btn = document.getElementById('btnActionMasuk');
        const icon = document.getElementById('btnActionMasukIcon');
        const badge = document.getElementById('badgeStatusMasuk');
        const btnText = document.getElementById('btnActionMasukText');
        const subText = document.getElementById('subActionMasukText');
        
        if (!btn || !badge) return;

        if (isAfterTimeOutNow()) {
            btn.className = "w-full py-3.5 px-4 rounded-xl bg-slate-200 border border-slate-300 text-slate-500 font-bold font-headline-sm text-headline-sm cursor-not-allowed flex items-center justify-center gap-2";
            if (icon) icon.textContent = "cancel";
            if (btnText) btnText.textContent = `Presensi Masuk Ditutup (Lewat ${scheduleConfig.timeOut} WIB)`;
            if (subText) {
                subText.textContent = `Jam kegiatan sekolah hari ini telah berakhir pada pukul ${scheduleConfig.timeOut} WIB. Presensi masuk telah ditutup.`;
                subText.className = "text-[11px] text-center text-rose-700 font-medium";
            }
            badge.className = "px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300 flex items-center gap-1";
            badge.innerHTML = `<span class="material-symbols-outlined text-[14px]">cancel</span><span>Presensi Ditutup</span>`;
        } else if (isBeforeTimeInNow()) {
            btn.className = "w-full py-3.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-600 shadow-amber-500/20 text-white font-bold font-headline-sm text-headline-sm shadow-md active:scale-[0.98] transition-all flex items-center justify-center gap-2";
            if (icon) icon.textContent = "lock_clock";
            if (btnText) btnText.textContent = `Presensi Belum Dibuka (Mulai ${scheduleConfig.timeIn} WIB)`;
            if (subText) {
                subText.textContent = `Sesuai aturan jadwal absen, guru baru boleh absen setelah pukul ${scheduleConfig.timeIn} WIB.`;
                subText.className = "text-[11px] text-center text-amber-800 font-semibold";
            }
            badge.className = "px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300 flex items-center gap-1";
            badge.innerHTML = `<span class="material-symbols-outlined text-[14px]">lock_clock</span><span>Belum Buka (${scheduleConfig.timeIn} WIB)</span>`;
        } else {
            btn.className = "w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold font-headline-sm text-headline-sm shadow-md active:scale-[0.98] transition-all flex items-center justify-center gap-2";
            if (icon) icon.textContent = "photo_camera_front";
            if (btnText) btnText.textContent = "Absen Masuk Sekarang (Scan Muka)";
            if (subText) {
                subText.textContent = "Posisikan wajah Anda di depan kamera ponsel di area sekolah.";
                subText.className = "text-[11px] text-center text-emerald-800 font-medium";
            }
            badge.className = "px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200";
            badge.innerHTML = `<span>Belum Absen Masuk</span>`;
        }
    }

    // State GPS Guru
    let teacherGps = {
        lat: null,
        lng: null,
        distance: null,
        isInside: false,
        ready: false,
        error: null
    };

    // Status pendaftaran wajah guru
    const isTeacherFaceRegistered = {{ $teacher->is_face_registered ? 'true' : 'false' }};

    // Rumus Haversine Akurat (menghitung jarak meter antara 2 koordinat bumi)
    function calculateDistance(lat1, lon1, lat2, lon2) {
        if (!lat1 || !lon1 || !lat2 || !lon2) return 0;
        const R = 6371000; // Radius bumi dalam meter
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon / 2) * Math.sin(dLon / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return Math.round(R * c);
    }

    // Live Tracker Geofence GPS Real-Time
    function initGeofenceTracker() {
        if (!schoolConfig.isGeofenceActive || !schoolConfig.lat || !schoolConfig.lng) {
            teacherGps.isInside = true;
            teacherGps.ready = true;
            applyGeofenceUiState(true, 0, 0, null, null, true);
            return;
        }

        if (!navigator.geolocation) {
            teacherGps.error = "Perangkat Anda tidak mendukung fitur GPS.";
            updateGeofenceUiError(teacherGps.error);
            return;
        }

        // Hentikan watch sebelumnya jika sudah ada
        if (teacherGps.watchId !== null) {
            navigator.geolocation.clearWatch(teacherGps.watchId);
        }

        // Watch Position Real-Time (Mengikuti pergerakan guru secara langsung)
        teacherGps.watchId = navigator.geolocation.watchPosition(
            function(pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const accuracy = Math.round(pos.coords.accuracy || 0);

                teacherGps.lat = lat;
                teacherGps.lng = lng;
                teacherGps.accuracy = accuracy;
                teacherGps.ready = true;
                teacherGps.error = null;

                // Sinkronisasi ke input form modal absen
                const inLat = document.getElementById('inputLatitude');
                const inLng = document.getElementById('inputLongitude');
                if (inLat) inLat.value = lat;
                if (inLng) inLng.value = lng;

                // Hitung jarak Haversine ke titik pusat sekolah
                const dist = calculateDistance(lat, lng, schoolConfig.lat, schoolConfig.lng);
                teacherGps.distance = dist;
                const isInside = dist <= schoolConfig.radius;
                teacherGps.isInside = isInside;

                // Perbarui tampilan halaman secara real-time
                applyGeofenceUiState(isInside, dist, accuracy, lat, lng, false);
            },
            function(err) {
                let msg = "Izin lokasi GPS belum diaktifkan di browser/ponsel Anda.";
                if (err.code === 1) msg = "Izin lokasi GPS ditolak oleh peramban web Anda.";
                else if (err.code === 2) msg = "Sinyal GPS tidak tersedia atau lemah.";
                else if (err.code === 3) msg = "Waktu tunggu GPS habis (timeout).";

                teacherGps.error = msg;
                teacherGps.ready = false;
                updateGeofenceUiError(msg);
            },
            { enableHighAccuracy: true, maximumAge: 3000, timeout: 12000 }
        );
    }

    // Terapkan Status Geofence ke Tampilan Halaman Guru Secara Real-Time
    function applyGeofenceUiState(isInside, dist, accuracy, lat, lng, isFreeRadius = false) {
        const banner = document.getElementById('geofenceBanner');
        const iconWrapper = document.getElementById('geofenceIconWrapper');
        const icon = document.getElementById('geofenceIcon');
        const liveDot = document.getElementById('geofenceLiveDot');
        const title = document.getElementById('geofenceTitle');
        const badge = document.getElementById('geofenceBadge');
        const text = document.getElementById('geofenceText');
        const mDist = document.getElementById('metricDistance');
        const mRad = document.getElementById('metricRadius');
        const mAcc = document.getElementById('metricAccuracy');
        const alertBanner = document.getElementById('outsideRadiusAlertBanner');
        const outsideDistVal = document.getElementById('outsideDistanceVal');
        const outsideDesc = document.getElementById('outsideRadiusDesc');
        const badgeMasuk = document.getElementById('badgeCardMasukStatus');
        const subMasuk = document.getElementById('subCardMasukText');
        const badgePulang = document.getElementById('badgeCardPulangStatus');
        const subPulang = document.getElementById('subCardPulangText');
        const locText = document.getElementById('scanLocationText');

        // Kasus 1: Bebas Radius (Geofence Nonaktif)
        if (isFreeRadius) {
            if (banner) banner.className = "p-3.5 rounded-2xl bg-emerald-50/90 border border-emerald-300 text-emerald-950 transition-all duration-300 flex flex-col gap-2.5 shadow-2xs";
            if (iconWrapper) iconWrapper.className = "w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs";
            if (icon) icon.className = "fa-solid fa-earth-asia text-xs";
            if (liveDot) liveDot.className = "w-2 h-2 rounded-full bg-emerald-500";
            if (title) title.textContent = "Lokasi Bebas Radius";
            if (badge) {
                badge.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300";
                badge.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-600 text-[10px]"></i><span>Bebas Radius</span>`;
            }
            if (text) text.innerHTML = `Pembatasan radius dinonaktifkan oleh Administrator. Presensi dapat dilakukan dari mana saja.`;
            if (mDist) {
                mDist.className = "px-2 py-0.5 rounded-md bg-emerald-100 border border-emerald-200 text-emerald-800 font-bold";
                mDist.innerHTML = `<i class="fa-solid fa-check text-[9px] text-emerald-600 mr-1"></i>Bebas Area`;
            }
            if (alertBanner) alertBanner.classList.add('hidden');
            if (locText) locText.innerHTML = `<span class="text-emerald-700 font-bold">📍 Lokasi: Bebas Radius</span>`;
            return;
        }

        const distKm = (dist >= 1000) ? (dist / 1000).toFixed(2).replace('.', ',') + ' km' : dist + ' m';
        const excess = Math.max(0, dist - schoolConfig.radius);
        const excessKm = (excess >= 1000) ? (excess / 1000).toFixed(2).replace('.', ',') + ' km' : excess + ' m';

        if (isInside) {
            // ==========================================
            // KASUS 2: DI DALAM JANGKAUAN (VALID)
            // ==========================================
            if (banner) banner.className = "p-3.5 rounded-2xl bg-emerald-50/95 border border-emerald-300 text-emerald-950 transition-all duration-300 flex flex-col gap-2.5 shadow-xs ring-1 ring-emerald-200/50";
            if (iconWrapper) iconWrapper.className = "w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs";
            if (icon) icon.className = "fa-solid fa-circle-check text-xs";
            if (liveDot) liveDot.className = "w-2 h-2 rounded-full bg-emerald-500 animate-pulse";
            if (title) title.textContent = "Lokasi Memenuhi Syarat Presensi";
            if (badge) {
                badge.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300";
                badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span><span>Dalam Radius (${dist}m)</span>`;
            }
            if (text) {
                text.innerHTML = `Posisi Anda terdeteksi <b>${dist} meter</b> dari titik pusat <b>${schoolConfig.name}</b>. Anda berada di dalam batas radius resmi (Maksimal <b>${schoolConfig.radius}m</b>). Presensi Face ID siap digunakan.`;
            }
            if (mDist) {
                mDist.className = "px-2 py-0.5 rounded-md bg-emerald-100 border border-emerald-300 text-emerald-900 font-bold";
                mDist.innerHTML = `<i class="fa-solid fa-route text-[9px] text-emerald-600 mr-1"></i>Jarak: ${dist} m (Valid)`;
            }
            if (mRad) {
                mRad.innerHTML = `<i class="fa-solid fa-bullseye text-[9px] text-emerald-600 mr-1"></i>Radius Maks: ${schoolConfig.radius}m`;
            }
            if (mAcc && accuracy > 0) {
                mAcc.classList.remove('hidden');
                mAcc.innerHTML = `<i class="fa-solid fa-crosshairs text-[9px] mr-1"></i>Akurasi: ±${accuracy}m`;
            }

            // Sembunyikan alert peringatan di luar jangkauan
            if (alertBanner) alertBanner.classList.add('hidden');

            // Kembalikan badge kartu ke mode normal BUKA
            if (badgeMasuk) {
                badgeMasuk.textContent = "BUKA";
                badgeMasuk.className = "px-2 py-0.5 rounded-full bg-white text-[#13793e] font-black text-[10px] tracking-wider shadow-xs";
            }
            if (subMasuk) {
                subMasuk.innerHTML = `<i class="fa-solid fa-camera-rotate text-[10px]"></i> Tap Kamera Selfie`;
            }
            if (badgePulang) {
                badgePulang.textContent = "BUKA";
                badgePulang.className = "px-2 py-0.5 rounded-full bg-white text-[#13793e] font-black text-[10px] tracking-wider shadow-xs";
            }
            if (subPulang) {
                subPulang.innerHTML = `<i class="fa-solid fa-camera-rotate text-[10px]"></i> Tap Kamera Selfie`;
            }

            // Status GPS di Modal Kamera
            if (locText) {
                locText.innerHTML = `<span class="text-emerald-700 font-bold flex items-center gap-1"><i class="fa-solid fa-circle-check text-xs"></i> 📍 GPS Valid: Dalam Radius (${dist}m)</span>`;
            }

        } else {
            // ==========================================
            // KASUS 3: DI LUAR JANGKAUAN (TIDAK BOLEH ABSEN)
            // ==========================================
            if (banner) banner.className = "p-3.5 rounded-2xl bg-rose-50 border border-rose-300 text-rose-950 transition-all duration-300 flex flex-col gap-2.5 shadow-sm ring-1 ring-rose-200";
            if (iconWrapper) iconWrapper.className = "w-8 h-8 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-xs animate-pulse";
            if (icon) icon.className = "fa-solid fa-triangle-exclamation text-xs";
            if (liveDot) liveDot.className = "w-2 h-2 rounded-full bg-rose-500 animate-ping";
            if (title) title.textContent = "⛔ DI LUAR JANGKAUAN RADIUS SEKOLAH";
            if (badge) {
                badge.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-black bg-rose-100 text-rose-800 border border-rose-300";
                badge.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-rose-600 animate-ping"></span><span>Di Luar Area (+${excessKm})</span>`;
            }
            if (text) {
                text.innerHTML = `Perhatian! Anda terdeteksi berjarak <b>${distKm} (${dist} meter)</b> dari lingkungan <b>${schoolConfig.name}</b>. Batas radius maksimal adalah <b>${schoolConfig.radius} meter</b> (Anda berada <b>${excessKm} di luar batas area</b>). Presensi Masuk & Pulang terkunci secara otomatis.`;
            }
            if (mDist) {
                mDist.className = "px-2 py-0.5 rounded-md bg-rose-100 border border-rose-300 text-rose-900 font-black";
                mDist.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-[9px] text-rose-600 mr-1"></i>Jarak: ${distKm} (Di Luar)`;
            }
            if (mRad) {
                mRad.innerHTML = `<i class="fa-solid fa-bullseye text-[9px] text-rose-600 mr-1"></i>Radius Maks: ${schoolConfig.radius}m`;
            }
            if (mAcc && accuracy > 0) {
                mAcc.classList.remove('hidden');
                mAcc.innerHTML = `<i class="fa-solid fa-crosshairs text-[9px] mr-1"></i>Akurasi: ±${accuracy}m`;
            }

            // Tampilkan Banner Peringatan Menonjol di Atas Gerbang Presensi
            if (alertBanner) {
                alertBanner.classList.remove('hidden');
            }
            if (outsideDistVal) {
                outsideDistVal.textContent = `${distKm} (${dist} meter)`;
            }
            if (outsideDesc) {
                outsideDesc.innerHTML = `Posisi Anda terdeteksi berjarak <strong>${distKm} (${dist} meter)</strong> dari <strong>${schoolConfig.name}</strong> (Batas radius maksimal: <strong>${schoolConfig.radius}m</strong>, selisih <strong>+${excessKm} di luar area</strong>). Tombol presensi ditahan hingga Anda berada di dalam lingkungan sekolah.`;
            }

            // Perbarui Badge Tombol Presensi Menjadi Peringatan "DI LUAR AREA"
            if (badgeMasuk) {
                badgeMasuk.textContent = "DI LUAR AREA";
                badgeMasuk.className = "px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-300 font-black text-[9px] tracking-wider shadow-xs";
            }
            if (subMasuk) {
                subMasuk.innerHTML = `<i class="fa-solid fa-location-crosshairs text-[10px] text-rose-300"></i> Terkunci (${distKm})`;
            }
            if (badgePulang) {
                badgePulang.textContent = "DI LUAR AREA";
                badgePulang.className = "px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-300 font-black text-[9px] tracking-wider shadow-xs";
            }
            if (subPulang) {
                subPulang.innerHTML = `<i class="fa-solid fa-location-crosshairs text-[10px] text-rose-300"></i> Terkunci (${distKm})`;
            }

            // Status GPS di Modal Kamera
            if (locText) {
                locText.innerHTML = `<span class="text-rose-600 font-bold flex items-center gap-1 animate-pulse"><i class="fa-solid fa-triangle-exclamation text-xs"></i> ⛔ Di Luar Radius: ${distKm} (Maks: ${schoolConfig.radius}m)</span>`;
            }
        }
    }

    // Tampilan Saat Terjadi Kesalahan GPS / Izin Ditolak
    function updateGeofenceUiError(msg) {
        const banner = document.getElementById('geofenceBanner');
        const iconWrapper = document.getElementById('geofenceIconWrapper');
        const icon = document.getElementById('geofenceIcon');
        const liveDot = document.getElementById('geofenceLiveDot');
        const title = document.getElementById('geofenceTitle');
        const badge = document.getElementById('geofenceBadge');
        const text = document.getElementById('geofenceText');
        const mDist = document.getElementById('metricDistance');
        const alertBanner = document.getElementById('outsideRadiusAlertBanner');
        const locText = document.getElementById('scanLocationText');

        if (banner) banner.className = "p-3.5 rounded-2xl bg-amber-50 border border-amber-300 text-amber-950 transition-all duration-300 flex flex-col gap-2.5 shadow-2xs";
        if (iconWrapper) iconWrapper.className = "w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs";
        if (icon) icon.className = "fa-solid fa-location-slash text-xs";
        if (liveDot) liveDot.className = "w-2 h-2 rounded-full bg-amber-500 animate-ping";
        if (title) title.textContent = "Izin Lokasi GPS Belum Aktif";
        if (badge) {
            badge.className = "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300";
            badge.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-amber-600 text-[10px]"></i><span>GPS Mati</span>`;
        }
        if (text) {
            text.innerHTML = `Sistem memerlukan akses koordinat GPS Anda: <b>${msg}</b>. Pastikan GPS/Lokasi HP aktif dan izinkan peramban mengakses lokasi, lalu tekan tombol perbarui di sebelah kanan.`;
        }
        if (mDist) {
            mDist.className = "px-2 py-0.5 rounded-md bg-amber-100 border border-amber-300 text-amber-900 font-bold";
            mDist.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-[9px] text-amber-600 mr-1"></i>GPS Belum Siap`;
        }
        if (alertBanner) alertBanner.classList.add('hidden');
        if (locText) {
            locText.innerHTML = `<span class="text-amber-700 font-bold flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation text-xs"></i> ⚠️ GPS Belum Aktif (${msg})</span>`;
        }
    }

    // Tombol Refresh GPS Manual (Segarkan Koordinat)
    function refreshGeofenceLocation(isManual = false) {
        const refreshIcon = document.getElementById('geofenceRefreshIcon');
        if (refreshIcon) refreshIcon.classList.add('fa-spin');

        if (!navigator.geolocation) {
            if (refreshIcon) refreshIcon.classList.remove('fa-spin');
            updateGeofenceUiError("Perangkat Anda tidak mendukung fitur GPS.");
            return;
        }

        if (isManual) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'info',
                title: 'Menyelaraskan koordinat GPS...'
            });
        }

        navigator.geolocation.getCurrentPosition(
            function(pos) {
                if (refreshIcon) refreshIcon.classList.remove('fa-spin');
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const accuracy = Math.round(pos.coords.accuracy || 0);

                teacherGps.lat = lat;
                teacherGps.lng = lng;
                teacherGps.accuracy = accuracy;
                teacherGps.ready = true;
                teacherGps.error = null;

                const inLat = document.getElementById('inputLatitude');
                const inLng = document.getElementById('inputLongitude');
                if (inLat) inLat.value = lat;
                if (inLng) inLng.value = lng;

                const dist = calculateDistance(lat, lng, schoolConfig.lat, schoolConfig.lng);
                teacherGps.distance = dist;
                const isInside = dist <= schoolConfig.radius;
                teacherGps.isInside = isInside;

                applyGeofenceUiState(isInside, dist, accuracy, lat, lng, !schoolConfig.isGeofenceActive);

                if (isManual) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                    const distKm = (dist >= 1000) ? (dist / 1000).toFixed(2).replace('.', ',') + ' km' : dist + ' m';
                    Toast.fire({
                        icon: isInside ? 'success' : 'warning',
                        title: isInside ? `📍 Dalam Radius: ${dist}m` : `⛔ Di Luar Radius: ${distKm}`
                    });
                }
            },
            function(err) {
                if (refreshIcon) refreshIcon.classList.remove('fa-spin');
                let msg = "Gagal memperbarui lokasi GPS.";
                if (err.code === 1) msg = "Izin akses lokasi ditolak.";
                teacherGps.error = msg;
                updateGeofenceUiError(msg);
            },
            { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 }
        );
    }

    // Alias fungsi untuk modal kamera
    function fetchGpsLocation() {
        refreshGeofenceLocation(false);
    }

    // Live Digital Clock WIB & Real-Time Schedule Checker
    (function initLiveClock() {
        const clockEl = document.getElementById('digital-clock');
        
        function update() {
            const now = new Date();
            const pad = (n) => String(n).padStart(2, '0');
            if (clockEl) {
                clockEl.textContent = `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
            }
            updateScheduleStatusUi();
        }
        update();
        setInterval(update, 1000);
        initGeofenceTracker();
    })();

    // Handler Utama ketika tombol Presensi MASUK / PULANG ditekan
    function handleAttendanceAction(type) {
        // Validasi Jadwal untuk Presensi MASUK
        if (type === 'masuk') {
            if (scheduleConfig.isHoliday) {
                Swal.fire({
                    icon: 'info',
                    title: 'Hari Libur Sekolah',
                    text: 'Hari ini adalah hari libur sekolah. Presensi masuk tidak dibuka.',
                    confirmButtonColor: '#004ac6'
                });
                return;
            }

            if (isBeforeTimeInNow()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Presensi Belum Dibuka!',
                    html: `<div class="text-sm space-y-3 text-slate-700 text-left">
                             <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900">
                                <p class="font-bold text-base mb-1">Belum Waktunya Presensi Masuk</p>
                                <p class="text-xs">Sesuai jadwal sekolah, presensi masuk hari ini baru dibuka pada pukul <b>${scheduleConfig.timeIn} WIB</b>.</p>
                             </div>
                             <p class="text-xs text-slate-600">
                               Sesuai aturan presensi, guru tidak diperkenankan absen sebelum jam masuk yang telah dijadwalkan.
                             </p>
                           </div>`,
                    confirmButtonColor: '#d97706',
                    confirmButtonText: 'Saya Mengerti'
                });
                return;
            }

            if (isAfterTimeOutNow()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Presensi Masuk Telah Ditutup!',
                    html: `<div class="text-sm space-y-3 text-slate-700 text-left">
                             <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-900">
                                <p class="font-bold text-base mb-1">Jam Kegiatan Sekolah Berakhir</p>
                                <p class="text-xs">Jam kepulangan sekolah hari ini telah selesai pada pukul <b>${scheduleConfig.timeOut} WIB</b>.</p>
                             </div>
                             <p class="text-xs text-slate-600">
                               Sesuai aturan absensi, guru yang belum absen masuk sampai jam kepulangan berakhir tidak dapat lagi melakukan presensi masuk hari ini.
                             </p>
                           </div>`,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Tutup'
                });
                return;
            }
        }

        // 1. Validasi Registrasi Wajah
        if (!isTeacherFaceRegistered) {
            Swal.fire({
                title: 'Wajah Belum Terdaftar!',
                html: '<div class="text-sm text-slate-600 space-y-2"><p>Halo <b>{{ $teacher->name }}</b>,</p><p>Anda belum mendaftarkan wajah di sistem SIMPRES BU. Perekaman wajah diperlukan satu kali agar sistem mengenali siapa yang absen dan mencegah kecurangan presensi.</p></div>',
                icon: 'warning',
                confirmButtonColor: '#004ac6',
                confirmButtonText: 'Mulai Rekam Wajah Sekarang',
                showCancelButton: true,
                cancelButtonText: 'Nanti Dulu'
            }).then((res) => {
                if (res.isConfirmed) {
                    openEnrollFaceModal();
                }
            });
            return;
        }

        // 2. Validasi Geofence (Titik Lokasi yang Ditentukan Admin)
        if (schoolConfig.isGeofenceActive && schoolConfig.lat && schoolConfig.lng) {
            if (!teacherGps.ready) {
                Swal.fire({
                    title: 'Memeriksa Titik Lokasi...',
                    text: 'Sedang mengunci koordinat GPS perangkat Anda. Pastikan GPS/Lokasi aktif di ponsel/browser Anda.',
                    icon: 'info',
                    confirmButtonColor: '#004ac6',
                    confirmButtonText: 'Coba Lagi'
                }).then(() => {
                    refreshGeofenceLocation(true);
                });
                return;
            }

            if (!teacherGps.isInside) {
                const dist = teacherGps.distance || 0;
                const distKm = (dist >= 1000) ? (dist / 1000).toFixed(2).replace('.', ',') + ' km' : dist + ' meter';
                const excess = Math.max(0, dist - schoolConfig.radius);
                const excessKm = (excess >= 1000) ? (excess / 1000).toFixed(2).replace('.', ',') + ' km' : excess + ' meter';

                Swal.fire({
                    icon: 'error',
                    title: 'Di Luar Jangkauan Sekolah!',
                    html: `<div class="text-sm space-y-3 text-slate-700 text-left">
                             <div class="p-3 rounded-2xl bg-rose-50 border border-rose-300 text-rose-950">
                               <p class="font-extrabold text-sm mb-1 flex items-center gap-1.5 text-rose-900">
                                 <i class="fa-solid fa-location-crosshairs text-rose-600"></i>
                                 <span>Presensi Tidak Dapat Dilakukan</span>
                               </p>
                               <p class="text-xs leading-relaxed">
                                 Posisi Anda saat ini berjarak <b>${distKm} (${dist} meter)</b> dari titik sekolah.
                               </p>
                               <div class="mt-2 pt-2 border-t border-rose-200 text-[11px] space-y-0.5">
                                 <p>• Batas radius maksimal: <b>${schoolConfig.radius} meter</b></p>
                                 <p>• Selisih di luar radius: <b class="text-rose-700">+${excessKm}</b></p>
                                 <p>• Lokasi tujuan: <b>${schoolConfig.name}</b></p>
                               </div>
                             </div>
                             <p class="text-xs text-slate-600 leading-relaxed">
                               Administrator menetapkan batas radius geofence agar presensi hanya dapat dilakukan di dalam lingkungan sekolah.
                             </p>
                             <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 text-xs">
                               <span class="font-bold">Saran:</span> Silakan berada di dalam area sekolah, lalu tekan tombol <b>Segarkan GPS</b> pada radar lokasi.
                             </div>
                           </div>`,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Saya Mengerti',
                    showCancelButton: true,
                    cancelButtonText: 'Segarkan GPS',
                    cancelButtonColor: '#004ac6'
                }).then((res) => {
                    if (res.dismiss === Swal.DismissReason.cancel) {
                        refreshGeofenceLocation(true);
                    }
                });
                return;
            }
        }

        // 3. Buka pemindai kamera dengan tema spesifik Masuk atau Pulang
        openFaceScanModal(type);
    }

    // ===================================
    // 1. FACE ENROLLMENT (PENDAFTARAN WAJAH BIOMETRIK)
    // ===================================
    let enrollStream = null;
    let enrollDetectInterval = null;
    let lastEnrollDescriptor = null;

    function openEnrollFaceModal() {
        closeFaceScanModal();
        const modal = document.getElementById('faceEnrollModal');
        const capturedImg = document.getElementById('enrollCapturedPreview');
        capturedImg.classList.add('hidden');
        document.getElementById('inputEnrollFaceImage').value = '';
        document.getElementById('inputEnrollFaceDescriptor').value = '';
        lastEnrollDescriptor = null;

        const btn = document.getElementById('btnCaptureEnroll');
        btn.disabled = true;
        btn.className = "w-full py-3 px-4 rounded-xl bg-slate-400 text-white font-bold font-headline-sm text-headline-sm shadow-none opacity-60 cursor-not-allowed flex items-center justify-center gap-2";

        modal.classList.remove('hidden');
        startEnrollCamera();
    }

    function closeEnrollFaceModal() {
        const modal = document.getElementById('faceEnrollModal');
        modal.classList.add('hidden');
        stopEnrollCamera();
    }

    async function startEnrollCamera() {
        const video = document.getElementById('enrollVideo');
        const statusText = document.getElementById('enrollStatusText');
        const fallbackBox = document.getElementById('enrollFallbackBox');

        statusText.innerHTML = '<span class="text-primary font-medium animate-pulse">Menghubungkan Kamera...</span>';

        // 1. Inisialisasi model di background
        const modelPromise = loadFaceApiModels();

        // 2. Buka kamera secara langsung
        try {
            const stream = await getBestCameraStream();
            enrollStream = stream;
            video.srcObject = stream;
            
            try {
                await video.play();
            } catch (playErr) {
                console.warn("video.play() warning on enroll:", playErr);
            }

            statusText.innerHTML = '<span class="text-amber-600 font-medium animate-pulse">Kamera aktif • Menghubungkan modul AI...</span>';

            // 3. Pastikan model AI sudah siap
            await modelPromise;

            // Tunggu frame pertama video siap
            const waitForFrame = () => {
                if (video.videoWidth > 0 && video.videoHeight > 0) {
                    statusText.textContent = "Arahkan wajah tegak lurus ke dalam lingkaran...";
                    startLiveEnrollDetection();
                } else {
                    setTimeout(waitForFrame, 100);
                }
            };
            waitForFrame();

        } catch (camErr) {
            console.error("Gagal membuka kamera enroll:", camErr);
            const errMsg = getCameraErrorMessage(camErr);
            statusText.innerHTML = `<span class="text-rose-600 font-medium">${errMsg}</span>`;
            if (fallbackBox) fallbackBox.classList.remove('hidden');
        }
    }

    function stopEnrollCamera() {
        clearInterval(enrollDetectInterval);
        enrollDetectInterval = null;
        if (enrollStream) {
            enrollStream.getTracks().forEach(track => track.stop());
            enrollStream = null;
        }
        const video = document.getElementById('enrollVideo');
        if (video) video.srcObject = null;
        const canvas = document.getElementById('enrollOverlayCanvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }

    function startLiveEnrollDetection() {
        const video = document.getElementById('enrollVideo');
        const statusText = document.getElementById('enrollStatusText');
        const statusBadge = document.getElementById('enrollStatusBadge');
        const btn = document.getElementById('btnCaptureEnroll');
        const overlayCanvas = document.getElementById('enrollOverlayCanvas');
        const wrapper = document.getElementById('enrollVideoWrapper');

        if (!video || !overlayCanvas) return;
        if (!isFaceApiLoaded || typeof faceapi === 'undefined') {
            console.warn("Face-API belum siap untuk deteksi enroll.");
            return;
        }

        const width = video.videoWidth || 320;
        const height = video.videoHeight || 320;
        overlayCanvas.width = width;
        overlayCanvas.height = height;
        const displaySize = { width, height };
        try {
            faceapi.matchDimensions(overlayCanvas, displaySize);
        } catch (e) {
            console.warn("matchDimensions error:", e);
        }

        clearInterval(enrollDetectInterval);
        enrollDetectInterval = setInterval(async () => {
            if (!enrollStream || video.paused || video.ended) return;

            try {
                const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.5 });
                const detection = await faceapi.detectSingleFace(video, options)
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                const ctx = overlayCanvas.getContext('2d');
                ctx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);

                if (detection && detection.detection.score > 0.55) {
                    lastEnrollDescriptor = Array.from(detection.descriptor);
                    const resized = faceapi.resizeResults(detection, displaySize);
                    faceapi.draw.drawFaceLandmarks(overlayCanvas, resized);

                    statusText.innerHTML = '<span class="text-emerald-700 font-bold">✅ Wajah Terdeteksi Jelas! Siap Disimpan</span>';
                    statusBadge.className = "flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 font-label-sm text-label-sm font-semibold border border-emerald-300";
                    if (wrapper) wrapper.className = "relative w-64 h-64 sm:w-72 sm:h-72 rounded-full overflow-hidden border-4 border-emerald-500 shadow-xl bg-slate-900 flex items-center justify-center my-1 ring-4 ring-emerald-300/40 transition-all";
                    btn.disabled = false;
                    btn.className = "w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold font-headline-sm text-headline-sm shadow-md active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer";
                } else {
                    lastEnrollDescriptor = null;
                    statusText.innerHTML = '<span>Posisikan wajah tepat di tengah lingkaran...</span>';
                    statusBadge.className = "flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 font-label-sm text-label-sm font-semibold border border-amber-300";
                    if (wrapper) wrapper.className = "relative w-64 h-64 sm:w-72 sm:h-72 rounded-full overflow-hidden border-4 border-primary shadow-xl bg-slate-900 flex items-center justify-center my-1 transition-all";
                    btn.disabled = true;
                    btn.className = "w-full py-3 px-4 rounded-xl bg-slate-400 text-white font-bold font-headline-sm text-headline-sm shadow-none opacity-60 cursor-not-allowed flex items-center justify-center gap-2";
                }
            } catch (err) {
                console.warn("Live enroll detection error:", err);
            }
        }, 300);
    }

    async function captureAndEnrollFace() {
        const video = document.getElementById('enrollVideo');
        const canvas = document.getElementById('enrollCanvas');
        const capturedImg = document.getElementById('enrollCapturedPreview');
        const statusText = document.getElementById('enrollStatusText');
        const btn = document.getElementById('btnCaptureEnroll');

        if (!lastEnrollDescriptor && !document.getElementById('inputEnrollFaceDescriptor').value) {
            Swal.fire({
                title: 'Wajah Belum Terdeteksi',
                text: 'Pastikan wajah Anda berada di dalam lingkaran dengan pencahayaan yang cukup hingga terdeteksi sistem.',
                icon: 'warning',
                confirmButtonColor: '#004ac6',
                confirmButtonText: 'Coba Lagi',
                customClass: { popup: 'rounded-2xl shadow-xl' }
            });
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-[20px] animate-spin">sync</span><span>Mendaftarkan Wajah Biometrik...</span>';

        if (enrollStream && video.videoWidth > 0) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
            document.getElementById('inputEnrollFaceImage').value = dataUrl;
            document.getElementById('inputEnrollFaceDescriptor').value = JSON.stringify(lastEnrollDescriptor);

            capturedImg.src = dataUrl;
            capturedImg.classList.remove('hidden');
        }

        statusText.innerHTML = '<span class="text-emerald-700 font-bold">✅ Data biometrik wajah berhasil direkam! Menyimpan...</span>';

        Swal.fire({
            title: 'Mendaftarkan Face ID...',
            html: '<div class="text-sm text-slate-600">Menyimpan acuan biometrik wajah resmi atas nama <b>{{ addslashes($teacher->name) }}</b>...</div>',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        setTimeout(() => {
            stopEnrollCamera();
            document.getElementById('enrollFaceForm').submit();
        }, 500);
    }

    function handleEnrollFallback(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = async function(e) {
                const img = new Image();
                img.onload = async () => {
                    await loadFaceApiModels();
                    const detect = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.5 }))
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (detect) {
                        lastEnrollDescriptor = Array.from(detect.descriptor);
                        document.getElementById('inputEnrollFaceDescriptor').value = JSON.stringify(lastEnrollDescriptor);
                        document.getElementById('inputEnrollFaceImage').value = e.target.result;
                        const capturedImg = document.getElementById('enrollCapturedPreview');
                        capturedImg.src = e.target.result;
                        capturedImg.classList.remove('hidden');
                        document.getElementById('enrollStatusText').innerHTML = '<span class="text-emerald-700 font-bold">✅ Wajah terdeteksi dari foto! Siap didaftarkan.</span>';
                        const btn = document.getElementById('btnCaptureEnroll');
                        btn.disabled = false;
                        btn.className = "w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold font-headline-sm text-headline-sm shadow-md active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer";
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Wajah Tidak Ditemukan',
                            text: 'Foto yang Anda pilih tidak memperlihatkan wajah manusia dengan jelas. Silakan pilih foto lain.',
                            confirmButtonColor: '#004ac6'
                        });
                    }
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ===================================
    // 2. FACE SCAN PRESENSI (VERIFIKASI BIOMETRIK WAJAH)
    // ===================================
    let videoStream = null;
    let currentScanType = 'masuk';
    let scanDetectInterval = null;
    let lastScanDescriptor = null;
    let isFaceMatched = false;
    let currentMatchDistance = 1.0;

    async function openFaceScanModal(type) {
        closeEnrollFaceModal();
        currentScanType = type;
        const modal = document.getElementById('faceScanModal');
        const form = document.getElementById('faceAttendanceForm');
        const title = document.getElementById('modalFaceTitle');
        const subTitle = document.getElementById('modalFaceSub');
        const headerIcon = document.getElementById('modalFaceHeaderIcon');
        const btn = document.getElementById('btnCaptureFace');
        const btnText = document.getElementById('btnCaptureText');
        const capturedImg = document.getElementById('faceCapturedPreview');
        const overlay = document.getElementById('scannerOverlay');

        capturedImg.classList.add('hidden');
        overlay.classList.remove('hidden');
        document.getElementById('inputFaceImage').value = '';
        document.getElementById('inputNotes').value = '';
        isFaceMatched = false;
        lastScanDescriptor = null;

        if (type === 'masuk') {
            form.action = "{{ route('guru.checkin') }}";
            title.innerHTML = '<span class="text-emerald-700 font-bold">Presensi MASUK</span>';
            subTitle.textContent = "Verifikasi Biometrik Wajah Kehadiran";
            headerIcon.className = "w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center";
            headerIcon.innerHTML = '<span class="material-symbols-outlined text-[20px]">login</span>';
            btn.className = "w-full py-3 px-4 rounded-xl bg-slate-400 text-white font-bold font-headline-sm text-headline-sm shadow-none opacity-60 cursor-not-allowed flex items-center justify-center gap-2";
            btnText.textContent = "Mencocokkan Wajah...";
        } else {
            form.action = "{{ route('guru.checkout') }}";
            title.innerHTML = '<span class="text-amber-800 font-bold">Presensi PULANG</span>';
            subTitle.textContent = "Verifikasi Biometrik Wajah Kepulangan";
            headerIcon.className = "w-8 h-8 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center";
            headerIcon.innerHTML = '<span class="material-symbols-outlined text-[20px]">logout</span>';
            btn.className = "w-full py-3 px-4 rounded-xl bg-slate-400 text-white font-bold font-headline-sm text-headline-sm shadow-none opacity-60 cursor-not-allowed flex items-center justify-center gap-2";
            btnText.textContent = "Mencocokkan Wajah...";
        }

        btn.disabled = true;

        modal.classList.remove('hidden');
        startFaceCamera();
        fetchGpsLocation();
    }

    function closeFaceScanModal() {
        const modal = document.getElementById('faceScanModal');
        modal.classList.add('hidden');
        stopFaceCamera();
    }

    async function startFaceCamera() {
        const video = document.getElementById('faceVideo');
        const statusText = document.getElementById('scanStatusText');
        const statusDot = document.getElementById('scanStatusDot');
        const fallbackBox = document.getElementById('cameraFallbackBox');

        statusText.innerHTML = '<span class="text-primary font-medium animate-pulse">Menghubungkan Kamera...</span>';
        statusDot.className = "w-2 h-2 rounded-full bg-primary animate-ping";

        // 1. Inisialisasi model di latar belakang secara paralel
        const modelPromise = loadFaceApiModels();

        // 2. Buka kamera secara langsung agar tampilan video segera tampil
        try {
            const stream = await getBestCameraStream();
            videoStream = stream;
            video.srcObject = stream;
            
            try {
                await video.play();
            } catch (playErr) {
                console.warn("video.play() warning on scan:", playErr);
            }

            statusText.innerHTML = '<span class="text-amber-600 font-medium animate-pulse">Kamera aktif • Mempersiapkan AI Wajah...</span>';
            statusDot.className = "w-2 h-2 rounded-full bg-amber-400 animate-ping";

            // 3. Pastikan model AI telah selesai dimuat
            await modelPromise;

            // Tunggu frame pertama video siap sebelum memulai deteksi
            const waitForFrame = () => {
                if (video.videoWidth > 0 && video.videoHeight > 0) {
                    statusText.textContent = "Arahkan wajah tegak lurus ke kamera...";
                    startLiveScanDetection();
                } else {
                    setTimeout(waitForFrame, 100);
                }
            };
            waitForFrame();

        } catch (camErr) {
            console.error("Gagal membuka kamera scan:", camErr);
            const errMsg = getCameraErrorMessage(camErr);
            statusText.innerHTML = `<span class="text-rose-600 font-medium">${errMsg}</span>`;
            statusDot.className = "w-2 h-2 rounded-full bg-rose-500";
            if (fallbackBox) fallbackBox.classList.remove('hidden');
        }
    }

    function stopFaceCamera() {
        clearInterval(scanDetectInterval);
        scanDetectInterval = null;
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        const video = document.getElementById('faceVideo');
        if (video) video.srcObject = null;
        const canvas = document.getElementById('faceOverlayCanvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }
    }

    function startLiveScanDetection() {
        const video = document.getElementById('faceVideo');
        const statusText = document.getElementById('scanStatusText');
        const statusBadge = document.getElementById('scanStatusBadge');
        const statusDot = document.getElementById('scanStatusDot');
        const btn = document.getElementById('btnCaptureFace');
        const btnText = document.getElementById('btnCaptureText');
        const overlayCanvas = document.getElementById('faceOverlayCanvas');
        const wrapper = document.getElementById('faceVideoWrapper');

        if (!video || !overlayCanvas) return;
        if (!isFaceApiLoaded || typeof faceapi === 'undefined') {
            console.warn("Face-API belum siap untuk deteksi scan.");
            return;
        }

        const width = video.videoWidth || 320;
        const height = video.videoHeight || 320;
        overlayCanvas.width = width;
        overlayCanvas.height = height;
        const displaySize = { width, height };
        try {
            faceapi.matchDimensions(overlayCanvas, displaySize);
        } catch (e) {
            console.warn("matchDimensions error:", e);
        }

        clearInterval(scanDetectInterval);
        scanDetectInterval = setInterval(async () => {
            if (!videoStream || video.paused || video.ended) return;

            try {
                const options = new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.5 });
                const detection = await faceapi.detectSingleFace(video, options)
                    .withFaceLandmarks()
                    .withFaceDescriptor();

                const ctx = overlayCanvas.getContext('2d');
                ctx.clearRect(0, 0, overlayCanvas.width, overlayCanvas.height);

                if (!detection) {
                    lastScanDescriptor = null;
                    isFaceMatched = false;
                    statusText.textContent = "Mencari wajah di depan kamera...";
                    statusDot.className = "w-2 h-2 rounded-full bg-amber-500 animate-ping";
                    statusBadge.className = "flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 font-label-sm text-label-sm font-semibold border border-amber-300";
                    if (wrapper) wrapper.className = "relative w-64 h-64 sm:w-72 sm:h-72 rounded-full overflow-hidden border-4 border-amber-400 shadow-xl bg-slate-900 flex items-center justify-center my-1 transition-colors";
                    btn.disabled = true;
                    btn.className = "w-full py-3 px-4 rounded-xl bg-slate-400 text-white font-bold font-headline-sm text-headline-sm shadow-none opacity-60 cursor-not-allowed flex items-center justify-center gap-2";
                    btnText.textContent = "Arahkan Wajah Anda...";
                    return;
                }

                lastScanDescriptor = Array.from(detection.descriptor);
                const resized = faceapi.resizeResults(detection, displaySize);
                faceapi.draw.drawFaceLandmarks(overlayCanvas, resized);

                // Bandingkan dengan data biometrik terdaftar milik guru
                if (registeredFaceDescriptor && Array.isArray(registeredFaceDescriptor) && registeredFaceDescriptor.length > 50) {
                    const distance = faceapi.euclideanDistance(lastScanDescriptor, registeredFaceDescriptor);
                    currentMatchDistance = distance;
                    const threshold = 0.58; // Ambang batas biometrik Face-API

                    if (distance <= threshold) {
                        isFaceMatched = true;
                        const score = Math.max(0, Math.min(100, Math.round((1 - (distance / 0.7)) * 100)));
                        statusText.innerHTML = `<span class="text-emerald-700 font-bold">✅ Wajah Cocok (${score}%) • {{ addslashes($teacher->name) }}</span>`;
                        statusDot.className = "w-2 h-2 rounded-full bg-emerald-500 animate-pulse";
                        statusBadge.className = "flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 font-label-sm text-label-sm font-semibold border border-emerald-300";
                        if (wrapper) wrapper.className = "relative w-64 h-64 sm:w-72 sm:h-72 rounded-full overflow-hidden border-4 border-emerald-500 shadow-xl bg-slate-900 flex items-center justify-center my-1 ring-4 ring-emerald-300/50 transition-all";
                        
                        btn.disabled = false;
                        if (currentScanType === 'masuk') {
                            btn.className = "w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold font-headline-sm text-headline-sm shadow-md active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer";
                            btnText.textContent = "Verifikasi & Catat Masuk";
                        } else {
                            btn.className = "w-full py-3 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:opacity-95 text-white font-bold font-headline-sm text-headline-sm shadow-md active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer";
                            btnText.textContent = "Verifikasi & Catat Pulang";
                        }
                    } else {
                        isFaceMatched = false;
                        statusText.innerHTML = `<span class="text-rose-700 font-bold">⛔ Wajah Tidak Cocok! Bukan {{ addslashes($teacher->name) }}</span>`;
                        statusDot.className = "w-2 h-2 rounded-full bg-rose-500";
                        statusBadge.className = "flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-800 font-label-sm text-label-sm font-semibold border border-rose-300";
                        if (wrapper) wrapper.className = "relative w-64 h-64 sm:w-72 sm:h-72 rounded-full overflow-hidden border-4 border-rose-500 shadow-xl bg-slate-900 flex items-center justify-center my-1 ring-4 ring-rose-300/50 transition-all";
                        
                        btn.disabled = true;
                        btn.className = "w-full py-3 px-4 rounded-xl bg-rose-400 text-white font-bold font-headline-sm text-headline-sm shadow-none opacity-60 cursor-not-allowed flex items-center justify-center gap-2";
                        btnText.textContent = "Wajah Tidak Cocok (Ditolak)";
                    }
                } else {
                    // Jika belum ada descriptor biometrik, pastikan wajah terdeteksi dengan jelas
                    isFaceMatched = true;
                    statusText.innerHTML = `<span class="text-emerald-700 font-bold">✅ Wajah Terdeteksi • Siap Verifikasi</span>`;
                    statusDot.className = "w-2 h-2 rounded-full bg-emerald-500 animate-pulse";
                    statusBadge.className = "flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-800 font-label-sm text-label-sm font-semibold border border-emerald-300";
                    if (wrapper) wrapper.className = "relative w-64 h-64 sm:w-72 sm:h-72 rounded-full overflow-hidden border-4 border-emerald-500 shadow-xl bg-slate-900 flex items-center justify-center my-1 transition-all";
                    
                    btn.disabled = false;
                    btn.className = "w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold font-headline-sm text-headline-sm shadow-md active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer";
                    btnText.textContent = currentScanType === 'masuk' ? "Verifikasi & Catat Masuk" : "Verifikasi & Catat Pulang";
                }
            } catch (err) {
                console.warn("Live scan detection error:", err);
            }
        }, 300);
    }

    function captureAndVerifyFace() {
        const video = document.getElementById('faceVideo');
        const canvas = document.getElementById('faceCanvas');
        const capturedImg = document.getElementById('faceCapturedPreview');
        const overlay = document.getElementById('scannerOverlay');
        const statusText = document.getElementById('scanStatusText');
        const btn = document.getElementById('btnCaptureFace');

        // Validasi Kamera
        if (!videoStream && !document.getElementById('inputFaceImage').value) {
            Swal.fire({
                title: 'Kamera Belum Aktif',
                text: 'Pastikan kamera depan aktif dan wajah terlihat jelas.',
                icon: 'warning',
                confirmButtonColor: '#004ac6',
                confirmButtonText: 'Mengerti',
                customClass: { popup: 'rounded-2xl shadow-xl' }
            });
            return;
        }

        // Validasi Biometrik Wajah
        if (!isFaceMatched) {
            Swal.fire({
                icon: 'error',
                title: 'Verifikasi Wajah Gagal!',
                html: `<div class="text-sm space-y-2 text-slate-700 text-left">
                         <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-900">
                           <p class="font-bold mb-1">Wajah Tidak Sesuai Profil</p>
                           <p class="text-xs">Sistem mendeteksi wajah di depan kamera tidak cocok dengan data biometrik resmi atas nama <b>{{ addslashes($teacher->name) }}</b>.</p>
                         </div>
                         <p class="text-xs text-slate-600">
                           Pastikan wajah Anda menghadap langsung ke kamera dengan pencahayaan terang dan tidak diwakilkan orang lain.
                         </p>
                       </div>`,
                confirmButtonColor: '#dc2626',
                confirmButtonText: 'Tutup'
            });
            return;
        }

        // Validasi Ketat Geofence Titik Lokasi
        if (schoolConfig.isGeofenceActive && schoolConfig.lat && schoolConfig.lng) {
            if (!teacherGps.isInside) {
                const dist = teacherGps.distance || 0;
                const distKm = (dist >= 1000) ? (dist / 1000).toFixed(2).replace('.', ',') + ' km' : dist + ' meter';
                const excess = Math.max(0, dist - schoolConfig.radius);
                const excessKm = (excess >= 1000) ? (excess / 1000).toFixed(2).replace('.', ',') + ' km' : excess + ' meter';

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal! Di Luar Titik Lokasi',
                    html: `<div class="text-sm space-y-2 text-slate-700 text-left">
                             <div class="p-3 rounded-xl bg-rose-50 border border-rose-300 text-rose-950">
                               <p class="font-bold text-xs text-rose-900 mb-1">Presensi ${currentScanType.toUpperCase()} Ditolak</p>
                               <p class="text-xs">Posisi Anda (${distKm}) berada di luar batas radius sekolah (maksimal ${schoolConfig.radius} meter).</p>
                               <p class="text-[11px] text-rose-700 font-semibold mt-1">• Selisih di luar radius: +${excessKm}</p>
                             </div>
                             <p class="text-xs text-slate-600">Absensi dibatalkan. Anda wajib berada di dalam area lingkungan ${schoolConfig.name}.</p>
                           </div>`,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Tutup'
                });
                return;
            }
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined text-[20px] animate-spin">sync</span><span>Menyimpan Presensi...</span>';

        if (videoStream && video.videoWidth > 0) {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
            document.getElementById('inputFaceImage').value = dataUrl;

            capturedImg.src = dataUrl;
            capturedImg.classList.remove('hidden');
            overlay.classList.add('hidden');
        }

        statusText.innerHTML = `<span class="text-emerald-700 font-bold">✅ Verifikasi Wajah Berhasil! Mengirim data presensi...</span>`;

        Swal.fire({
            title: `Memverifikasi Presensi ${currentScanType.toUpperCase()}...`,
            html: `<div class="text-sm text-slate-600">Wajah <b>{{ addslashes($teacher->name) }}</b> cocok & titik GPS tervalidasi...</div>`,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        setTimeout(() => {
            stopFaceCamera();
            document.getElementById('faceAttendanceForm').submit();
        }, 500);
    }

    function handleFallbackImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = async function(e) {
                const img = new Image();
                img.onload = async () => {
                    await loadFaceApiModels();
                    await ensureRegisteredDescriptorFromPhoto();
                    const detect = await faceapi.detectSingleFace(img, new faceapi.TinyFaceDetectorOptions({ inputSize: 320, scoreThreshold: 0.5 }))
                        .withFaceLandmarks()
                        .withFaceDescriptor();

                    if (!detect) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Wajah Tidak Terdeteksi',
                            text: 'Foto yang Anda unggah tidak mendeteksi wajah dengan jelas.',
                            confirmButtonColor: '#dc2626'
                        });
                        return;
                    }

                    if (registeredFaceDescriptor && Array.isArray(registeredFaceDescriptor) && registeredFaceDescriptor.length > 50) {
                        const dist = faceapi.euclideanDistance(Array.from(detect.descriptor), registeredFaceDescriptor);
                        if (dist > 0.58) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Wajah Tidak Sesuai!',
                                text: 'Foto wajah yang Anda pilih tidak cocok dengan profil biometrik Anda.',
                                confirmButtonColor: '#dc2626'
                            });
                            return;
                        }
                    }

                    isFaceMatched = true;
                    document.getElementById('inputFaceImage').value = e.target.result;
                    const capturedImg = document.getElementById('faceCapturedPreview');
                    capturedImg.src = e.target.result;
                    capturedImg.classList.remove('hidden');
                    document.getElementById('scannerOverlay').classList.add('hidden');
                    document.getElementById('scanStatusText').innerHTML = '<span class="text-emerald-700 font-bold">✅ Foto wajah tervalidasi cocok!</span>';
                    const btn = document.getElementById('btnCaptureFace');
                    btn.disabled = false;
                    btn.className = "w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold font-headline-sm text-headline-sm shadow-md active:scale-95 transition-all flex items-center justify-center gap-2 cursor-pointer";
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ====================================================
    // PENGINGAT OTOMATIS KETIKA MEMASUKI JAM PRESENSI
    // ====================================================
    (function initAttendanceTimeReminder() {
        let reminderTriggered = {
            masuk: false,
            pulang: false
        };

        function checkAttendanceSchedule() {
            const now = new Date();
            const hh = String(now.getHours()).padStart(2, '0');
            const mm = String(now.getMinutes()).padStart(2, '0');
            const ss = String(now.getSeconds()).padStart(2, '0');
            const currentTimeStr = `${hh}:${mm}:${ss}`;

            const timeIn = "{{ $setting->time_in ?? '06:45:00' }}";
            const timeInLate = "{{ $setting->time_in_late_threshold ?? '07:00:00' }}";
            const timeOut = "{{ $setting->time_out ?? '14:30:00' }}";
            const hasCheckedIn = {{ $todayAttendance ? 'true' : 'false' }};
            const hasCheckedOut = {{ ($todayAttendance && $todayAttendance->time_out) ? 'true' : 'false' }};

            // Notifikasi Jam Masuk
            if (!hasCheckedIn && currentTimeStr >= timeIn && currentTimeStr <= timeInLate && !reminderTriggered.masuk) {
                reminderTriggered.masuk = true;
                if (typeof playReminderChime === 'function') playReminderChime();
                
                if ("Notification" in window && Notification.permission === "granted") {
                    new Notification("🔔 Waktunya Presensi Masuk!", {
                        body: "Jam presensi masuk telah dibuka ({{ substr($setting->time_in ?? '06:45', 0, 5) }} - {{ substr($setting->time_in_late_threshold ?? '07:00', 0, 5) }} WIB). Silakan lakukan Face ID.",
                        icon: "{{ $appSetting->logo_url ?? asset('logo.jpg') }}"
                    });
                }

                Swal.fire({
                    title: '🔔 Jam Presensi Masuk Dibuka!',
                    html: `<div class="text-sm text-slate-600">Halo <b>{{ addslashes($teacher->name) }}</b>, waktu presensi masuk telah dibuka. Silakan lakukan verifikasi Face ID sekarang sebelum jam <b>{{ substr($setting->time_in_late_threshold ?? '07:00', 0, 5) }} WIB</b> agar tidak terlambat.</div>`,
                    icon: 'info',
                    confirmButtonColor: '#004ac6',
                    confirmButtonText: 'Ambil Presensi Sekarang',
                    showCancelButton: true,
                    cancelButtonText: 'Nanti'
                }).then((res) => {
                    if (res.isConfirmed) {
                        handleAttendanceAction('masuk');
                    }
                });
            }

            // Notifikasi Jam Pulang
            if (hasCheckedIn && !hasCheckedOut && currentTimeStr >= timeOut && !reminderTriggered.pulang) {
                reminderTriggered.pulang = true;
                if (typeof playReminderChime === 'function') playReminderChime();
                
                if ("Notification" in window && Notification.permission === "granted") {
                    new Notification("🔔 Waktunya Presensi Pulang!", {
                        body: "Jam kepulangan telah tiba ({{ substr($setting->time_out ?? '14:30', 0, 5) }} WIB). Silakan verifikasi presensi pulang Anda.",
                        icon: "{{ $appSetting->logo_url ?? asset('logo.jpg') }}"
                    });
                }

                Swal.fire({
                    title: '🔔 Waktunya Presensi Pulang!',
                    html: `<div class="text-sm text-slate-600">Halo <b>{{ addslashes($teacher->name) }}</b>, kegiatan sekolah hari ini telah selesai. Silakan verifikasi presensi pulang Anda.</div>`,
                    icon: 'info',
                    confirmButtonColor: '#d97706',
                    confirmButtonText: 'Presensi Pulang',
                    showCancelButton: true,
                    cancelButtonText: 'Nanti'
                }).then((res) => {
                    if (res.isConfirmed) {
                        handleAttendanceAction('pulang');
                    }
                });
            }
        }

        // Jalankan pengecekan setiap 30 detik
        setInterval(checkAttendanceSchedule, 30000);
        // Jalankan saat pertama kali halaman terbuka
        setTimeout(checkAttendanceSchedule, 2000);
    })();

    // Fungsi Modal Detail Maklumat Yayasan / Informasi Sekolah
    function showAnnouncementDetail(item) {
        if (!item) return;

        const modal = document.getElementById('announcementDetailModal');
        const catBadge = document.getElementById('modalAnnounceCategory');
        const dateEl = document.getElementById('modalAnnounceDate');
        const titleEl = document.getElementById('modalAnnounceTitle');
        const letterEl = document.getElementById('modalAnnounceLetter');
        const contentEl = document.getElementById('modalAnnounceContent');
        const actionBox = document.getElementById('modalAnnounceActionBox');
        const attachLink = document.getElementById('modalAnnounceAttachLink');
        const extLink = document.getElementById('modalAnnounceExtLink');

        catBadge.textContent = item.category || 'MAKLUMAT';
        
        // Sesuaikan warna badge kategori
        catBadge.className = 'px-2 py-0.5 rounded-md text-[9px] font-black tracking-wider uppercase text-white ' + 
            (item.category === 'PENTING' ? 'bg-rose-600' :
             item.category === 'SURAT EDARAN' ? 'bg-[#13793e]' :
             item.category === 'AGENDA' ? 'bg-blue-600' : 'bg-amber-600');

        dateEl.textContent = item.date ? new Date(item.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
        titleEl.textContent = item.title || 'Maklumat Resmi';

        if (item.letter_number) {
            letterEl.innerHTML = `<i class="fa-regular fa-file-lines text-slate-400"></i> <span>${item.letter_number}</span>`;
            letterEl.classList.remove('hidden');
        } else {
            letterEl.innerHTML = '';
            letterEl.classList.add('hidden');
        }

        contentEl.textContent = item.content || '';

        let hasActions = false;
        if (item.attachment_path) {
            attachLink.href = `/storage/${item.attachment_path}`;
            attachLink.classList.remove('hidden');
            hasActions = true;
        } else {
            attachLink.classList.add('hidden');
        }

        if (item.external_link) {
            extLink.href = item.external_link;
            extLink.classList.remove('hidden');
            hasActions = true;
        } else {
            extLink.classList.add('hidden');
        }

        if (hasActions) {
            actionBox.classList.remove('hidden');
        } else {
            actionBox.classList.add('hidden');
        }

        modal.classList.remove('hidden');
    }

    function closeAnnouncementModal() {
        const modal = document.getElementById('announcementDetailModal');
        if (modal) modal.classList.add('hidden');
    }

    // Preload model Face-API di latar belakang saat halaman selesai dimuat
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof loadFaceApiModels === 'function') {
            loadFaceApiModels().catch(err => console.warn("Background model preload:", err));
        }
    });
</script>
@endsection
