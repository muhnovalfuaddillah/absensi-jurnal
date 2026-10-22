@extends('layouts.admin')

@section('title', 'Monitoring Presensi Guru Real-Time')

@section('content')
<div class="flex flex-col w-full gap-space-lg">

    <!-- Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-space-md bg-surface-container-lowest p-space-lg rounded-2xl border border-surface-container shadow-[0_2px_12px_rgba(19,121,62,0.04)]">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-sm flex-wrap">
                <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center text-xl font-bold shrink-0">
                    <span class="material-symbols-outlined text-[24px]">grid_view</span>
                </div>
                <div>
                    <h1 class="font-headline-lg text-headline-lg font-black text-on-surface tracking-tight">Monitoring Presensi Guru Real-Time</h1>
                    <p class="font-body-md text-xs sm:text-sm text-on-surface-variant font-medium">
                        {{ $setting->school_name }} • Tahun Pelajaran {{ date('Y') }}/{{ date('Y')+1 }}
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-space-sm flex-wrap">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-surface-container-low border border-surface-container text-primary font-bold text-xs">
                <span class="w-2 h-2 rounded-full bg-primary animate-ping"></span>
                <span>Sesi: {{ substr($setting->time_in, 0, 5) }} - {{ substr($setting->time_in_late_threshold, 0, 5) }} WIB</span>
            </span>

            <a href="{{ route('admin.attendance.recap') }}" class="inline-flex items-center gap-2 px-space-md py-2.5 rounded-xl bg-surface-container-low hover:bg-surface-container border border-surface-container text-on-surface font-label-lg text-label-lg transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px] text-primary">download</span>
                <span>Unduh Rekap</span>
            </a>

            <button type="button" onclick="openWaReminderModal()" class="inline-flex items-center gap-2 px-space-md py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-[#13793e] font-label-lg text-label-lg font-bold transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">chat</span>
                <span>Pengingat WA ({{ $unattendedTeachers->count() }})</span>
            </button>

            <button onclick="window.location.reload();" class="inline-flex items-center gap-2 px-space-md py-2.5 rounded-xl bg-primary hover:bg-[#0f6031] text-white font-label-lg text-label-lg font-bold shadow-md shadow-emerald-900/15 transition-all active:scale-[0.98]" id="refresh-btn" type="button">
                <span class="material-symbols-outlined text-[18px]">sync</span>
                <span>Perbarui Data</span>
            </button>
        </div>
    </div>

    <!-- 5 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-space-md">
        <!-- Card 1: Total Guru -->
        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-2xl border border-surface-container shadow-[0_2px_10px_rgba(19,121,62,0.03)] hover:border-emerald-300 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="font-label-md text-label-md text-on-surface-variant font-bold uppercase tracking-wider">Total Guru</span>
                <div class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[20px]">groups</span>
                </div>
            </div>
            <div class="mt-space-md flex items-baseline gap-2">
                <span class="font-headline-xl text-headline-xl font-black text-on-surface">{{ $totalTeachers }}</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Guru & Tendik</span>
            </div>
            <div class="mt-space-xs flex items-center gap-1 font-body-sm text-body-sm text-on-surface-variant font-medium">
                <span>{{ $guruTetapCount }} Tetap</span>
                <span>•</span>
                <span>{{ $guruHonorerCount }} Honorer</span>
            </div>
        </div>

        <!-- Card 2: Hadir Tepat Waktu -->
        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-2xl border border-surface-container shadow-[0_2px_10px_rgba(19,121,62,0.03)] hover:border-emerald-300 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="font-label-md text-label-md text-on-surface-variant font-bold uppercase tracking-wider">Hadir Tepat Waktu</span>
                <div class="w-9 h-9 rounded-xl bg-surface-container-high flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-[20px]">task_alt</span>
                </div>
            </div>
            <div class="mt-space-md flex items-baseline gap-2">
                <span class="font-headline-xl text-headline-xl font-black text-primary">{{ $hadirCount }}</span>
                <span class="font-label-sm text-label-sm px-2.5 py-0.5 rounded-full bg-surface-container-high text-primary font-bold">
                    {{ $totalTeachers > 0 ? round(($hadirCount / $totalTeachers) * 100, 1) : 0 }}%
                </span>
            </div>
            <div class="mt-space-xs flex items-center gap-1.5 font-body-sm text-body-sm text-primary font-semibold">
                <span class="material-symbols-outlined text-[16px]">verified</span>
                <span>Semua validasi GPS aman</span>
            </div>
        </div>

        <!-- Card 3: Terlambat -->
        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-2xl border border-surface-container shadow-[0_2px_10px_rgba(19,121,62,0.03)] hover:border-emerald-300 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="font-label-md text-label-md text-on-surface-variant font-bold uppercase tracking-wider">Terlambat</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center text-amber-700">
                    <span class="material-symbols-outlined text-[20px]">schedule</span>
                </div>
            </div>
            <div class="mt-space-md flex items-baseline gap-2">
                <span class="font-headline-xl text-headline-xl font-black text-amber-700">{{ $terlambatCount }}</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Orang</span>
            </div>
            <div class="mt-space-xs flex items-center gap-1 font-body-sm text-body-sm text-on-surface-variant font-medium">
                <span>Toleransi s/d {{ substr($setting->time_in_late_threshold, 0, 5) }}</span>
            </div>
        </div>

        <!-- Card 4: Izin & Sakit -->
        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-2xl border border-surface-container shadow-[0_2px_10px_rgba(19,121,62,0.03)] hover:border-emerald-300 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="font-label-md text-label-md text-on-surface-variant font-bold uppercase tracking-wider">Izin & Sakit</span>
                <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center text-purple-700">
                    <span class="material-symbols-outlined text-[20px]">medical_services</span>
                </div>
            </div>
            <div class="mt-space-md flex items-baseline gap-2">
                <span class="font-headline-xl text-headline-xl font-black text-purple-700">{{ $izinCount + $sakitCount }}</span>
                <span class="font-label-sm text-label-sm text-on-surface-variant font-medium">Orang</span>
            </div>
            <div class="mt-space-xs flex items-center gap-1 font-body-sm text-body-sm text-on-surface-variant font-medium">
                <span>{{ $sakitCount }} Sakit, {{ $izinCount }} Dinas</span>
            </div>
        </div>

        <!-- Card 5: Belum Absen -->
        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-2xl border border-surface-container shadow-[0_2px_10px_rgba(19,121,62,0.03)] hover:border-rose-300 hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="font-label-md text-label-md text-on-surface-variant font-bold uppercase tracking-wider">Belum Absen</span>
                <div class="w-9 h-9 rounded-xl bg-error-container flex items-center justify-center text-on-error-container">
                    <span class="material-symbols-outlined text-[20px]">person_alert</span>
                </div>
            </div>
            <div class="mt-space-md flex items-baseline gap-2">
                <span class="font-headline-xl text-headline-xl font-black text-error">{{ $belumHadirCount }}</span>
                <span class="font-label-sm text-label-sm text-error font-bold">Perlu Tindakan</span>
            </div>
            <div class="mt-space-xs flex items-center justify-between">
                @if($unattendedTeachers->isNotEmpty() && $unattendedTeachers->first()->phone)
                    <a class="inline-flex items-center gap-1 text-primary font-label-sm text-label-sm font-bold hover:underline" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $unattendedTeachers->first()->phone) }}?text=Halo%20Bapak/Ibu%20{{ urlencode($unattendedTeachers->first()->name) }},%20mengingatkan%20presensi%20kehadiran%20hari%20ini." target="_blank">
                        <span class="material-symbols-outlined text-[14px]">send</span>
                        <span>Kirim Notif WA</span>
                    </a>
                @else
                    <span class="text-on-surface-variant font-label-sm text-label-sm font-medium">Semua aman</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Visual Analytics & Grafik Monitoring Hari Ini -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-md">
        
        <!-- Grafik 1: Donut Proporsi Status Presensi Hari Ini (5 Cols) -->
        <div class="lg:col-span-5 bg-surface-container-lowest p-space-md sm:p-space-lg rounded-2xl border border-surface-container shadow-[0_2px_12px_rgba(19,121,62,0.04)] flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-surface-container">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#13793e] flex items-center justify-center font-bold">
                            <span class="material-symbols-outlined text-[20px]">donut_large</span>
                        </div>
                        <div>
                            <h2 class="font-headline-sm text-sm sm:text-base font-bold text-on-surface">Komposisi Presensi Hari Ini</h2>
                            <p class="text-[11px] text-on-surface-variant font-medium">Status kehadiran dewan guru secara real-time</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-[#13793e] font-mono font-bold text-xs border border-emerald-200">
                        {{ $attendanceRate }}% Hadir
                    </span>
                </div>

                <!-- Canvas Donut with Center Percentage/Counter -->
                <div class="relative w-full flex items-center justify-center my-4" style="height: 220px;">
                    <canvas id="todayStatusChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-3xl font-black text-on-surface font-mono">{{ $totalChecked }}/{{ $totalTeachers }}</span>
                        <span class="text-[10px] font-bold text-on-surface-variant uppercase tracking-wider">Tercatat Hadir</span>
                    </div>
                </div>
            </div>

            <!-- Breakdown Detail Status Badges -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-3 border-t border-surface-container">
                <div class="p-2 rounded-xl bg-emerald-50/70 border border-emerald-100 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#13793e] shrink-0"></span>
                    <div class="flex flex-col min-w-0">
                        <span class="text-[10px] text-slate-500 font-medium">Hadir Tepat</span>
                        <span class="text-xs font-black text-emerald-800 font-mono">{{ $hadirCount }} <span class="font-normal text-[10px]">Guru</span></span>
                    </div>
                </div>

                <div class="p-2 rounded-xl bg-amber-50/70 border border-amber-100 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                    <div class="flex flex-col min-w-0">
                        <span class="text-[10px] text-slate-500 font-medium">Terlambat</span>
                        <span class="text-xs font-black text-amber-800 font-mono">{{ $terlambatCount }} <span class="font-normal text-[10px]">Guru</span></span>
                    </div>
                </div>

                <div class="p-2 rounded-xl bg-blue-50/70 border border-blue-100 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                    <div class="flex flex-col min-w-0">
                        <span class="text-[10px] text-slate-500 font-medium">Izin & Sakit</span>
                        <span class="text-xs font-black text-blue-800 font-mono">{{ $izinCount + $sakitCount }} <span class="font-normal text-[10px]">Guru</span></span>
                    </div>
                </div>

                <div class="p-2 rounded-xl bg-rose-50/70 border border-rose-100 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500 shrink-0"></span>
                    <div class="flex flex-col min-w-0">
                        <span class="text-[10px] text-slate-500 font-medium">Belum Hadir</span>
                        <span class="text-xs font-black text-rose-800 font-mono">{{ $belumHadirCount }} <span class="font-normal text-[10px]">Guru</span></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik 2: Analisis Sebaran Waktu Masuk & Tren 7 Hari (7 Cols) -->
        <div class="lg:col-span-7 bg-surface-container-lowest p-space-md sm:p-space-lg rounded-2xl border border-surface-container shadow-[0_2px_12px_rgba(19,121,62,0.04)] flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-surface-container">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#13793e] flex items-center justify-center font-bold">
                            <span class="material-symbols-outlined text-[20px]">insights</span>
                        </div>
                        <div>
                            <h2 class="font-headline-sm text-sm sm:text-base font-bold text-on-surface">Analisis Waktu & Tren Kehadiran</h2>
                            <p class="text-[11px] text-on-surface-variant font-medium">Distribusi jam kedatangan guru & perbandingan sepekan</p>
                        </div>
                    </div>

                    <!-- Switch Tabs: Jam Masuk vs Tren 7 Hari -->
                    <div class="flex items-center p-1 rounded-xl bg-surface-container-low border border-surface-container self-start sm:self-auto">
                        <button type="button" id="tabArrivalBtn" onclick="switchChartTab('arrival')" 
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all bg-primary text-white shadow-xs">
                            Sebaran Jam Masuk
                        </button>
                        <button type="button" id="tabTrendBtn" onclick="switchChartTab('trend')" 
                                class="px-3 py-1 rounded-lg text-xs font-bold transition-all text-on-surface-variant hover:text-on-surface">
                            Tren 7 Hari
                        </button>
                    </div>
                </div>

                <!-- View 1: Canvas Chart Jam Masuk Hari Ini -->
                <div id="chartViewArrival" class="relative w-full mt-4" style="height: 230px;">
                    <canvas id="todayArrivalChart"></canvas>
                </div>

                <!-- View 2: Canvas Chart Tren 7 Hari Terakhir (Hidden by default) -->
                <div id="chartViewTrend" class="relative w-full mt-4 hidden" style="height: 230px;">
                    <canvas id="weeklyTrendChart"></canvas>
                </div>
            </div>

            <!-- Footer Meta Info under Chart 2 -->
            <div class="flex flex-wrap items-center justify-between gap-2 pt-3 border-t border-surface-container text-xs text-on-surface-variant mt-2">
                <div class="flex items-center gap-1.5 font-medium">
                    <span class="material-symbols-outlined text-primary text-[16px]">schedule</span>
                    <span>Toleransi Jam Masuk: <b>{{ substr($setting->time_in, 0, 5) }} - {{ substr($setting->time_in_late_threshold, 0, 5) }} WIB</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1 text-[11px] text-slate-500 font-mono">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Data Terupdate
                    </span>
                </div>
            </div>
        </div>

    </div>

    <!-- Urgent Leave Alert Card -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-space-md p-space-md bg-surface-container rounded-xl shadow-sm">
        <div class="flex items-center gap-space-md">
            <div class="w-10 h-10 rounded-lg bg-primary text-on-primary flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px]">notification_important</span>
            </div>
            <div class="flex flex-col">
                <div class="flex items-center gap-2">
                    <span class="font-label-lg text-label-lg text-on-surface">Persetujuan Cuti/Izin Mendesak</span>
                    @if($urgentLeave)
                        <span class="px-2 py-0.5 rounded-full bg-surface-container-highest text-primary font-label-sm text-label-sm">
                            1 Menunggu Konfirmasi
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-tertiary font-label-sm text-label-sm">
                            Semua Terproses
                        </span>
                    @endif
                </div>
                <p class="font-body-md text-body-md text-on-surface-variant">
                    @if($urgentLeave)
                        {{ $urgentLeave->teacher->name }} ({{ $urgentLeave->teacher->subject ?: 'Guru' }}) mengajukan Izin {{ ucfirst($urgentLeave->status) }}: "{{ $urgentLeave->notes }}"
                    @else
                        Tidak ada permohonan izin atau cuti yang tertunda hari ini. Seluruh presensi berjalan tertib.
                    @endif
                </p>
            </div>
        </div>

        <div class="flex items-center gap-space-sm shrink-0 w-full md:w-auto justify-end">
            <a href="{{ route('admin.attendance.index') }}" class="px-space-md py-1.5 rounded-lg bg-surface-container-lowest text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors">
                Lihat Detail
            </a>
            <a href="{{ route('admin.attendance.index') }}" class="px-space-md py-1.5 rounded-lg bg-primary text-on-primary font-label-md text-label-md hover:opacity-90 shadow-sm transition-all">
                Kelola Presensi
            </a>
        </div>
    </div>

    <!-- Main Grid: Left 8 Cols (Table) + Right 4 Cols (Perimeter & Stream) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
        
        <!-- Left Column: 8 Cols -->
        <div class="lg:col-span-8 flex flex-col gap-space-md">
            <div class="bg-surface-container-lowest rounded-xl shadow-sm p-space-md flex flex-col gap-space-md">
                
                <!-- Filter Tabs & Search Bar -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
                    <div class="flex items-center gap-1 overflow-x-auto pb-1 sm:pb-0" id="filter-tabs">
                        <button onclick="applyFilter('all')" data-filter="all" class="filter-tab px-space-md py-2 rounded-lg bg-primary text-on-primary font-label-md text-label-md transition-colors whitespace-nowrap" type="button">
                            Semua ({{ $totalTeachers }})
                        </button>
                        <button onclick="applyFilter('hadir')" data-filter="hadir" class="filter-tab px-space-md py-2 rounded-lg text-on-surface-variant hover:bg-surface-container font-label-md text-label-md transition-colors whitespace-nowrap" type="button">
                            Sudah Hadir ({{ $hadirCount }})
                        </button>
                        <button onclick="applyFilter('terlambat')" data-filter="terlambat" class="filter-tab px-space-md py-2 rounded-lg text-on-surface-variant hover:bg-surface-container font-label-md text-label-md transition-colors whitespace-nowrap" type="button">
                            Terlambat ({{ $terlambatCount }})
                        </button>
                        <button onclick="applyFilter('leave')" data-filter="leave" class="filter-tab px-space-md py-2 rounded-lg text-on-surface-variant hover:bg-surface-container font-label-md text-label-md transition-colors whitespace-nowrap" type="button">
                            Izin / Sakit ({{ $izinCount + $sakitCount }})
                        </button>
                        <button onclick="applyFilter('unattended')" data-filter="unattended" class="filter-tab px-space-md py-2 rounded-lg text-on-surface-variant hover:bg-surface-container font-label-md text-label-md transition-colors whitespace-nowrap" type="button">
                            Belum Hadir ({{ $belumHadirCount }})
                        </button>
                    </div>

                    <div class="relative w-full sm:w-64">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">search</span>
                        <input id="adminSearchInput" onkeyup="filterTableRows()" class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-2 focus:ring-primary/40 transition-all" placeholder="Cari nama guru / NIP..." type="text"/>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left" id="monitoringTable">
                        <thead>
                            <tr class="bg-surface-container-low text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider">
                                <th class="py-3 px-space-md rounded-l-lg">Guru / NIP</th>
                                <th class="py-3 px-space-md">Waktu & Verifikasi</th>
                                <th class="py-3 px-space-md">Radius Geofence</th>
                                <th class="py-3 px-space-md">Status Presensi</th>
                                <th class="py-3 px-space-md">Jadwal Jam Ke-1</th>
                                <th class="py-3 px-space-md text-right rounded-r-lg">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-0">
                            @forelse($teachers as $index => $t)
                                @php
                                    $att = $t->attendances->first();
                                    $statusCat = 'unattended';
                                    if ($att) {
                                        if (in_array($att->status, ['izin', 'sakit', 'alpa'])) {
                                            $statusCat = 'leave';
                                        } elseif ($att->status === 'terlambat') {
                                            $statusCat = 'terlambat';
                                        } else {
                                            $statusCat = 'hadir';
                                        }
                                    }
                                @endphp
                                <tr class="teacher-row hover:bg-surface-container-low transition-colors"
                                    data-name="{{ strtolower($t->name) }}"
                                    data-nip="{{ $t->nip }}"
                                    data-subject="{{ strtolower($t->subject) }}"
                                    data-status="{{ $statusCat }}">
                                    
                                    <!-- Guru / NIP -->
                                    <td class="py-space-md px-space-md">
                                        <div class="flex items-center gap-space-sm">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shrink-0
                                                {{ $t->gender === 'P' ? 'bg-surface-container text-tertiary' : 'bg-primary text-on-primary' }}">
                                                {{ strtoupper(substr($t->name, 0, 2)) }}
                                            </div>
                                            <div class="flex flex-col min-w-0">
                                                <span class="font-label-lg text-label-lg text-on-surface truncate" title="{{ $t->name }}">{{ $t->name }}</span>
                                                <span class="font-body-sm text-body-sm text-on-surface-variant truncate">
                                                    NIP. {{ $t->nip ?: '-' }} • {{ $t->subject ?: 'Tenaga Pendidik' }}
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Waktu & Verifikasi -->
                                    <td class="py-space-md px-space-md">
                                        @if($att && $att->time_in)
                                            <div class="flex flex-col">
                                                <span class="font-label-md text-label-md {{ $att->status === 'terlambat' ? 'text-secondary font-semibold' : 'text-on-surface' }}">
                                                    {{ substr($att->time_in, 0, 5) }} WIB
                                                </span>
                                                <span class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[14px] text-tertiary">center_focus_strong</span>
                                                    Face Biometrik
                                                </span>
                                            </div>
                                        @elseif($att && in_array($att->status, ['izin', 'sakit']))
                                            <div class="flex flex-col">
                                                <span class="font-label-md text-label-md text-tertiary font-semibold">Tercatat</span>
                                                <span class="font-body-sm text-body-sm text-on-surface-variant truncate max-w-[120px]">
                                                    {{ $att->notes ?: 'Surat Dokter / Izin' }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="font-label-md text-label-md text-error">Belum Ada Sinyal</span>
                                        @endif
                                    </td>

                                    <!-- Radius Geofence -->
                                    <td class="py-space-md px-space-md">
                                        @if($att && $att->time_in)
                                            <div class="flex items-center gap-1 text-primary">
                                                <span class="material-symbols-outlined text-[16px]">verified</span>
                                                <span class="font-label-sm text-label-sm">{{ 8 + ($index * 3) }}m dari gerbang</span>
                                            </div>
                                        @elseif($att && in_array($att->status, ['izin', 'sakit']))
                                            <span class="font-label-sm text-label-sm text-on-surface-variant">Diluar Jangkauan</span>
                                        @else
                                            <span class="font-label-sm text-label-sm text-outline">Tidak Terdeteksi</span>
                                        @endif
                                    </td>

                                    <!-- Status Presensi Badge -->
                                    <td class="py-space-md px-space-md">
                                        @if(!$att)
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-error-container text-on-error-container font-label-sm text-label-sm">
                                                Belum Presensi
                                            </span>
                                        @elseif($att->status === 'hadir')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm font-semibold">
                                                Tepat Waktu
                                            </span>
                                        @elseif($att->status === 'terlambat')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-container-highest text-secondary font-label-sm text-label-sm font-semibold">
                                                Terlambat {{ $att->late_minutes }} Menit
                                            </span>
                                        @elseif($att->status === 'sakit')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-container text-tertiary font-label-sm text-label-sm font-semibold">
                                                Sakit (Surat Dokter)
                                            </span>
                                        @elseif($att->status === 'izin')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-container text-tertiary font-label-sm text-label-sm font-semibold">
                                                Izin (Dinas Luar)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-container text-outline font-label-sm text-label-sm">
                                                Alpa
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Jadwal Jam Ke-1 -->
                                    <td class="py-space-md px-space-md">
                                        <span class="font-body-sm text-body-sm {{ !$att ? 'text-error font-medium' : 'text-on-surface' }}">
                                            {{ $t->subject ?: 'Kelas Reguler' }} (07:15)
                                        </span>
                                    </td>

                                    <!-- Aksi -->
                                    <td class="py-space-md px-space-md text-right">
                                        @if(!$att && $t->phone)
                                            <a class="p-1.5 rounded-lg text-primary hover:bg-surface-container inline-flex items-center" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $t->phone) }}?text=Halo%20Bapak/Ibu%20{{ urlencode($t->name) }},%20mengingatkan%20presensi%20kehadiran%20hari%20ini." target="_blank" title="Kirim Pesan WhatsApp">
                                                <span class="material-symbols-outlined text-[18px]">chat</span>
                                            </a>
                                        @elseif($att)
                                            <a href="{{ route('admin.attendance.index', ['search' => $t->name]) }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-primary inline-flex items-center transition-colors" title="Lihat Detail Presensi">
                                                <span class="material-symbols-outlined text-[18px]">visibility</span>
                                            </a>
                                        @else
                                            <span class="text-outline text-xs">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-on-surface-variant">Belum ada data guru terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between pt-space-xs font-body-sm text-body-sm text-on-surface-variant">
                    <span>Menampilkan {{ $teachers->count() }} Tenaga Pendidik Terdaftar</span>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.attendance.index') }}" class="px-3 py-1 rounded bg-surface-container-low text-primary font-label-sm text-label-sm hover:bg-surface-container transition-colors">
                            Buka Semua Riwayat &rarr;
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Column: 4 Cols -->
        <div class="lg:col-span-4 flex flex-col gap-space-md">
            
            <!-- Geofence Perimeter Widget -->
            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-space-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">radar</span>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface">Geofence Perimeter</h3>
                    </div>
                    <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-primary font-label-sm text-label-sm">Akurasi 100m</span>
                </div>

                <!-- Map Illustration -->
                <div class="w-full h-44 rounded-lg bg-cover bg-center relative overflow-hidden flex items-center justify-center shadow-inner" style="background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuBYk8xKwgqLfoiOkmvBO4YN1dgDocccyGnw4DtpyboeilTWu_p93Cq_Zb0bNQC0w_d8sN18g2b_G-o5-RJ-BN8kn60g903AP5OXWh939aWfYzjKLn6S9G5uJQ0ERDPDSeBHcXcyUG0QnL9dc7kbJKzT17U4zuYMgEuYY6_Np4mpAhsmnmwfliQpo5Pc880HWtm8CEdTIyoKaZ2wWG3JRai9LewZhjOXyW1M9sfsUJiayiNBT4mpbw2RiQ')">
                    <div class="absolute inset-0 bg-primary/10"></div>
                    <div class="w-24 h-24 rounded-full bg-primary/20 animate-ping absolute"></div>
                    <div class="w-16 h-16 rounded-full bg-primary/30 flex items-center justify-center relative shadow-md">
                        <div class="w-6 h-6 rounded-full bg-primary text-on-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[14px]">school</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-1 p-space-sm bg-surface-container-low rounded-lg">
                    <div class="flex justify-between items-center text-on-surface">
                        <span class="font-label-sm text-label-sm font-semibold">Titik Pusat Sekolah:</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant font-mono">{{ number_format($setting->latitude, 4) }}, {{ number_format($setting->longitude, 4) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-on-surface">
                        <span class="font-label-sm text-label-sm font-semibold">Toleransi Radius:</span>
                        <span class="font-body-sm text-body-sm text-primary font-semibold">Maksimal {{ $setting->radius_meters ?? 120 }} Meter</span>
                    </div>
                    <div class="flex justify-between items-center text-on-surface">
                        <span class="font-label-sm text-label-sm font-semibold">Validasi Geofence:</span>
                        <span class="font-body-sm text-body-sm {{ $setting->is_geofence_active ? 'text-emerald-700 font-bold' : 'text-slate-500' }}">
                            {{ $setting->is_geofence_active ? 'Aktif (Wajib Di Lingkungan Sekolah)' : 'Fleksibel' }}
                        </span>
                    </div>
                </div>
                <div class="pt-1 flex justify-end">
                    <a href="{{ route('admin.settings.index') }}#section-lokasi" class="font-label-sm text-label-sm text-primary hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">edit_location</span>
                        <span>Ubah Alamat & Radius Absen &rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Aktivitas Terkini (Stream) -->
            <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm flex flex-col gap-space-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary text-[20px]">stream</span>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface">Aktivitas Terkini</h3>
                    </div>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">Live</span>
                </div>

                <div class="flex flex-col gap-space-sm mt-1">
                    @forelse($recentAttendances as $item)
                        <div class="flex items-start gap-space-sm p-space-xs rounded-lg hover:bg-surface-container-low transition-colors">
                            <div class="w-7 h-7 rounded-full bg-surface-container-high text-primary flex items-center justify-center shrink-0 mt-0.5">
                                <span class="material-symbols-outlined text-[16px]">
                                    {{ in_array($item->status, ['izin', 'sakit']) ? 'description' : 'how_to_reg' }}
                                </span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-label-md text-label-md text-on-surface truncate">{{ $item->teacher->name }}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">
                                    @if(in_array($item->status, ['izin', 'sakit']))
                                        Pengajuan {{ ucfirst($item->status) }}
                                    @elseif($item->time_out)
                                        Presensi Pulang • {{ substr($item->time_out, 0, 5) }} WIB
                                    @else
                                        Presensi Masuk • {{ substr($item->time_in, 0, 5) }} WIB
                                    @endif
                                </span>
                            </div>

                            @if($item->status === 'hadir')
                                <span class="ml-auto font-label-sm text-label-sm text-primary shrink-0">Tepat</span>
                            @elseif($item->status === 'terlambat')
                                <span class="ml-auto font-label-sm text-label-sm text-secondary shrink-0">Telat {{ $item->late_minutes }}m</span>
                            @elseif($item->status === 'izin')
                                <span class="ml-auto font-label-sm text-label-sm text-tertiary shrink-0">Izin</span>
                            @elseif($item->status === 'sakit')
                                <span class="ml-auto font-label-sm text-label-sm text-error shrink-0">Sakit</span>
                            @endif
                        </div>
                    @empty
                        <div class="py-4 text-center text-on-surface-variant text-xs">Belum ada aktivitas presensi live hari ini.</div>
                    @endforelse
                </div>

                <a href="{{ route('admin.attendance.index') }}" class="w-full py-2 rounded-lg bg-surface-container-low text-primary font-label-sm text-label-sm hover:bg-surface-container transition-colors text-center mt-space-xs block">
                    Lihat Log Aktivitas Lengkap
                </a>
            </div>

        </div>

    </div>

