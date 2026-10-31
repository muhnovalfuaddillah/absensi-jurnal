@extends('layouts.admin')

@section('title', 'Laporan & Rekapitulasi Presensi Siswa - SIMPRES BU')

@section('content')
<div class="flex flex-col gap-space-md">

    <!-- Tab Navigasi Antara Laporan Guru & Siswa -->
    <div class="no-print flex items-center gap-2 border-b border-surface-container pb-2">
        <a href="{{ route('admin.attendance.recap') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-all">
            <span class="material-symbols-outlined text-[18px]">badge</span>
            <span>Laporan Absensi Guru</span>
        </a>
        <a href="{{ route('admin.students.attendance_recap') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-[#13793e] text-white shadow-sm shadow-emerald-900/15 transition-all">
            <span class="material-symbols-outlined text-[18px]">school</span>
            <span>Laporan Absensi Siswa</span>
        </a>
    </div>

    <!-- Top Action Bar (Hidden on Print) -->
    <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-space-sm bg-surface-container-lowest p-space-md rounded-2xl shadow-sm border border-surface-container">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-2xl font-bold shrink-0">
                <span class="material-symbols-outlined text-[28px]">how_to_reg</span>
            </div>
            <div>
                <h1 class="font-headline-md text-headline-md font-bold text-on-surface">Laporan Rekapitulasi Presensi Siswa</h1>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Akumulasi presensi kehadiran siswa per kelas untuk pembinaan, wali kelas, dan rapor berkala
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($selectedClass)
            <!-- Download Excel -->
            <a href="{{ route('admin.students.attendance_recap.excel', ['class_id' => $selectedClassId, 'month' => $month]) }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-900/10 transition-all active:scale-95">
                <i class="fa-solid fa-file-excel text-[14px]"></i>
                <span>Download Excel</span>
            </a>
            @endif

            <!-- Print / Simpan PDF -->
            <button type="button" onclick="window.print()" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-surface-container-high hover:bg-surface-container text-on-surface font-bold text-xs border border-surface-container transition-all active:scale-95">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak Rekap</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar (Hidden on Print) -->
    <div class="no-print bg-surface-container-lowest p-space-md rounded-2xl shadow-sm border border-surface-container">
        <form method="GET" action="{{ route('admin.students.attendance_recap') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">Pilih Kelas:</label>
                <select name="class_id" class="w-full text-xs rounded-xl bg-surface-container-low border border-surface-container-high py-2.5 px-3 text-on-surface focus:outline-none focus:border-primary font-bold">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} (Wali: {{ $c->homeroomTeacher->name ?? 'Belum Ada' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">Periode Bulan:</label>
                <input type="month" name="month" value="{{ $month }}"
                       class="w-full text-xs rounded-xl bg-surface-container-low border border-surface-container-high py-2.5 px-3 text-on-surface focus:outline-none focus:border-primary font-mono font-bold">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full py-2.5 px-4 bg-primary hover:bg-[#0f6031] text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-900/10 transition-all">
                    Tampilkan Rekapitulasi
                </button>
            </div>
        </form>
    </div>

    @if($selectedClass)
        @php
            $totHadir = 0;
            $totSakit = 0;
            $totIzin = 0;
            $totAlpa = 0;
            foreach ($students as $s) {
                $st = $stats[$s->id] ?? ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0, 'total' => 0, 'persentase' => 100];
                $totHadir += $st['hadir'];
                $totSakit += $st['sakit'];
                $totIzin += $st['izin'];
                $totAlpa += $st['alpa'];
            }
            $grandTotal = $totHadir + $totSakit + $totIzin + $totAlpa;
            $avgAttendance = $grandTotal > 0 ? round(($totHadir / $grandTotal) * 100, 1) : 100;
        @endphp

        <!-- 4 Quick Stats Cards (Hidden on Print) -->
        <div class="no-print grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-surface-container shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-on-surface-variant uppercase tracking-wider block">Rata-rata Kelas</span>
                    <span class="text-xl font-black text-primary">{{ $avgAttendance }}%</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-[#13793e] flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">pie_chart</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-surface-container shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-on-surface-variant uppercase tracking-wider block">Total Hadir (H)</span>
                    <span class="text-xl font-black text-emerald-700">{{ $totHadir }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">check_circle</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-surface-container shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-on-surface-variant uppercase tracking-wider block">Izin & Sakit (I/S)</span>
                    <span class="text-xl font-black text-amber-700">{{ $totIzin + $totSakit }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">sick</span>
                </div>
            </div>

            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-surface-container shadow-xs flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold text-on-surface-variant uppercase tracking-wider block">Tanpa Keterangan (A)</span>
                    <span class="text-xl font-black text-rose-600">{{ $totAlpa }}</span>
                </div>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">cancel</span>
                </div>
            </div>
        </div>

        <!-- Official Report Paper Container -->
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-surface-container p-6 sm:p-8">
            
            <!-- School Formal Letterhead (KOP SURAT) - Always shown & beautiful on print -->
            <div class="flex items-center justify-between pb-6 border-b-2 border-slate-900 mb-6 gap-4">
                <img src="{{ $setting->logo_url ?? asset('logo.jpg') }}" alt="Logo Sekolah" class="w-20 h-20 object-contain shrink-0 rounded-lg">
                <div class="text-center flex-1">
                    @if($setting->foundation_name)
                        <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-600">{{ $setting->foundation_name }}</h3>
                    @endif
                    <h2 class="text-xl sm:text-2xl font-black uppercase tracking-wider text-slate-900">{{ $setting->school_name }}</h2>
                    <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">{{ $setting->school_address ?: 'Laporan Rekapitulasi Presensi Kehadiran Siswa' }}</p>
                    @if($setting->school_phone || $setting->school_email)
                        <p class="text-[11px] text-slate-500 font-normal">
                            @if($setting->school_phone) Telp: {{ $setting->school_phone }} @endif
                            @if($setting->school_phone && $setting->school_email) &bull; @endif
                            @if($setting->school_email) Email: {{ $setting->school_email }} @endif
                        </p>
                    @endif
                    <div class="mt-3 pt-2 border-t border-slate-200 flex flex-wrap items-center justify-center gap-x-3 gap-y-1 text-xs text-slate-700">
                        <span>Kelas: <strong class="text-[#13793e]">{{ $selectedClass->name }}</strong> (Tingkat {{ $selectedClass->grade_level }})</span>
                        <span>&bull;</span>
                        <span>Periode: <strong class="text-[#13793e]">{{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</strong></span>
                        <span>&bull;</span>
                        <span>Wali Kelas: <strong>{{ $selectedClass->homeroomTeacher->name ?? 'Belum Ditentukan' }}</strong></span>
                        <span>&bull;</span>
                        <span class="inline-flex items-center gap-1 bg-emerald-50 px-2 py-0.5 rounded text-[#13793e] font-bold">
                            <span class="material-symbols-outlined text-[14px]">school</span>
                            Kepala Sekolah: <u>{{ $setting->headmaster_name ?: 'Muh Noval Fuaddillah' }}</u>
                        </span>
                    </div>
                </div>
                <div class="w-20 hidden sm:block"></div>
            </div>

            @if($students->isEmpty())
                <div class="py-12 text-center text-xs text-slate-400">
                    Belum ada siswa yang terdaftar di kelas ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-surface-container bg-surface-container-low text-on-surface uppercase text-[10px] tracking-wider font-bold">
                                <th class="py-3 px-3 text-center w-10">No</th>
                                <th class="py-3 px-3">NIS</th>
                                <th class="py-3 px-3">Nama Lengkap Siswa</th>
                                <th class="py-3 px-3 text-center">L/P</th>
                                <th class="py-3 px-3 text-center text-emerald-800 bg-emerald-50/70">Hadir (H)</th>
                                <th class="py-3 px-3 text-center text-amber-800 bg-amber-50/70">Sakit (S)</th>
                                <th class="py-3 px-3 text-center text-blue-800 bg-blue-50/70">Izin (I)</th>
                                <th class="py-3 px-3 text-center text-rose-800 bg-rose-50/70">Alpa (A)</th>
                                <th class="py-3 px-3 text-center font-black bg-emerald-100/60 text-[#13793e]">% Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container-low">
                            @foreach($students as $idx => $s)
                                @php
                                    $st = $stats[$s->id] ?? ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0, 'total' => 0, 'persentase' => 100];
                                @endphp
                                <tr class="hover:bg-surface-container-low/50 transition-colors">
                                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono">{{ $idx + 1 }}</td>
                                    <td class="py-2.5 px-3 font-mono text-slate-600">{{ $s->nis ?? '-' }}</td>
                                    <td class="py-2.5 px-3 font-bold text-on-surface">{{ $s->name }}</td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="font-bold {{ $s->gender == 'L' ? 'text-blue-700' : 'text-pink-600' }}">{{ $s->gender }}</span>
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-bold text-emerald-700 font-mono bg-emerald-50/30">{{ $st['hadir'] }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold text-amber-700 font-mono bg-amber-50/30">{{ $st['sakit'] }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold text-blue-700 font-mono bg-blue-50/30">{{ $st['izin'] }}</td>
                                    <td class="py-2.5 px-3 text-center font-bold text-rose-700 font-mono bg-rose-50/30">{{ $st['alpa'] }}</td>
                                    <td class="py-2.5 px-3 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-black font-mono {{ $st['persentase'] >= 85 ? 'bg-emerald-100 text-[#13793e]' : ($st['persentase'] >= 75 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                            {{ $st['persentase'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-surface-container-low font-bold border-t-2 border-surface-container text-on-surface">
                                <td colspan="4" class="py-3 px-3 text-right font-black uppercase tracking-wider">Total Akumulasi:</td>
                                <td class="py-3 px-3 text-center text-emerald-700 font-black bg-emerald-100/50">{{ $totHadir }}</td>
                                <td class="py-3 px-3 text-center text-amber-700 font-black bg-amber-100/50">{{ $totSakit }}</td>
                                <td class="py-3 px-3 text-center text-blue-700 font-black bg-blue-100/50">{{ $totIzin }}</td>
                                <td class="py-3 px-3 text-center text-rose-700 font-black bg-rose-100/50">{{ $totAlpa }}</td>
                                <td class="py-3 px-3 text-center text-[#13793e] font-black bg-emerald-100/80 text-sm">{{ $avgAttendance }}%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Footer Tanda Tangan Formal (Always on Print) -->
                <div class="mt-8 pt-6 border-t border-slate-200 grid grid-cols-2 gap-8 text-center text-xs text-slate-800">
                    <div>
                        <p class="font-medium">Mengetahui,</p>
                        <p class="font-bold">Kepala {{ $setting->school_name }}</p>
                        <div class="h-16"></div>
                        <p class="font-black underline">{{ $setting->headmaster_name ?: 'Muh Noval Fuaddillah' }}</p>
                        <p class="text-[11px] text-slate-500">NIP. {{ $setting->headmaster_nip ?: '-' }}</p>
                    </div>

                    <div>
                        <p class="font-medium">{{ $setting->school_city ?: 'Ditetapkan' }}, {{ now()->translatedFormat('d F Y') }}</p>
                        <p class="font-bold">Wali Kelas {{ $selectedClass->name }}</p>
                        <div class="h-16"></div>
                        <p class="font-black underline">{{ $selectedClass->homeroomTeacher->name ?? '..........................................' }}</p>
                        <p class="text-[11px] text-slate-500">NIP. {{ $selectedClass->homeroomTeacher->nip ?? '...................................' }}</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
