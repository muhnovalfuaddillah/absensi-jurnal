@extends('layouts.app')

@section('title', 'Jurnal Mengajar Guru - E-Presensi')

@section('content')
<div class="flex flex-col gap-4">

    <!-- Top Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#13793e] via-[#0f6332] to-[#0a4d26] p-5 sm:p-6 text-white shadow-lg shadow-emerald-900/15">
        <!-- Background Ornament -->
        <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-white/5 pointer-events-none blur-2xl"></div>
        <div class="absolute -left-10 -bottom-10 w-44 h-44 rounded-full bg-emerald-400/10 pointer-events-none blur-2xl"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-[11px] font-bold text-emerald-100 border border-white/10 mb-2">
                    <i class="fa-solid fa-book-bookmark text-[10px]"></i>
                    <span>E-Jurnal & Presensi Kelas</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white leading-tight">
                    Jurnal Mengajar Guru
                </h1>
                <p class="text-xs text-emerald-100/90 font-medium mt-1 max-w-md leading-relaxed">
                    Catatan materi harian tatap muka, capaian pembelajaran, serta absensi kehadiran siswa terintegrasi barcode ruang kelas.
                </p>
            </div>

            <!-- Action Button -->
            <div class="shrink-0">
                <a href="{{ route('guru.scan_kelas') }}" 
                   class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-white hover:bg-emerald-50 text-[#13793e] rounded-2xl font-black text-xs sm:text-sm shadow-md shadow-black/10 active:scale-95 transition-all">
                    <i class="fa-solid fa-qrcode text-base text-[#13793e]"></i>
                    <span>Scan QR & Isi Jurnal</span>
                </a>
            </div>
        </div>

        <!-- Quick Summary Strip inside Hero -->
        <div class="relative z-10 mt-5 pt-4 border-t border-white/15 grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-white/15 backdrop-blur-md flex items-center justify-center text-white shrink-0 text-xs">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <div>
                    <p class="text-[10px] text-emerald-100 uppercase tracking-wider font-bold">Periode Bulan Ini</p>
                    <p class="text-sm font-black text-white">{{ $totalJournalsMonth }} Pertemuan</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-white/15 backdrop-blur-md flex items-center justify-center text-white shrink-0 text-xs">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
                <div>
                    <p class="text-[10px] text-emerald-100 uppercase tracking-wider font-bold">Jam Terpakai Bulan Ini</p>
                    <p class="text-sm font-black text-white">{{ $totalHoursMonth }} Jam ({{ $totalHoursMonth }} JP)</p>
                </div>
            </div>

            <div class="hidden sm:flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-white/15 backdrop-blur-md flex items-center justify-center text-white shrink-0 text-xs">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <div>
                    <p class="text-[10px] text-emerald-100 uppercase tracking-wider font-bold">Guru Pengampu</p>
                    <p class="text-sm font-black text-white truncate max-w-[130px]">{{ $teacher->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Sub-Bar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
        <a href="{{ route('guru.jadwal') }}" class="px-3.5 py-2 rounded-2xl text-xs font-bold bg-white border border-slate-200/80 text-slate-600 hover:text-[#13793e] hover:border-emerald-300 hover:bg-emerald-50/50 shrink-0 transition-all shadow-2xs flex items-center gap-1.5">
            <i class="fa-regular fa-calendar text-[11px] text-slate-400"></i>
            <span>Jadwal Mengajar</span>
        </a>
        <a href="{{ route('guru.jurnal.index') }}" class="px-3.5 py-2 rounded-2xl text-xs font-black bg-[#13793e] text-white shrink-0 shadow-xs flex items-center gap-1.5">
            <i class="fa-solid fa-book-bookmark text-[11px]"></i>
            <span>Riwayat Jurnal Guru</span>
        </a>
        <a href="{{ route('guru.absen_siswa.index') }}" class="px-3.5 py-2 rounded-2xl text-xs font-bold bg-white border border-slate-200/80 text-slate-600 hover:text-[#13793e] hover:border-emerald-300 hover:bg-emerald-50/50 shrink-0 transition-all shadow-2xs flex items-center gap-1.5">
            <i class="fa-solid fa-users text-[11px] text-slate-400"></i>
            <span>Rekap Presensi Siswa</span>
        </a>
        <a href="{{ route('guru.laporan_izin') }}" class="px-3.5 py-2 rounded-2xl text-xs font-bold bg-white border border-slate-200/80 text-slate-600 hover:text-[#13793e] hover:border-emerald-300 hover:bg-emerald-50/50 shrink-0 transition-all shadow-2xs flex items-center gap-1.5">
            <i class="fa-solid fa-user-clock text-[11px] text-slate-400"></i>
            <span>Laporan Izin Guru</span>
        </a>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs">
        <form method="GET" action="{{ route('guru.jurnal.index') }}" class="flex flex-col sm:flex-row gap-2.5">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari materi, pokok bahasan, atau mata pelajaran..."
                       class="w-full pl-9 pr-3 py-2.5 text-xs rounded-2xl bg-slate-50 border border-slate-200/70 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 transition-all font-medium">
            </div>

            <div class="flex items-center gap-2 flex-wrap sm:flex-nowrap">
                <div class="relative">
                    <input type="month" name="month" value="{{ $month }}"
                           class="py-2.5 px-3 text-xs rounded-2xl bg-slate-50 border border-slate-200/70 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-medium">
                </div>

                <select name="class_id" class="py-2.5 px-3 text-xs rounded-2xl bg-slate-50 border border-slate-200/70 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-medium flex-1 sm:flex-initial">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2.5 bg-[#13793e] hover:bg-[#0f6332] text-white rounded-2xl text-xs font-black transition-all shrink-0 shadow-xs flex items-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-filter text-[11px]"></i>
                    <span>Terapkan</span>
                </button>

                @if($search || $classId || $month != now()->format('Y-m'))
                    <a href="{{ route('guru.jurnal.index') }}" class="p-2.5 text-slate-400 hover:text-rose-500 rounded-2xl hover:bg-rose-50 text-xs transition-colors shrink-0" title="Reset Filter">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Active Filter Info (if filtered) -->
    <div class="flex items-center justify-between px-1 text-xs">
        <span class="text-slate-500 font-semibold flex items-center gap-1.5">
            <i class="fa-solid fa-layer-group text-[#13793e]"></i>
            <span>Menampilkan data untuk: <b>{{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</b></span>
        </span>
        <span class="text-xs font-extrabold text-[#13793e] bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200/60">
            {{ $journals->total() }} Catatan Ditemukan
        </span>
    </div>

    <!-- Jurnal List Cards -->
    @if($journals->isEmpty())
        <div class="py-14 px-4 text-center bg-white rounded-3xl border border-dashed border-slate-200 shadow-xs flex flex-col items-center justify-center gap-3">
            <div class="w-16 h-16 rounded-3xl bg-emerald-50 text-[#13793e] flex items-center justify-center text-3xl shadow-2xs">
                <i class="fa-solid fa-book-open-reader"></i>
            </div>
            <div>
                <h3 class="text-sm font-extrabold text-slate-800">Belum Ada Jurnal Pembelajaran</h3>
                <p class="text-xs text-slate-500 max-w-sm mt-1 leading-relaxed">
                    Tidak ditemukan catatan jurnal mengajar pada periode ini. Masuk ke ruang kelas dan pindai barcode kelas untuk mengisi jurnal.
                </p>
            </div>
            <a href="{{ route('guru.scan_kelas') }}" class="mt-2 px-5 py-2.5 bg-[#13793e] hover:bg-[#0f6332] text-white text-xs font-bold rounded-2xl shadow-md shadow-emerald-800/15 flex items-center gap-2 active:scale-95 transition-all">
                <i class="fa-solid fa-qrcode"></i>
                <span>Scan Barcode Ruang Kelas</span>
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-3.5">
            @foreach($journals as $j)
                @php
                    $sum = $j->attendance_summary;
                    $totalStudents = $j->studentAttendances->count();
                    $presentPercent = $totalStudents > 0 ? round(($sum['hadir'] / $totalStudents) * 100) : 0;
                @endphp
                <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-xs hover:shadow-md hover:border-emerald-200 transition-all flex flex-col gap-3.5 group">
                    
                    <!-- Top Row: Date, Class & Subject Badges, Status -->
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <!-- Date Block -->
                            <div class="w-13 h-13 rounded-2xl bg-gradient-to-b from-[#eaf8ef] to-[#dcfce7] text-[#13793e] flex flex-col items-center justify-center font-bold text-xs shrink-0 font-mono shadow-2xs border border-emerald-200/60">
                                <span class="text-base font-black leading-none">{{ $j->date->format('d') }}</span>
                                <span class="text-[10px] uppercase font-extrabold text-emerald-800/90 leading-none mt-1">{{ $j->date->translatedFormat('M') }}</span>
                            </div>

                            <div class="min-w-0">
                                <!-- Tags: Class, Subject, Substitute -->
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-[#13793e] text-white shadow-2xs">
                                        {{ $j->schoolClass->name ?? 'Kelas' }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                        <i class="fa-solid fa-book text-[10px] text-[#13793e] mr-1"></i>
                                        {{ $j->subject->name ?? 'Mata Pelajaran' }}
                                    </span>

                                    @if($j->is_substitute)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200 flex items-center gap-1">
                                            <i class="fa-solid fa-arrow-right-arrow-left text-[9px]"></i>
                                            Inval
                                        </span>
                                    @endif
                                </div>

                                <!-- Materi Title -->
                                <h3 class="font-extrabold text-sm sm:text-base text-slate-900 mt-1.5 leading-snug group-hover:text-[#13793e] transition-colors line-clamp-1">
                                    {{ $j->materi }}
                                </h3>

                                <!-- Time & Teaching Mode Info -->
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 mt-1 flex-wrap">
                                    <span class="flex items-center gap-1 font-medium text-slate-500">
                                        <i class="fa-regular fa-clock text-[10px] text-slate-400"></i>
                                        <span>{{ substr($j->start_time, 0, 5) }} - {{ substr($j->end_time, 0, 5) }} WIB</span>
                                    </span>
                                    <span>•</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10.5px] font-bold bg-amber-50 text-amber-900 border border-amber-200/80 shadow-2xs">
                                        <i class="fa-solid fa-hourglass-half text-[9px] text-amber-600"></i>
                                        <span>{{ $j->duration_label }} ({{ $j->estimated_hours }} JP)</span>
                                    </span>
                                    <span>•</span>
                                    <span class="capitalize font-semibold text-slate-600">{{ $j->status }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="shrink-0">
                            @if($j->status === 'terlaksana')
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-[#dcfce7] text-[#15803d] border border-emerald-200/80 shadow-2xs flex items-center gap-1">
                                    <i class="fa-solid fa-check text-[10px]"></i> Terlaksana
                                </span>
                            @elseif($j->status === 'daring')
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-blue-50 text-blue-700 border border-blue-200 shadow-2xs">
                                    Daring
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-50 text-amber-700 border border-amber-200 shadow-2xs capitalize">
                                    {{ $j->status }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Uraian Ringkas Pembelajaran -->
                    <div class="bg-slate-50/80 p-3 rounded-2xl border border-slate-100 text-xs text-slate-600 leading-relaxed font-normal">
                        <p class="line-clamp-2">{{ $j->kegiatan }}</p>
                    </div>

                    <!-- Presensi Summary & Footer Action -->
                    <div class="pt-3 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5">
                        <!-- Attendance Stats Pills -->
                        <div class="flex items-center gap-1.5 text-xs flex-wrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-emerald-50 text-emerald-800 font-bold border border-emerald-200/80 text-[11px]">
                                <i class="fa-solid fa-user-check text-[10px] text-emerald-600"></i>
                                Hadir: <b>{{ $sum['hadir'] }}</b>
                            </span>

                            @if($sum['sakit'] > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-amber-50 text-amber-800 font-bold border border-amber-200/80 text-[11px]">
                                    <i class="fa-solid fa-head-side-cough text-[10px] text-amber-600"></i>
                                    Sakit: <b>{{ $sum['sakit'] }}</b>
                                </span>
                            @endif

                            @if($sum['izin'] > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-blue-50 text-blue-800 font-bold border border-blue-200/80 text-[11px]">
                                    <i class="fa-regular fa-envelope text-[10px] text-blue-600"></i>
                                    Izin: <b>{{ $sum['izin'] }}</b>
                                </span>
                            @endif

                            @if($sum['alpa'] > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl bg-rose-50 text-rose-800 font-bold border border-rose-200/80 text-[11px]">
                                    <i class="fa-solid fa-user-xmark text-[10px] text-rose-600"></i>
                                    Alpa: <b>{{ $sum['alpa'] }}</b>
                                </span>
                            @endif

                            <span class="text-[11px] text-slate-400 ml-1 font-medium">
                                ({{ $presentPercent }}% Hadir)
                            </span>
                        </div>

                        <!-- Button Detail -->
                        <a href="{{ route('guru.jurnal.show', $j->id) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-[#13793e] hover:text-white text-slate-700 font-bold text-xs transition-all shadow-2xs self-start sm:self-auto">
                            <span>Detail Presensi</span>
                            <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                </div>
            @endforeach

            <!-- Pagination -->
            <div class="mt-3">
                {{ $journals->links() }}
            </div>
        </div>
    @endif

</div>
@endsection
