@extends('layouts.app')

@section('title', 'Jadwal Mengajar - E-Presensi')

@section('content')
<div class="flex flex-col gap-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-regular fa-calendar-check text-[#13793e]"></i>
                <span>Jadwal Mengajar</span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Jadwal tatap muka dan sesi pembelajaran mingguan Anda</p>
        </div>
        <a href="{{ route('guru.scan_kelas') }}" 
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#13793e] hover:bg-[#0f6332] text-white rounded-2xl font-bold text-xs sm:text-sm shadow-md shadow-emerald-800/15 active:scale-95 transition-all self-start sm:self-auto">
            <i class="fa-solid fa-qrcode text-sm"></i>
            <span>Scan QR & Tulis Jurnal</span>
        </a>
    </div>

    <!-- Quick Navigation Sub-Bar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
        <a href="{{ route('guru.jadwal') }}" class="px-3.5 py-2 rounded-2xl text-xs font-black bg-[#13793e] text-white shrink-0 shadow-xs flex items-center gap-1.5">
            <i class="fa-regular fa-calendar text-[11px]"></i>
            <span>Jadwal Mengajar</span>
        </a>
        <a href="{{ route('guru.jurnal.index') }}" class="px-3.5 py-2 rounded-2xl text-xs font-bold bg-white border border-slate-200/80 text-slate-600 hover:text-[#13793e] hover:border-emerald-300 hover:bg-emerald-50/50 shrink-0 transition-all shadow-2xs flex items-center gap-1.5">
            <i class="fa-solid fa-book-bookmark text-[11px] text-slate-400"></i>
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

    <!-- 1. Jadwal Hari Ini (Card Unggulan) -->
    <div class="bg-gradient-to-br from-[#13793e] via-[#0f6332] to-[#0a4d26] text-white rounded-3xl p-5 sm:p-6 shadow-lg shadow-emerald-900/15 flex flex-col gap-4 relative overflow-hidden">
        <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/5 pointer-events-none blur-2xl"></div>

        <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white text-[#13793e] flex items-center justify-center font-black text-sm shadow-2xs">
                    {{ substr($todayDay, 0, 3) }}
                </div>
                <div>
                    <h2 class="text-base sm:text-lg font-black leading-tight text-white">Jadwal Hari Ini ({{ $todayDay }})</h2>
                    <p class="text-xs text-emerald-100/90 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black bg-white/20 backdrop-blur-md border border-white/20 text-white shadow-2xs">
                {{ $todaySchedules->count() }} Sesi Mengajar
            </span>
        </div>

        @if($todaySchedules->isEmpty())
            <div class="relative z-10 py-8 px-4 text-center bg-white/10 backdrop-blur-md rounded-2xl border border-white/15 flex flex-col items-center justify-center gap-2">
                <i class="fa-regular fa-calendar-check text-3xl text-emerald-200"></i>
                <p class="text-xs font-bold text-white">Tidak ada jadwal mengajar pada hari {{ $todayDay }}.</p>
                <p class="text-[11px] text-emerald-100/80">Gunakan waktu luang untuk evaluasi pembelajaran atau persiapan bahan ajar.</p>
            </div>
        @else
            <div class="relative z-10 flex flex-col gap-3">
                @foreach($todaySchedules as $sch)
                    @php
                        $isJournalFilled = isset($todayJournals[$sch->id]);
                    @endphp
                    <div class="p-4 bg-white text-slate-800 rounded-2xl border border-white/20 shadow-md flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3 min-w-0">
                                <div class="px-2.5 py-1.5 rounded-xl bg-[#eaf8ef] text-[#13793e] font-mono font-black text-xs shrink-0 mt-0.5 border border-emerald-200/60 text-center">
                                    <div>{{ $sch->formatted_time }}</div>
                                    <div class="text-[10px] font-bold text-amber-800 bg-amber-100/70 rounded px-1.5 py-0.5 mt-1 border border-amber-200/50">
                                        {{ $sch->duration_label }}
                                    </div>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-black text-slate-900 leading-tight truncate">
                                        {{ $sch->subject->name ?? 'Mata Pelajaran' }}
                                    </h3>
                                    <div class="flex items-center gap-2 mt-1 text-xs text-slate-500 flex-wrap">
                                        <span class="inline-flex items-center gap-1 font-bold text-[#13793e]">
                                            <i class="fa-solid fa-graduation-cap text-[11px]"></i>
                                            {{ $sch->schoolClass->name ?? 'Kelas' }}
                                        </span>
                                        @if($sch->room)
                                            <span class="text-slate-300">•</span>
                                            <span class="inline-flex items-center gap-1 text-slate-500 font-medium">
                                                <i class="fa-solid fa-door-open text-[11px]"></i>
                                                {{ $sch->room }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="shrink-0">
                                @if($isJournalFilled)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-[#dcfce7] text-[#15803d]">
                                        <i class="fa-solid fa-check text-[9px]"></i>
                                        <span>Terisi</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800">
                                        <i class="fa-regular fa-clock text-[9px]"></i>
                                        <span>Belum</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400 font-medium">
                                {{ $sch->schoolClass->students->count() }} Siswa terdaftar
                            </span>
                            @if($isJournalFilled)
                                <a href="{{ route('guru.jurnal.show', $todayJournals[$sch->id]) }}" 
                                   class="inline-flex items-center gap-1.5 text-xs font-black text-[#13793e] hover:underline">
                                    <span>Lihat Jurnal</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            @else
                                <a href="{{ route('guru.scan_kelas', ['schedule_id' => $sch->id]) }}" 
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-[#13793e] hover:bg-[#0f6332] text-white rounded-xl text-xs font-black shadow-2xs active:scale-95 transition-all">
                                    <i class="fa-solid fa-qrcode text-[11px]"></i>
                                    <span>Scan & Mulai</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- 2. Jadwal Lengkap Mingguan -->
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-xs flex flex-col gap-3.5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Jadwal Mingguan Lengkap</h2>
                <p class="text-[11px] text-slate-400">Daftar seluruh jadwal mengajar Anda dari Senin sampai Sabtu</p>
            </div>
            <span class="text-xs text-[#13793e] font-bold bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                T.A. 2025/2026
            </span>
        </div>

        @php
            $dayList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        @endphp

        <div class="flex flex-col gap-3">
            @foreach($dayList as $d)
                @php
                    $daySchedules = $allSchedules->get($d, collect());
                    $isCurrentDay = ($d === $todayDay);
                @endphp
                <div class="rounded-2xl border {{ $isCurrentDay ? 'border-emerald-300 bg-emerald-50/30' : 'border-slate-200/80 bg-white' }} overflow-hidden">
                    <div class="px-3.5 py-2.5 flex items-center justify-between {{ $isCurrentDay ? 'bg-emerald-100/60' : 'bg-slate-50' }} border-b {{ $isCurrentDay ? 'border-emerald-200' : 'border-slate-200/60' }}">
                        <div class="flex items-center gap-2">
                            <span class="font-black text-xs {{ $isCurrentDay ? 'text-[#13793e]' : 'text-slate-800' }}">{{ $d }}</span>
                            @if($isCurrentDay)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-[#13793e] text-white">Hari Ini</span>
                            @endif
                        </div>
                        <span class="text-[11px] text-slate-500 font-semibold">
                            {{ $daySchedules->count() }} Sesi ({{ $daySchedules->sum('estimated_hours') }} JP)
                        </span>
                    </div>

                    @if($daySchedules->isEmpty())
                        <div class="py-3 px-3.5 text-xs text-slate-400 italic">
                            Tidak ada jadwal mengajar pada hari ini.
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($daySchedules as $item)
                                <div class="px-3.5 py-2.5 flex items-center justify-between gap-2 hover:bg-slate-50/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="shrink-0 text-center min-w-[85px]">
                                            <span class="block font-mono text-[11px] font-bold text-slate-700 bg-slate-100 px-2 py-0.5 rounded-lg">
                                                {{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }}
                                            </span>
                                            <span class="inline-block mt-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-200/70">
                                                {{ $item->duration_label }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-extrabold text-xs text-slate-900 leading-tight">{{ $item->subject->name ?? 'Mapel' }}</p>
                                            <p class="text-[11px] text-slate-400 font-medium">{{ $item->schoolClass->name ?? 'Kelas' }} {{ $item->room ? '• ' . $item->room : '' }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('guru.scan_kelas', ['schedule_id' => $item->id]) }}" 
                                       title="Scan QR & Isi Jurnal" 
                                       class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-[#13793e] hover:text-white text-slate-600 flex items-center justify-center transition-colors shadow-2xs">
                                        <i class="fa-solid fa-qrcode text-xs"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
