@extends('layouts.app')

@section('title', 'Tulis Jurnal & Presensi Siswa - E-Presensi')

@section('content')
<div class="flex flex-col gap-4">

    <!-- Top Navigation & Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('guru.jurnal.index') }}" 
               class="w-9 h-9 rounded-full bg-white border border-slate-200/80 text-slate-700 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-2xs">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-lg font-extrabold text-slate-900 tracking-tight leading-tight">Tulis Jurnal & Presensi</h1>
                <p class="text-xs text-slate-500 font-medium">Catat materi pembelajaran & presensi kehadiran siswa</p>
            </div>
        </div>

        <a href="{{ route('guru.scan_kelas', ['schedule_id' => $selectedSchedule?->id]) }}" 
           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-[#13793e] text-xs font-bold border border-emerald-200 transition-colors">
            <i class="fa-solid fa-qrcode text-xs"></i>
            <span class="hidden sm:inline">Pindai Ulang</span>
        </a>
    </div>

    @if($selectedSchedule)
        <!-- Info Terkait Jadwal Terpilih -->
        <div class="p-3.5 bg-emerald-50/70 border border-emerald-200 rounded-3xl flex items-center justify-between text-xs text-emerald-900 shadow-2xs">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-[#13793e] text-white flex items-center justify-center text-xs shrink-0 shadow-2xs">
                    <i class="fa-regular fa-calendar-check"></i>
                </div>
                <div>
                    <p class="font-extrabold text-emerald-950">
                        {{ $selectedSchedule->subject->name }} • {{ $selectedSchedule->schoolClass->name ?? $targetClass?->name }}
                    </p>
                    <p class="text-[11px] text-emerald-700 font-medium flex items-center gap-1.5 flex-wrap">
                        <span>Jadwal {{ $selectedSchedule->day }} ({{ $selectedSchedule->formatted_time }})</span>
                        <span class="bg-amber-100 text-amber-900 font-bold px-1.5 py-0.5 rounded text-[10px] border border-amber-200/80">{{ $selectedSchedule->duration_label }}</span>
                    </p>
                </div>
            </div>
            <span class="text-[10px] font-bold bg-[#13793e] text-white px-2.5 py-1 rounded-full shadow-2xs shrink-0">
                Sesuai Jadwal
            </span>
        </div>
    @endif

    <!-- Banner Status QR Code Ruang Kelas -->
    @if($isQrVerified)
        <div class="p-3.5 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-300 rounded-3xl flex items-center justify-between text-xs text-emerald-900 font-semibold shadow-xs">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-2xl bg-[#13793e] text-white flex items-center justify-center shrink-0 shadow-2xs">
                    <i class="fa-solid fa-circle-check text-sm"></i>
                </div>
                <div>
                    <p class="font-black text-emerald-950 text-xs">Ruang Kelas Terverifikasi Barcode</p>
                    <p class="text-[11px] text-emerald-700 font-medium">Lokasi: <b>{{ $targetClass->name ?? 'Kelas' }}</b> • Token Fisik Terkonfirmasi</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-200/60 text-emerald-900 border border-emerald-300 shrink-0">
                Aktif
            </span>
        </div>
    @else
        <div class="p-4 bg-rose-50 border-2 border-rose-300 rounded-3xl flex flex-col gap-3 shadow-xs">
            <div class="flex items-center gap-2 text-rose-800 font-bold text-sm">
                <div class="w-8 h-8 rounded-2xl bg-rose-600 text-white flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-qrcode text-sm"></i>
                </div>
                <span>Wajib Pindai QR Code Ruang Kelas Terlebih Dahulu</span>
            </div>
            <p class="text-xs text-rose-700 leading-relaxed">
                Anda wajib berada di ruang <b>{{ $targetClass->name ?? 'kelas' }}</b> dan memindai QR Code di dinding kelas untuk membuka kunci form pengisian jurnal dan absensi siswa.
            </p>
            <a href="{{ route('guru.scan_kelas', ['schedule_id' => $selectedSchedule?->id]) }}" 
               class="w-full py-3 px-4 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl text-xs font-black flex items-center justify-center gap-2 shadow-md active:scale-98 transition-all">
                <i class="fa-solid fa-camera"></i>
                <span>Buka Kamera & Scan QR Kelas Sekarang</span>
            </a>
        </div>
    @endif

    <form action="{{ route('guru.jurnal.store') }}" method="POST" enctype="multipart/form-data" id="journalForm" 
          class="flex flex-col gap-4 {{ !$isQrVerified ? 'opacity-60 pointer-events-none select-none' : '' }}">
        @csrf

        <input type="hidden" name="qr_token" id="qrTokenInput" value="{{ $qrToken }}">
        <input type="hidden" name="lesson_schedule_id" id="lessonScheduleIdInput" value="{{ $selectedSchedule?->id }}">

        <!-- 1. KARTU INFORMASI PEMBELAJARAN (DATA KELAS & MAPEL) -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-xs flex flex-col gap-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">1. Data Sesi Pembelajaran</h2>
                    <p class="text-[11px] text-slate-400">Pilih mata pelajaran, jam mengajar, dan status kehadiran kelas</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Kelas (Otomatis Terisi & Terkunci dari Hasil Scan Barcode) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Kelas Target <span class="text-rose-500">*</span></label>
                        @if($isQrVerified && $targetClass)
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">
                                <i class="fa-solid fa-lock text-[9px]"></i>
                                Terkunci (Barcode)
                            </span>
                        @endif
                    </div>

                    @if($isQrVerified && $targetClass)
                        <!-- Input ID Kelas Tersembunyi -->
                        <input type="hidden" name="class_id" id="classSelect" value="{{ $targetClass->id }}">

                        <!-- Tampilan Terkunci (Read-Only) -->
                        <div class="w-full px-3.5 py-2.5 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-emerald-950 flex items-center justify-between shadow-2xs select-none">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-xl bg-[#13793e] text-white flex items-center justify-center shadow-2xs shrink-0">
                                    <i class="fa-solid fa-school text-xs"></i>
                                </div>
                                <div class="truncate">
                                    <p class="text-xs font-black text-emerald-950 truncate">
                                        {{ $targetClass->name }} ({{ $targetClass->academic_year }})
                                    </p>
                                    <p class="text-[10px] text-emerald-700 font-semibold flex items-center gap-1">
                                        <i class="fa-solid fa-qrcode text-[10px]"></i>
                                        Tingkat {{ $targetClass->grade_level }} • Ruang Terkonfirmasi
                                    </p>
                                </div>
                            </div>
                            <div class="shrink-0 pl-2">
                                <i class="fa-solid fa-circle-check text-emerald-600 text-base" title="Kelas otomatis terkunci sesuai barcode ruangan"></i>
                            </div>
                        </div>
                    @else
                        <select name="class_id" id="classSelect" onchange="onClassChange(this.value)" required
                                class="w-full px-3.5 py-2.5 text-xs rounded-2xl bg-slate-50 border border-slate-200/80 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-semibold transition-all">
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->academic_year }})
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <!-- Mata Pelajaran -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-slate-700">Mata Pelajaran <span class="text-rose-500">*</span></label>
                        @if($todayClassSchedules->isNotEmpty())
                            <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">
                                {{ $todayClassSchedules->count() }} Jadwal Hari Ini ({{ $todayDay }})
                            </span>
                        @endif
                    </div>
                    <select name="subject_id" id="subjectSelect" onchange="onSubjectChange(this)" required
                            class="w-full px-3.5 py-2.5 text-xs rounded-2xl bg-slate-50 border border-slate-200/80 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-bold transition-all">
                        @if($todayClassSchedules->isNotEmpty())
                            <optgroup label="⏰ Jadwal Hari Ini ({{ $todayDay }}) — Sesuai Jam Pelajaran">
                                @foreach($todayClassSchedules as $sch)
                                    <option value="{{ $sch->subject_id }}"
                                            data-schedule-id="{{ $sch->id }}"
                                            data-start-time="{{ substr($sch->start_time, 0, 5) }}"
                                            data-end-time="{{ substr($sch->end_time, 0, 5) }}"
                                            {{ ($selectedSchedule && $selectedSchedule->id == $sch->id) || (!$selectedSchedule && $selectedSubjectId == $sch->subject_id) ? 'selected' : '' }}>
                                        ⭐ {{ $sch->subject->name }} ({{ $sch->subject->code }}) • Jam {{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }} WIB
                                        @if($sch->teacher_id != $teacher->id)
                                            [Guru: {{ $sch->teacher->name ?? '-' }}]
                                        @endif
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        @if($otherDayClassSchedules->isNotEmpty())
                            <optgroup label="📅 Jadwal Hari Lain di Kelas Ini">
                                @foreach($otherDayClassSchedules as $sch)
                                    <option value="{{ $sch->subject_id }}"
                                            data-schedule-id="{{ $sch->id }}"
                                            data-start-time="{{ substr($sch->start_time, 0, 5) }}"
                                            data-end-time="{{ substr($sch->end_time, 0, 5) }}"
                                            {{ (!$selectedSchedule && $selectedSubjectId == $sch->subject_id) ? 'selected' : '' }}>
                                        {{ $sch->subject->name }} ({{ $sch->subject->code }}) • {{ $sch->day }}, Jam {{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }} WIB
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif

                        <optgroup label="📚 Semua Mata Pelajaran (Di Luar Jadwal)">
                            @foreach($subjects as $sb)
                                @php
                                    $inToday = $todayClassSchedules->contains('subject_id', $sb->id);
                                @endphp
                                @if(!$inToday)
                                    <option value="{{ $sb->id }}"
                                            data-schedule-id=""
                                            data-start-time=""
                                            data-end-time=""
                                            {{ $selectedSubjectId == $sb->id ? 'selected' : '' }}>
                                        {{ $sb->name }} ({{ $sb->code }})
                                    </option>
                                @endif
                            @endforeach
                        </optgroup>
                    </select>

                    <div id="scheduleInfoBadge" class="mt-1.5 flex items-center gap-1.5 text-[11px] font-semibold text-[#13793e]">
                        <i class="fa-regular fa-clock text-xs"></i>
                        <span id="scheduleInfoText">
                            @if($selectedSchedule)
                                Jam Terjadwal: <b>{{ substr($selectedSchedule->start_time, 0, 5) }} - {{ substr($selectedSchedule->end_time, 0, 5) }} WIB</b> ({{ $selectedSchedule->day }})
                            @else
                                Jam Mengajar: <b>{{ $defaultStartTime }} - {{ $defaultEndTime }} WIB</b>
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Mengajar <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', $todayDate) }}" required
                           class="w-full px-3.5 py-2.5 text-xs rounded-2xl bg-slate-50 border border-slate-200/80 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-mono font-bold transition-all">
                </div>

                <!-- Jam Mulai & Jam Selesai -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Mulai <span class="text-rose-500">*</span></label>
                        <input type="time" name="start_time" value="{{ old('start_time', $defaultStartTime) }}" required
                               class="w-full px-3 py-2.5 text-xs rounded-2xl bg-slate-50 border border-slate-200/80 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-mono font-bold transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Jam Selesai <span class="text-rose-500">*</span></label>
                        <input type="time" name="end_time" value="{{ old('end_time', $defaultEndTime) }}" required
                               class="w-full px-3 py-2.5 text-xs rounded-2xl bg-slate-50 border border-slate-200/80 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-mono font-bold transition-all">
                    </div>
                </div>
            </div>

            <!-- Peran Mengajar: Guru Utama vs Guru Pengganti (Inval) -->
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200/70 flex flex-col gap-2.5">
                <label class="block text-xs font-bold text-slate-800">Peran Mengajar di Sesi Ini:</label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 has-[:checked]:border-[#13793e] has-[:checked]:bg-emerald-50/60 transition-all">
                        <input type="radio" name="is_substitute" value="0" {{ !$isSubstituteAuto ? 'checked' : '' }} onchange="toggleSubstituteOptions(false)" class="text-[#13793e] focus:ring-0">
                        <span class="text-xs font-bold text-slate-800">Guru Pengampu Utama</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 has-[:checked]:border-purple-600 has-[:checked]:bg-purple-50 transition-all">
                        <input type="radio" name="is_substitute" value="1" {{ $isSubstituteAuto ? 'checked' : '' }} onchange="toggleSubstituteOptions(true)" class="text-purple-600 focus:ring-0">
                        <span class="text-xs font-bold text-purple-900 flex items-center gap-1">
                            <i class="fa-solid fa-arrow-right-arrow-left text-[11px]"></i>
                            <span>Guru Pengganti (Inval)</span>
                        </span>
                    </label>
                </div>

                <div id="substituteTeacherContainer" class="{{ $isSubstituteAuto ? '' : 'hidden' }} pt-2 border-t border-slate-200 flex flex-col gap-2">
                    <label class="block text-xs font-bold text-purple-950">
                        Menggantikan Guru yang Berhalangan: <span class="text-rose-500">*</span>
                    </label>
                    <select name="substituted_teacher_id" id="substitutedTeacherSelect" {{ $isSubstituteAuto ? 'required' : '' }}
                            class="w-full px-3 py-2 text-xs rounded-xl bg-white border border-purple-300 focus:outline-none focus:border-purple-600 text-slate-800 font-semibold">
                        <option value="">-- Pilih Guru yang Digantikan --</option>
                        @foreach($otherTeachers as $ot)
                            <option value="{{ $ot->id }}" {{ ($substituteForTeacherId == $ot->id) ? 'selected' : '' }}>
                                {{ $ot->name }} (NIP: {{ $ot->nip ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-purple-700">
                        * Sesi ini akan tercatat sebagai guru pengganti resmi dan dilaporkan ke supervisi madrasah/sekolah.
                    </p>
                </div>
            </div>

            <!-- Status Pelaksanaan -->
            <div>
                <label class="block text-xs font-bold text-slate-800 mb-1.5">Metode / Status Pembelajaran <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <label class="flex items-center gap-2 p-2.5 rounded-2xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 has-[:checked]:border-[#13793e] has-[:checked]:bg-emerald-50/70 transition-all">
                        <input type="radio" name="status" value="terlaksana" checked class="text-[#13793e] focus:ring-0">
                        <span class="text-xs font-bold text-slate-800">Tatap Muka</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-2xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 has-[:checked]:border-[#13793e] has-[:checked]:bg-emerald-50/70 transition-all">
                        <input type="radio" name="status" value="daring" class="text-[#13793e] focus:ring-0">
                        <span class="text-xs font-bold text-slate-800">Daring / Online</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-2xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 has-[:checked]:border-[#13793e] has-[:checked]:bg-emerald-50/70 transition-all">
                        <input type="radio" name="status" value="tugas" class="text-[#13793e] focus:ring-0">
                        <span class="text-xs font-bold text-slate-800">Pemberian Tugas</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-2xl border border-slate-200 bg-white cursor-pointer hover:bg-slate-50 has-[:checked]:border-[#13793e] has-[:checked]:bg-emerald-50/70 transition-all">
                        <input type="radio" name="status" value="tidak_terlaksana" class="text-[#13793e] focus:ring-0">
                        <span class="text-xs font-bold text-slate-800">Tidak Terlaksana</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- 2. KARTU MATERI & AKTIVITAS PEMBELAJARAN -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-xs flex flex-col gap-4">
            <div class="flex items-center gap-2 border-b border-slate-100 pb-3">
                <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900">2. Materi & Aktivitas Belajar</h2>
                    <p class="text-[11px] text-slate-400">Tuliskan materi, kegiatan diskusi/latihan, serta catatan dokumentasi</p>
                </div>
            </div>

            <!-- Materi / Topik Pokok -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Materi Pokok / Judul Bab <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <i class="fa-solid fa-heading absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" name="materi" value="{{ old('materi') }}" required placeholder="Contoh: Bab 3 - Persamaan Linear Dua Variabel (SPLDV)"
                           class="w-full pl-9 pr-3.5 py-2.5 text-xs rounded-2xl bg-slate-50 border border-slate-200/80 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-bold transition-all">
                </div>
            </div>

            <!-- Uraian Aktivitas Pembelajaran -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">Uraian Kegiatan Pembelajaran / Capaian <span class="text-rose-500">*</span></label>
                <textarea name="kegiatan" rows="3" required placeholder="Jelaskan ringkasan materi yang disampaikan, metode belajar (ceramah/diskusi/praktikum), dan evaluasi penugasan..."
                          class="w-full p-3 text-xs rounded-2xl bg-slate-50 border border-slate-200/80 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-medium leading-relaxed transition-all">{{ old('kegiatan') }}</textarea>
            </div>

            <!-- Catatan Khusus & Foto Lampiran -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Khusus / Kendala (Opsional)</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan kondisi siswa, kendala fasilitas, atau pekerjaan rumah (PR)..."
                              class="w-full p-3 text-xs rounded-2xl bg-slate-50 border border-slate-200/80 focus:outline-none focus:border-[#13793e] focus:bg-white text-slate-800 font-medium leading-relaxed transition-all">{{ old('catatan') }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Foto Dokumentasi Kelas (Opsional)</label>
                    <div class="p-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl flex flex-col gap-1.5">
                        <input type="file" name="attachment" accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#13793e] file:text-white hover:file:bg-[#0f6332]">
                        <p class="text-[10px] text-slate-400">Format JPG/PNG maksimal 3MB.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. KARTU PRESENSI KEHADIRAN SISWA -->
        <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-100 shadow-xs flex flex-col gap-3.5">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                        <i class="fa-solid fa-users-viewfinder"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm font-extrabold text-slate-900">3. Presensi Siswa</h2>
                            <span id="studentCountBadge" class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-[#13793e] border border-emerald-200">
                                {{ $students->count() }} Siswa
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400">Tentukan status kehadiran: Hadir, Sakit, Izin, atau Alpa</p>
                    </div>
                </div>

                <!-- Tombol Cepat: Semua Hadir -->
                <button type="button" onclick="setAllPresent()" 
                        class="px-3 py-1.5 bg-[#13793e] hover:bg-[#0f6332] text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-2xs active:scale-95 transition-all">
                    <i class="fa-solid fa-check-double text-[11px]"></i>
                    <span>Semua Hadir</span>
                </button>
            </div>

            <!-- Petunjuk Singkat Label -->
            <div class="flex items-center justify-between text-[11px] text-slate-500 px-1 font-medium">
                <span>Daftar Siswa Terdaftar:</span>
                <span class="flex items-center gap-2 text-[10px]">
                    <span class="text-emerald-700 font-bold">H: Hadir</span>
                    <span class="text-amber-700 font-bold">S: Sakit</span>
                    <span class="text-blue-700 font-bold">I: Izin</span>
                    <span class="text-rose-700 font-bold">A: Alpa</span>
                </span>
            </div>

            <!-- Indikator Loading Saat Ganti Kelas -->
            <div id="loadingStudents" class="hidden py-8 text-center text-xs text-slate-500 flex flex-col items-center justify-center gap-2">
                <i class="fa-solid fa-circle-notch animate-spin text-[#13793e] text-2xl"></i>
                <span class="font-medium">Memuat data siswa kelas...</span>
            </div>

            <!-- Daftar Siswa -->
            <div id="studentContainer" class="flex flex-col gap-2">
                @if($students->isEmpty())
                    <div class="p-8 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        Tidak ada siswa terdaftar di kelas ini.
                    </div>
                @else
                    @foreach($students as $idx => $s)
                        <div class="p-3 bg-slate-50/70 hover:bg-slate-50 rounded-2xl border border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 transition-colors">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-white border border-slate-200 text-slate-600 flex items-center justify-center text-[10px] font-black shrink-0 shadow-2xs font-mono">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="truncate">
                                    <p class="font-bold text-xs text-slate-900 truncate">{{ $s->name }}</p>
                                    <p class="text-[10px] text-slate-400">
                                        NIS: {{ $s->nis ?? '-' }} • 
                                        <span class="{{ $s->gender == 'L' ? 'text-blue-600' : 'text-pink-600' }} font-bold">
                                            {{ $s->gender == 'L' ? 'L' : 'P' }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Pilihan Radio Kehadiran: H / S / I / A -->
                            <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-auto bg-white p-1 rounded-xl border border-slate-200 shadow-2xs">
                                <label class="cursor-pointer">
                                    <input type="radio" name="attendances[{{ $s->id }}]" value="hadir" checked class="peer sr-only">
                                    <span class="px-3 py-1 text-xs font-black rounded-lg text-slate-600 peer-checked:bg-[#13793e] peer-checked:text-white transition-all block text-center min-w-[34px]">
                                        H
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="attendances[{{ $s->id }}]" value="sakit" class="peer sr-only">
                                    <span class="px-3 py-1 text-xs font-black rounded-lg text-slate-600 peer-checked:bg-amber-500 peer-checked:text-white transition-all block text-center min-w-[34px]">
                                        S
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="attendances[{{ $s->id }}]" value="izin" class="peer sr-only">
                                    <span class="px-3 py-1 text-xs font-black rounded-lg text-slate-600 peer-checked:bg-blue-600 peer-checked:text-white transition-all block text-center min-w-[34px]">
                                        I
                                    </span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="attendances[{{ $s->id }}]" value="alpa" class="peer sr-only">
                                    <span class="px-3 py-1 text-xs font-black rounded-lg text-slate-600 peer-checked:bg-rose-600 peer-checked:text-white transition-all block text-center min-w-[34px]">
                                        A
                                    </span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Tombol Submit (Sticky Floating Bottom) -->
        <div class="sticky bottom-20 z-10 pt-2">
            <button type="submit" 
                    class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-[#13793e] to-[#0f6332] hover:opacity-95 text-white font-black text-sm shadow-lg shadow-emerald-900/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk text-base"></i>
                <span>Simpan Jurnal & Presensi Siswa</span>
            </button>
        </div>
    </form>

</div>

<script>
    // 1-Click Tandai Semua Hadir
    function setAllPresent() {
        const radios = document.querySelectorAll('input[type="radio"][value="hadir"]');
        radios.forEach(r => r.checked = true);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Semua siswa ditandai HADIR',
                showConfirmButton: false,
                timer: 1500
            });
        }
    }

    // Dynamic AJAX saat kelas diubah
    function onClassChange(classId) {
        const container = document.getElementById('studentContainer');
        const loader = document.getElementById('loadingStudents');
        const badge = document.getElementById('studentCountBadge');

        container.classList.add('hidden');
        loader.classList.remove('hidden');

        fetch(`{{ url('guru/api/kelas') }}/${classId}/siswa`)
            .then(res => res.json())
            .then(students => {
                loader.classList.add('hidden');
                container.classList.remove('hidden');
                badge.innerText = `${students.length} Siswa`;

                if (students.length === 0) {
                    container.innerHTML = `<div class="p-8 text-center text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">Tidak ada siswa terdaftar di kelas ini.</div>`;
                    return;
                }

                let html = '';
                students.forEach((s, idx) => {
                    const genderLabel = s.gender === 'L' ? 'L' : 'P';
                    const genderColor = s.gender === 'L' ? 'text-blue-600' : 'text-pink-600';
                    html += `
                        <div class="p-3 bg-slate-50/70 hover:bg-slate-50 rounded-2xl border border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 transition-colors">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="w-6 h-6 rounded-lg bg-white border border-slate-200 text-slate-600 flex items-center justify-center text-[10px] font-black shrink-0 shadow-2xs font-mono">
                                    ${idx + 1}
                                </span>
                                <div class="truncate">
                                    <p class="font-bold text-xs text-slate-900 truncate">${s.name}</p>
                                    <p class="text-[10px] text-slate-400">
                                        NIS: ${s.nis || '-'} • <span class="${genderColor} font-bold">${genderLabel}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0 self-end sm:self-auto bg-white p-1 rounded-xl border border-slate-200 shadow-2xs">
                                <label class="cursor-pointer">
                                    <input type="radio" name="attendances[${s.id}]" value="hadir" checked class="peer sr-only">
                                    <span class="px-3 py-1 text-xs font-black rounded-lg text-slate-600 peer-checked:bg-[#13793e] peer-checked:text-white transition-all block text-center min-w-[34px]">H</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="attendances[${s.id}]" value="sakit" class="peer sr-only">
                                    <span class="px-3 py-1 text-xs font-black rounded-lg text-slate-600 peer-checked:bg-amber-500 peer-checked:text-white transition-all block text-center min-w-[34px]">S</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="attendances[${s.id}]" value="izin" class="peer sr-only">
                                    <span class="px-3 py-1 text-xs font-black rounded-lg text-slate-600 peer-checked:bg-blue-600 peer-checked:text-white transition-all block text-center min-w-[34px]">I</span>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="attendances[${s.id}]" value="alpa" class="peer sr-only">
                                    <span class="px-3 py-1 text-xs font-black rounded-lg text-slate-600 peer-checked:bg-rose-600 peer-checked:text-white transition-all block text-center min-w-[34px]">A</span>
                                </label>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            })
            .catch(err => {
                loader.classList.add('hidden');
                container.classList.remove('hidden');
                console.error(err);
            });
    }

    function toggleSubstituteOptions(isSub) {
        const container = document.getElementById('substituteTeacherContainer');
        const select = document.getElementById('substitutedTeacherSelect');
        if (isSub) {
            container.classList.remove('hidden');
            select.required = true;
        } else {
            container.classList.add('hidden');
            select.required = false;
            select.value = '';
        }
    }

    function onSubjectChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        if (!selectedOption) return;

        const scheduleId = selectedOption.getAttribute('data-schedule-id') || '';
        const startTime = selectedOption.getAttribute('data-start-time') || '';
        const endTime = selectedOption.getAttribute('data-end-time') || '';

        // Update hidden lesson_schedule_id
        const scheduleIdInput = document.getElementById('lessonScheduleIdInput');
        if (scheduleIdInput) {
            scheduleIdInput.value = scheduleId;
        }

        // Update input jam mulai & selesai
        const startTimeInput = document.querySelector('input[name="start_time"]');
        const endTimeInput = document.querySelector('input[name="end_time"]');
        const infoText = document.getElementById('scheduleInfoText');

        if (startTime && endTime) {
            if (startTimeInput) startTimeInput.value = startTime;
            if (endTimeInput) endTimeInput.value = endTime;
            if (infoText) {
                infoText.innerHTML = `Jam Terjadwal: <b>${startTime} - ${endTime} WIB</b>`;
            }
        } else {
            if (infoText) {
                infoText.innerHTML = `Di luar jadwal reguler (Jam manual)`;
            }
        }
    }
</script>
@endsection
