@extends('layouts.admin')

@section('title', 'Detail Jurnal Pembelajaran - HadirGuru')

@section('content')
<div class="flex flex-col gap-space-md">

    <!-- Header Action Bar -->
    <div class="flex items-center justify-between bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
        <div class="flex items-center gap-space-sm">
            <a href="{{ route('admin.journals.index') }}" class="w-9 h-9 rounded-lg bg-surface-container-high flex items-center justify-center text-on-surface hover:bg-surface-container-highest transition-colors">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <h1 class="font-headline-md text-headline-md font-bold text-on-surface">Detail Jurnal Mengajar Guru</h1>
                <p class="font-body-sm text-body-sm text-on-surface-variant">{{ $journal->teacher->name ?? '-' }} • {{ $journal->date->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
        <button type="button" onclick="window.print()" class="flex items-center gap-2 px-3.5 py-2 bg-surface-container text-on-surface rounded-lg font-label-md text-label-md font-semibold hover:bg-surface-container-high transition-all">
            <span class="material-symbols-outlined text-[18px]">print</span>
            <span>Cetak Jurnal</span>
        </button>
    </div>

    <!-- Metadata Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
        <!-- Info Pokok -->
        <div class="md:col-span-2 bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container flex flex-col gap-3">
            <div class="flex items-center justify-between pb-2 border-b border-surface-container">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-primary text-on-primary">
                        {{ $journal->schoolClass->name ?? 'Kelas' }}
                    </span>
                    <span class="text-xs font-bold text-primary">
                        {{ $journal->subject->name ?? 'Mata Pelajaran' }}
                    </span>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold capitalize {{ $journal->status == 'terlaksana' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $journal->status }}
                </span>
            </div>

            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Materi Pokok / Pembahasan:</span>
                <h2 class="font-bold text-base text-on-surface mt-0.5">{{ $journal->materi }}</h2>
            </div>

            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Uraian Aktivitas Pembelajaran:</span>
                <p class="text-xs text-on-surface whitespace-pre-line mt-1 bg-surface-container-low p-3 rounded-lg leading-relaxed">
                    {{ $journal->kegiatan }}
                </p>
            </div>

            @if($journal->catatan)
                <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-xs">
                    <p class="font-bold text-amber-800 mb-0.5">Catatan Guru / Kendala Kelas:</p>
                    <p class="text-amber-900">{{ $journal->catatan }}</p>
                </div>
            @endif
        </div>

        <!-- Info Guru & Foto -->
        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container flex flex-col gap-3">
            <h3 class="font-bold text-xs text-on-surface border-b border-surface-container pb-2">Informasi Guru & Jadwal</h3>

            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold text-base">
                    {{ $journal->teacher->initials ?? 'GU' }}
                </div>
                <div>
                    <h4 class="font-bold text-xs text-on-surface">{{ $journal->teacher->name ?? '-' }}</h4>
                    <p class="text-[11px] text-slate-500">NIP: {{ $journal->teacher->nip ?? '-' }}</p>
                    <p class="text-[11px] text-slate-500">{{ $journal->teacher->employment_status ?? 'Guru' }}</p>
                    @if($journal->is_substitute)
                        <div class="mt-1 p-2 bg-purple-50 rounded-lg border border-purple-200 text-[11px] text-purple-900 font-bold">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">swap_horiz</span>
                                <span>Guru Pengganti (Inval)</span>
                            </span>
                            <span class="text-[10px] text-purple-700 font-normal">Menggantikan: {{ $journal->substitutedTeacher->name ?? 'Guru Berhalangan' }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="text-xs space-y-1.5 pt-2 border-t border-surface-container text-slate-600">
                <div class="flex justify-between">
                    <span>Tanggal:</span>
                    <strong class="text-on-surface">{{ $journal->date->format('d/m/Y') }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Waktu Pembelajaran:</span>
                    <strong class="font-mono text-on-surface">{{ substr($journal->start_time, 0, 5) }} - {{ substr($journal->end_time, 0, 5) }} WIB</strong>
                </div>
                <div class="flex justify-between items-center">
                    <span>Durasi Jam Terhitung:</span>
                    <span class="px-2 py-0.5 rounded text-xs font-black bg-amber-50 text-amber-900 border border-amber-200/80">
                        {{ $journal->duration_label }} ({{ $journal->estimated_hours }} JP)
                    </span>
                </div>
                <div class="flex justify-between">
                    <span>Total Siswa:</span>
                    <strong class="text-on-surface">{{ $journal->studentAttendances->count() }} Orang</strong>
                </div>
            </div>

            @if($journal->attachment_url)
                <div class="pt-2 border-t border-surface-container">
                    <p class="text-xs font-bold text-on-surface mb-1.5">Foto Dokumentasi:</p>
                    <img src="{{ $journal->attachment_url }}" alt="Dokumentasi Kelas" class="w-full max-h-48 object-cover rounded-lg border border-surface-container">
                </div>
            @endif
        </div>
    </div>

    <!-- Student Attendance Records -->
    @php
        $sum = $journal->attendance_summary;
    @endphp
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
        <div class="p-space-md border-b border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h3 class="font-bold text-xs text-on-surface">Daftar Kehadiran Siswa</h3>
                <p class="text-[11px] text-on-surface-variant">Catatan presensi pada sesi tatap muka ini</p>
            </div>

            <!-- Stats Badge -->
            <div class="flex items-center gap-1.5 text-xs font-bold">
                <span class="px-2.5 py-1 rounded bg-emerald-100 text-emerald-800">Hadir: {{ $sum['hadir'] }}</span>
                <span class="px-2.5 py-1 rounded bg-amber-100 text-amber-800">Sakit: {{ $sum['sakit'] }}</span>
                <span class="px-2.5 py-1 rounded bg-blue-100 text-blue-800">Izin: {{ $sum['izin'] }}</span>
                <span class="px-2.5 py-1 rounded bg-rose-100 text-rose-800">Alpa: {{ $sum['alpa'] }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-surface-container bg-surface-container-low text-on-surface-variant uppercase text-[10px] tracking-wider">
                        <th class="py-2.5 px-space-md">No</th>
                        <th class="py-2.5 px-space-md">Nama Siswa</th>
                        <th class="py-2.5 px-space-md">NIS</th>
                        <th class="py-2.5 px-space-md">Jenis Kelamin</th>
                        <th class="py-2.5 px-space-md text-right">Status Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container-low">
                    @foreach($journal->studentAttendances as $idx => $att)
                        @php
                            $b = $att->status_badge;
                        @endphp
                        <tr class="hover:bg-surface-container-low/50">
                            <td class="py-2.5 px-space-md text-slate-400 font-mono">{{ $idx + 1 }}</td>
                            <td class="py-2.5 px-space-md font-bold text-on-surface">{{ $att->student->name ?? '-' }}</td>
                            <td class="py-2.5 px-space-md font-mono text-slate-600">{{ $att->student->nis ?? '-' }}</td>
                            <td class="py-2.5 px-space-md">
                                <span class="{{ $att->student->gender == 'L' ? 'text-blue-600' : 'text-pink-600' }} font-semibold">
                                    {{ $att->student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td class="py-2.5 px-space-md text-right">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $b['color'] }}">
                                    {{ $b['label'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
