@extends('layouts.app')

@section('title', 'Pengajuan Izin & Sakit - E-Presensi')

@section('content')
<div class="flex flex-col gap-4">

    <!-- Top Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#13793e] via-[#0f6332] to-[#0a4d26] p-5 sm:p-6 text-white shadow-lg shadow-emerald-900/15">
        <!-- Background Ornament -->
        <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-white/5 pointer-events-none blur-2xl"></div>
        <div class="absolute -left-10 -bottom-10 w-44 h-44 rounded-full bg-emerald-400/10 pointer-events-none blur-2xl"></div>

        <div class="relative z-10 flex items-center justify-between gap-3">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-[11px] font-bold text-emerald-100 border border-white/10 mb-2">
                    <i class="fa-solid fa-file-signature text-[10px]"></i>
                    <span>Dispensasi & Cuti Mandiri</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white leading-tight">
                    Pengajuan Izin & Sakit
                </h1>
                <p class="text-xs text-emerald-100/90 font-medium mt-1 max-w-md leading-relaxed">
                    Formulir pemberitahuan ketidakhadiran resmi guru & tenaga kependidikan terhubung langsung dengan sistem presensi madrasah.
                </p>
            </div>

            <a href="{{ route('guru.dashboard') }}" 
               class="w-10 h-10 rounded-2xl bg-white/15 hover:bg-white/25 backdrop-blur-md text-white flex items-center justify-center transition-all shadow-xs shrink-0 border border-white/20 active:scale-95" 
               title="Kembali ke Beranda">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
        </div>

        <!-- Teacher Info Strip -->
        <div class="relative z-10 mt-4 pt-3.5 border-t border-white/15 flex items-center justify-between text-xs">
            <span class="text-emerald-100 font-semibold flex items-center gap-1.5">
                <i class="fa-solid fa-user-tie text-[11px]"></i>
                <span>Pemohon: <b>{{ $teacher->name }}</b></span>
            </span>
            <span class="text-[11px] font-bold text-emerald-200 bg-white/10 px-2.5 py-0.5 rounded-full border border-white/10">
                NIP: {{ $teacher->nip ?? '-' }}
            </span>
        </div>
    </div>

    <!-- Form Pengajuan Izin / Sakit -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-xs flex flex-col gap-4">
        <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3.5">
            <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                <i class="fa-solid fa-pen-to-square"></i>
            </div>
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Formulir Keterangan Tidak Hadir</h2>
                <p class="text-[11px] text-slate-400">Pastikan tanggal dan alasan yang diisi sudah sesuai dan valid</p>
            </div>
        </div>

        <form action="{{ route('guru.izin.store') }}" method="POST" class="flex flex-col gap-4"
              data-confirm="Apakah Anda yakin ingin mengirim permohonan izin/sakit ini ke administrasi madrasah?"
              data-confirm-title="Kirim Pengajuan Izin"
              data-confirm-icon="question"
              data-confirm-btn="Ya, Kirim Sekarang">
            @csrf

            <!-- 1. Tanggal Berhalangan Hadir -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Tanggal Berhalangan Hadir <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <i class="fa-regular fa-calendar-days absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="date" name="date" value="{{ old('date', $today) }}" required
                           class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-2xl bg-slate-50 border border-slate-200/80 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-mono font-bold transition-all">
                </div>
                <p class="text-[10px] text-slate-400 mt-1 font-medium">
                    * Pilih tanggal hari saat Anda berhalangan hadir di madrasah/sekolah.
                </p>
            </div>

            <!-- 2. Kategori Keterangan (Izin vs Sakit Cards) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Kategori Keterangan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Kartu Izin -->
                    <label class="relative flex items-start gap-3 p-3.5 rounded-2xl border-2 border-slate-200 bg-white cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/60 transition-all group">
                        <input type="radio" name="status" value="izin" checked class="mt-1 text-blue-600 focus:ring-0">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs shrink-0">
                                    <i class="fa-solid fa-briefcase text-[10px]"></i>
                                </span>
                                <span class="text-xs font-extrabold text-slate-900 group-hover:text-blue-800">Izin Resmi</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1 leading-snug">
                                Dinas Luar, Pelatihan/MGMP, Keperluan Keluarga Mendesak.
                            </p>
                        </div>
                    </label>

                    <!-- Kartu Sakit -->
                    <label class="relative flex items-start gap-3 p-3.5 rounded-2xl border-2 border-slate-200 bg-white cursor-pointer hover:border-rose-400 hover:bg-rose-50/30 has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50/60 transition-all group">
                        <input type="radio" name="status" value="sakit" class="mt-1 text-rose-600 focus:ring-0">
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5">
                                <span class="w-6 h-6 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center text-xs shrink-0">
                                    <i class="fa-solid fa-heart-pulse text-[10px]"></i>
                                </span>
                                <span class="text-xs font-extrabold text-slate-900 group-hover:text-rose-800">Sakit</span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1 leading-snug">
                                Kondisi kesehatan kurang fit atau dalam masa pemulihan medis.
                            </p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 3. Alasan / Keterangan Lengkap -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alasan / Keterangan Lengkap <span class="text-rose-500">*</span>
                </label>
                <textarea name="notes" rows="3" required minlength="5" maxlength="500"
                          placeholder="Jelaskan alasan ketidakhadiran Anda secara ringkas dan jelas (contoh: Mengikuti pembinaan guru madrasah di Kemenag Kabupaten)..."
                          class="w-full p-3.5 text-xs rounded-2xl bg-slate-50 border border-slate-200/80 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-medium leading-relaxed transition-all">{{ old('notes') }}</textarea>
                <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1">
                    <span>* Minimal 5 karakter, maksimal 500 karakter.</span>
                    <span class="text-emerald-700 font-semibold flex items-center gap-1">
                        <i class="fa-solid fa-circle-check text-[9px]"></i>
                        Otomatis tercatat pada presensi harian
                    </span>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full mt-2 py-3.5 px-4 rounded-2xl bg-gradient-to-r from-[#13793e] to-[#0f6332] hover:opacity-95 text-white font-black text-xs sm:text-sm shadow-md shadow-emerald-900/15 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-paper-plane text-xs"></i>
                <span>Kirim Pengajuan Izin</span>
            </button>
        </form>
    </div>

    <!-- Riwayat Pengajuan Terakhir Card -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-xs flex flex-col gap-3.5">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <h3 class="text-sm font-extrabold text-slate-900">Riwayat Pengajuan Terakhir</h3>
            </div>
            <span class="text-[11px] font-bold text-slate-400">
                10 Pengajuan Terakhir
            </span>
        </div>

        @forelse($leaveHistory as $leave)
            @php
                $isSakit = ($leave->status === 'sakit');
            @endphp
            <div class="p-3.5 bg-slate-50/70 hover:bg-slate-50 rounded-2xl border border-slate-200/70 flex flex-col gap-2 transition-all">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl {{ $isSakit ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700' }} flex items-center justify-center text-xs font-bold shrink-0">
                            <i class="{{ $isSakit ? 'fa-solid fa-heart-pulse' : 'fa-solid fa-briefcase' }} text-[11px]"></i>
                        </span>
                        <span class="text-xs font-extrabold text-slate-900">
                            {{ \Carbon\Carbon::parse($leave->date)->translatedFormat('l, d F Y') }}
                        </span>
                    </div>

                    <span class="px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider {{ $isSakit ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                        {{ $leave->status }}
                    </span>
                </div>

                <div class="pl-9 text-xs text-slate-600 italic bg-white/70 p-2.5 rounded-xl border border-slate-100 font-normal leading-relaxed">
                    "{{ $leave->notes ?: 'Tidak ada keterangan tambahan.' }}"
                </div>
            </div>
        @empty
            <div class="py-10 px-4 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200 flex flex-col items-center justify-center gap-2">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-[#13793e] flex items-center justify-center text-xl shadow-2xs">
                    <i class="fa-solid fa-shield-heart"></i>
                </div>
                <h4 class="text-xs font-extrabold text-slate-800">Belum Ada Riwayat Izin / Sakit</h4>
                <p class="text-[11px] text-slate-400 max-w-xs">
                    Semua kehadiran Anda tercatat aktif dan belum ada permohonan izin atau dispensasi yang diajukan.
                </p>
            </div>
        @endforelse
    </div>

</div>
@endsection
