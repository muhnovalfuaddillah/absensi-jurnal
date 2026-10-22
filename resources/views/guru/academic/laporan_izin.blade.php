@extends('layouts.app')

@section('title', 'Laporan Izin Guru & Kelas Inval - E-Presensi')

@section('content')
<div class="flex flex-col gap-4">

    <!-- Top Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#13793e] via-[#0f6332] to-[#0a4d26] p-5 sm:p-6 text-white shadow-lg shadow-emerald-900/15">
        <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-white/5 pointer-events-none blur-2xl"></div>
        <div class="absolute -left-10 -bottom-10 w-44 h-44 rounded-full bg-emerald-400/10 pointer-events-none blur-2xl"></div>

        <div class="relative z-10 flex items-center justify-between gap-3">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-[11px] font-bold text-emerald-100 border border-white/10 mb-2">
                    <i class="fa-solid fa-people-arrows text-[10px]"></i>
                    <span>Informasi Inval & Guru Pengganti</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white leading-tight">
                    Laporan Guru Izin & Inval
                </h1>
                <p class="text-xs text-emerald-100/90 font-medium mt-1 max-w-md leading-relaxed">
                    Pantau guru yang berhalangan hadir dan ambil peran sebagai <b>Guru Pengganti (Inval)</b> agar kelas tidak kosong.
                </p>
            </div>

            <a href="{{ route('guru.dashboard') }}" 
               class="w-10 h-10 rounded-2xl bg-white/15 hover:bg-white/25 backdrop-blur-md text-white flex items-center justify-center transition-all shadow-xs shrink-0 border border-white/20 active:scale-95" 
               title="Kembali ke Beranda">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
        </div>

        <!-- Metric Counter Grid inside Hero -->
        <div class="relative z-10 mt-5 pt-4 border-t border-white/15 grid grid-cols-2 sm:grid-cols-4 gap-2 text-center">
            <div class="bg-white/15 backdrop-blur-md p-2.5 rounded-2xl border border-white/10">
                <p class="text-base sm:text-lg font-black text-white leading-tight">{{ $totalAbsentTeachers }} Guru</p>
                <p class="text-[10px] text-emerald-100 font-bold uppercase mt-0.5">Izin / Sakit</p>
            </div>
            <div class="bg-white/15 backdrop-blur-md p-2.5 rounded-2xl border border-white/10">
                <p class="text-base sm:text-lg font-black text-white leading-tight">{{ $totalClassesNeedSub }} Sesi</p>
                <p class="text-[10px] text-emerald-100 font-bold uppercase mt-0.5">Jadwal Kelas</p>
            </div>
            <div class="bg-white/15 backdrop-blur-md p-2.5 rounded-2xl border border-white/10 {{ $emptyClassesCount > 0 ? 'ring-2 ring-amber-300' : '' }}">
                <p class="text-base sm:text-lg font-black {{ $emptyClassesCount > 0 ? 'text-amber-200 animate-pulse' : 'text-white' }} leading-tight">
                    {{ $emptyClassesCount }} Kelas
                </p>
                <p class="text-[10px] text-emerald-100 font-bold uppercase mt-0.5">Butuh Pengganti</p>
            </div>
            <div class="bg-white/15 backdrop-blur-md p-2.5 rounded-2xl border border-white/10">
                <p class="text-base sm:text-lg font-black text-emerald-200 leading-tight">{{ $filledClassesCount }} Kelas</p>
                <p class="text-[10px] text-emerald-100 font-bold uppercase mt-0.5">Sudah Terisi</p>
            </div>
        </div>
    <!-- Quick Navigation Sub-Bar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
        <a href="{{ route('guru.jadwal') }}" class="px-3.5 py-2 rounded-2xl text-xs font-bold bg-white border border-slate-200/80 text-slate-600 hover:text-[#13793e] hover:border-emerald-300 hover:bg-emerald-50/50 shrink-0 transition-all shadow-2xs flex items-center gap-1.5">
            <i class="fa-regular fa-calendar text-[11px] text-slate-400"></i>
            <span>Jadwal Pelajaran</span>
        </a>
        <a href="{{ route('guru.jurnal.index') }}" class="px-3.5 py-2 rounded-2xl text-xs font-bold bg-white border border-slate-200/80 text-slate-600 hover:text-[#13793e] hover:border-emerald-300 hover:bg-emerald-50/50 shrink-0 transition-all shadow-2xs flex items-center gap-1.5">
            <i class="fa-solid fa-book-bookmark text-[11px] text-slate-400"></i>
            <span>Riwayat Jurnal Guru</span>
        </a>
        <a href="{{ route('guru.absen_siswa.index') }}" class="px-3.5 py-2 rounded-2xl text-xs font-bold bg-white border border-slate-200/80 text-slate-600 hover:text-[#13793e] hover:border-emerald-300 hover:bg-emerald-50/50 shrink-0 transition-all shadow-2xs flex items-center gap-1.5">
            <i class="fa-solid fa-users text-[11px] text-slate-400"></i>
            <span>Rekap Presensi Siswa</span>
        </a>
        <a href="{{ route('guru.laporan_izin') }}" class="px-3.5 py-2 rounded-2xl text-xs font-black bg-[#13793e] text-white shrink-0 shadow-xs flex items-center gap-1.5">
            <i class="fa-solid fa-user-clock text-[11px]"></i>
            <span>Laporan Izin Guru</span>
        </a>
    </div>

    <!-- Date Filter Card -->
    <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                <i class="fa-regular fa-calendar-days"></i>
            </div>
            <div>
                <p class="text-xs font-black text-slate-800">
                    Hari {{ $dayName }}, {{ $carbonDate->translatedFormat('d F Y') }}
                </p>
                <p class="text-[11px] text-slate-400">
                    {{ $date === now()->toDateString() ? 'Menampilkan data hari ini (Real-time)' : 'Menampilkan data arsip' }}
                </p>
            </div>
        </div>

        <form method="GET" action="{{ route('guru.laporan_izin') }}" class="flex items-center gap-2">
            <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                   class="px-3.5 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-[#13793e] transition-all">
            
            @if($date !== now()->toDateString())
                <a href="{{ route('guru.laporan_izin') }}" class="px-3 py-2 rounded-2xl bg-slate-100 text-slate-600 text-xs font-bold hover:bg-slate-200 transition-colors" title="Kembali ke Hari Ini">
                    Hari Ini
                </a>
            @endif
        </form>
    </div>

    <!-- Daftar Guru yang Berhalangan Hadir -->
    <div class="flex flex-col gap-3.5">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-extrabold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-chalkboard-user text-[#13793e]"></i>
                <span>Daftar Guru Izin & Jadwal Mengajar</span>
            </h3>
            <span class="text-xs font-bold text-[#13793e] bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                {{ $reports->count() }} Guru Berhalangan
            </span>
        </div>

        @if($reports->isEmpty())
            <!-- Empty State: Semua Guru Hadir -->
            <div class="bg-white p-10 sm:p-14 rounded-3xl text-center border border-slate-100 shadow-xs flex flex-col items-center justify-center gap-3">
                <div class="w-16 h-16 rounded-3xl bg-emerald-50 text-[#13793e] flex items-center justify-center text-3xl shadow-2xs">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h4 class="text-sm sm:text-base font-black text-slate-900">Alhamdulillah, Semua Guru Hadir</h4>
                    <p class="text-xs text-slate-500 max-w-sm mt-1 leading-relaxed">
                        Tidak ada guru yang mengajukan izin atau sakit pada hari {{ $dayName }}, {{ $carbonDate->translatedFormat('d F Y') }}. Seluruh kelas berjalan sesuai pengampu utama.
                    </p>
                </div>
                <a href="{{ route('guru.jadwal') }}" class="mt-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-2xl transition-all">
                    Lihat Jadwal Mengajar Saya
                </a>
            </div>
        @else
            <!-- Loop Guru yang Berhalangan -->
            @foreach($reports as $report)
                @php
                    $att = $report['attendance'];
                    $t = $report['teacher'];
                    $schedules = $report['schedules'];
                    $isSakit = ($att->status === 'sakit');
                @endphp

                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-xs flex flex-col gap-4">
                    
                    <!-- Header Kartu Guru -->
                    <div class="flex items-start justify-between gap-3 pb-3.5 border-b border-slate-100">
                        <div class="flex items-center gap-3 min-w-0">
                            <!-- Avatar Initials -->
                            <div class="w-12 h-12 rounded-2xl {{ $isSakit ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-blue-50 text-blue-700 border-blue-200' }} border flex items-center justify-center text-base font-black shrink-0 shadow-2xs">
                                {{ strtoupper(substr($t->name, 0, 2)) }}
                            </div>

                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="font-black text-sm sm:text-base text-slate-900 leading-tight truncate">
                                        {{ $t->name }}
                                    </h4>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider {{ $isSakit ? 'bg-rose-100 text-rose-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $att->status }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    NIP: {{ $t->nip ?? '-' }} • Mapel: {{ $t->subject ?? 'Guru' }}
                                </p>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        @if($att->notes)
                            <div class="hidden sm:block text-right">
                                <span class="text-[10px] text-slate-400 font-semibold block">Keterangan:</span>
                                <span class="text-xs text-slate-700 italic font-medium max-w-xs truncate block">"{{ $att->notes }}"</span>
                            </div>
                        @endif
                    </div>

                    <!-- Keterangan Izin Mobile View -->
                    @if($att->notes)
                        <div class="sm:hidden p-3 bg-slate-50 rounded-2xl border border-slate-100 text-xs text-slate-700 italic">
                            <span class="font-bold text-slate-500 not-italic block text-[10px] mb-0.5">Alasan Ketidakhadiran:</span>
                            "{{ $att->notes }}"
                        </div>
                    @endif

                    <!-- Sesi Jadwal Kelas yang Ditinggalkan -->
                    <div class="flex flex-col gap-2.5">
                        <div class="flex items-center justify-between text-xs px-1">
                            <span class="font-extrabold text-slate-800 flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-[#13793e]"></i>
                                <span>Jadwal Mengajar Hari Ini ({{ $dayName }}):</span>
                            </span>
                            <span class="text-[11px] font-bold text-slate-500">
                                {{ $schedules->count() }} Jam Pelajaran
                            </span>
                        </div>

                        @if($schedules->isEmpty())
                            <div class="p-4 bg-slate-50 rounded-2xl text-center text-xs text-slate-400 border border-dashed border-slate-200">
                                Guru ini tidak memiliki jadwal mengajar tatap muka di kelas pada hari {{ $dayName }}.
                            </div>
                        @else
                            <div class="grid grid-cols-1 gap-2.5">
                                @foreach($schedules as $sch)
                                    @php
                                        $journal = $filledJournals->get($sch->id);
                                        $isFilled = !is_null($journal);
                                    @endphp

                                    <div class="p-3.5 rounded-2xl border {{ $isFilled ? 'border-emerald-200 bg-emerald-50/30' : 'border-amber-300 bg-amber-50/40' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all">
                                        <!-- Info Jadwal -->
                                        <div class="flex items-start gap-3 min-w-0">
                                            <div class="px-2.5 py-1.5 rounded-xl {{ $isFilled ? 'bg-[#dcfce7] text-[#15803d]' : 'bg-amber-100 text-amber-900' }} font-mono font-black text-xs shrink-0 mt-0.5 shadow-2xs">
                                                {{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }}
                                            </div>

                                            <div class="min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-black bg-white border border-slate-200 text-slate-800 shadow-2xs">
                                                        {{ $sch->schoolClass->name ?? 'Kelas' }}
                                                    </span>
                                                    <h5 class="font-extrabold text-xs sm:text-sm text-slate-900 truncate">
                                                        {{ $sch->subject->name ?? 'Mata Pelajaran' }}
                                                    </h5>
                                                    @if($sch->room)
                                                        <span class="text-[11px] text-slate-400">• Ruang: {{ $sch->room }}</span>
                                                    @endif
                                                </div>

                                                <!-- Status Keterisian Pengganti -->
                                                <div class="mt-1 flex items-center gap-2">
                                                    @if($isFilled)
                                                        <span class="text-[11px] font-bold text-emerald-800 flex items-center gap-1">
                                                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                                            <span>Digantikan oleh: <b>{{ $journal->teacher->name ?? 'Guru Lain' }}</b></span>
                                                        </span>
                                                    @else
                                                        <span class="text-[11px] font-black text-amber-800 flex items-center gap-1 animate-pulse">
                                                            <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                                                            <span>Kelas Kosong • Membutuhkan Guru Pengganti (Inval)</span>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Aksi: Gantikan Mengajar (Inval) vs Lihat Jurnal -->
                                        <div class="shrink-0 self-end sm:self-auto">
                                            @if($isFilled)
                                                <a href="{{ route('guru.jurnal.show', $journal->id) }}" 
                                                   class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-emerald-50 text-[#13793e] border border-emerald-200 font-bold text-xs transition-all shadow-2xs flex items-center gap-1.5">
                                                    <span>Lihat Jurnal</span>
                                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                                </a>
                                            @else
                                                <a href="{{ route('guru.scan_kelas', ['schedule_id' => $sch->id]) }}" 
                                                   class="px-4 py-2 rounded-xl bg-[#13793e] hover:bg-[#0f6332] text-white font-black text-xs shadow-md shadow-emerald-800/15 active:scale-95 transition-all flex items-center gap-2">
                                                    <i class="fa-solid fa-qrcode text-xs"></i>
                                                    <span>Gantikan Mengajar (Inval)</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>
            @endforeach
        @endif
    </div>

</div>
@endsection
