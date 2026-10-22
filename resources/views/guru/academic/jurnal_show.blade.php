@extends('layouts.app')

@section('title', 'Detail Jurnal Mengajar - E-Presensi')

@section('content')
<div class="flex flex-col gap-4">

    <!-- Header & Action Bar -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('guru.jurnal.index') }}" 
               class="w-9 h-9 rounded-full bg-white border border-slate-200/80 text-slate-700 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-2xs">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-lg font-extrabold text-slate-900 tracking-tight leading-tight">Detail Jurnal Mengajar</h1>
                <p class="text-xs text-slate-500 font-medium">{{ $journal->date->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <!-- Print Button -->
            <button type="button" onclick="window.print()" 
                    class="w-9 h-9 rounded-2xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 flex items-center justify-center transition-colors shadow-2xs" 
                    title="Cetak Jurnal">
                <i class="fa-solid fa-print text-sm"></i>
            </button>

            <!-- Hapus Jurnal Form -->
            <form action="{{ route('guru.jurnal.destroy', $journal->id) }}" method="POST"
                  data-confirm="Apakah Anda yakin ingin menghapus jurnal ini beserta data absensi siswa yang tercatat?"
                  data-confirm-title="Hapus Jurnal" data-confirm-btn="Ya, Hapus">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-9 h-9 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition-colors shadow-2xs" title="Hapus Jurnal">
                    <i class="fa-regular fa-trash-can text-sm"></i>
                </button>
            </form>
        </div>
    </div>

    @if(session('wa_absent_alerts'))
        <div class="p-4 bg-amber-50 border border-amber-300 rounded-3xl flex flex-col gap-1.5 shadow-xs">
            <div class="flex items-center gap-2 text-amber-900 font-bold text-xs">
                <i class="fa-solid fa-bell text-amber-600 text-sm"></i>
                <span>Pemberitahuan Otomatis ke Orang Tua Siswa:</span>
            </div>
            <p class="text-[11px] text-amber-800 leading-relaxed">
                Terdapat <b>{{ count(session('wa_absent_alerts')) }} siswa</b> yang tercatat tidak hadir. Sistem telah memproses pengiriman notifikasi WhatsApp ke nomor orang tua/wali siswa yang bersangkutan.
            </p>
        </div>
    @endif

    <!-- Hero Card: Ringkasan Sesi Mengajar -->
    <div class="bg-gradient-to-br from-[#13793e] via-[#0f6332] to-[#0a4d26] text-white rounded-3xl p-5 sm:p-6 shadow-lg shadow-emerald-900/15 flex flex-col gap-4 relative overflow-hidden">
        <!-- Background Ornament -->
        <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/5 pointer-events-none blur-2xl"></div>

        <div class="relative z-10 flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1 rounded-full text-xs font-black bg-white/20 backdrop-blur-md border border-white/20 text-white shadow-2xs">
                    {{ $journal->schoolClass->name ?? 'Kelas' }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-white text-[#13793e] shadow-2xs">
                    {{ $journal->subject->name ?? 'Mata Pelajaran' }}
                </span>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-400/20 border border-emerald-300/40 text-emerald-100 uppercase tracking-wide">
                {{ $journal->status }}
            </span>
        </div>

        <div class="relative z-10">
            <h2 class="text-lg sm:text-xl font-black leading-snug text-white">{{ $journal->materi }}</h2>
            <div class="flex items-center gap-3 text-xs text-emerald-100/90 mt-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-regular fa-clock text-[11px]"></i>
                    <span>{{ substr($journal->start_time, 0, 5) }} - {{ substr($journal->end_time, 0, 5) }} WIB</span>
                </span>
                <span>•</span>
                <span class="inline-flex items-center gap-1.5 bg-white/20 text-white font-bold px-2.5 py-0.5 rounded-full text-[11px] border border-white/20 shadow-2xs">
                    <i class="fa-solid fa-hourglass-half text-[10px]"></i>
                    <span>{{ $journal->duration_label }} ({{ $journal->estimated_hours }} JP)</span>
                </span>
                <span>•</span>
                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-chalkboard-user text-[11px]"></i>
                    <span>Pengampu: <b>{{ $journal->teacher->name }}</b></span>
                </span>
                <span>•</span>
                <span class="inline-flex items-center gap-1.5">
                    <i class="fa-solid fa-users text-[11px]"></i>
                    <span>{{ $journal->studentAttendances->count() }} Siswa</span>
                </span>
            </div>
        </div>

        @if($journal->is_substitute)
            <div class="relative z-10 p-3 rounded-2xl bg-white/15 backdrop-blur-md flex flex-col sm:flex-row sm:items-center justify-between gap-1 text-xs border border-white/15">
                <span class="flex items-center gap-1.5 font-bold text-white">
                    <i class="fa-solid fa-arrow-right-arrow-left text-xs"></i>
                    <span>Sesi Guru Pengganti (Inval)</span>
                </span>
                <span class="text-[11px] text-emerald-100">
                    Menggantikan: <b>{{ $journal->substitutedTeacher->name ?? 'Guru Terkait' }}</b>
                </span>
            </div>
        @endif
    </div>

    <!-- 1. Uraian Kegiatan Pembelajaran -->
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-xs flex flex-col gap-3.5">
        <h3 class="text-xs sm:text-sm font-extrabold text-slate-900 flex items-center gap-2 border-b border-slate-100 pb-3">
            <div class="w-7 h-7 rounded-lg bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                <i class="fa-solid fa-align-left"></i>
            </div>
            <span>Uraian Aktivitas & Capaian Pembelajaran</span>
        </h3>
        
        <div class="p-3.5 bg-slate-50/70 rounded-2xl border border-slate-100 text-xs text-slate-700 whitespace-pre-line leading-relaxed font-medium">
            {{ $journal->kegiatan }}
        </div>

        @if($journal->catatan)
            <div class="p-3.5 bg-amber-50 rounded-2xl border border-amber-200">
                <p class="text-xs font-bold text-amber-900 flex items-center gap-1.5 mb-1">
                    <i class="fa-solid fa-lightbulb text-amber-600 text-xs"></i>
                    <span>Catatan Khusus / Kendala Pembelajaran:</span>
                </p>
                <p class="text-xs text-amber-800 leading-relaxed font-medium">{{ $journal->catatan }}</p>
            </div>
        @endif

        @if($journal->attachment_url)
            <div class="mt-1">
                <p class="text-xs font-bold text-slate-800 mb-2 flex items-center gap-1.5">
                    <i class="fa-solid fa-image text-[#13793e]"></i>
                    <span>Foto Dokumentasi Kelas:</span>
                </p>
                <img src="{{ $journal->attachment_url }}" alt="Dokumentasi Kelas" class="w-full max-h-80 object-cover rounded-2xl border border-slate-200 shadow-xs">
            </div>
        @endif
    </div>

    <!-- 2. Rekapitulasi Presensi Siswa -->
    @php
        $sum = $journal->attendance_summary;
    @endphp
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-xs flex flex-col gap-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <h3 class="text-xs sm:text-sm font-extrabold text-slate-900">Presensi Kehadiran Siswa</h3>
            </div>
            <span class="text-xs text-[#13793e] font-black bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                Total: {{ $sum['total'] }} Siswa
            </span>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-4 gap-2 text-center">
            <div class="p-3 rounded-2xl bg-[#eaf8ef] border border-emerald-200/80">
                <p class="text-base sm:text-xl font-black text-[#13793e] leading-tight">{{ $sum['hadir'] }}</p>
                <p class="text-[10px] text-emerald-800 font-bold uppercase mt-1">Hadir</p>
            </div>
            <div class="p-3 rounded-2xl bg-amber-50 border border-amber-200">
                <p class="text-base sm:text-xl font-black text-amber-700 leading-tight">{{ $sum['sakit'] }}</p>
                <p class="text-[10px] text-amber-800 font-bold uppercase mt-1">Sakit</p>
            </div>
            <div class="p-3 rounded-2xl bg-blue-50 border border-blue-200">
                <p class="text-base sm:text-xl font-black text-blue-700 leading-tight">{{ $sum['izin'] }}</p>
                <p class="text-[10px] text-blue-800 font-bold uppercase mt-1">Izin</p>
            </div>
            <div class="p-3 rounded-2xl bg-rose-50 border border-rose-200">
                <p class="text-base sm:text-xl font-black text-rose-700 leading-tight">{{ $sum['alpa'] }}</p>
                <p class="text-[10px] text-rose-800 font-bold uppercase mt-1">Alpa</p>
            </div>
        </div>

        <!-- Daftar Siswa -->
        <div class="divide-y divide-slate-100 mt-1">
            @foreach($journal->studentAttendances as $idx => $att)
                @php
                    $student = $att->student;
                @endphp
                <div class="py-3 flex items-center justify-between gap-2.5">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-black shrink-0 font-mono">
                            {{ $idx + 1 }}
                        </span>
                        <div class="min-w-0">
                            <p class="font-extrabold text-xs text-slate-900 truncate">{{ $student->name ?? 'Siswa' }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5 truncate">
                                NIS: {{ $student->nis ?? '-' }}
                                @if($student && $student->parent_name)
                                    • Wali: <span class="font-semibold text-slate-600">{{ $student->parent_name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if($att->status === 'hadir')
                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-black bg-[#dcfce7] text-[#15803d]">
                                Hadir
                            </span>
                        @elseif($att->status === 'sakit')
                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-black bg-amber-100 text-amber-800">
                                Sakit
                            </span>
                        @elseif($att->status === 'izin')
                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-black bg-blue-100 text-blue-800">
                                Izin
                            </span>
                        @else
                            <span class="px-2.5 py-1 rounded-xl text-[11px] font-black bg-rose-100 text-rose-800">
                                Alpa
                            </span>
                        @endif

                        @if($att->status !== 'hadir' && $student && $student->formatted_parent_phone)
                            @php
                                $waMsg = \App\Services\WhatsAppNotificationService::formatAbsenceMessage($student, $journal, $att->status, $att->notes);
                                $waUrl = "https://wa.me/{$student->formatted_parent_phone}?text=" . urlencode($waMsg);
                            @endphp
                            <a href="{{ $waUrl }}" target="_blank"
                               title="Kirim Pesan WhatsApp ke Orang Tua ({{ $student->parent_name ?: 'Wali' }})"
                               class="px-2.5 py-1 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] flex items-center gap-1 shadow-2xs active:scale-95 transition-all">
                                <i class="fa-brands fa-whatsapp text-xs"></i>
                                <span>WA Ortu</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
