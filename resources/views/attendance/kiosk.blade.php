@extends('layouts.admin')

@section('title', 'Absen Cepat Guru')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">

    <!-- Hero Digital Clock & Schedule Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#052e16] via-[#0d3b1e] to-[#13793e] text-white shadow-xl shadow-emerald-950/20 mb-8 p-6 sm:p-8">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-10 -top-10 w-64 h-64 bg-teal-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Left: Clock & Greeting -->
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-xs font-semibold text-emerald-200 mb-3 border border-white/10">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>{{ $setting->school_name }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Presensi Kehadiran Guru</h1>
                <p id="heroDigitalDate" class="text-sm text-emerald-100/80 mt-1">Memuat tanggal...</p>
                
                <!-- Huge Digital Clock -->
                <div class="mt-4 flex items-baseline gap-3">
                    <span id="heroDigitalClock" class="font-mono text-4xl sm:text-5xl font-black tracking-wider text-emerald-300 drop-shadow-md">
                        00:00:00
                    </span>
                    <span class="text-sm font-semibold text-emerald-100/80">WIB</span>
                </div>
            </div>

            <!-- Right: Schedule & Rules Badge -->
            <div class="grid grid-cols-3 gap-2 sm:gap-3 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 text-center">
                <div class="p-2 sm:p-3 rounded-xl bg-white/5">
                    <span class="text-[11px] font-medium text-emerald-200/80 block">Jam Masuk</span>
                    <span class="text-base sm:text-lg font-bold text-white block mt-0.5">{{ substr($setting->time_in, 0, 5) }}</span>
                    <span class="text-[10px] text-emerald-300 font-medium">Tepat Waktu</span>
                </div>
                <div class="p-2 sm:p-3 rounded-xl bg-white/5">
                    <span class="text-[11px] font-medium text-emerald-200/80 block">Batas Toleransi</span>
                    <span class="text-base sm:text-lg font-bold text-amber-300 block mt-0.5">{{ substr($setting->time_in_late_threshold, 0, 5) }}</span>
                    <span class="text-[10px] text-amber-200/80 font-medium">> Batas = Telat</span>
                </div>
                <div class="p-2 sm:p-3 rounded-xl bg-white/5">
                    <span class="text-[11px] font-medium text-emerald-200/80 block">Jam Pulang</span>
                    <span class="text-base sm:text-lg font-bold text-teal-200 block mt-0.5">{{ substr($setting->time_out, 0, 5) }}</span>
                    <span class="text-[10px] text-teal-200/80 font-medium">Bisa Checkout</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4 mb-8">
        <!-- Total Guru -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-[#13793e] flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 font-medium block">Total Guru</span>
                <span class="text-xl font-bold text-slate-800">{{ $totalTeachers }}</span>
            </div>
        </div>

        <!-- Hadir Tepat Waktu -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-clock-check"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 font-medium block">Tepat Waktu</span>
                <span class="text-xl font-bold text-emerald-600">{{ $hadirCount }}</span>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 font-medium block">Terlambat</span>
                <span class="text-xl font-bold text-amber-600">{{ $terlambatCount }}</span>
            </div>
        </div>

        <!-- Izin / Sakit -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3">
            <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-notes-medical"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 font-medium block">Izin / Sakit</span>
                <span class="text-xl font-bold text-purple-600">{{ $izinSakitCount }}</span>
            </div>
        </div>

        <!-- Belum Absen -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex items-center gap-3 col-span-2 sm:col-span-1">
            <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg font-bold">
                <i class="fa-solid fa-user-xmark"></i>
            </div>
            <div>
                <span class="text-xs text-slate-500 font-medium block">Belum Absen</span>
                <span class="text-xl font-bold text-rose-600">{{ $belumAbsenCount }}</span>
            </div>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        
        <!-- Live Instant Search -->
        <div class="relative flex-1 max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" 
                   id="quickSearchInput" 
                   onkeyup="filterTeacherCards()" 
                   placeholder="Cari nama guru, NIP, atau mata pelajaran..." 
                   class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all">
        </div>

        <!-- Filter Status Buttons -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
            <button type="button" onclick="filterByTab('all')" data-tab="all" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-[#13793e] text-white transition-all whitespace-nowrap shadow-xs">Semua</button>
            <button type="button" onclick="filterByTab('unattended')" data-tab="unattended" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all whitespace-nowrap">Belum Absen ({{ $belumAbsenCount }})</button>
            <button type="button" onclick="filterByTab('checkedin')" data-tab="checkedin" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all whitespace-nowrap">Sudah Masuk ({{ $hadirCount + $terlambatCount }})</button>
            <button type="button" onclick="filterByTab('completed')" data-tab="completed" class="tab-btn px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all whitespace-nowrap">Sudah Pulang</button>
            <button type="button" onclick="openLeaveModal()" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-purple-50 text-purple-700 hover:bg-purple-100 transition-all whitespace-nowrap border border-purple-200 flex items-center gap-1.5">
                <i class="fa-solid fa-file-signature"></i>
                <span>Catat Izin / Sakit</span>
            </button>
        </div>
    </div>

    <!-- Teacher Cards Grid -->
    <div id="teachersGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($teachers as $teacher)
            @php
                $todayAtt = $teacher->attendances->first();
                $isAttended = (bool) $todayAtt;
                $statusType = 'unattended';
                if ($todayAtt) {
                    if (in_array($todayAtt->status, ['izin', 'sakit', 'alpa'])) {
                        $statusType = 'leave';
                    } elseif ($todayAtt->time_out) {
                        $statusType = 'completed';
                    } else {
                        $statusType = 'checkedin';
                    }
                }
            @endphp

            <div class="teacher-card bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-md transition-all p-5 flex flex-col justify-between"
                 data-name="{{ strtolower($teacher->name) }}"
                 data-nip="{{ $teacher->nip }}"
                 data-subject="{{ strtolower($teacher->subject) }}"
                 data-status="{{ $statusType }}">
                
                <!-- Card Header: Profile Info -->
                <div class="flex items-start gap-3.5">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-base shadow-xs shrink-0
                        {{ $teacher->gender === 'P' ? 'bg-pink-100 text-pink-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ strtoupper(substr($teacher->name, 0, 2)) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-slate-900 text-base leading-snug truncate" title="{{ $teacher->name }}">{{ $teacher->name }}</h3>
                        <p class="text-xs text-slate-500 font-medium truncate mt-0.5">
                            <i class="fa-solid fa-id-card text-[11px] mr-1 text-slate-400"></i>NIP: {{ $teacher->nip ?: '-' }}
                        </p>
                        <p class="text-xs text-blue-600 font-semibold truncate mt-0.5">
                            <i class="fa-solid fa-book-bookmark text-[11px] mr-1"></i>{{ $teacher->subject ?: 'Tenaga Pengajar' }}
                        </p>
                    </div>
                </div>

                <!-- Card Body: Current Attendance Status Info -->
                <div class="my-4 py-3 px-3.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                    @if(!$todayAtt)
                        <div class="flex items-center justify-between text-slate-500">
                            <span class="flex items-center gap-1.5 text-rose-600 font-semibold">
                                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                                Belum Absen Hari Ini
                            </span>
                            <span class="text-slate-400">Silakan tap tombol masuk</span>
                        </div>
                    @elseif($todayAtt->status === 'hadir')
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 font-bold text-[11px] inline-flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check"></i> Hadir Tepat Waktu
                                </span>
                                <span class="font-mono font-bold text-slate-700">{{ substr($todayAtt->time_in, 0, 5) }} WIB</span>
                            </div>
                            @if($todayAtt->time_out)
                                <div class="flex items-center justify-between text-slate-600 pt-1 border-t border-slate-200/60">
                                    <span>Pulang: <strong class="font-mono text-slate-800">{{ substr($todayAtt->time_out, 0, 5) }}</strong></span>
                                    <span class="text-[11px] text-blue-600 font-medium">Durasi: {{ $todayAtt->duration }}</span>
                                </div>
                            @endif
                        </div>
                    @elseif($todayAtt->status === 'terlambat')
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-0.5 rounded-md bg-amber-100 text-amber-800 font-bold text-[11px] inline-flex items-center gap-1">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Terlambat {{ $todayAtt->late_minutes }} mnt
                                </span>
                                <span class="font-mono font-bold text-amber-800">{{ substr($todayAtt->time_in, 0, 5) }} WIB</span>
                            </div>
                            @if($todayAtt->notes)
                                <p class="text-[11px] text-slate-500 italic truncate">"{{ $todayAtt->notes }}"</p>
                            @endif
                            @if($todayAtt->time_out)
                                <div class="flex items-center justify-between text-slate-600 pt-1 border-t border-slate-200/60">
                                    <span>Pulang: <strong class="font-mono text-slate-800">{{ substr($todayAtt->time_out, 0, 5) }}</strong></span>
                                    <span class="text-[11px] text-blue-600 font-medium">Durasi: {{ $todayAtt->duration }}</span>
                                </div>
                            @endif
                        </div>
                    @elseif(in_array($todayAtt->status, ['izin', 'sakit']))
                        <div class="flex items-center justify-between">
                            <span class="px-2 py-0.5 rounded-md {{ $todayAtt->status === 'sakit' ? 'bg-rose-100 text-rose-800' : 'bg-purple-100 text-purple-800' }} font-bold text-[11px]">
                                {{ strtoupper($todayAtt->status) }}
                            </span>
                            <span class="text-[11px] text-slate-600 truncate max-w-[150px]">{{ $todayAtt->notes ?: 'Telah terkonfirmasi' }}</span>
                        </div>
                    @endif
                </div>

                <!-- Card Actions: Fast Check-in / Check-out Buttons -->
                <div class="pt-1">
                    @if(!$todayAtt)
                        <!-- Tombol Absen Masuk Cepat -->
                        @php
                            $standardTimeIn = \Carbon\Carbon::parse($today . ' ' . $setting->time_in);
                            $standardTimeOut = \Carbon\Carbon::parse($today . ' ' . $setting->time_out);
                            $isBeforeTimeIn = now()->lt($standardTimeIn);
                            $isAfterTimeOut = now()->gt($standardTimeOut);
                            $isHoliday = (bool) ($setting->is_holiday_today ?? false);
                        @endphp
                        <div class="flex gap-2">
                            @if($isHoliday)
                                <div class="w-full py-2.5 px-3 rounded-xl bg-slate-100 text-slate-500 font-semibold text-xs border border-slate-200 text-center flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-calendar-xmark text-sm"></i>
                                    <span>Hari Libur Sekolah</span>
                                </div>
                            @elseif($isAfterTimeOut)
                                <div class="w-full py-2.5 px-3 rounded-xl bg-slate-100 text-rose-600 font-semibold text-xs border border-rose-200 text-center flex items-center justify-center gap-1.5" title="Jam kepulangan sekolah telah berakhir">
                                    <i class="fa-solid fa-ban text-sm"></i>
                                    <span>Ditutup (Lewat {{ substr($setting->time_out, 0, 5) }})</span>
                                </div>
                            @elseif($isBeforeTimeIn)
                                <button type="button" onclick="alertBeforeTimeIn('{{ substr($setting->time_in, 0, 5) }}')" class="w-full py-2.5 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-md shadow-amber-500/20 active:scale-95 transition-all flex items-center justify-center gap-1.5" title="Presensi belum dimulai">
                                    <i class="fa-solid fa-lock text-sm"></i>
                                    <span>Belum Dibuka ({{ substr($setting->time_in, 0, 5) }})</span>
                                </button>
                                <button type="button" onclick="openSingleLeaveModal({{ $teacher->id }}, '{{ addslashes($teacher->name) }}')" title="Catat Izin/Sakit" class="p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-medium">
                                    <i class="fa-solid fa-ellipsis-vertical px-1"></i>
                                </button>
                            @else
                                <form action="{{ route('admin.checkin') }}" method="POST" class="flex-1" onsubmit="return confirmCheckIn('{{ $teacher->name }}')">
                                    @csrf
                                    <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                                    <button type="submit" class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 active:scale-95 transition-all flex items-center justify-center gap-1.5">
                                        <i class="fa-solid fa-arrow-right-to-bracket text-sm"></i>
                                        <span>Absen Masuk</span>
                                    </button>
                                </form>
                                
                                <button type="button" onclick="openSingleLeaveModal({{ $teacher->id }}, '{{ addslashes($teacher->name) }}')" title="Catat Izin/Sakit" class="p-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-100 text-xs font-medium">
                                    <i class="fa-solid fa-ellipsis-vertical px-1"></i>
                                </button>
                            @endif
                        </div>

                    @elseif(!$todayAtt->time_out && in_array($todayAtt->status, ['hadir', 'terlambat']))
                        <!-- Tombol Absen Pulang Cepat -->
                        @php
                            $standardTimeOut = \Carbon\Carbon::parse($today . ' ' . $setting->time_out);
                            $isBeforeTimeOut = now()->lt($standardTimeOut);
                        @endphp
                        <form action="{{ route('admin.checkout') }}" method="POST" onsubmit="return confirmCheckOut('{{ $teacher->name }}', {{ $isBeforeTimeOut ? 'true' : 'false' }}, '{{ substr($setting->time_out, 0, 5) }}')">
                            @csrf
                            <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                            <button type="submit" class="w-full py-2.5 px-3 rounded-xl {{ $isBeforeTimeOut ? 'bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700' : 'bg-gradient-to-r from-emerald-600 to-[#13793e] hover:from-emerald-700 hover:to-[#0f6031]' }} text-white font-bold text-xs shadow-md active:scale-95 transition-all flex items-center justify-center gap-1.5" title="{{ $isBeforeTimeOut ? 'Absen pulang lebih awal sebelum jam ' . substr($setting->time_out, 0, 5) : 'Absen pulang sekarang' }}">
                                <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                                <span>{{ $isBeforeTimeOut ? 'Absen Pulang (Lebih Awal)' : 'Absen Pulang Sekarang' }}</span>
                            </button>
                        </form>

                    @elseif($todayAtt->time_out)
                        <!-- Selesai Hari Ini -->
                        <div class="py-2 px-3 rounded-xl bg-emerald-50 text-emerald-700 text-center font-bold text-xs border border-emerald-200 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-check-double text-sm"></i>
                            <span>Presensi Hari Ini Selesai</span>
                        </div>
                    @else
                        <!-- Izin / Sakit Terdaftar -->
                        <div class="py-2 px-3 rounded-xl bg-purple-50 text-purple-700 text-center font-bold text-xs border border-purple-200 flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-calendar-xmark text-sm"></i>
                            <span>Keterangan Izin / Sakit Tercatat</span>
                        </div>
                    @endif
                </div>

            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200 p-8">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4 text-2xl">
                    <i class="fa-solid fa-user-slash"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-700">Belum Ada Data Guru</h3>
                <p class="text-sm text-slate-500 mt-1 mb-4">Tambahkan guru terlebih dahulu untuk memulai absensi.</p>
                <a href="{{ route('admin.teachers.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#13793e] text-white font-semibold text-sm hover:bg-[#0f6031] transition-all">
                    <i class="fa-solid fa-plus"></i> Tambah Data Guru
                </a>
            </div>
        @endforelse
    </div>

</div>

<!-- Modal Catat Izin / Sakit -->
<div id="leaveModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-purple-600"></i>
                <span>Catat Izin / Sakit / Alpa</span>
            </h3>
            <button type="button" onclick="closeLeaveModal()" class="text-slate-400 hover:text-slate-600 text-lg">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form action="{{ route('admin.attendance.manual') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            <input type="hidden" name="date" value="{{ $today }}">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Guru</label>
                <select name="teacher_id" id="modalTeacherId" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->nip ?: 'NIP -' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status Kehadiran</label>
                <div class="grid grid-cols-3 gap-2">
                    <label class="border border-slate-200 rounded-xl p-2.5 text-center cursor-pointer hover:bg-slate-50 flex flex-col items-center gap-1 text-xs font-semibold">
                        <input type="radio" name="status" value="izin" checked class="text-blue-600">
                        <span>Izin</span>
                    </label>
                    <label class="border border-slate-200 rounded-xl p-2.5 text-center cursor-pointer hover:bg-slate-50 flex flex-col items-center gap-1 text-xs font-semibold">
                        <input type="radio" name="status" value="sakit" class="text-blue-600">
                        <span>Sakit</span>
                    </label>
                    <label class="border border-slate-200 rounded-xl p-2.5 text-center cursor-pointer hover:bg-slate-50 flex flex-col items-center gap-1 text-xs font-semibold">
                        <input type="radio" name="status" value="alpa" class="text-blue-600">
                        <span>Alpa</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keterangan / Alasan</label>
                <textarea name="notes" rows="3" placeholder="Contoh: Mengikuti MGMP di Dinas Pendidikan / Surat dokter terlampir" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div class="pt-3 flex gap-2">
                <button type="button" onclick="closeLeaveModal()" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs hover:bg-slate-100">Batal</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs shadow-md">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function confirmCheckIn(teacherName) {
        return confirm(`Konfirmasi Absen Masuk untuk: ${teacherName} sekarang?`);
    }

    function confirmCheckOut(teacherName, isEarly = false, timeOut = '') {
        if (isEarly) {
            return confirm(`Perhatian: Jam kepulangan reguler adalah pukul ${timeOut} WIB.\nApakah Anda yakin ingin memproses absen pulang lebih awal untuk: ${teacherName}?`);
        }
        return confirm(`Konfirmasi Absen Pulang untuk: ${teacherName} sekarang?`);
    }

    function openLeaveModal() {
        document.getElementById('leaveModal').classList.remove('hidden');
    }

    function openSingleLeaveModal(teacherId, teacherName) {
        document.getElementById('modalTeacherId').value = teacherId;
        openLeaveModal();
    }

    function closeLeaveModal() {
        document.getElementById('leaveModal').classList.add('hidden');
    }

    let activeTab = 'all';

    function filterByTab(tab) {
        activeTab = tab;
        document.querySelectorAll('.tab-btn').forEach(btn => {
            if (btn.getAttribute('data-tab') === tab) {
                btn.classList.remove('bg-slate-100', 'text-slate-600');
                btn.classList.add('bg-[#13793e]', 'text-white');
            } else {
                btn.classList.remove('bg-[#13793e]', 'text-white');
                btn.classList.add('bg-slate-100', 'text-slate-600');
            }
        });
        filterTeacherCards();
    }

    function filterTeacherCards() {
        const query = document.getElementById('quickSearchInput').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.teacher-card');

        cards.forEach(card => {
            const name = card.getAttribute('data-name');
            const nip = card.getAttribute('data-nip');
            const subject = card.getAttribute('data-subject');
            const status = card.getAttribute('data-status');

            const matchesSearch = name.includes(query) || nip.includes(query) || subject.includes(query);
            
            let matchesTab = true;
            if (activeTab === 'unattended') {
                matchesTab = (status === 'unattended');
            } else if (activeTab === 'checkedin') {
                matchesTab = (status === 'checkedin' || status === 'completed');
            } else if (activeTab === 'completed') {
                matchesTab = (status === 'completed');
            }

            if (matchesSearch && matchesTab) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function alertBeforeTimeIn(timeIn) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Presensi Belum Dibuka!',
                html: `<div class="text-sm space-y-2 text-slate-700">
                         <p>Jadwal presensi masuk hari ini dimulai pukul <b>${timeIn} WIB</b>.</p>
                         <p class="text-xs text-slate-500">Guru tidak diperbolehkan absen sebelum jam absensi dimulai.</p>
                       </div>`,
                confirmButtonColor: '#d97706',
                confirmButtonText: 'Saya Mengerti'
            });
        } else {
            alert('Presensi Belum Dibuka! Jadwal absen masuk dimulai pada pukul ' + timeIn + ' WIB.');
        }
    }

    function alertBeforeTimeOut(timeOut) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Waktunya Presensi Pulang!',
                html: `<div class="text-sm space-y-2 text-slate-700">
                         <p>Jadwal kepulangan sekolah hari ini baru dibuka pada pukul <b>${timeOut} WIB</b>.</p>
                         <p class="text-xs text-slate-500">Guru tidak diperbolehkan absen pulang sebelum jam kepulangan tiba.</p>
                       </div>`,
                confirmButtonColor: '#d97706',
                confirmButtonText: 'Saya Mengerti'
            });
        } else {
            alert('Belum Waktunya Pulang! Jadwal absen pulang dimulai pada pukul ' + timeOut + ' WIB.');
        }
    }
</script>
@endpush
@endsection