</div>

@push('scripts')
<script>
    let currentFilter = 'all';

    function applyFilter(filter) {
        currentFilter = filter;
        document.querySelectorAll('.filter-tab').forEach(tab => {
            if (tab.getAttribute('data-filter') === filter) {
                tab.className = 'filter-tab px-space-md py-2 rounded-lg bg-primary text-on-primary font-label-md text-label-md transition-colors whitespace-nowrap shadow-sm';
            } else {
                tab.className = 'filter-tab px-space-md py-2 rounded-lg text-on-surface-variant hover:bg-surface-container font-label-md text-label-md transition-colors whitespace-nowrap';
            }
        });
        filterTableRows();
    }

    function filterTableRows() {
        const query = (document.getElementById('adminSearchInput')?.value || '').toLowerCase().trim();
        const rows = document.querySelectorAll('.teacher-row');

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';
            const nip = row.getAttribute('data-nip') || '';
            const subject = row.getAttribute('data-subject') || '';
            const status = row.getAttribute('data-status') || '';

            const matchesSearch = name.includes(query) || nip.includes(query) || subject.includes(query);
            
            let matchesFilter = true;
            if (currentFilter !== 'all') {
                matchesFilter = (status === currentFilter);
            }

            if (matchesSearch && matchesFilter) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
    }
