@extends('layouts.admin')

@section('title', 'Jurnal Mengajar Guru - HadirGuru')

@section('content')
<div class="flex flex-col gap-space-md">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
        <div>
            <h1 class="font-headline-md text-headline-md font-bold text-on-surface">Monitoring Jurnal Pembelajaran Guru</h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Pantau materi pembelajaran, jam mengajar, dan absensi siswa di setiap kelas</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="flex items-center gap-2 px-3.5 py-2.5 bg-surface-container text-on-surface rounded-lg font-label-md text-label-md font-semibold hover:bg-surface-container-high transition-all">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
        <form method="GET" action="{{ route('admin.journals.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">Tanggal:</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="w-full text-xs rounded-lg bg-surface-container-low border border-surface-container-high py-2 px-3 text-on-surface focus:outline-none focus:border-primary font-mono">
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">Guru Pengampu:</label>
                <select name="teacher_id" class="w-full text-xs rounded-lg bg-surface-container-low border border-surface-container-high py-2 px-3 text-on-surface focus:outline-none focus:border-primary">
                    <option value="">Semua Guru</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ $teacherId == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">Kelas:</label>
                <select name="class_id" class="w-full text-xs rounded-lg bg-surface-container-low border border-surface-container-high py-2 px-3 text-on-surface focus:outline-none focus:border-primary">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-primary text-on-primary rounded-lg text-xs font-semibold hover:bg-primary/90">
                    Filter
                </button>
                <a href="{{ route('admin.journals.index') }}" class="py-2 px-3 bg-surface-container text-on-surface-variant rounded-lg text-xs font-semibold hover:bg-surface-container-high">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Journals -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
        <div class="p-space-md border-b border-surface-container flex items-center justify-between">
            <span class="font-bold text-xs text-on-surface">Total: {{ $journals->total() }} Sesi Pembelajaran Tercatat</span>
        </div>

        @if($journals->isEmpty())
            <div class="py-12 text-center text-xs text-slate-400 flex flex-col items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[36px] text-slate-300">menu_book</span>
                <span>Belum ada data jurnal mengajar yang sesuai filter.</span>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-surface-container bg-surface-container-low text-on-surface-variant uppercase text-[10px] tracking-wider">
                            <th class="py-3 px-space-md">Tanggal & Jam</th>
                            <th class="py-3 px-space-md">Guru Pengampu</th>
                            <th class="py-3 px-space-md">Kelas & Mapel</th>
                            <th class="py-3 px-space-md">Materi Pokok</th>
                            <th class="py-3 px-space-md text-center">Presensi Siswa</th>
                            <th class="py-3 px-space-md">Status</th>
                            <th class="py-3 px-space-md text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-low">
                        @foreach($journals as $j)
                            @php
                                $sum = $j->attendance_summary;
                            @endphp
                            <tr class="hover:bg-surface-container-low/50 transition-colors">
                                <td class="py-3 px-space-md">
                                    <p class="font-bold text-on-surface">{{ $j->date->format('d/m/Y') }}</p>
                                    <p class="font-mono text-slate-500 text-[11px]">{{ substr($j->start_time, 0, 5) }} - {{ substr($j->end_time, 0, 5) }} WIB</p>
                                </td>
                                <td class="py-3 px-space-md">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-[10px]">
                                            {{ $j->teacher->initials ?? 'GU' }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-on-surface leading-tight">{{ $j->teacher->name ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-400">NIP: {{ $j->teacher->nip ?? '-' }}</p>
                                            @if($j->is_substitute)
                                                <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-100 text-purple-900 border border-purple-200 mt-0.5">
                                                    Inval: Menggantikan {{ $j->substitutedTeacher->name ?? 'Guru Lain' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-space-md">
                                    <span class="px-2 py-0.5 rounded font-bold text-[11px] bg-primary/10 text-primary mr-1">
                                        {{ $j->schoolClass->name ?? '-' }}
                                    </span>
                                    <span class="font-semibold text-slate-700">{{ $j->subject->name ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-space-md max-w-xs">
                                    <p class="font-bold text-on-surface truncate">{{ $j->materi }}</p>
                                    <p class="text-[11px] text-slate-500 line-clamp-1">{{ $j->kegiatan }}</p>
                                </td>
                                <td class="py-3 px-space-md text-center">
                                    <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold border border-emerald-200 text-[11px]">
                                        H: {{ $sum['hadir'] }}
                                    </span>
                                    @if($sum['sakit'] > 0 || $sum['izin'] > 0 || $sum['alpa'] > 0)
                                        <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 font-bold border border-amber-200 text-[11px] ml-1">
                                            Tdk: {{ $sum['sakit'] + $sum['izin'] + $sum['alpa'] }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-space-md">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $j->status == 'terlaksana' ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($j->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-space-md text-right">
                                    <a href="{{ route('admin.journals.show', $j->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-surface-container-high hover:bg-primary hover:text-white transition-colors text-xs font-semibold">
                                        <span>Detail</span>
                                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-space-md border-t border-surface-container">
                {{ $journals->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
