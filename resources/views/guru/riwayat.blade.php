@extends('layouts.app')

@section('title', 'Riwayat Presensi - E-Presensi')

@section('content')
<div class="flex flex-col gap-4">

    <!-- Top Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#13793e] via-[#0f6332] to-[#0a4d26] p-5 sm:p-6 text-white shadow-lg shadow-emerald-900/15">
        <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-white/5 pointer-events-none blur-2xl"></div>
        <div class="absolute -left-10 -bottom-10 w-44 h-44 rounded-full bg-emerald-400/10 pointer-events-none blur-2xl"></div>

        <div class="relative z-10 flex items-center justify-between gap-3">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-[11px] font-bold text-emerald-100 border border-white/10 mb-2">
                    <i class="fa-solid fa-clock-rotate-left text-[10px]"></i>
                    <span>Rekam Jejak Kehadiran</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white leading-tight">
                    Riwayat Presensi Pribadi
                </h1>
                <p class="text-xs text-emerald-100/90 font-medium mt-1 max-w-md leading-relaxed">
                    Daftar catatan jam masuk, jam pulang, durasi kerja, dan status kehadiran Anda.
                </p>
            </div>

            <a href="{{ route('guru.dashboard') }}" 
               class="w-10 h-10 rounded-2xl bg-white/15 hover:bg-white/25 backdrop-blur-md text-white flex items-center justify-center transition-all shadow-xs shrink-0 border border-white/20 active:scale-95" 
               title="Kembali ke Beranda">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
        </div>

        <!-- Monthly Summary Pills inside Hero -->
        <div class="relative z-10 mt-5 pt-4 border-t border-white/15 grid grid-cols-4 gap-2 text-center">
            <div class="bg-white/15 backdrop-blur-md p-2.5 rounded-2xl border border-white/10">
                <p class="text-base sm:text-lg font-black text-white leading-tight">{{ $hadirCount }}</p>
                <p class="text-[10px] text-emerald-100 font-bold uppercase mt-0.5">Tepat</p>
            </div>
            <div class="bg-white/15 backdrop-blur-md p-2.5 rounded-2xl border border-white/10">
                <p class="text-base sm:text-lg font-black text-amber-200 leading-tight">{{ $telatCount }}</p>
                <p class="text-[10px] text-emerald-100 font-bold uppercase mt-0.5">Telat</p>
            </div>
            <div class="bg-white/15 backdrop-blur-md p-2.5 rounded-2xl border border-white/10">
                <p class="text-base sm:text-lg font-black text-blue-200 leading-tight">{{ $izinCount }}</p>
                <p class="text-[10px] text-emerald-100 font-bold uppercase mt-0.5">Izin</p>
            </div>
            <div class="bg-white/15 backdrop-blur-md p-2.5 rounded-2xl border border-white/10">
                <p class="text-base sm:text-lg font-black text-rose-200 leading-tight">{{ $sakitCount }}</p>
                <p class="text-[10px] text-emerald-100 font-bold uppercase mt-0.5">Sakit</p>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-xs">
        <form method="GET" action="{{ route('guru.riwayat') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Pilih Bulan & Tahun</label>
                <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()"
                       class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">Filter Status</label>
                <select name="status" onchange="this.form.submit()" 
                        class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ $status === 'hadir' ? 'selected' : '' }}>Tepat Waktu</option>
                    <option value="terlambat" {{ $status === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="izin" {{ $status === 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ $status === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpa" {{ $status === 'alpa' ? 'selected' : '' }}>Alpa</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" 
                        class="flex-1 py-2.5 px-4 rounded-2xl bg-[#13793e] hover:bg-[#0f6332] text-white font-black text-xs transition-all shadow-xs flex items-center justify-center gap-1.5 active:scale-95">
                    <i class="fa-solid fa-filter text-[11px]"></i>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('guru.riwayat') }}" 
                   class="w-10 h-10 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-all shadow-2xs shrink-0" 
                   title="Reset Filter">
                    <i class="fa-solid fa-rotate-left text-xs"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Record List -->
    <div class="flex flex-col gap-3">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-extrabold text-slate-700 flex items-center gap-1.5">
                <i class="fa-regular fa-calendar-check text-[#13793e]"></i>
                <span>Catatan Presensi Harian</span>
            </h3>
            <span class="text-xs font-bold text-[#13793e] bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                {{ $attendances->total() }} Catatan
            </span>
        </div>

        @forelse($attendances as $item)
            <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-100 shadow-xs hover:border-emerald-200 transition-all flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs shrink-0">
                            <i class="fa-regular fa-calendar text-[11px]"></i>
                        </div>
                        <span class="text-xs font-black text-slate-900">
                            {{ \Carbon\Carbon::parse($item->date)->translatedFormat('l, d F Y') }}
                        </span>
                    </div>
                    
                    @if($item->status === 'hadir')
                        <span class="bg-[#dcfce7] text-[#15803d] px-3 py-1 rounded-full text-[10px] font-black border border-emerald-200 flex items-center gap-1">
                            <i class="fa-solid fa-check text-[9px]"></i> Tepat Waktu
                        </span>
                    @elseif($item->status === 'terlambat')
                        <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-[10px] font-black border border-amber-200 flex items-center gap-1">
                            <i class="fa-solid fa-clock text-[9px]"></i> Telat {{ $item->late_minutes }}m
                        </span>
                    @elseif($item->status === 'izin')
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-[10px] font-black border border-blue-200 flex items-center gap-1">
                            <i class="fa-solid fa-briefcase text-[9px]"></i> Izin
                        </span>
                    @elseif($item->status === 'sakit')
                        <span class="bg-rose-100 text-rose-800 px-3 py-1 rounded-full text-[10px] font-black border border-rose-200 flex items-center gap-1">
                            <i class="fa-solid fa-heart-pulse text-[9px]"></i> Sakit
                        </span>
                    @else
                        <span class="bg-slate-100 text-slate-700 px-3 py-1 rounded-full text-[10px] font-black border border-slate-200">
                            Alpa
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-3 gap-2 bg-slate-50/80 p-3 rounded-2xl border border-slate-100 text-center text-xs">
                    <div>
                        <span class="block text-[10px] text-slate-400 font-semibold">Jam Masuk</span>
                        <strong class="font-mono font-black text-slate-800">{{ $item->time_in ? substr($item->time_in, 0, 5) : '-' }}</strong>
                    </div>
                    <div>
                        <span class="block text-[10px] text-slate-400 font-semibold">Jam Pulang</span>
                        <strong class="font-mono font-black text-slate-800">{{ $item->time_out ? substr($item->time_out, 0, 5) : '-' }}</strong>
                    </div>
                    <div>
                        <span class="block text-[10px] text-slate-400 font-semibold">Total Durasi</span>
                        <strong class="font-mono font-black text-[#13793e]">{{ $item->duration }}</strong>
                    </div>
                </div>

                @if($item->photo_in_url || $item->photo_out_url)
                    <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                        @if($item->photo_in_url)
                            <div class="flex items-center gap-2 bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-200/60">
                                <img src="{{ $item->photo_in_url }}" alt="Face Masuk" class="w-7 h-7 rounded-lg object-cover border border-emerald-400">
                                <span class="text-[10px] font-bold text-emerald-800">Face Masuk</span>
                            </div>
                        @endif
                        @if($item->photo_out_url)
                            <div class="flex items-center gap-2 bg-slate-50 px-2.5 py-1.5 rounded-xl border border-slate-200/60">
                                <img src="{{ $item->photo_out_url }}" alt="Face Out" class="w-7 h-7 rounded-lg object-cover border border-blue-400">
                                <span class="text-[10px] font-bold text-blue-800">Face Pulang</span>
                            </div>
                        @endif
                    </div>
                @endif

                @if($item->notes)
                    <div class="text-[11px] text-slate-600 flex items-center gap-2 pt-2 border-t border-slate-100 font-medium">
                        <i class="fa-regular fa-comment-dots text-slate-400 text-xs"></i>
                        <span class="truncate">{{ $item->notes }}</span>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white p-12 rounded-3xl text-center border border-dashed border-slate-200 shadow-xs flex flex-col items-center justify-center gap-2">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-[#13793e] flex items-center justify-center text-2xl shadow-2xs">
                    <i class="fa-regular fa-calendar-xmark"></i>
                </div>
                <h4 class="text-xs font-black text-slate-800">Tidak Ada Data Presensi</h4>
                <p class="text-[11px] text-slate-400 max-w-xs leading-relaxed">
                    Belum ditemukan rekaman presensi pada periode bulan yang Anda pilih.
                </p>
            </div>
        @endforelse

        @if($attendances->hasPages())
            <div class="pt-2">
                {{ $attendances->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