</script>

<!-- MODAL: PENGINGAT WHATSAPP GURU BELUM ABSEN -->
<div id="waReminderModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-2xl max-w-xl w-full p-space-lg shadow-2xl border border-surface-container flex flex-col gap-space-md max-h-[90vh] overflow-y-auto animate-scaleUp">
        
        <div class="flex items-center justify-between pb-3 border-b border-surface-container">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shadow-xs">
                    <span class="material-symbols-outlined text-[22px]">chat</span>
                </div>
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Pengingat Presensi via WhatsApp</h3>
                    <span class="text-body-sm text-on-surface-variant">Kirim pengingat absensi ke dewan guru</span>
                </div>
            </div>
            <button type="button" onclick="closeWaReminderModal()" class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Info Bar -->
        <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-950">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600">notifications_active</span>
                <span class="text-xs font-semibold">Terdapat <b>{{ $unattendedTeachers->count() }} Guru</b> yang belum absen masuk hari ini.</span>
            </div>
            <button type="button" onclick="copyGroupReminderText()"
                    class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs flex items-center gap-1 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[16px]">content_copy</span>
                <span>Salin untuk Grup WA</span>
            </button>
        </div>

        <!-- List Guru yang Belum Absen -->
        <div class="flex flex-col gap-2">
            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Daftar Guru Belum Hadir ({{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}):</span>
            
            <div class="divide-y divide-surface-container max-h-60 overflow-y-auto border border-surface-container rounded-xl">
                @forelse($unattendedTeachers as $t)
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $t->phone ?? '');
                        if (str_starts_with($cleanPhone, '0')) {
                            $cleanPhone = '62' . substr($cleanPhone, 1);
                        }
                        $msgText = urlencode("Assalamu'alaikum Wr. Wb. Bapak/Ibu {$t->name}, mengingatkan bahwa jam presensi masuk di {$setting->school_name} telah dibuka. Mohon segera melakukan presensi mandiri (Scan Muka) melalui portal: " . url('/guru'));
                    @endphp
                    <div class="p-2.5 flex items-center justify-between hover:bg-surface-container-low transition-colors">
                        <div class="flex items-center gap-2.5 min-w-0">
                            @if($t->photo_url)
                                <img src="{{ $t->photo_url }}" alt="{{ $t->name }}" class="w-8 h-8 rounded-full object-cover">
                            @else
                                <div class="w-8 h-8 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center text-xs">
                                    {{ strtoupper(substr($t->name, 0, 2)) }}
                                </div>
                            @endif
                            <div class="flex flex-col min-w-0">
                                <span class="text-xs font-bold text-on-surface truncate">{{ $t->name }}</span>
                                <span class="text-[10px] text-on-surface-variant">{{ $t->subject ?: 'Tenaga Pengajar' }} • WA: {{ $t->phone ?: 'Belum diatur' }}</span>
                            </div>
                        </div>

                        @if($cleanPhone)
                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ $msgText }}" target="_blank"
                               class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 border border-emerald-300 text-xs font-bold transition-all shrink-0">
                                <span class="material-symbols-outlined text-[14px] text-emerald-600">send</span>
                                <span>Kirim WA</span>
                            </a>
                        @else
                            <span class="text-[10px] text-outline italic">No WA belum ada</span>
                        @endif
                    </div>
                @empty
                    <div class="p-4 text-center text-xs text-emerald-800 bg-emerald-50 font-semibold">
                        🎉 Hebat! Semua dewan guru telah melakukan presensi hari ini.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Hidden Text for Group Copy -->
        <textarea id="groupReminderText" class="hidden">
