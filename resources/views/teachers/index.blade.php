@extends('layouts.admin')

@section('title', 'Data Master Guru & Tenaga Kependidikan')

@section('content')
<div class="flex flex-col w-full gap-space-lg">

    <!-- Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-space-md bg-surface-container-lowest p-space-lg rounded-xl shadow-sm border border-surface-container">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-sm flex-wrap">
                <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary-container flex items-center justify-center shadow-sm">
                    <span class="material-symbols-outlined text-[24px]">badge</span>
                </div>
                <div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface">Data Master Guru & Tendik</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        Kelola data profil lengkap dewan guru, riwayat kepegawaian, dan akun login portal mandiri.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-space-sm flex-wrap">
            <button type="button" onclick="openAddTeacherModal()"
                    class="inline-flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-primary text-on-primary font-label-lg text-label-lg shadow-sm hover:opacity-95 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                <span>Tambah Guru & Buat Akun</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
    <div class="flex items-center gap-space-sm p-space-md bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-xs">
        <span class="material-symbols-outlined text-[22px] text-emerald-600">check_circle</span>
        <span class="font-label-lg text-label-lg">{{ session('success') }}</span>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="flex items-start gap-space-sm p-space-md bg-red-50 border border-red-200 text-red-800 rounded-xl shadow-xs">
        <span class="material-symbols-outlined text-[22px] text-red-600 mt-0.5">warning</span>
        <div class="flex flex-col">
            <span class="font-label-lg text-label-lg font-bold">Terdapat kesalahan isian data:</span>
            <ul class="list-disc list-inside font-body-sm text-body-sm text-red-700 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- 4 KPI Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Total Tenaga Pendidik</span>
                <span class="material-symbols-outlined text-primary text-[20px]">groups</span>
            </div>
            <div class="mt-2 font-headline-xl text-headline-xl text-on-surface">{{ $totalTeachers }}</div>
            <span class="font-body-sm text-body-sm text-on-surface-variant">Dewan Guru & Tendik</span>
        </div>

        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Guru Tetap (GTY/PNS)</span>
                <span class="material-symbols-outlined text-emerald-600 text-[20px]">verified_user</span>
            </div>
            <div class="mt-2 font-headline-xl text-headline-xl text-emerald-600">{{ $tetapCount }}</div>
            <span class="font-body-sm text-body-sm text-emerald-700">Status Tetap</span>
        </div>

        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Honorer / GTT</span>
                <span class="material-symbols-outlined text-amber-600 text-[20px]">contract</span>
            </div>
            <div class="mt-2 font-headline-xl text-headline-xl text-amber-600">{{ $honorerCount }}</div>
            <span class="font-body-sm text-body-sm text-amber-700">Status Honorer</span>
        </div>

        <div class="flex flex-col justify-between p-space-md bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container">
            <div class="flex items-center justify-between">
                <span class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-wider">Aktif Mengajar</span>
                <span class="material-symbols-outlined text-indigo-600 text-[20px]">school</span>
            </div>
            <div class="mt-2 font-headline-xl text-headline-xl text-indigo-600">{{ $activeTeachers }}</div>
            <span class="font-body-sm text-body-sm text-indigo-700">Akun Siap Presensi</span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
        <form method="GET" action="{{ route('admin.teachers.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search Text -->
            <div class="lg:col-span-2">
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Cari Guru</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">search</span>
                    <input type="text" name="search" value="{{ $search }}" 
                           placeholder="Cari nama guru, NIP, NUPTK, mata pelajaran..."
                           class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface placeholder:text-outline border border-surface-container focus:border-primary focus:outline-none">
                </div>
            </div>

            <!-- Filter Status Kepegawaian -->
            <div>
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Status Kepegawaian</label>
                <select name="employment_status" onchange="this.form.submit()"
                        class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    <option value="">Semua Status Pegawai</option>
                    <option value="Guru Tetap Yayasan (GTY)" {{ $employmentStatus === 'Guru Tetap Yayasan (GTY)' ? 'selected' : '' }}>Guru Tetap Yayasan (GTY)</option>
                    <option value="Guru Tidak Tetap (GTT) / Honorer" {{ $employmentStatus === 'Guru Tidak Tetap (GTT) / Honorer' ? 'selected' : '' }}>Guru Tidak Tetap / Honorer</option>
                    <option value="PNS / ASN" {{ $employmentStatus === 'PNS / ASN' ? 'selected' : '' }}>PNS / ASN</option>
                    <option value="PPPK" {{ $employmentStatus === 'PPPK' ? 'selected' : '' }}>PPPK</option>
                </select>
            </div>

            <!-- Filter Keaktifan & Tombol -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Status Aktif</label>
                    <select name="status" onchange="this.form.submit()"
                            class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                        <option value="">Semua Status</option>
                        <option value="1" {{ $status === '1' ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ $status === '0' ? 'selected' : '' }}>Non-Aktif / Cuti</option>
                    </select>
                </div>

                <a href="{{ route('admin.teachers.index') }}" 
                   class="py-2 px-3 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface-variant font-label-md text-label-md transition-colors h-[38px] flex items-center justify-center"
                   title="Reset Filter">
                    <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Data Master Guru -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
        <div class="p-space-md border-b border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm bg-surface-container-low">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">badge</span>
                <span class="font-headline-sm text-headline-sm text-on-surface">Daftar Tenaga Pendidik</span>
            </div>
            <span class="font-body-sm text-body-sm text-on-surface-variant">
                Total terdaftar: {{ $teachers->total() }} Guru & Tendik
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-surface-container text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider border-b border-surface-container-high">
                        <th class="py-3 px-space-md w-12 text-center">#</th>
                        <th class="py-3 px-space-md">Profil Guru & NIP</th>
                        <th class="py-3 px-space-md">Mata Pelajaran & Tugas</th>
                        <th class="py-3 px-space-md">Kepegawaian</th>
                        <th class="py-3 px-space-md">Kontak WhatsApp</th>
                        <th class="py-3 px-space-md">Akun Login</th>
                        <th class="py-3 px-space-md text-center">Status</th>
                        <th class="py-3 px-space-md text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container">
                    @forelse($teachers as $index => $t)
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="py-space-md px-space-md text-center font-mono font-medium text-on-surface-variant">
                            {{ $teachers->firstItem() + $index }}
                        </td>

                        <!-- Profil & NIP -->
                        <td class="py-space-md px-space-md">
                            <div class="flex items-center gap-space-sm">
                                @if($t->photo_url)
                                    <img src="{{ $t->photo_url }}" alt="{{ $t->name }}"
                                         class="w-11 h-11 rounded-full object-cover shadow-xs border border-surface-container shrink-0">
                                @else
                                    <div class="w-11 h-11 rounded-full bg-primary-container text-on-primary-container font-headline-sm flex items-center justify-center shrink-0 shadow-xs">
                                        {{ $t->initials }}
                                    </div>
                                @endif
                                <div class="flex flex-col min-w-0">
                                    <span class="font-label-lg text-label-lg text-on-surface font-semibold truncate">{{ $t->name }}</span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant flex items-center gap-1.5 flex-wrap">
                                        <span>NIP. {{ $t->nip ?: '-' }}</span>
                                        @if($t->nuptk)
                                            <span class="text-outline">• NUPTK. {{ $t->nuptk }}</span>
                                        @endif
                                        <span class="px-1.5 py-0.2 rounded bg-surface-container font-mono text-[10px]">
                                            {{ $t->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Mata Pelajaran & Tugas -->
                        <td class="py-space-md px-space-md">
                            <div class="flex flex-col">
                                <span class="font-label-md text-label-md text-on-surface font-medium">
                                    {{ $t->subject ?: 'Tenaga Kependidikan' }}
                                </span>
                                @if($t->class_duty)
                                    <span class="font-body-sm text-body-sm text-primary flex items-center gap-1 mt-0.5">
                                        <span class="material-symbols-outlined text-[14px]">assignment_ind</span>
                                        <span>{{ $t->class_duty }}</span>
                                    </span>
                                @else
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">Guru Mata Pelajaran</span>
                                @endif
                            </div>
                        </td>

                        <!-- Status Kepegawaian & Pendidikan -->
                        <td class="py-space-md px-space-md">
                            <div class="flex flex-col gap-0.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-label-sm text-label-sm font-semibold w-max
                                    {{ str_contains($t->employment_status, 'Tetap') ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ str_contains($t->employment_status, 'Honorer') || str_contains($t->employment_status, 'GTT') ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ str_contains($t->employment_status, 'PNS') || str_contains($t->employment_status, 'PPPK') ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ !str_contains($t->employment_status, 'Tetap') && !str_contains($t->employment_status, 'Honorer') && !str_contains($t->employment_status, 'GTT') && !str_contains($t->employment_status, 'PNS') ? 'bg-surface-container text-on-surface' : '' }}">
                                    {{ $t->employment_status ?: 'Guru Tetap' }}
                                </span>
                                @if($t->education)
                                    <span class="font-body-sm text-body-sm text-on-surface-variant">
                                        {{ $t->education }}
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Kontak WhatsApp -->
                        <td class="py-space-md px-space-md">
                            @if($t->phone)
                                @php
                                    $cleanPhone = preg_replace('/[^0-9]/', '', $t->phone);
                                    if (str_starts_with($cleanPhone, '0')) {
                                        $cleanPhone = '62' . substr($cleanPhone, 1);
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank"
                                   class="inline-flex items-center gap-1 text-emerald-700 font-label-md text-label-md hover:underline bg-emerald-50 px-2 py-1 rounded-lg">
                                    <span class="material-symbols-outlined text-[16px] text-emerald-600">chat</span>
                                    <span>{{ $t->phone }}</span>
                                </a>
                            @else
                                <span class="text-outline text-body-sm">-</span>
                            @endif
                        </td>

                        <!-- Akun Login Guru & Face ID -->
                        <td class="py-space-md px-space-md">
                            @if($t->user)
                                <div class="flex flex-col gap-1">
                                    <span class="font-mono text-label-sm text-label-sm text-on-surface font-semibold flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] text-primary">person</span>
                                        <span>{{ $t->user->username }}</span>
                                    </span>
                                    <span class="font-body-sm text-body-sm text-on-surface-variant truncate max-w-[150px]">
                                        {{ $t->user->email }}
                                    </span>
                                    @if($t->is_face_registered)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full w-max"
                                              title="Wajah didaftarkan pada {{ $t->face_registered_at ? $t->face_registered_at->translatedFormat('d M Y, H:i') : '-' }}">
                                            <span class="material-symbols-outlined text-[12px] text-emerald-600">verified</span>
                                            <span>Face ID Aktif</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full w-max"
                                              title="Guru belum melakukan pendaftaran wajah di aplikasi">
                                            <span class="material-symbols-outlined text-[12px] text-amber-600">face</span>
                                            <span>Face ID Belum</span>
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 font-label-sm text-label-sm">
                                    Belum Ada Akun
                                </span>
                            @endif
                        </td>

                        <!-- Status Aktif -->
                        <td class="py-space-md px-space-md text-center">
                            <form action="{{ route('admin.teachers.toggle', $t->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold transition-all hover:opacity-80
                                        {{ $t->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-200 text-slate-700 hover:bg-slate-300' }}"
                                        title="Klik untuk mengubah status aktif/non-aktif">
                                    {{ $t->is_active ? 'Aktif' : 'Non-Aktif' }}
                                </button>
                            </form>
                        </td>

                        <!-- Aksi -->
                        <td class="py-space-md px-space-md text-right">
                            <div class="flex items-center justify-end gap-1">
                                <!-- Tombol Lihat Detail Profil -->
                                <button type="button" onclick="showProfileModal({{ json_encode($t) }}, {{ json_encode($t->user) }})"
                                        class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors"
                                        title="Lihat Profil Lengkap">
                                    <span class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>

                                <!-- Tombol Edit -->
                                <button type="button" onclick="openEditTeacherModal({{ json_encode($t) }}, {{ json_encode($t->user) }})"
                                        class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors"
                                        title="Edit Profil & Akun">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>

                                <!-- Tombol Reset Face ID (Jika sudah terdaftar) -->
                                @if($t->is_face_registered)
                                    <form action="{{ route('admin.teachers.reset_face', $t->id) }}" method="POST"
                                          data-confirm="Reset pendaftaran biometrik wajah untuk <b>{{ addslashes($t->name) }}</b>?<br><br><span class='text-xs text-slate-500'>Guru akan diminta melakukan foto ulang wajahnya saat login di aplikasi portal.</span>"
                                          data-confirm-title="Reset Face ID Guru"
                                          data-confirm-icon="warning"
                                          data-confirm-color="#d97706"
                                          data-confirm-btn="Ya, Reset Wajah"
                                          class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" 
                                                class="p-1.5 rounded-lg text-on-surface-variant hover:bg-amber-50 hover:text-amber-700 transition-colors"
                                                title="Reset Wajah Terdaftar (Wajibkan Foto Ulang)">
                                            <span class="material-symbols-outlined text-[18px]">no_photography</span>
                                        </button>
                                    </form>
                                @endif

                                <!-- Tombol Reset Password -->
                                <button type="button" onclick="openResetPasswordModal({{ $t->id }}, '{{ addslashes($t->name) }}')"
                                        class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-amber-600 transition-colors"
                                        title="Reset Password Akun">
                                    <span class="material-symbols-outlined text-[18px]">lock_reset</span>
                                </button>

                                <!-- Tombol Hapus -->
                                <form action="{{ route('admin.teachers.destroy', $t->id) }}" method="POST"
                                      data-confirm="Apakah Anda yakin ingin menghapus data guru <b>{{ addslashes($t->name) }}</b> beserta akun loginnya?<br><br><span class='text-xs text-red-500 font-semibold'>Tindakan ini permanen dan tidak dapat dibatalkan.</span>"
                                      data-confirm-title="Hapus Data Guru"
                                      data-confirm-icon="error"
                                      data-confirm-color="#dc2626"
                                      data-confirm-btn="Ya, Hapus Data"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-1.5 rounded-lg text-on-surface-variant hover:bg-red-50 hover:text-red-600 transition-colors"
                                            title="Hapus Guru">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[48px] text-outline mb-2">person_off</span>
                            <p class="font-headline-sm text-headline-sm text-on-surface">Tidak ada data guru yang ditemukan.</p>
                            <p class="font-body-sm text-body-sm mt-1">
                                Klik tombol <strong>Tambah Guru & Buat Akun</strong> di atas untuk mendaftarkan guru baru.
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
        <div class="p-space-md bg-surface-container-low border-t border-surface-container">
            {{ $teachers->links() }}
        </div>
        @endif
    </div>

</div>

<!-- MODAL 1: Tambah Guru & Buat Akun Baru -->
<div id="addTeacherModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-2xl max-w-2xl w-full p-space-lg shadow-xl border border-surface-container flex flex-col gap-space-md max-h-[90vh] overflow-y-auto animate-scaleUp">
        
        <div class="flex items-center justify-between pb-3 border-b border-surface-container">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary-container text-on-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                </div>
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Tambah Guru & Akun Login</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">Lengkapi profil dan tentukan akses akun login guru.</p>
                </div>
            </div>
            <button type="button" onclick="closeAddTeacherModal()"
                    class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-space-md">
            @csrf

            <!-- Section 1: Biodata Guru -->
            <div>
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold block mb-2">
                    1. Identitas Pribadi Guru
                </span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <!-- Nama Lengkap -->
                    <div class="sm:col-span-2">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">
                            Nama Lengkap & Gelar <span class="text-error">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="Contoh: Drs. H. Muhammad Ilyas, M.Ag."
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <!-- NIP -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">
                            NIP (Nomor Induk Pegawai)
                        </label>
                        <input type="text" name="nip" placeholder="19740512 200112 1 002"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <!-- NUPTK -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">
                            NUPTK (Nomor Unik Pendidik)
                        </label>
                        <input type="text" name="nuptk" placeholder="1234567890123456"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">
                            Jenis Kelamin <span class="text-error">*</span>
                        </label>
                        <select name="gender" required
                                class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <!-- No WhatsApp / HP -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">
                            Nomor WhatsApp / HP
                        </label>
                        <input type="text" name="phone" placeholder="Contoh: 08123456789"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <!-- Tempat & Tanggal Lahir -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Tempat Lahir</label>
                        <input type="text" name="birth_place" placeholder="Contoh: Jombang"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <!-- Alamat -->
                    <div class="sm:col-span-2">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Alamat Tinggal</label>
                        <textarea name="address" rows="2" placeholder="Alamat domisili lengkap guru..."
                                  class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none"></textarea>
                    </div>

                    <!-- Foto Profil -->
                    <div class="sm:col-span-2">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Upload Foto Profil Guru</label>
                        <input type="file" name="photo" accept="image/*"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-surface-container-highest file:text-primary file:font-semibold">
                        <span class="font-body-sm text-[11px] text-on-surface-variant mt-1 block">Format: JPG, PNG, WEBP (Maksimal 3MB). Foto akan tampil di monitoring dashboard dan portal guru.</span>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="h-px bg-surface-container"></div>

            <!-- Section 2: Data Akademik & Sekolah -->
            <div>
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary font-bold block mb-2">
                    2. Data Akademik & Penugasan Sekolah
                </span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <!-- Mata Pelajaran -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Mata Pelajaran Ampuan</label>
                        <input type="text" name="subject" placeholder="Contoh: Matematika, Fisika, PAI"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <!-- Status Kepegawaian -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">
                            Status Kepegawaian <span class="text-error">*</span>
                        </label>
                        <select name="employment_status" required
                                class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                            <option value="Guru Tetap Yayasan (GTY)">Guru Tetap Yayasan (GTY)</option>
                            <option value="Guru Tidak Tetap (GTT) / Honorer">Guru Tidak Tetap / Honorer</option>
                            <option value="PNS / ASN">PNS / ASN</option>
                            <option value="PPPK">PPPK</option>
                            <option value="Tenaga Kependidikan (Tendik)">Tenaga Kependidikan (Tendik)</option>
                        </select>
                    </div>

                    <!-- Pendidikan Terakhir -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Pendidikan Terakhir</label>
                        <input type="text" name="education" placeholder="Contoh: S1 Pendidikan Fisika"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <!-- Tugas Tambahan / Wali Kelas -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Tugas Tambahan / Wali Kelas</label>
                        <input type="text" name="class_duty" placeholder="Contoh: Wali Kelas XII MIPA 1"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="h-px bg-surface-container"></div>

            <!-- Section 3: Akun Login Portal Guru -->
            <div class="p-space-md bg-surface-container-low rounded-xl border border-surface-container">
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold block mb-1">
                    3. Pengaturan Akun Login Guru
                </span>
                <p class="font-body-sm text-body-sm text-on-surface-variant mb-3">
                    Admin yang menentukan akun login. Guru dapat menggunakannya untuk login mandiri di portal absen.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
                    <!-- Username -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Username Login</label>
                        <input type="text" name="username" placeholder="Otomatis NIP jika kosong"
                               class="w-full px-3 py-2 bg-surface-container-lowest rounded-lg font-mono text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Email Login</label>
                        <input type="email" name="email" placeholder="Otomatis jika kosong"
                               class="w-full px-3 py-2 bg-surface-container-lowest rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <!-- Password Awal -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Password Awal</label>
                        <input type="text" name="password" value="guru123" placeholder="Default: guru123"
                               class="w-full px-3 py-2 bg-surface-container-lowest rounded-lg font-mono font-bold text-body-sm text-primary border border-surface-container focus:border-primary focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Tombol Bawah -->
            <div class="flex items-center justify-end gap-2 pt-2 border-t border-surface-container">
                <button type="button" onclick="closeAddTeacherModal()"
                        class="px-space-md py-2 rounded-lg bg-surface-container text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-1.5 px-space-lg py-2 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm hover:opacity-95 transition-all">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan Data & Akun Guru</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- MODAL 2: Edit Guru & Akun -->
<div id="editTeacherModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-2xl max-w-2xl w-full p-space-lg shadow-xl border border-surface-container flex flex-col gap-space-md max-h-[90vh] overflow-y-auto animate-scaleUp">
        
        <div class="flex items-center justify-between pb-3 border-b border-surface-container">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary-container text-on-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Edit Profil Guru & Akun</h3>
            </div>
            <button type="button" onclick="closeEditTeacherModal()"
                    class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="editTeacherForm" method="POST" enctype="multipart/form-data" class="flex flex-col gap-space-md">
            @csrf
            @method('PUT')

            <!-- Identitas Pribadi -->
            <div>
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold block mb-2">
                    1. Identitas Guru
                </span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <div class="sm:col-span-2">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Nama Lengkap & Gelar</label>
                        <input type="text" name="name" id="edit_name" required
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">NIP</label>
                        <input type="text" name="nip" id="edit_nip"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">NUPTK</label>
                        <input type="text" name="nuptk" id="edit_nuptk"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Jenis Kelamin</label>
                        <select name="gender" id="edit_gender" required
                                class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Nomor WhatsApp / HP</label>
                        <input type="text" name="phone" id="edit_phone"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Tempat Lahir</label>
                        <input type="text" name="birth_place" id="edit_birth_place"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Tanggal Lahir</label>
                        <input type="date" name="birth_date" id="edit_birth_date"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Alamat Tinggal</label>
                        <textarea name="address" id="edit_address" rows="2"
                                  class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none"></textarea>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Ganti Foto Profil (Opsional)</label>
                        <input type="file" name="photo" accept="image/*"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-surface-container-highest file:text-primary file:font-semibold">
                    </div>
                </div>
            </div>

            <!-- Penugasan Sekolah -->
            <div>
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-secondary font-bold block mb-2">
                    2. Data Akademik
                </span>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Mata Pelajaran</label>
                        <input type="text" name="subject" id="edit_subject"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Status Kepegawaian</label>
                        <select name="employment_status" id="edit_employment_status" required
                                class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                            <option value="Guru Tetap Yayasan (GTY)">Guru Tetap Yayasan (GTY)</option>
                            <option value="Guru Tidak Tetap (GTT) / Honorer">Guru Tidak Tetap / Honorer</option>
                            <option value="PNS / ASN">PNS / ASN</option>
                            <option value="PPPK">PPPK</option>
                            <option value="Tenaga Kependidikan (Tendik)">Tenaga Kependidikan (Tendik)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Pendidikan Terakhir</label>
                        <input type="text" name="education" id="edit_education"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Tugas Tambahan / Wali Kelas</label>
                        <input type="text" name="class_duty" id="edit_class_duty"
                               class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Status Keaktifan Mengajar</label>
                        <select name="is_active" id="edit_is_active" required
                                class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                            <option value="1">Aktif Mengajar</option>
                            <option value="0">Non-Aktif / Cuti</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Akun Login Guru -->
            <div class="p-space-md bg-surface-container-low rounded-xl border border-surface-container">
                <span class="font-label-sm text-label-sm uppercase tracking-wider text-primary font-bold block mb-2">
                    3. Pengaturan Akun Login
                </span>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Username Login</label>
                        <input type="text" name="username" id="edit_username"
                               class="w-full px-3 py-2 bg-surface-container-lowest rounded-lg font-mono text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Email Login</label>
                        <input type="email" name="email" id="edit_email"
                               class="w-full px-3 py-2 bg-surface-container-lowest rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>

                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Ganti Password (Kosongkan jika tetap)</label>
                        <input type="password" name="password" placeholder="Isi untuk ubah password"
                               class="w-full px-3 py-2 bg-surface-container-lowest rounded-lg font-mono text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-surface-container">
                <button type="button" onclick="closeEditTeacherModal()"
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

<!-- MODAL 3: Detail Profil Lengkap Guru -->
<div id="profileDetailModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full p-space-lg shadow-xl border border-surface-container flex flex-col gap-space-md max-h-[90vh] overflow-y-auto animate-scaleUp">
        
        <div class="flex items-center justify-between pb-3 border-b border-surface-container">
            <h3 class="font-headline-sm text-headline-sm text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">account_circle</span>
                <span>Biodata Lengkap Guru</span>
            </h3>
            <button type="button" onclick="closeProfileModal()"
                    class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="flex flex-col items-center text-center p-4 bg-surface-container-low rounded-xl">
            <div id="detail_photo_container" class="w-20 h-20 rounded-full overflow-hidden mb-2 shadow-sm border-2 border-primary">
                <!-- Image or Initial -->
            </div>
            <h4 id="detail_name" class="font-headline-md text-headline-md text-on-surface font-bold"></h4>
            <span id="detail_subject" class="font-label-md text-label-md text-primary font-semibold mt-0.5"></span>
            <span id="detail_status_badge" class="mt-2 px-3 py-0.5 rounded-full text-label-sm font-label-sm font-semibold"></span>
        </div>

        <div class="space-y-2 text-body-sm font-body-sm divide-y divide-surface-container">
            <div class="flex justify-between py-1.5">
                <span class="text-on-surface-variant">NIP:</span>
                <strong id="detail_nip" class="text-on-surface font-mono"></strong>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-on-surface-variant">NUPTK:</span>
                <strong id="detail_nuptk" class="text-on-surface font-mono"></strong>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-on-surface-variant">Jenis Kelamin:</span>
                <span id="detail_gender" class="text-on-surface"></span>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-on-surface-variant">Tugas / Wali Kelas:</span>
                <span id="detail_class_duty" class="text-on-surface font-medium"></span>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-on-surface-variant">Pendidikan:</span>
                <span id="detail_education" class="text-on-surface"></span>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-on-surface-variant">Tempat, Tgl Lahir:</span>
                <span id="detail_ttl" class="text-on-surface"></span>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-on-surface-variant">No WhatsApp:</span>
                <span id="detail_phone" class="text-on-surface"></span>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-on-surface-variant">Alamat:</span>
                <span id="detail_address" class="text-on-surface text-right max-w-xs"></span>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-on-surface-variant">Username Login:</span>
                <strong id="detail_username" class="text-primary font-mono"></strong>
            </div>
            <div class="flex justify-between py-1.5">
                <span class="text-on-surface-variant">Email Login:</span>
                <span id="detail_email" class="text-on-surface font-mono text-xs"></span>
            </div>
            <div class="flex justify-between py-1.5 items-center">
                <span class="text-on-surface-variant">Status Face ID:</span>
                <span id="detail_face_id" class="text-xs font-semibold px-2 py-0.5 rounded-full"></span>
            </div>
        </div>

        <div class="pt-2 border-t border-surface-container flex justify-end">
            <button type="button" onclick="closeProfileModal()"
                    class="px-space-lg py-2 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm">
                Tutup
            </button>
        </div>

    </div>
</div>

<!-- MODAL 4: Reset Password Akun Guru -->
<div id="resetPasswordModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-2xl max-w-md w-full p-space-lg shadow-xl border border-surface-container flex flex-col gap-space-md animate-scaleUp">
        
        <div class="flex items-center justify-between pb-3 border-b border-surface-container">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Reset Password Guru</h3>
            </div>
            <button type="button" onclick="closeResetPasswordModal()"
                    class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form id="resetPasswordForm" method="POST" class="flex flex-col gap-space-md">
            @csrf

            <p class="font-body-md text-body-md text-on-surface-variant">
                Anda akan me-reset password akun login untuk <strong id="resetTeacherName" class="text-on-surface"></strong>.
            </p>

            <div>
                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Password Baru</label>
                <input type="text" name="new_password" value="guru123" required
                       class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-mono font-bold text-headline-sm text-primary border border-surface-container focus:border-primary focus:outline-none">
                <span class="font-body-sm text-[11px] text-on-surface-variant mt-1 block">Default: guru123. Guru dapat mengubahnya setelah login.</span>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-surface-container">
                <button type="button" onclick="closeResetPasswordModal()"
                        class="px-space-md py-2 rounded-lg bg-surface-container text-on-surface font-label-md text-label-md hover:bg-surface-container-high transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-space-lg py-2 rounded-lg bg-amber-600 text-white font-label-md text-label-md shadow-sm hover:opacity-95 transition-all">
                    Reset Password Sekarang
                </button>
            </div>
        </form>

    </div>
</div>

@push('scripts')
<script>
    function openAddTeacherModal() {
        document.getElementById('addTeacherModal').classList.remove('hidden');
    }
    function closeAddTeacherModal() {
        document.getElementById('addTeacherModal').classList.add('hidden');
    }

    function openEditTeacherModal(t, user) {
        document.getElementById('editTeacherForm').action = `/admin/guru/${t.id}`;
        document.getElementById('edit_name').value = t.name || '';
        document.getElementById('edit_nip').value = t.nip || '';
        document.getElementById('edit_nuptk').value = t.nuptk || '';
        document.getElementById('edit_gender').value = t.gender || 'L';
        document.getElementById('edit_phone').value = t.phone || '';
        document.getElementById('edit_birth_place').value = t.birth_place || '';
        document.getElementById('edit_birth_date').value = t.birth_date ? t.birth_date.substring(0, 10) : '';
        document.getElementById('edit_address').value = t.address || '';
        document.getElementById('edit_subject').value = t.subject || '';
        document.getElementById('edit_employment_status').value = t.employment_status || 'Guru Tetap Yayasan (GTY)';
        document.getElementById('edit_education').value = t.education || '';
        document.getElementById('edit_class_duty').value = t.class_duty || '';
        document.getElementById('edit_is_active').value = t.is_active ? '1' : '0';

        if (user) {
            document.getElementById('edit_username').value = user.username || '';
            document.getElementById('edit_email').value = user.email || '';
        } else {
            document.getElementById('edit_username').value = t.nip || '';
            document.getElementById('edit_email').value = t.email || '';
        }

        document.getElementById('editTeacherModal').classList.remove('hidden');
    }
    function closeEditTeacherModal() {
        document.getElementById('editTeacherModal').classList.add('hidden');
    }

    function showProfileModal(t, user) {
        document.getElementById('detail_name').textContent = t.name;
        document.getElementById('detail_subject').textContent = (t.subject || 'Tenaga Kependidikan') + ' • ' + (t.class_duty || 'Guru Mata Pelajaran');
        
        const photoContainer = document.getElementById('detail_photo_container');
        if (t.photo) {
            photoContainer.innerHTML = `<img src="/storage/${t.photo}" class="w-full h-full object-cover">`;
        } else {
            photoContainer.innerHTML = `<div class="w-full h-full bg-primary-container text-on-primary-container font-headline-md flex items-center justify-center font-bold">${t.name.substring(0, 2).toUpperCase()}</div>`;
        }

        const badge = document.getElementById('detail_status_badge');
        badge.textContent = t.employment_status || 'Guru Tetap';
        badge.className = 'mt-2 px-3 py-0.5 rounded-full text-label-sm font-label-sm font-semibold ' + 
            (t.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700');

        document.getElementById('detail_nip').textContent = t.nip || '-';
        document.getElementById('detail_nuptk').textContent = t.nuptk || '-';
        document.getElementById('detail_gender').textContent = t.gender === 'L' ? 'Laki-laki' : 'Perempuan';
        document.getElementById('detail_class_duty').textContent = t.class_duty || '-';
        document.getElementById('detail_education').textContent = t.education || '-';
        document.getElementById('detail_ttl').textContent = (t.birth_place ? t.birth_place + ', ' : '') + (t.birth_date ? t.birth_date.substring(0, 10) : '-');
        document.getElementById('detail_phone').textContent = t.phone || '-';
        document.getElementById('detail_address').textContent = t.address || '-';
        document.getElementById('detail_username').textContent = user ? user.username : (t.nip || '-');
        document.getElementById('detail_email').textContent = user ? user.email : (t.email || '-');

        const faceEl = document.getElementById('detail_face_id');
        if (t.face_registered_at) {
            faceEl.className = 'text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 flex items-center gap-1';
            faceEl.innerHTML = '<span class="material-symbols-outlined text-[14px]">verified</span> Terdaftar (' + t.face_registered_at.substring(0, 10) + ')';
        } else {
            faceEl.className = 'text-xs font-semibold px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 flex items-center gap-1';
            faceEl.innerHTML = '<span class="material-symbols-outlined text-[14px]">warning</span> Belum Didaftarkan';
        }

        document.getElementById('profileDetailModal').classList.remove('hidden');
    }
    function closeProfileModal() {
        document.getElementById('profileDetailModal').classList.add('hidden');
    }

    function openResetPasswordModal(teacherId, teacherName) {
        document.getElementById('resetPasswordForm').action = `/admin/guru/${teacherId}/reset-password`;
        document.getElementById('resetTeacherName').textContent = teacherName;
        document.getElementById('resetPasswordModal').classList.remove('hidden');
    }
    function closeResetPasswordModal() {
        document.getElementById('resetPasswordModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
