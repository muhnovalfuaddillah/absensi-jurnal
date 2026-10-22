@extends('layouts.admin')

@section('title', 'Dashboard Superadmin - Pusat Multi-Sekolah')

@section('content')
<div class="space-y-6">

    <!-- Top Hero Banner: Super Administrator -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-[#0a4d26] to-[#13793e] p-6 sm:p-8 text-white shadow-xl shadow-emerald-950/20">
        <div class="absolute -right-10 -top-10 w-60 h-60 rounded-full bg-emerald-400/10 pointer-events-none blur-3xl"></div>
        <div class="absolute -left-12 -bottom-12 w-64 h-64 rounded-full bg-amber-400/10 pointer-events-none blur-3xl"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md text-[11px] font-bold text-amber-300 border border-amber-300/20">
                    <span class="material-symbols-outlined text-[15px] text-amber-300">verified</span>
                    <span>Pusat Kendali Aplikasi Multi-Sekolah</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white leading-tight">
                    Dashboard Super Administrator
                </h1>
                <p class="text-xs sm:text-sm text-emerald-100/90 font-medium max-w-xl leading-relaxed">
                    Kelola seluruh ekosistem sekolah, pantau rekapitulasi guru dan siswa, atur akun administrator sekolah, dan berpindah antar sekolah dalam satu aplikasi terpusat.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="{{ route('superadmin.schools.index') }}" 
                   class="px-4 py-2.5 rounded-2xl bg-white text-[#13793e] hover:bg-emerald-50 font-bold text-xs flex items-center gap-2 shadow-lg shadow-black/10 transition-all hover:scale-[1.02] active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">add_business</span>
                    <span>Tambah Sekolah Baru</span>
                </a>
                <a href="{{ route('superadmin.admins.index') }}" 
                   class="px-4 py-2.5 rounded-2xl bg-white/15 hover:bg-white/25 backdrop-blur-md text-white font-bold text-xs flex items-center gap-2 border border-white/20 transition-all hover:scale-[1.02] active:scale-95">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    <span>Kelola Admin Sekolah</span>
                </a>
            </div>
        </div>
    </div>

    <!-- STATISTIK GLOBAL (RINGKASAN SELURUH SEKOLAH) -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3.5 sm:gap-4">
        <!-- Total Sekolah -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase text-slate-400">Total Sekolah</span>
                <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">apartment</span>
                </div>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ $totalSchools }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-0.5 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>{{ $activeSchools }} Sekolah Aktif</span>
                </div>
            </div>
        </div>

        <!-- Total Guru -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase text-slate-400">Total Guru</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">badge</span>
                </div>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalTeachers) }}</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Semua Lembaga</div>
            </div>
        </div>

        <!-- Total Siswa -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase text-slate-400">Total Siswa</span>
                <div class="w-8 h-8 rounded-xl bg-violet-50 text-violet-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">school</span>
                </div>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalStudents) }}</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Seluruh Kelas</div>
            </div>
        </div>

        <!-- Total Rombel / Kelas -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase text-slate-400">Total Kelas</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">meeting_room</span>
                </div>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalClasses) }}</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Rombongan Belajar</div>
            </div>
        </div>

        <!-- Guru Hadir Hari Ini (Global) -->
        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-xs flex flex-col justify-between col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-bold uppercase text-slate-400">Presensi Hari Ini</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                </div>
            </div>
            <div>
                <div class="text-2xl font-black text-slate-900">{{ number_format($totalHadirToday) }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-0.5">Guru Hadir / Terlambat</div>
            </div>
        </div>
    </div>

    <!-- DAFTAR DAN MONITORING SELURUH SEKOLAH -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-[#13793e] flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[22px]">corporate_fare</span>
                </div>
                <div>
                    <h2 class="text-base font-black text-slate-900">Daftar Sekolah & Status Kehadiran</h2>
                    <p class="text-xs text-slate-400">Klik tombol "Masuk ke Sekolah" untuk mengelola operasional sekolah secara langsung</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('superadmin.schools.index') }}" 
                   class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">settings</span>
                    <span>Kelola Data Sekolah</span>
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider text-[11px] border-b border-slate-100">
                        <th class="py-3.5 px-4 sm:px-6">Lembaga Sekolah</th>
                        <th class="py-3.5 px-4">NPSN & Yayasan</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Guru</th>
                        <th class="py-3.5 px-4 text-center">Jumlah Siswa</th>
                        <th class="py-3.5 px-4 text-center">Kehadiran Hari Ini</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Aksi Superadmin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($schoolsStats as $stat)
                        @php
                            $sch = $stat['school'];
                            $isCurrentActive = current_school_id() == $sch->id;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors {{ $isCurrentActive ? 'bg-emerald-50/30' : '' }}">
                            <!-- Nama & Logo Sekolah -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $sch->logo_url }}" alt="{{ $sch->name }}" 
                                         class="w-10 h-10 rounded-xl object-contain bg-white border border-slate-200 p-1 shadow-xs shrink-0">
                                    <div class="flex flex-col min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-extrabold text-slate-900 text-sm truncate max-w-[200px] sm:max-w-[260px]">{{ $sch->name }}</span>
                                            @if($isCurrentActive)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    Sedang Dipilih
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-slate-400 truncate max-w-[220px]">
                                            {{ $sch->address ?: 'Alamat belum diisi' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- NPSN & Yayasan -->
                            <td class="py-4 px-4">
                                <div class="flex flex-col">
                                    <span class="font-mono font-bold text-slate-800">{{ $sch->npsn ?: '-' }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $sch->foundation_name ?: '-' }}</span>
                                </div>
                            </td>

                            <!-- Guru -->
                            <td class="py-4 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 font-black">
                                    {{ $stat['teachers_count'] }} Guru
                                </span>
                            </td>

                            <!-- Siswa -->
                            <td class="py-4 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-xl bg-slate-100 text-slate-800 font-black">
                                    {{ $stat['students_count'] }} Siswa
                                </span>
                            </td>

                            <!-- Kehadiran Hari Ini -->
                            <td class="py-4 px-4 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="font-extrabold text-slate-900">
                                        {{ $stat['hadir_today'] }} / {{ $stat['teachers_count'] }}
                                    </span>
                                    <div class="w-20 bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $stat['hadir_percentage'] }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-600 mt-0.5">{{ $stat['hadir_percentage'] }}%</span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4 text-center">
                                @if($sch->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi Switcher & Akses -->
                            <td class="py-4 px-4 sm:px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Tombol Switch Masuk ke Sekolah -->
                                    <form action="{{ route('superadmin.switch_school') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="school_id" value="{{ $sch->id }}">
                                        @if($isCurrentActive)
                                            <a href="{{ route('admin.dashboard') }}" 
                                               class="px-3 py-1.5 rounded-xl bg-[#13793e] hover:bg-[#0f6332] text-white font-black text-xs inline-flex items-center gap-1.5 shadow-xs transition-all active:scale-95"
                                               title="Buka Dashboard Sekolah Ini">
                                                <span class="material-symbols-outlined text-[16px]">dashboard</span>
                                                <span>Kelola Sekolah</span>
                                            </a>
                                        @else
                                            <button type="submit" 
                                                    class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-[#13793e] font-bold text-xs inline-flex items-center gap-1.5 border border-slate-200 transition-all active:scale-95"
                                                    title="Beralih ke konteks sekolah ini">
                                                <span class="material-symbols-outlined text-[16px]">sync_alt</span>
                                                <span>Pilih Sekolah</span>
                                            </button>
                                        @endif
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">domain_disabled</span>
                                <p class="text-sm font-semibold">Belum ada sekolah terdaftar di sistem.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
