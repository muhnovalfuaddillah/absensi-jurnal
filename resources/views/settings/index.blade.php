@extends('layouts.admin')

@section('title', 'Pengaturan Data Sekolah, Yayasan & Jadwal')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    .leaflet-container {
        font-family: inherit;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col w-full gap-space-lg">

    <!-- Header Banner -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-space-md bg-surface-container-lowest p-space-lg rounded-xl shadow-sm border border-surface-container">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-space-sm flex-wrap">
                <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary-container flex items-center justify-center shadow-sm">
                    <span class="material-symbols-outlined text-[24px]">tune</span>
                </div>
                <div>
                    <h1 class="font-headline-lg text-headline-lg text-on-surface">Pengaturan Data Sekolah, Yayasan & Jadwal</h1>
                    <p class="font-body-md text-body-md text-on-surface-variant">
                        Kelola data profil yayasan, sekolah, logo resmi, koordinat GPS geofence, dan jam masuk-pulang per hari.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-space-sm flex-wrap">
            <!-- Tombol Unduh Template CSV -->
            <a href="{{ route('admin.settings.template') }}" 
               class="inline-flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-surface-container-low text-on-surface font-label-lg text-label-lg hover:bg-surface-container transition-all"
               title="Unduh file Excel/CSV contoh jadwal sekolah">
                <span class="material-symbols-outlined text-[18px] text-tertiary">download</span>
                <span>Unduh Template Jadwal</span>
            </a>

            <!-- Tombol Buka Modal Upload CSV -->
            <button type="button" onclick="document.getElementById('uploadCsvModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-surface-container text-primary font-label-lg text-label-lg hover:bg-surface-container-high transition-all">
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
                <span>Upload CSV Jadwal</span>
            </button>

            <!-- Tombol Simpan Form Pengaturan -->
            <button type="submit" form="mainScheduleForm"
                    class="inline-flex items-center gap-2 px-space-md py-2.5 rounded-lg bg-primary text-on-primary font-label-lg text-label-lg shadow-sm hover:opacity-95 transition-all active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Simpan Seluruh Pengaturan</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
    <div class="flex items-center gap-space-sm p-space-md bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl shadow-xs animate-fadeIn">
        <span class="material-symbols-outlined text-[24px] text-emerald-600">check_circle</span>
        <div class="flex flex-col">
            <span class="font-label-lg text-label-lg font-bold">Berhasil!</span>
            <span class="font-body-sm text-body-sm">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="flex items-center gap-space-sm p-space-md bg-red-50 border border-red-200 text-red-800 rounded-xl shadow-xs">
        <span class="material-symbols-outlined text-[24px] text-red-600">error</span>
        <div class="flex flex-col">
            <span class="font-label-lg text-label-lg font-bold">Terjadi Kesalahan!</span>
            <span class="font-body-sm text-body-sm">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="flex items-start gap-space-sm p-space-md bg-red-50 border border-red-200 text-red-800 rounded-xl shadow-xs">
        <span class="material-symbols-outlined text-[22px] text-red-600 mt-0.5">warning</span>
        <div class="flex flex-col">
            <span class="font-label-lg text-label-lg font-bold">Mohon periksa isian data:</span>
            <ul class="list-disc list-inside font-body-sm text-body-sm text-red-700 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- MAIN FORM PENGATURAN -->
    <form id="mainScheduleForm" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-space-lg">
        @csrf

        <!-- ========================================== -->
        <!-- BAGIAN 1: PROFIL SEKOLAH, YAYASAN & LOGO -->
        <!-- ========================================== -->
        <div id="section-identitas" class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
            <div class="p-space-md bg-surface-container-low border-b border-surface-container flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">domain</span>
                    <div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface">Data Lembaga, Yayasan & Logo Resmi</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Informasi identitas yayasan naungan, detail sekolah, kontak dinas, dan logo aplikasi presensi.
                        </p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold bg-primary/10 text-primary">
                    Profil Resmi
                </span>
            </div>

            <div class="p-space-lg grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
                
                <!-- Kolom Kiri: Logo Aplikasi & Preview (4 Kolom) -->
                <div class="lg:col-span-4 flex flex-col gap-space-md p-space-md rounded-xl bg-surface-container-low border border-surface-container text-center">
                    <span class="font-label-md text-label-md text-on-surface font-bold text-left flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-primary text-[18px]">image</span>
                        <span>Logo Resmi Sekolah / Yayasan</span>
                    </span>

                    <div class="flex flex-col items-center justify-center p-4 bg-surface-container-lowest rounded-xl border border-surface-container shadow-inner">
                        <div class="relative group">
                            <img id="logoPreview" src="{{ $setting->logo_url }}" alt="Logo Sekolah" 
                                 class="w-32 h-32 rounded-2xl object-contain p-2 bg-white border-2 border-primary/20 shadow-md transition-transform group-hover:scale-105"
                                 onerror="this.src='{{ asset('logo.jpg') }}'">
                            <span class="absolute bottom-1 right-1 w-4 h-4 bg-emerald-500 rounded-full ring-2 ring-white"></span>
                        </div>
                        <span class="font-label-sm text-label-sm text-on-surface font-bold mt-3 block">
                            {{ $setting->school_name }}
                        </span>
                        <span class="font-body-sm text-[11px] text-on-surface-variant block">
                            {{ $setting->foundation_name ?: 'Yayasan Barul Ulum' }}
                        </span>
                    </div>

                    <!-- Input Upload File Logo -->
                    <div class="flex flex-col gap-1 text-left">
                        <label class="font-label-sm text-label-sm text-on-surface-variant font-semibold">
                            Pilih Logo Baru (PNG, JPG, WebP)
                        </label>
                        <input type="file" name="logo" id="logoInput" accept="image/png,image/jpeg,image/jpg,image/webp"
                               onchange="previewLogo(this)"
                               class="w-full text-xs text-on-surface file:mr-2 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-on-primary hover:file:opacity-90 cursor-pointer bg-surface-container-lowest rounded-lg border border-surface-container p-1">
                        <span class="font-body-sm text-[11px] text-on-surface-variant mt-1 leading-snug">
                            Format persegi (1:1), resolusi minimal 200x200 px, maks 2MB. Logo ini otomatis tampil di portal guru, dashboard admin, dan kop laporan TPG.
                        </span>
                    </div>
                </div>

                <!-- Kolom Kanan: Detail Isian Profil Sekolah & Yayasan (8 Kolom) -->
                <div class="lg:col-span-8 grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                    
                    <!-- Nama Yayasan -->
                    <div class="sm:col-span-2">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1 font-semibold">
                            Nama Yayasan Naungan <span class="text-error">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">account_balance</span>
                            <input type="text" name="foundation_name" value="{{ old('foundation_name', $setting->foundation_name ?? 'Yayasan Barul Ulum') }}" required
                                   placeholder="Contoh: Yayasan Barul Ulum"
                                   class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-headline-sm text-headline-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                        </div>
                    </div>

                    <!-- Nama Lembaga / Sekolah -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1 font-semibold">
                            Nama Sekolah / Satuan Pendidikan <span class="text-error">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">school</span>
                            <input type="text" name="school_name" value="{{ old('school_name', $setting->school_name) }}" required
                                   placeholder="Contoh: Yayasan Barul Ulum"
                                   class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-headline-sm text-headline-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                        </div>
                    </div>

                    <!-- NPSN -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1 font-semibold">
                            NPSN (Nomor Pokok Sekolah Nasional)
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">badge</span>
                            <input type="text" name="npsn" value="{{ old('npsn', $setting->npsn) }}"
                                   placeholder="Contoh: 20567890"
                                   class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                        </div>
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="sm:col-span-2">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1 font-semibold">
                            Alamat Lengkap Yayasan & Sekolah (Untuk Kop Laporan Resmi)
                        </label>
                        <textarea name="school_address" id="school_address" rows="2" 
                                  placeholder="Contoh: Jl. KH. Wahab Chasbullah Gg. IV, Tambakberas, Jombang, Jawa Timur"
                                  class="w-full px-3 py-2 bg-surface-container-low rounded-lg font-body-sm text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none">{{ old('school_address', $setting->school_address) }}</textarea>
                    </div>

                    <!-- No. Telepon / WhatsApp -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1 font-semibold">
                            No. Telepon / WhatsApp Resmi Sekolah
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">call</span>
                            <input type="text" name="school_phone" value="{{ old('school_phone', $setting->school_phone) }}"
                                   placeholder="Contoh: (0321) 876543 / 0812xxxxxxxx"
                                   class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                        </div>
                    </div>

                    <!-- Email Resmi -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1 font-semibold">
                            Email Resmi Lembaga
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">mail</span>
                            <input type="email" name="school_email" value="{{ old('school_email', $setting->school_email) }}"
                                   placeholder="Contoh: info@barululum.sch.id"
                                   class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                        </div>
                    </div>

                    <!-- Nama Kepala Sekolah / Pimpinan -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1 font-semibold">
                            Nama Kepala Sekolah / Pimpinan Yayasan
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">person</span>
                            <input type="text" name="headmaster_name" value="{{ old('headmaster_name', $setting->headmaster_name) }}"
                                   placeholder="Contoh: Drs. H. Ahmad Fauzi, M.Pd"
                                   class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                        </div>
                    </div>

                    <!-- NIP / NUPTK Kepala Sekolah -->
                    <div>
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1 font-semibold">
                            NIP / NUPTK Kepala Sekolah
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">id_card</span>
                            <input type="text" name="headmaster_nip" value="{{ old('headmaster_nip', $setting->headmaster_nip) }}"
                                   placeholder="Contoh: 19700101 199503 1 002"
                                   class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                        </div>
                    </div>

                    <!-- Website / Portal -->
                    <div class="sm:col-span-2">
                        <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1 font-semibold">
                            Website / Portal Yayasan
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">language</span>
                            <input type="text" name="website" value="{{ old('website', $setting->website) }}"
                                   placeholder="Contoh: https://barululum.sch.id"
                                   class="w-full pl-9 pr-3 py-2 bg-surface-container-low rounded-lg font-body-md text-body-md text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- BAGIAN 2: ALAMAT SEKOLAH & TITIK GPS GEOFENCE -->
        <!-- ========================================== -->
        <div id="section-lokasi" class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
            <div class="p-space-md bg-surface-container-low border-b border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">radar</span>
                    <div>
                        <h3 class="font-headline-sm text-headline-sm text-on-surface">Titik Lokasi Presensi & Geofence GPS</h3>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Tentukan koordinat gerbang/kampus sekolah dan radius batas jarak absensi guru.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full font-label-sm text-label-sm font-semibold {{ $setting->is_geofence_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                        {{ $setting->is_geofence_active ? 'Radius Geofence: AKTIF' : 'Radius Geofence: FLEKSIBEL' }}
                    </span>
                </div>
            </div>

            <div class="p-space-lg grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
                
                <!-- Kolom Input (7 Kolom) -->
                <div class="lg:col-span-7 flex flex-col gap-space-md">

                    <!-- Koordinat Latitude & Longitude -->
                    <div class="p-space-md rounded-xl bg-surface-container-low border border-surface-container flex flex-col gap-space-sm">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <span class="font-label-md text-label-md text-on-surface font-bold flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-primary">my_location</span>
                                <span>Titik Koordinat Gerbang / Kampus</span>
                            </span>
                            
                            <!-- Tombol Cerdas: Ambil Lokasi Saya Sekarang -->
                            <button type="button" onclick="getCurrentLocation()"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary text-on-primary font-label-sm text-label-sm hover:opacity-90 shadow-xs transition-all active:scale-95">
                                <span class="material-symbols-outlined text-[16px]">near_me</span>
                                <span>Ambil Lokasi Saya Sekarang (GPS)</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1">
                            <div>
                                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Latitude (Garis Lintang)</label>
                                <input type="number" step="any" name="latitude" id="latitude_input" 
                                       value="{{ old('latitude', $setting->latitude ?? -7.53420000) }}" required
                                       oninput="updateMapFromInputs()"
                                       class="w-full px-3 py-2 bg-surface-container-lowest rounded-lg font-mono text-body-sm font-semibold text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                            </div>
                            <div>
                                <label class="block font-label-sm text-label-sm text-on-surface-variant mb-1">Longitude (Garis Bujur)</label>
                                <input type="number" step="any" name="longitude" id="longitude_input" 
                                       value="{{ old('longitude', $setting->longitude ?? 112.23510000) }}" required
                                       oninput="updateMapFromInputs()"
                                       class="w-full px-3 py-2 bg-surface-container-lowest rounded-lg font-mono text-body-sm font-semibold text-on-surface border border-surface-container focus:border-primary focus:outline-none">
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 pt-1 border-t border-surface-container">
                            <span id="loc_status" class="font-body-sm text-[11px] text-on-surface-variant">
                                💡 Klik tombol biru di atas atau geser pin marker pada peta di sebelah kanan.
                            </span>
                            <a id="btn_google_maps" href="https://www.google.com/maps?q={{ $setting->latitude ?? -7.5342 }},{{ $setting->longitude ?? 112.2351 }}" target="_blank"
                               class="font-label-sm text-label-sm text-primary hover:underline flex items-center gap-1 shrink-0">
                                <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                <span>Cek di Google Maps</span>
                            </a>
                        </div>
                    </div>

                    <!-- Radius Geofence & Toggle Validasi -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-space-md">
                        <div class="p-space-md rounded-xl bg-surface-container-low border border-surface-container">
                            <div class="flex items-center justify-between mb-1">
                                <label class="font-label-md text-label-md text-on-surface font-semibold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[18px] text-tertiary">track_changes</span>
                                    <span>Toleransi Radius (Meter)</span>
                                </label>
                            </div>
                            <input type="number" name="radius_meters" id="radius_input" 
                                   value="{{ old('radius_meters', $setting->radius_meters ?? 120) }}" min="10" max="5000" required
                                   oninput="updateMapFromInputs()"
                                   class="w-full px-3 py-2 bg-surface-container-lowest rounded-lg font-headline-sm text-headline-sm font-mono text-primary border border-surface-container focus:border-primary focus:outline-none">
                            
                            <!-- Preset Cepat Radius -->
                            <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                <span class="font-body-sm text-[11px] text-on-surface-variant">Pilihan:</span>
                                <button type="button" onclick="setRadius(50)" class="px-2 py-0.5 rounded bg-surface-container font-label-sm text-label-sm hover:bg-surface-container-high">50m</button>
                                <button type="button" onclick="setRadius(100)" class="px-2 py-0.5 rounded bg-surface-container font-label-sm text-label-sm hover:bg-surface-container-high">100m</button>
                                <button type="button" onclick="setRadius(120)" class="px-2 py-0.5 rounded bg-surface-container font-label-sm text-label-sm hover:bg-surface-container-high">120m</button>
                                <button type="button" onclick="setRadius(200)" class="px-2 py-0.5 rounded bg-surface-container font-label-sm text-label-sm hover:bg-surface-container-high">200m</button>
                                <button type="button" onclick="setRadius(300)" class="px-2 py-0.5 rounded bg-surface-container font-label-sm text-label-sm hover:bg-surface-container-high">300m</button>
                            </div>
                        </div>

                        <div class="p-space-md rounded-xl bg-surface-container-low border border-surface-container flex flex-col justify-between">
                            <div>
                                <span class="font-label-md text-label-md text-on-surface font-semibold block mb-1">
                                    Validasi Jarak Lokasi
                                </span>
                                <p class="font-body-sm text-[12px] text-on-surface-variant">
                                    Jika aktif, guru wajib berada di dalam radius lingkaran sekolah saat menekan tombol absen dari ponsel.
                                </p>
                            </div>

                            <label class="relative inline-flex items-center cursor-pointer mt-2">
                                <input type="checkbox" name="is_geofence_active" value="1" 
                                       {{ $setting->is_geofence_active ? 'checked' : '' }}
                                       class="sr-only peer">
                                <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                <span class="ml-2 font-label-md text-label-md text-on-surface font-semibold">
                                    Wajibkan Geofence GPS
                                </span>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Kolom Peta Interaktif (5 Kolom) -->
                <div class="lg:col-span-5 flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md text-on-surface font-semibold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[18px] text-primary">map</span>
                            <span>Peta Lokasi & Geofence (Geser Pin)</span>
                        </span>
                        <span class="font-body-sm text-[11px] text-primary font-medium">Geser marker untuk set titik</span>
                    </div>

                    <!-- Map Container -->
                    <div id="schoolMap" class="w-full h-80 rounded-xl border border-surface-container shadow-inner overflow-hidden relative z-0"></div>
                    <span class="font-body-sm text-[11px] text-on-surface-variant text-center block">
                        Lingkaran biru adalah area radius absensi guru (<span id="txt_radius_meter">{{ $setting->radius_meters ?? 120 }}</span> meter).
                    </span>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- BAGIAN 3: INTEGRASI WHATSAPP GATEWAY (NOTIFIKASI ORANG TUA) -->
        <!-- ========================================== -->
        <div id="section-whatsapp" class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
            <div class="p-space-md bg-surface-container-low border-b border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-space-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-500/20 shadow-xs">
                        <span class="material-symbols-outlined text-[24px]">chat</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-headline-sm text-headline-sm text-on-surface">Integrasi WhatsApp Gateway</h3>
                            <span class="px-2.5 py-0.5 rounded-full font-label-sm text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Notifikasi Otomatis Orang Tua
                            </span>
                        </div>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Kirim otomatis pesan peringatan ke WhatsApp orang tua ketika siswa tercatat Alpa / Tidak Hadir.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    @if(!empty($setting->wa_gateway_token))
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                            Gateway Aktif (Otomatis)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-label-sm text-label-sm font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            Mode Cadangan (Tautan wa.me)
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-space-lg flex flex-col gap-space-md">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
                    
                    <!-- Kolom Kiri: Form Konfigurasi Token & URL (7 Kolom) -->
                    <div class="lg:col-span-7 flex flex-col gap-space-md">
                        <!-- Token API Gateway -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block font-label-sm text-label-sm text-on-surface font-semibold">
                                    Token API Gateway <span class="font-normal text-on-surface-variant">(Fonnte / Wablas)</span>
                                </label>
                                <a href="https://fonnte.com" target="_blank" rel="noopener noreferrer" class="text-xs text-primary hover:underline inline-flex items-center gap-1 font-medium">
                                    <span>Buka Fonnte.com</span>
                                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                                </a>
                            </div>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">key</span>
                                <input type="password" id="wa_gateway_token_input" name="wa_gateway_token" 
                                       value="{{ old('wa_gateway_token', $setting->wa_gateway_token) }}"
                                       placeholder="Tempelkan API Token gateway disini..."
                                       class="w-full pl-9 pr-10 py-2.5 bg-surface-container-low rounded-lg font-mono text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none transition-all">
                                <button type="button" onclick="toggleTokenVisibility()" title="Lihat / Sembunyikan Token"
                                        class="absolute right-3 top-2.5 text-on-surface-variant hover:text-on-surface transition-colors">
                                    <span id="token_eye_icon" class="material-symbols-outlined text-[18px]">visibility</span>
                                </button>
                            </div>
                            <p class="font-body-sm text-[11px] text-on-surface-variant mt-1.5">
                                Dapatkan token dari dasbor penyedia gateway WhatsApp (contoh: <a href="https://fonnte.com" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline">fonnte.com</a>).
                            </p>
                        </div>

                        <!-- Endpoint API URL -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block font-label-sm text-label-sm text-on-surface font-semibold">
                                    Endpoint API URL <span class="font-normal text-on-surface-variant">(Opsional)</span>
                                </label>
                                <span class="text-[11px] text-on-surface-variant font-mono">Default: https://api.fonnte.com/send</span>
                            </div>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-on-surface-variant text-[18px]">link</span>
                                <input type="text" name="wa_gateway_url" 
                                       value="{{ old('wa_gateway_url', $setting->wa_gateway_url ?? 'https://api.fonnte.com/send') }}"
                                       placeholder="https://api.fonnte.com/send"
                                       class="w-full pl-9 pr-3 py-2.5 bg-surface-container-low rounded-lg font-mono text-body-sm text-on-surface border border-surface-container focus:border-primary focus:outline-none transition-all">
                            </div>
                            <p class="font-body-sm text-[11px] text-on-surface-variant mt-1.5">
                                Sesuaikan bila menggunakan layanan gateway lain (seperti Wablas) atau server proxy mandiri.
                            </p>
                        </div>
                    </div>

                    <!-- Kolom Kanan: Panduan & Info Operasional (5 Kolom) -->
                    <div class="lg:col-span-5 p-space-md rounded-xl bg-surface-container-low border border-surface-container flex flex-col gap-3">
                        <div class="flex items-center gap-2 text-on-surface font-bold text-xs uppercase tracking-wider">
                            <span class="material-symbols-outlined text-emerald-600 text-[18px]">info</span>
                            <span>Mekanisme Notifikasi WhatsApp</span>
                        </div>

                        <div class="flex flex-col gap-2.5 text-body-sm text-[12px] text-on-surface-variant">
                            <div class="flex items-start gap-2.5">
                                <span class="material-symbols-outlined text-emerald-600 text-[18px] shrink-0 mt-0.5">bolt</span>
                                <div>
                                    <strong class="text-on-surface">Notifikasi Otomatis (Token Terisi):</strong>
                                    <p class="mt-0.5 leading-relaxed">
                                        Saat absensi siswa ditandai <em>Alpa</em>, sistem otomatis menembak API gateway WhatsApp ke nomor orang tua secara instan tanpa perlu tindakan manual.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 pt-2 border-t border-surface-container">
                                <span class="material-symbols-outlined text-primary text-[18px] shrink-0 mt-0.5">touch_app</span>
                                <div>
                                    <strong class="text-on-surface">Mode Cadangan 1-Klik (Tautan wa.me):</strong>
                                    <p class="mt-0.5 leading-relaxed">
                                        Jika token dikosongkan, guru dan admin tetap dapat mengirimkan pesan notifikasi WhatsApp secara instan 1-klik ke nomor orang tua melalui tautan <code>wa.me</code> pada tabel presensi siswa (gratis tanpa biaya langganan).
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5 pt-2 border-t border-surface-container">
                                <span class="material-symbols-outlined text-amber-600 text-[18px] shrink-0 mt-0.5">contact_phone</span>
                                <div>
                                    <strong class="text-on-surface">Format Nomor Telepon:</strong>
                                    <p class="mt-0.5 leading-relaxed">
                                        Pastikan data nomor handphone orang tua/wali murid di menu <em>Data Siswa</em> telah terisi dengan format valid (diawali <code>08...</code> atau <code>62...</code>).
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- BAGIAN 4: JADWAL JAM MASUK & PULANG PER HARI -->
        <!-- ========================================== -->
        @php
            $todayCarbon = now();
            $todaySchedule = $setting->today_schedule ?? $setting->getScheduleForDate($todayCarbon);
            $dayIsoToday = $todayCarbon->dayOfWeekIso;
        @endphp

        <!-- Banner Jadwal Hari Ini -->
        <div class="bg-gradient-to-r from-primary/10 via-primary-container/20 to-surface-container-low p-space-md rounded-xl border border-primary/20 flex flex-col md:flex-row md:items-center justify-between gap-space-md">
            <div class="flex items-center gap-space-md">
                <div class="w-12 h-12 rounded-xl bg-primary text-on-primary flex items-center justify-center shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-[28px]">today</span>
                </div>
                <div class="flex flex-col">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-headline-sm text-headline-sm text-on-surface">Jadwal Aktif Hari Ini: {{ $todayCarbon->translatedFormat('l, d F Y') }}</span>
                        @if($todaySchedule && $todaySchedule->is_active)
                            <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-label-sm text-label-sm font-bold flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                                Hari Kerja Aktif
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 font-label-sm text-label-sm font-bold">
                                Hari Libur
                            </span>
                        @endif
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant">
                        @if($todaySchedule && $todaySchedule->is_active)
                            Jam Masuk: <strong class="text-primary font-mono">{{ substr($todaySchedule->time_in, 0, 5) }} WIB</strong> • 
                            Batas Telat: <strong class="text-amber-700 font-mono">{{ substr($todaySchedule->time_in_late_threshold, 0, 5) }} WIB</strong> • 
                            Jam Pulang: <strong class="text-indigo-700 font-mono">{{ substr($todaySchedule->time_out, 0, 5) }} WIB</strong>
                            @if($todaySchedule->note)
                                <span class="text-on-surface-variant">• ({{ $todaySchedule->note }})</span>
                            @endif
                        @else
                            Hari ini diset sebagai hari libur sekolah. Guru tidak diwajibkan melakukan presensi.
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <span class="font-label-sm text-label-sm text-on-surface-variant">Server WIB:</span>
                <span class="font-mono font-bold text-on-surface px-2.5 py-1 bg-surface-container-lowest rounded-lg border border-surface-container text-label-md">
                    {{ now()->format('H:i:s') }}
                </span>
            </div>
        </div>

        <!-- Preset Cepat Seminggu -->
        <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
            <div class="flex items-center justify-between pb-2 mb-3 border-b border-surface-container">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">magic_button</span>
                    <span class="font-headline-sm text-headline-sm text-on-surface">Template Jadwal Cepat Seminggu (Preset)</span>
                </div>
                <span class="font-body-sm text-body-sm text-on-surface-variant hidden sm:inline">Terapkan pola jam kerja seminggu dengan 1-klik:</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <button type="button" onclick="applyPresetPattern('standard')"
                        class="p-3 text-left rounded-xl bg-surface-container-low hover:bg-surface-container-high border border-surface-container transition-all hover:border-primary group">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md text-primary font-bold">Reguler (Senin Upacara, Jumat Singkat)</span>
                        <span class="material-symbols-outlined text-outline group-hover:text-primary text-[18px]">bolt</span>
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                        Senin: 06:45 - 14:30 | Sel-Kam: 07:00 - 14:30 | Jum: 07:00 - 11:30 | Sab-Min: Libur
                    </p>
                </button>

                <button type="button" onclick="applyPresetPattern('sixday')"
                        class="p-3 text-left rounded-xl bg-surface-container-low hover:bg-surface-container-high border border-surface-container transition-all hover:border-primary group">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md text-secondary font-bold">6 Hari Kerja (Sabtu Masuk)</span>
                        <span class="material-symbols-outlined text-outline group-hover:text-secondary text-[18px]">bolt</span>
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                        Sen-Kam: 07:00 - 14:00 | Jum: 07:00 - 11:30 | Sab: 07:00 - 13:00 | Min: Libur
                    </p>
                </button>

                <button type="button" onclick="applyPresetPattern('fullday')"
                        class="p-3 text-left rounded-xl bg-surface-container-low hover:bg-surface-container-high border border-surface-container transition-all hover:border-primary group">
                    <div class="flex items-center justify-between">
                        <span class="font-label-md text-label-md text-tertiary font-bold">Full Day School</span>
                        <span class="material-symbols-outlined text-outline group-hover:text-tertiary text-[18px]">bolt</span>
                    </div>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">
                        Senin: 06:45 - 15:30 | Sel-Kam: 07:00 - 15:30 | Jum: 07:00 - 14:30 | Sab-Min: Libur
                    </p>
                </button>
            </div>
        </div>

        <!-- Tabel Jadwal Per Hari -->
        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
            <div class="p-space-md bg-surface-container-low border-b border-surface-container flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">calendar_month</span>
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Tabel Jam Masuk & Jam Pulang Per Hari</h3>
                </div>
                <span class="font-body-sm text-body-sm text-on-surface-variant">Setiap hari dapat ditentukan jam masuk, batas telat, dan jam pulang yang berbeda.</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-surface-container text-on-surface-variant font-label-sm text-label-sm uppercase tracking-wider border-b border-surface-container-high">
                            <th class="py-3 px-space-md w-36">Hari</th>
                            <th class="py-3 px-space-md w-36">Status Hari</th>
                            <th class="py-3 px-space-md w-44">Jam Masuk (WIB)</th>
                            <th class="py-3 px-space-md w-44">Batas Telat (WIB)</th>
                            <th class="py-3 px-space-md w-44">Jam Pulang (WIB)</th>
                            <th class="py-3 px-space-md">Keterangan / Catatan Khusus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container">
                        @foreach($schedules as $sched)
                        @php
                            $isTodayRow = ($sched->day_of_week === $dayIsoToday);
                        @endphp
                        <tr class="transition-colors {{ $isTodayRow ? 'bg-primary-container/10 font-medium' : 'hover:bg-surface-container-low' }}" id="row_{{ $sched->day_of_week }}">
                            
                            <!-- Kolom Hari -->
                            <td class="py-space-md px-space-md">
                                <div class="flex items-center gap-2">
                                    <span class="font-headline-sm text-headline-sm text-on-surface">
                                        {{ $sched->day_name }}
                                    </span>
                                    @if($isTodayRow)
                                        <span class="px-2 py-0.5 rounded-full bg-primary text-on-primary font-label-sm text-label-sm shadow-xs">
                                            Hari Ini
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Kolom Status Hari (Aktif/Libur) -->
                            <td class="py-space-md px-space-md">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="schedules[{{ $sched->id }}][is_active]" value="1" 
                                           {{ $sched->is_active ? 'checked' : '' }}
                                           onchange="toggleDayStatus({{ $sched->day_of_week }}, this.checked)"
                                           id="status_{{ $sched->day_of_week }}"
                                           class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                                    <span class="ml-2 font-label-md text-label-md" id="statusLabel_{{ $sched->day_of_week }}">
                                        {{ $sched->is_active ? 'Masuk' : 'Libur' }}
                                    </span>
                                </label>
                            </td>

                            <!-- Kolom Jam Masuk -->
                            <td class="py-space-md px-space-md">
                                <input type="time" name="schedules[{{ $sched->id }}][time_in]" 
                                       value="{{ substr($sched->time_in, 0, 5) }}"
                                       id="timeIn_{{ $sched->day_of_week }}"
                                       class="w-full px-2.5 py-1.5 rounded-lg border border-surface-container-high bg-surface-container-lowest font-mono font-bold text-on-surface focus:border-primary focus:outline-none transition-all">
                            </td>

                            <!-- Kolom Batas Toleransi Masuk -->
                            <td class="py-space-md px-space-md">
                                <input type="time" name="schedules[{{ $sched->id }}][time_in_late_threshold]" 
                                       value="{{ substr($sched->time_in_late_threshold, 0, 5) }}"
                                       id="late_{{ $sched->day_of_week }}"
                                       class="w-full px-2.5 py-1.5 rounded-lg border border-surface-container-high bg-surface-container-lowest font-mono font-bold text-amber-700 focus:border-amber-600 focus:outline-none transition-all">
                            </td>

                            <!-- Kolom Jam Pulang -->
                            <td class="py-space-md px-space-md">
                                <input type="time" name="schedules[{{ $sched->id }}][time_out]" 
                                       value="{{ substr($sched->time_out, 0, 5) }}"
                                       id="timeOut_{{ $sched->day_of_week }}"
                                       class="w-full px-2.5 py-1.5 rounded-lg border border-surface-container-high bg-surface-container-lowest font-mono font-bold text-indigo-700 focus:border-indigo-600 focus:outline-none transition-all">
                            </td>

                            <!-- Kolom Keterangan / Note -->
                            <td class="py-space-md px-space-md">
                                <input type="text" name="schedules[{{ $sched->id }}][note]" 
                                       value="{{ $sched->note }}"
                                       id="note_{{ $sched->day_of_week }}"
                                       placeholder="Contoh: Upacara, Sholat Jumat, dll"
                                       class="w-full px-3 py-1.5 rounded-lg border border-surface-container-high bg-surface-container-lowest font-body-sm text-body-sm text-on-surface placeholder:text-outline focus:border-primary focus:outline-none transition-all">
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer Simpan Action -->
            <div class="p-space-md bg-surface-container-low border-t border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-space-md">
                <div class="flex items-center gap-2 text-on-surface-variant font-body-sm text-body-sm">
                    <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
                    <span>Pengaturan profil lembaga, logo, alamat, geofence, dan jam presensi akan otomatis berlaku langsung.</span>
                </div>

                <div class="flex items-center gap-space-sm justify-end">
                    <button type="reset" class="px-space-md py-2.5 rounded-lg bg-surface-container text-on-surface font-label-lg text-label-lg hover:bg-surface-container-high transition-all">
                        Reset Nilai
                    </button>
                    <button type="submit" class="inline-flex items-center gap-2 px-space-lg py-2.5 rounded-lg bg-primary text-on-primary font-label-lg text-label-lg shadow-sm hover:opacity-95 transition-all active:scale-[0.98]">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Simpan Seluruh Pengaturan</span>
                    </button>
                </div>
            </div>

        </div>
    </form>

</div>

<!-- Modal Upload File CSV -->
<div id="uploadCsvModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-2xl max-w-lg w-full p-space-lg shadow-xl border border-surface-container flex flex-col gap-space-md animate-scaleUp">
        
        <div class="flex items-center justify-between pb-3 border-b border-surface-container">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-primary-container text-on-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">upload_file</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface">Upload File Jadwal (CSV)</h3>
            </div>
            <button type="button" onclick="document.getElementById('uploadCsvModal').classList.add('hidden')"
                    class="p-1 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('admin.settings.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-space-md">
            @csrf

            <p class="font-body-md text-body-md text-on-surface-variant">
                Pilih file <strong>.CSV</strong> hasil editan di Microsoft Excel / Google Sheets untuk memperbarui jadwal hari dan jam secara otomatis sekaligus.
            </p>

            <div class="p-4 rounded-xl border-2 border-dashed border-primary/30 bg-surface-container-low hover:bg-surface-container transition-all flex flex-col items-center justify-center text-center cursor-pointer relative">
                <span class="material-symbols-outlined text-[36px] text-primary mb-1">cloud_upload</span>
                <span class="font-label-lg text-label-lg text-on-surface mb-0.5">Klik untuk memilih file CSV</span>
                <span class="font-body-sm text-body-sm text-on-surface-variant">Format kolom: Hari, Jam_Masuk, Batas_Telat, Jam_Pulang, Status_Hari, Keterangan</span>
                <input type="file" name="file_schedule" accept=".csv,text/csv,text/plain" required
                       class="absolute inset-0 opacity-0 cursor-pointer"
                       onchange="updateFileName(this)">
                <span id="selectedFileName" class="mt-2 text-xs font-mono font-semibold text-primary"></span>
            </div>

            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('admin.settings.template') }}" class="font-label-sm text-label-sm text-primary hover:underline flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">download</span>
                    <span>Download Template Dulu</span>
                </a>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="document.getElementById('uploadCsvModal').classList.add('hidden')"
                            class="px-space-md py-2 rounded-lg bg-surface-container-low text-on-surface font-label-md text-label-md hover:bg-surface-container">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-space-md py-2 rounded-lg bg-primary text-on-primary font-label-md text-label-md shadow-sm hover:opacity-95">
                        Upload & Terapkan
                    </button>
                </div>
            </div>

        </form>

    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    // ===================================
    // LOGO LIVE PREVIEW SCRIPT
    // ===================================
    function previewLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('logoPreview');
                if (img) {
                    img.src = e.target.result;
                }
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // ===================================
    // INTERACTIVE MAP & GEOFENCE SCRIPT
    // ===================================
    let map, marker, circle;
    const initialLat = parseFloat("{{ $setting->latitude ?? -7.53420000 }}");
    const initialLng = parseFloat("{{ $setting->longitude ?? 112.23510000 }}");
    let currentRadius = parseInt("{{ $setting->radius_meters ?? 120 }}");

    function initSchoolMap() {
        if (!document.getElementById('schoolMap')) return;

        map = L.map('schoolMap').setView([initialLat, initialLng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Marker Titik Sekolah (Bisa di-drag)
        marker = L.marker([initialLat, initialLng], {
            draggable: true
        }).addTo(map);

        marker.bindPopup("<b>Gerbang / Kampus Sekolah</b><br>Geser pin untuk menentukan titik presensi.").openPopup();

        // Lingkaran Radius Geofence
        circle = L.circle([initialLat, initialLng], {
            color: '#004ac6',
            fillColor: '#2563eb',
            fillOpacity: 0.2,
            radius: currentRadius
        }).addTo(map);

        // Event: Saat Marker digeser (dragend)
        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            updateInputsFromCoords(pos.lat, pos.lng);
            circle.setLatLng(pos);
            map.panTo(pos);
        });

        // Event: Saat Peta diklik
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            circle.setLatLng(e.latlng);
            updateInputsFromCoords(e.latlng.lat, e.latlng.lng);
        });
    }

    function updateInputsFromCoords(lat, lng) {
        document.getElementById('latitude_input').value = lat.toFixed(8);
        document.getElementById('longitude_input').value = lng.toFixed(8);
        document.getElementById('btn_google_maps').href = `https://www.google.com/maps?q=${lat},${lng}`;
    }

    function updateMapFromInputs() {
        const lat = parseFloat(document.getElementById('latitude_input').value);
        const lng = parseFloat(document.getElementById('longitude_input').value);
        const radius = parseInt(document.getElementById('radius_input').value) || 120;

        document.getElementById('txt_radius_meter').textContent = radius;

        if (!isNaN(lat) && !isNaN(lng) && marker && circle) {
            const newPos = [lat, lng];
            marker.setLatLng(newPos);
            circle.setLatLng(newPos);
            circle.setRadius(radius);
            map.panTo(newPos);
            document.getElementById('btn_google_maps').href = `https://www.google.com/maps?q=${lat},${lng}`;
        }
    }

    function setRadius(meter) {
        document.getElementById('radius_input').value = meter;
        updateMapFromInputs();
    }

    function getCurrentLocation() {
        const statusEl = document.getElementById('loc_status');
        if (!navigator.geolocation) {
            Swal.fire({
                title: 'GPS Tidak Didukung',
                text: 'Peramban web Anda tidak mendukung fitur Geolocation GPS.',
                icon: 'error',
                confirmButtonColor: '#004ac6',
                confirmButtonText: 'Mengerti',
                customClass: { popup: 'rounded-2xl shadow-xl' }
            });
            return;
        }

        statusEl.innerHTML = '<span class="text-primary font-semibold flex items-center gap-1"><span class="material-symbols-outlined text-[14px] animate-spin">sync</span> Mengambil koordinat GPS perangkat Anda...</span>';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy;

                updateInputsFromCoords(lat, lng);
                if (marker && circle && map) {
                    marker.setLatLng([lat, lng]);
                    circle.setLatLng([lat, lng]);
                    map.setView([lat, lng], 17);
                }

                statusEl.innerHTML = `<span class="text-emerald-700 font-semibold">✅ Lokasi GPS berhasil diambil! (Akurasi: ±${Math.round(accuracy)}m)</span>`;
            },
            function(error) {
                let msg = 'Gagal mengambil lokasi GPS.';
                if (error.code === error.PERMISSION_DENIED) {
                    msg = 'Izin lokasi ditolak oleh browser. Mohon aktifkan izin GPS di browser Anda.';
                }
                statusEl.innerHTML = `<span class="text-error font-semibold">❌ ${msg}</span>`;
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    document.addEventListener('DOMContentLoaded', function() {
        initSchoolMap();
    });

    // ===================================
    // JADWAL PRESET & TOGGLE SCRIPT
    // ===================================
    function updateFileName(input) {
        const nameDisplay = document.getElementById('selectedFileName');
        if (input.files && input.files[0]) {
            nameDisplay.textContent = 'File terpilih: ' + input.files[0].name;
        } else {
            nameDisplay.textContent = '';
        }
    }

    function toggleDayStatus(dayNumber, isChecked) {
        const label = document.getElementById('statusLabel_' + dayNumber);
        if (label) {
            label.textContent = isChecked ? 'Masuk' : 'Libur';
            label.className = isChecked ? 'ml-2 font-label-md text-label-md text-emerald-700 font-bold' : 'ml-2 font-label-md text-label-md text-slate-400';
        }
    }

    function applyPresetPattern(type) {
        if (type === 'standard') {
            setDayValues(1, true,  '06:45', '07:00', '14:30', 'Upacara Bendera Pagi');
            setDayValues(2, true,  '07:00', '07:15', '14:30', 'KBM Reguler');
            setDayValues(3, true,  '07:00', '07:15', '14:30', 'KBM Reguler');
            setDayValues(4, true,  '07:00', '07:15', '14:30', 'KBM Reguler');
            setDayValues(5, true,  '07:00', '07:15', '11:30', 'Jumat Berkah / Sholat Jumat');
            setDayValues(6, false, '07:00', '07:15', '13:00', 'Libur Akhir Pekan');
            setDayValues(7, false, '07:00', '07:15', '14:00', 'Libur Akhir Pekan');
        } else if (type === 'sixday') {
            setDayValues(1, true,  '06:45', '07:00', '14:00', 'Upacara Bendera Pagi');
            setDayValues(2, true,  '07:00', '07:15', '14:00', 'KBM Reguler');
            setDayValues(3, true,  '07:00', '07:15', '14:00', 'KBM Reguler');
            setDayValues(4, true,  '07:00', '07:15', '14:00', 'KBM Reguler');
            setDayValues(5, true,  '07:00', '07:15', '11:30', 'Jumat Berkah / Sholat Jumat');
            setDayValues(6, true,  '07:00', '07:15', '13:00', 'KBM Sabtu & Ekstrakurikuler');
            setDayValues(7, false, '07:00', '07:15', '14:00', 'Libur Akhir Pekan');
        } else if (type === 'fullday') {
            setDayValues(1, true,  '06:45', '07:00', '15:30', 'Upacara Bendera Pagi');
            setDayValues(2, true,  '07:00', '07:15', '15:30', 'KBM Full Day');
            setDayValues(3, true,  '07:00', '07:15', '15:30', 'KBM Full Day');
            setDayValues(4, true,  '07:00', '07:15', '15:30', 'KBM Full Day');
            setDayValues(5, true,  '07:00', '07:15', '14:30', 'KBM & Kegiatan Rohani');
            setDayValues(6, false, '07:00', '07:15', '13:00', 'Libur Akhir Pekan');
            setDayValues(7, false, '07:00', '07:15', '14:00', 'Libur Akhir Pekan');
        }

        const form = document.getElementById('mainScheduleForm');
        form.classList.add('ring-2', 'ring-primary');
        setTimeout(() => form.classList.remove('ring-2', 'ring-primary'), 1000);
    }

    function setDayValues(dayNumber, isActive, timeIn, late, timeOut, note) {
        const chk = document.getElementById('status_' + dayNumber);
        if (chk) {
            chk.checked = isActive;
            toggleDayStatus(dayNumber, isActive);
        }
        const tin = document.getElementById('timeIn_' + dayNumber);
        if (tin) tin.value = timeIn;

        const lt = document.getElementById('late_' + dayNumber);
        if (lt) lt.value = late;

        const tout = document.getElementById('timeOut_' + dayNumber);
        if (tout) tout.value = timeOut;

        const nt = document.getElementById('note_' + dayNumber);
        if (nt && note !== undefined) nt.value = note;
    }

    // ===================================
    // WHATSAPP GATEWAY TOKEN VISIBILITY
    // ===================================
    function toggleTokenVisibility() {
        const input = document.getElementById('wa_gateway_token_input');
        const icon = document.getElementById('token_eye_icon');
        if (!input || !icon) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }
</script>
@endpush
@endsection
