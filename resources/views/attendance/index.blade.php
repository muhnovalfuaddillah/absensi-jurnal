@extends('layouts.admin')

@section('title', 'Presensi Guru & Absen Manual')

@section('content')
<div class="flex flex-col w-full gap-space-lg">

    <!-- Top Header Banner & Action Button -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-space-md bg-surface-container-lowest p-space-lg rounded-xl shadow-sm border border-surface-container">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-sm flex-wrap">
                <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary-container flex items-center justify-center shadow-sm">
                    <span class="material-symbols-outlined text-[24px]">fact_check</span>
                </div>
                <div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface">Data Presensi & Absen Manual</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        Kelola data kehadiran seluruh guru dan lakukan input absensi manual apabila guru berhalangan membawa HP.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-space-sm flex-wrap">
            <a href="{{ route('admin.attendance.recap') }}" 
               class="inline-flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-surface-container-low text-on-surface font-label-lg text-label-lg hover:bg-surface-container transition-all">
                <span class="material-symbols-outlined text-[18px] text-tertiary">summarize</span>
                <span>Rekap Bulanan TPG</span>
            </a>

            <!-- Tombol Utama: Input Absen Manual -->
            <button type="button" onclick="openManualModal()"
                    class="inline-flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-primary text-on-primary font-label-lg text-label-lg shadow-sm hover:opacity-95 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Input Absen Manual</span>
            </button>
        </div>
    </div>

    <!-- Alert Flash Notifications -->
    @if(session('success'))
    <div class="flex items-center gap-space-sm p-space-md bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-xs">
        <span class="material-symbols-outlined text-[22px] text-emerald-600">check_circle</span>
        <span class="font-label-lg text-label-lg">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-space-sm p-space-md bg-red-50 border border-red-200 text-red-800 rounded-xl shadow-xs">
        <span class="material-symbols-outlined text-[22px] text-red-600">error</span>
        <span class="font-label-lg text-label-lg">{{ session('error') }}</span>
    </div>
    @endif

    <!-- 5 KPI Stat Cards Harian -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total Guru</span>
                <span class="material-symbols-outlined text-primary text-[18px]">groups</span>
            </div>
            <div class="mt-2 font-headline-xl text-headline-xl text-on-surface">{{ $totalTeachers }}</div>
            <span class="font-body-sm text-body-sm text-on-surface-variant">Terdaftar</span>
        </div>

        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Hadir Tepat</span>
                <span class="material-symbols-outlined text-emerald-600 text-[18px]">task_alt</span>
            </div>
            <div class="mt-2 font-headline-xl text-headline-xl text-emerald-600">{{ $hadirCount }}</div>
            <span class="font-body-sm text-body-sm text-emerald-700">Tepat Waktu</span>
        </div>

        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Terlambat</span>
                <span class="material-symbols-outlined text-amber-600 text-[18px]">schedule</span>
            </div>
            <div class="mt-2 font-headline-xl text-headline-xl text-amber-600">{{ $terlambatCount }}</div>
            <span class="font-body-sm text-body-sm text-amber-700">Lewat Toleransi</span>
        </div>

        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Izin</span>
                <span class="material-symbols-outlined text-purple-600 text-[18px]">description</span>
            </div>
            <div class="mt-2 font-headline-xl text-headline-xl text-purple-600">{{ $izinCount }}</div>
            <span class="font-body-sm text-body-sm text-purple-700">Tugas / Dinas</span>
        </div>

        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Sakit</span>
                <span class="material-symbols-outlined text-rose-600 text-[18px]">medical_services</span>
            </div>
            <div class="mt-2 font-headline-xl text-headline-xl text-rose-600">{{ $sakitCount }}</div>
            <span class="font-body-sm text-body-sm text-rose-700">Surat Dokter</span>
        </div>

        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Belum Hadir</span>
                <span class="material-symbols-outlined text-slate-400 text-[18px]">person_off</span>
            </div>
            <div class="mt-2 font-headline-xl text-headline-xl text-slate-500">{{ $belumAbsen }}</div>
            <span class="font-body-sm text-body-sm text-slate-500">Belum Presensi</span>
        </div>
    </div>

    <!-- Filter Bar (Tanggal, Status, Cari Nama) -->
    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
        <form method="GET" action="{{ route('admin.attendance.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Filter Tanggal -->
            <div>
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Pilih Tanggal</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">event</span>
                    <input type="date" name="date" value="{{ $selectedDate }}" onchange="this.form.submit()"
                           class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                </div>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Status Kehadiran</label>
                <select name="status" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ $statusFilter === 'hadir' ? 'selected' : '' }}>Hadir (Tepat Waktu)</option>
                    <option value="terlambat" {{ $statusFilter === 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="izin" {{ $statusFilter === 'izin' ? 'selected' : '' }}>Izin Dinas</option>
                    <option value="sakit" {{ $statusFilter === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpa" {{ $statusFilter === 'alpa' ? 'selected' : '' }}>Alpa (Tanpa Keterangan)</option>
                </select>
            </div>

            <!-- Cari Nama Guru -->
            <div>
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Cari Guru / NIP</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">search</span>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama atau NIP..."
                           class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-outline border border-surface-container focus:border-primary focus:outline-none">
                </div>
            </div>

            <!-- Tombol Submit & Reset -->
            <div class="flex items-end gap-2">
                <button type="submit" 
                        class="flex-1 py-2 px-4 rounded-lg bg-surface-container-high hover:bg-surface-container-highest text-on-surface font-label-md text-label-md flex items-center justify-center gap-1.5 transition-colors h-[38px]">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('admin.attendance.index') }}" 
                   class="py-2 px-3 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface-variant font-label-md text-label-md transition-colors h-[38px] flex items-center justify-center"
                   title="Reset Filter Tanggal Hari Ini">
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Presensi Harian -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
        <div class="p-space-md border-b border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm bg-surface-container-low">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">calendar_month</span>
                <span class="font-headline-sm text-headline-sm text-on-surface">
                    Daftar Presensi: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <span class="font-body-sm text-body-sm text-on-surface-variant">
                Menampilkan {{ $attendances->count() }} data presensi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-surface-container text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider border-b border-surface-container-high">
                        <th class="py-3 px-space-md w-12 text-center">#</th>
                        <th class="py-3 px-space-md">Guru & NIP</th>
                        <th class="py-3 px-space-md text-center">Jam Masuk</th>
                        <th class="py-3 px-space-md text-center">Jam Pulang</th>
                        <th class="py-3 px-space-md text-center">Durasi Kerja</th>
                        <th class="py-3 px-space-md text-center">Status</th>
                        <th class="py-3 px-space-md">Keterangan / Catatan</th>
                        <th class="py-3 px-space-md text-right w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container">
                    @forelse($attendances as $index => $item)
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="py-space-md px-space-md text-center font-mono font-medium text-on-surface-variant">
                            {{ $attendances->firstItem() + $index }}
                        </td>
                        <td class="py-space-md px-space-md">
                            <div class="flex items-center gap-space-sm">
                                @if($item->photo_in_url)
                                    <div class="relative shrink-0">
                                        <img src="{{ $item->photo_in_url }}" alt="Face ID" class="w-10 h-10 rounded-xl object-cover border-2 border-emerald-500 shadow-sm cursor-pointer hover:scale-105 transition-transform" onclick="viewFacePhoto('{{ $item->photo_in_url }}', '{{ $item->teacher->name }}', '{{ substr($item->time_in, 0, 5) }} WIB')" title="Klik untuk perbesar foto Face ID">
                                        <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-emerald-500 rounded-full border-2 border-white"></span>
                                    </div>
                                @elseif($item->teacher->photo_url)
                                    <img src="{{ $item->teacher->photo_url }}" alt="{{ $item->teacher->name }}" class="w-10 h-10 rounded-xl object-cover border border-surface-container shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary-container font-label-md flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($item->teacher->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-1.5 flex-wrap">
                                        <span class="font-label-lg text-label-lg text-on-surface truncate">{{ $item->teacher->name }}</span>
                                        @if($item->attendance_method === 'face_scan' || $item->photo_in)
                                            <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                <span class="material-symbols-outlined text-[11px]">face</span> Face ID
                                            </span>
                                        @endif
                                    </div>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">
                                        NIP. {{ $item->teacher->nip ?? '-' }} • {{ $item->teacher->subject ?? 'Guru' }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Jam Masuk -->
                        <td class="py-space-md px-space-md text-center">
                            @if($item->time_in)
                                <span class="font-mono font-bold text-on-surface">{{ substr($item->time_in, 0, 5) }}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">WIB</span>
                                @if($item->late_minutes > 0)
                                    <div class="text-[11px] font-semibold text-amber-600 mt-0.5">
                                        +{{ $item->late_minutes }}m telat
                                    </div>
                                @endif
                            @else
                                <span class="text-outline">-</span>
                            @endif
                        </td>

                        <!-- Jam Pulang -->
                        <td class="py-space-md px-space-md text-center">
                            @if($item->time_out)
                                <span class="font-mono font-bold text-indigo-700">{{ substr($item->time_out, 0, 5) }}</span>
                                <span class="font-body-sm text-body-sm text-on-surface-variant">WIB</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface-variant font-label-sm text-label-sm">
                                    Belum Pulang
                                </span>
                            @endif
                        </td>

                        <!-- Durasi Kerja -->
                        <td class="py-space-md px-space-md text-center">
                            @if($item->work_duration_formatted)
                                <span class="font-label-md text-label-md text-emerald-700 font-mono">
                                    {{ $item->work_duration_formatted }}
                                </span>
                            @else
                                <span class="text-outline text-body-sm">-</span>
                            @endif
                        </td>

                        <!-- Status Badge -->
                        <td class="py-space-md px-space-md text-center">
                            @if($item->status === 'hadir')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-semibold">
                                    <span class="material-symbols-outlined text-[14px]">check_circle</span>
                                    <span>Tepat Waktu</span>
                                </span>
                            @elseif($item->status === 'terlambat')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 font-label-sm text-label-sm font-semibold">
                                    <span class="material-symbols-outlined text-[14px]">schedule</span>
                                    <span>Terlambat</span>
                                </span>
                            @elseif($item->status === 'izin')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-purple-100 text-purple-800 font-label-sm text-label-sm font-semibold">
                                    <span class="material-symbols-outlined text-[14px]">description</span>
                                    <span>Izin Dinas</span>
                                </span>
                            @elseif($item->status === 'sakit')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 font-label-sm text-label-sm font-semibold">
                                    <span class="material-symbols-outlined text-[14px]">medical_services</span>
                                    <span>Sakit</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-100 text-red-800 font-label-sm text-label-sm font-semibold">
                                    <span class="material-symbols-outlined text-[14px]">cancel</span>
                                    <span>Alpa</span>
                                </span>
                            @endif
                        </td>

                        <!-- Catatan -->
                        <td class="py-space-md px-space-md font-body-sm text-body-sm text-on-surface-variant max-w-xs truncate">
                            {{ $item->notes ?? '-' }}
                        </td>

                        <!-- Aksi (Edit & Hapus) -->
                        <td class="py-space-md px-space-md text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button type="button" onclick="openEditModal({{ json_encode($item) }})"
                                        class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors"
                                        title="Edit Jam & Status">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>

                                <form action="{{ route('admin.attendance.destroy', $item->id) }}" method="POST"
                                      data-confirm="Hapus catatan presensi untuk <b>{{ addslashes($item->teacher->name ?? 'Guru') }}</b> tanggal {{ \Carbon\Carbon::parse($item->date)->translatedFormat('d M Y') }}?"
                                      data-confirm-title="Hapus Presensi"
                                      data-confirm-icon="warning"
                                      data-confirm-color="#dc2626"
                                      data-confirm-btn="Ya, Hapus"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-1.5 rounded-lg text-on-surface-variant hover:bg-red-50 hover:text-red-600 transition-colors"
                                            title="Hapus Presensi">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[48px] text-outline mb-2">event_busy</span>
                            <p class="font-headline-sm text-headline-sm text-on-surface">Belum ada data presensi pada tanggal ini.</p>
                            <p class="font-body-sm text-body-sm mt-1">
                                Guru dapat melakukan presensi mandiri dari HP, atau Admin dapat mengklik tombol <strong>Input Absen Manual</strong> di atas.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($attendances->hasPages())
        <div class="p-space-md bg-surface-container-low border-t border-surface-container">
            {{ $attendances->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

</div>

<!-- Modal Input Absen Manual -->
<div id="manualModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full p-space-lg shadow-xl border border-surface-container flex flex-col gap-space-md animate-scaleUp">
        
        <div class="flex items-center justify-between pb-3 border-b border-surface-container">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary-container text-on-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">add_circle</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Input Absensi Manual (Admin)</h3>
            </div>
            <button type="button" onclick="closeManualModal()"
                    class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('admin.attendance.manual') }}" method="POST" class="flex flex-col gap-space-md">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                <!-- Tanggal -->
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Tanggal Absensi</label>
                    <input type="date" name="date" value="{{ $selectedDate }}" required
                           class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                </div>

                <!-- Guru -->
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Pilih Guru / Tendik</label>
                    <select name="teacher_id" required
                            class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                        <option value="">-- Pilih Guru --</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->nip ?? 'Non-NIP' }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Status Kehadiran -->
            <div>
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Status Kehadiran</label>
                <select name="status" id="manualStatusSelect" onchange="toggleTimeFields()" required
                        class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-headline-sm text-headline-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    <option value="hadir">Hadir (Tepat Waktu)</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="izin">Izin Dinas</option>
                    <option value="sakit">Sakit (Surat Dokter)</option>
                    <option value="alpa">Alpa (Tanpa Keterangan)</option>
                </select>
            </div>

            <!-- Jam Masuk & Jam Pulang -->
            <div id="manualTimeRow" class="grid grid-cols-2 gap-space-md">
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Jam Masuk (WIB)</label>
                    <input type="time" name="time_in" value="07:00"
                           class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-mono font-bold text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Jam Pulang (WIB)</label>
                    <input type="time" name="time_out" value="14:30"
                           class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-mono font-bold text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                </div>
            </div>

            <!-- Keterangan / Alasan -->
            <div>
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Keterangan / Alasan Manual</label>
                <textarea name="notes" rows="2" placeholder="Contoh: Guru lupa membawa HP / ponsel mati, hadir pukul 07.00 WIB..."
                          class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-outline border border-surface-container focus:border-primary focus:outline-none"></textarea>
            </div>

            <!-- Tombol Bawah -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-surface-container">
                <button type="button" onclick="closeManualModal()"
                        class="px-space-md py-2 rounded-lg bg-surface-container text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-space-lg py-2 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm hover:opacity-95 transition-all">
                    <span class="material-symbols-outlined text-[18px]">check</span>
                    <span>Simpan Absensi Manual</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- Modal Edit Presensi -->
<div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full p-space-lg shadow-xl border border-surface-container flex flex-col gap-space-md animate-scaleUp">
        
        <div class="flex items-center justify-between pb-3 border-b border-surface-container">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary-container text-on-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">edit_note</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Edit Catatan Presensi</h3>
            </div>
            <button type="button" onclick="closeEditModal()"
                    class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="editForm" method="POST" class="flex flex-col gap-space-md">
            @csrf
            @method('PUT')

            <div class="p-3 bg-surface-container-low rounded-xl">
                <span class="block font-label-sm text-label-sm text-on-surface-variant">Nama Guru:</span>
                <span id="editTeacherName" class="font-headline-sm text-headline-sm text-on-surface"></span>
            </div>

            <div>
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Status Kehadiran</label>
                <select name="status" id="editStatus" required
                        class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-headline-sm text-headline-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    <option value="hadir">Hadir (Tepat Waktu)</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="izin">Izin Dinas</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpa">Alpa</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-space-md">
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Jam Masuk (WIB)</label>
                    <input type="time" name="time_in" id="editTimeIn"
                           class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-mono font-bold text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                </div>
                <div>
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Jam Pulang (WIB)</label>
                    <input type="time" name="time_out" id="editTimeOut"
                           class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-mono font-bold text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Menit Keterlambatan</label>
                <input type="number" name="late_minutes" id="editLateMinutes" min="0"
                       class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-mono font-bold text-amber-700 border border-surface-container focus:border-primary focus:outline-none">
            </div>

            <div>
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Keterangan / Alasan</label>
                <textarea name="notes" id="editNotes" rows="2"
                          class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-surface-container">
                <button type="button" onclick="closeEditModal()"
                        class="px-space-md py-2 rounded-lg bg-surface-container text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-space-lg py-2 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm hover:opacity-95 transition-all">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    function openManualModal() {
        document.getElementById('manualModal').classList.remove('hidden');
    }

    function closeManualModal() {
        document.getElementById('manualModal').classList.add('hidden');
    }

    function toggleTimeFields() {
        const status = document.getElementById('manualStatusSelect').value;
        const timeRow = document.getElementById('manualTimeRow');
        if (['izin', 'sakit', 'alpa'].includes(status)) {
            timeRow.classList.add('opacity-40');
        } else {
            timeRow.classList.remove('opacity-40');
        }
    }

    function openEditModal(item) {
        document.getElementById('editForm').action = `/admin/riwayat/${item.id}`;
        document.getElementById('editTeacherName').textContent = item.teacher.name;
        document.getElementById('editStatus').value = item.status;
        document.getElementById('editTimeIn').value = item.time_in ? item.time_in.substring(0, 5) : '';
        document.getElementById('editTimeOut').value = item.time_out ? item.time_out.substring(0, 5) : '';
        document.getElementById('editLateMinutes').value = item.late_minutes || 0;
        document.getElementById('editNotes').value = item.notes || '';
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    function viewFacePhoto(url, name, time) {
        Swal.fire({
            title: 'Verifikasi Face ID',
            text: `${name} (Presensi: ${time})`,
            imageUrl: url,
            imageWidth: 280,
            imageHeight: 280,
            imageAlt: 'Snapshot Face ID',
            confirmButtonColor: '#004ac6',
            confirmButtonText: 'Tutup'
        });
    }
</script>
@endpush
@endsection