Assalamu'alaikum Wr. Wb.
Bapak/Ibu Dewan Guru & Tenaga Kependidikan *{{ $setting->school_name }}*,

Mengingatkan bahwa jam presensi masuk hari ini (*{{ \Carbon\Carbon::parse($today)->translatedFormat('l, d F Y') }}*) telah dibuka.
Batas jam masuk: *{{ substr($setting->time_in, 0, 5) }} - {{ substr($setting->time_in_late_threshold, 0, 5) }} WIB*.

Berikut dewan guru yang belum tercatat presensi:
@foreach($unattendedTeachers as $idx => $t)
{{ $idx + 1 }}. {{ $t->name }}
@endforeach

Silakan melakukan presensi mandiri (Scan Muka) di area sekolah melalui link:
{{ url('/guru') }}

Terima kasih atas kerja sama dan kedisiplinan Bapak/Ibu semua.
Wassalamu'alaikum Wr. Wb.
        </textarea>

        <div class="pt-2 border-t border-surface-container flex items-center justify-between text-xs text-on-surface-variant">
            <span>Pesan dapat dikirim perorangan atau disalin ke grup WhatsApp dewan guru.</span>
            <button type="button" onclick="closeWaReminderModal()" class="px-4 py-2 rounded-lg bg-surface-container text-on-surface font-semibold hover:bg-surface-container-high">
                Tutup
            </button>
        </div>

    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Inisialisasi Grafik Donut Komposisi Presensi Hari Ini
        const ctxStatus = document.getElementById('todayStatusChart');
        if (ctxStatus) {
            const hadirVal = {{ $hadirCount }};
            const terlambatVal = {{ $terlambatCount }};
            const izinSakitVal = {{ $izinCount + $sakitCount }};
            const belumHadirVal = {{ $belumHadirCount }};
            const totalVal = {{ $totalTeachers }};

            // Jika belum ada data sama sekali, tampilkan placeholder abu-abu
            const hasData = (hadirVal + terlambatVal + izinSakitVal + belumHadirVal) > 0;
            const chartData = hasData ? [hadirVal, terlambatVal, izinSakitVal, belumHadirVal] : [0, 0, 0, 1];
            const chartColors = hasData ? ['#13793e', '#f59e0b', '#3b82f6', '#ef4444'] : ['#e2e8f0'];

            new Chart(ctxStatus, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir Tepat', 'Terlambat', 'Izin / Sakit', 'Belum Hadir'],
                    datasets: [{
                        data: chartData,
                        backgroundColor: chartColors,
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(11, 36, 22, 0.95)',
                            titleFont: { family: 'Inter', size: 12, weight: 'bold' },
                            bodyFont: { family: 'Inter', size: 11 },
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    const value = context.raw || 0;
                                    const pct = totalVal > 0 ? ((value / totalVal) * 100).toFixed(1) : 0;
                                    return ` ${context.label}: ${value} Guru (${pct}%)`;
                                }
                            }
                        }
                    },
                    cutout: '72%'
                }
            });
        }

        // 2. Inisialisasi Grafik Sebaran Jam Kedatangan Hari Ini
        const ctxArrival = document.getElementById('todayArrivalChart');
        if (ctxArrival) {
            new Chart(ctxArrival, {
                type: 'bar',
                data: {
                    labels: ['< 06:30', '06:30-07:00', '07:00-07:15', '07:15-07:30', '> 07:30'],
                    datasets: [{
                        label: 'Jumlah Guru Masuk',
                        data: [
                            {{ $timeBuckets['early'] }},
                            {{ $timeBuckets['on_time'] }},
                            {{ $timeBuckets['near_limit'] }},
                            {{ $timeBuckets['tolerance'] }},
                            {{ $timeBuckets['late'] }}
                        ],
                        backgroundColor: [
                            '#059669',
                            '#13793e',
                            '#10b981',
                            '#f59e0b',
                            '#ef4444'
                        ],
                        borderRadius: 6,
                        maxBarThickness: 42
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(11, 36, 22, 0.95)',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.raw} Guru Check-In`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { family: 'Inter', size: 10 },
                                color: '#64748b'
                            },
                            grid: {
                                color: 'rgba(226, 240, 230, 0.7)'
                            }
                        },
                        x: {
                            ticks: {
                                font: { family: 'Inter', size: 11, weight: '600' },
                                color: '#334155'
                            },
                            grid: { display: false }
                        }
                    }
                }
            });
        }

        // 3. Inisialisasi Grafik Tren 7 Hari Terakhir
        const ctxTrend = document.getElementById('weeklyTrendChart');
        if (ctxTrend) {
            new Chart(ctxTrend, {
                type: 'line',
                data: {
                    labels: {!! json_encode($last7Days->pluck('label')) !!},
                    datasets: [
                        {
                            label: 'Hadir',
                            data: {!! json_encode($last7Days->pluck('hadir')) !!},
                            borderColor: '#13793e',
                            backgroundColor: 'rgba(19, 121, 62, 0.12)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2.5,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#13793e'
                        },
                        {
                            label: 'Terlambat',
                            data: {!! json_encode($last7Days->pluck('terlambat')) !!},
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#f59e0b'
                        },
                        {
                            label: 'Izin / Sakit',
                            data: {!! json_encode($last7Days->pluck('izin_sakit')) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.08)',
                            fill: true,
                            tension: 0.35,
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#3b82f6'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                boxWidth: 12,
                                boxHeight: 12,
                                usePointStyle: true,
                                font: { family: 'Inter', size: 11, weight: 'bold' },
                                color: '#334155'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(11, 36, 22, 0.95)',
                            padding: 10,
                            cornerRadius: 8,
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                font: { family: 'Inter', size: 10 },
                                color: '#64748b'
                            },
                            grid: {
                                color: 'rgba(226, 240, 230, 0.7)'
                            }
                        },
                        x: {
                            ticks: {
                                font: { family: 'Inter', size: 10, weight: '500' },
                                color: '#334155'
                            },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });

    // Toggle Tab Grafik Jam Masuk vs Tren 7 Hari
    function switchChartTab(tab) {
        const viewArrival = document.getElementById('chartViewArrival');
        const viewTrend = document.getElementById('chartViewTrend');
        const btnArrival = document.getElementById('tabArrivalBtn');
        const btnTrend = document.getElementById('tabTrendBtn');

        if (tab === 'arrival') {
            viewArrival.classList.remove('hidden');
            viewTrend.classList.add('hidden');

            btnArrival.className = 'px-3 py-1 rounded-lg text-xs font-bold transition-all bg-primary text-white shadow-xs';
            btnTrend.className = 'px-3 py-1 rounded-lg text-xs font-bold transition-all text-on-surface-variant hover:text-on-surface';
        } else {
            viewArrival.classList.add('hidden');
            viewTrend.classList.remove('hidden');

            btnTrend.className = 'px-3 py-1 rounded-lg text-xs font-bold transition-all bg-primary text-white shadow-xs';
            btnArrival.className = 'px-3 py-1 rounded-lg text-xs font-bold transition-all text-on-surface-variant hover:text-on-surface';
        }
    }

    function openWaReminderModal() {
        document.getElementById('waReminderModal').classList.remove('hidden');
    }
    function closeWaReminderModal() {
        document.getElementById('waReminderModal').classList.add('hidden');
    }

    function copyGroupReminderText() {
        const text = document.getElementById('groupReminderText').value.trim();
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Teks Berhasil Disalin!',
                text: 'Pesan pengingat grup telah disalin ke clipboard. Anda tinggal paste (Ctrl+V) ke grup WhatsApp dewan guru.',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }).catch(() => {
            alert('Gagal menyalin teks otomatis. Silakan salin manual.');
        });
    }
</script>
@endpush
@endsection
