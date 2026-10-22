@extends('layouts.app')

@section('title', 'Rekap Presensi Siswa - E-Presensi')

@section('content')
<div class="flex flex-col gap-4">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-users-line text-[#13793e]"></i>
                <span>Rekap Presensi Siswa</span>
            </h1>
            <p class="text-xs text-slate-500 font-medium mt-0.5">Laporan akumulasi kehadiran siswa per kelas dan persentase</p>
        </div>
        <a href="{{ route('guru.scan_kelas') }}" 
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#13793e] hover:bg-[#0f6332] text-white rounded-2xl font-bold text-xs sm:text-sm shadow-md shadow-emerald-800/15 active:scale-95 transition-all self-start sm:self-auto">
            <i class="fa-solid fa-qrcode text-sm"></i>
            <span>Scan QR & Absen</span>
        </a>
    </div>

    <!-- Quick Navigation Sub-Bar -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar">
        <a href="{{ route('guru.jadwal') }}" class="px-3.5 py-2 rounded-2xl text-xs font-bold bg-white border border-slate-200/80 text-slate-600 hover:text-[#13793e] hover:border-emerald-300 hover:bg-emerald-50/50 shrink-0 transition-all shadow-2xs flex items-center gap-1.5">
            <i class="fa-regular fa-calendar text-[11px] text-slate-400"></i>
            <span>Jadwal Mengajar</span>
        </a>
        <a href="{{ route('guru.jurnal.index') }}" class="px-3.5 py-2 rounded-2xl text-xs font-bold bg-white border border-slate-200/80 text-slate-600 hover:text-[#13793e] hover:border-emerald-300 hover:bg-emerald-50/50 shrink-0 transition-all shadow-2xs flex items-center gap-1.5">
            <i class="fa-solid fa-book-bookmark text-[11px] text-slate-400"></i>
            <span>Riwayat Jurnal Guru</span>
        </a>
        <a href="{{ route('guru.absen_siswa.index') }}" class="px-3.5 py-2 rounded-2xl text-xs font-black bg-[#13793e] text-white shrink-0 shadow-xs flex items-center gap-1.5">
            <i class="fa-solid fa-users text-[11px]"></i>
            <span>Rekap Presensi Siswa</span>
        </a>
        <a href="{{ route('guru.laporan_izin') }}" class="px-3.5 py-2 rounded-2xl text-xs font-bold bg-white border border-slate-200/80 text-slate-600 hover:text-[#13793e] hover:border-emerald-300 hover:bg-emerald-50/50 shrink-0 transition-all shadow-2xs flex items-center gap-1.5">
            <i class="fa-solid fa-user-clock text-[11px] text-slate-400"></i>
            <span>Laporan Izin Guru</span>
        </a>
    </div>

    <!-- Filter Kelas & Bulan Card -->
    <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs">
        <form method="GET" action="{{ route('guru.absen_siswa.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Pilih Kelas:</label>
                <select name="class_id" class="w-full py-2.5 px-3 text-xs rounded-2xl bg-slate-50 border border-slate-200/80 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-bold transition-all">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} (Wali: {{ $c->homeroomTeacher->name ?? 'Belum ditentukan' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Pilih Bulan:</label>
                <input type="month" name="month" value="{{ $month }}"
                       class="w-full sm:w-auto py-2.5 px-3 text-xs rounded-2xl bg-slate-50 border border-slate-200/80 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-mono font-bold transition-all">
            </div>

            <div class="self-end w-full sm:w-auto">
                <button type="submit" class="w-full px-5 py-2.5 bg-[#13793e] hover:bg-[#0f6332] text-white rounded-2xl text-xs font-black shadow-xs flex items-center justify-center gap-1.5 transition-all active:scale-95">
                    <i class="fa-solid fa-filter text-[11px]"></i>
                    <span>Tampilkan</span>
                </button>
            </div>
        </form>
    </div>

    @if($selectedClass)
        <!-- Tabel Rekapitulasi Siswa -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-xs flex flex-col gap-3.5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-graduation-cap text-[#13793e]"></i>
                        <span>Kelas {{ $selectedClass->name }}</span>
                    </h2>
                    <p class="text-[11px] text-slate-400">Periode: {{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-black bg-emerald-50 text-[#13793e] border border-emerald-200">
                    {{ $students->count() }} Siswa
                </span>
            </div>

            @if($students->isEmpty())
                <div class="py-12 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                    Belum ada data siswa di kelas ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-slate-400 uppercase text-[10px] tracking-wider font-extrabold">
                                <th class="py-2.5 px-2">No</th>
                                <th class="py-2.5 px-2">Nama Siswa</th>
                                <th class="py-2.5 px-2 text-center text-emerald-700">H</th>
                                <th class="py-2.5 px-2 text-center text-amber-700">S</th>
                                <th class="py-2.5 px-2 text-center text-blue-700">I</th>
                                <th class="py-2.5 px-2 text-center text-rose-700">A</th>
                                <th class="py-2.5 px-2 text-right">Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @foreach($students as $idx => $s)
                                @php
                                    $st = $studentStats[$s->id] ?? ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0, 'total' => 0, 'persentase' => 100];
                                @endphp
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="py-3 px-2 text-slate-400 font-mono text-[11px]">{{ $idx + 1 }}</td>
                                    <td class="py-3 px-2">
                                        <p class="font-extrabold text-slate-900 leading-tight">{{ $s->name }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">NIS: {{ $s->nis ?? '-' }}</p>
                                    </td>
                                    <td class="py-3 px-2 text-center font-black text-emerald-700 font-mono text-xs">{{ $st['hadir'] }}</td>
                                    <td class="py-3 px-2 text-center font-black text-amber-700 font-mono text-xs">{{ $st['sakit'] }}</td>
                                    <td class="py-3 px-2 text-center font-black text-blue-700 font-mono text-xs">{{ $st['izin'] }}</td>
                                    <td class="py-3 px-2 text-center font-black text-rose-700 font-mono text-xs">{{ $st['alpa'] }}</td>
                                    <td class="py-3 px-2 text-right">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-black font-mono {{ $st['persentase'] >= 85 ? 'bg-emerald-100 text-emerald-800' : ($st['persentase'] >= 75 ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                                            {{ $st['persentase'] }}%
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-2xl text-[11px] text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-1 border border-slate-100">
                    <span><b>Keterangan:</b> H = Hadir, S = Sakit, I = Izin, A = Alpa / Tanpa Keterangan</span>
                    <span class="font-bold text-slate-700">Tahun Ajaran: {{ $selectedClass->academic_year }}</span>
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
