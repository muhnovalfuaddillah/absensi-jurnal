@extends('layouts.admin')

@section('title', 'Pengaturan Yayasan & Logo Login - Superadmin SIMPRES BU')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">

    <!-- Top Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-3xl border border-slate-100 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-[11px] font-black border border-amber-200 mb-1.5">
                <span class="material-symbols-outlined text-[14px]">palette</span>
                <span>Branding & Identitas Yayasan</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                Pengaturan Logo & Portal Login
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Kelola logo portal login, nama aplikasi, identitas yayasan induk, dan kontak bantuan agar halaman login netral untuk seluruh madrasah/sekolah.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('login') }}" target="_blank" 
               class="px-4 py-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                <span>Lihat Halaman Login</span>
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-3 shadow-xs">
            <span class="material-symbols-outlined text-emerald-600 text-[22px] shrink-0">check_circle</span>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs space-y-1">
            <div class="flex items-center gap-2 font-black text-rose-900">
                <span class="material-symbols-outlined text-[20px]">error</span>
                <span>Terdapat kesalahan pengisian data:</span>
            </div>
            <ul class="list-disc pl-5 space-y-0.5 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- Left Column: Logo Configuration & Live Preview -->
            <div class="lg:col-span-5 space-y-6">
                
                <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-100 shadow-xs">
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-sm font-black text-slate-800">Logo Portal Login</h2>
                            <p class="text-[11px] text-slate-400 mt-0.5">Tampil di halaman login & icon aplikasi PWA</p>
                        </div>
                        <span class="material-symbols-outlined text-amber-500 text-[22px]">photo_camera</span>
                    </div>

                    <!-- Live Circular Preview Badge -->
                    <div class="flex flex-col items-center justify-center p-6 rounded-2xl bg-gradient-to-b from-slate-50 to-slate-100/60 border border-dashed border-slate-200 mb-5">
                        <div class="relative inline-flex items-center justify-center mb-3">
                            <div class="w-28 h-28 rounded-full bg-white p-2.5 shadow-md border border-slate-100 flex items-center justify-center overflow-hidden">
                                <img id="logoPreviewImg" 
                                     src="{{ $setting->logo_url }}" 
                                     alt="Logo Login" 
                                     class="w-full h-full object-contain rounded-full"
                                     onerror="this.onerror=null;this.src='{{ asset('logo.jpg') }}';">
                            </div>
                            <span class="w-5 h-5 rounded-full bg-[#13793e] border-2 border-white absolute bottom-1 right-1 shadow-sm"></span>
                        </div>
                        <span class="text-[11px] font-bold text-slate-600">Pratinjau Logo Login</span>
                        <span class="text-[10px] text-slate-400">Rekomendasi rasio 1:1 (PNG transparan atau JPG)</span>
                    </div>

                    <!-- File Input -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">Unggah File Logo Baru</label>
                        <div class="relative">
                            <input type="file" name="logo" id="logoFileInput" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml"
                                   onchange="previewUploadedLogo(this)"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-[#13793e] hover:file:bg-emerald-100 cursor-pointer border border-slate-200 rounded-2xl p-1.5 bg-slate-50/60 focus:outline-none">
                        </div>
                        <p class="text-[11px] text-slate-400">Maksimal ukuran: 4 MB (Format didukung: PNG, JPG, WEBP, SVG).</p>
                    </div>

                    <!-- Information Box -->
                    <div class="mt-4 p-3.5 rounded-2xl bg-blue-50/70 border border-blue-200/60 text-blue-900 text-[11px] space-y-1">
                        <div class="flex items-center gap-1.5 font-bold">
                            <span class="material-symbols-outlined text-[16px] text-blue-600">info</span>
                            <span>Otomatis Sinkron:</span>
                        </div>
                        <p class="text-blue-800 leading-relaxed font-medium">
                            Logo yang diunggah di sini secara otomatis akan digunakan pada halaman login utama, favicon web, dan ikon aplikasi Android/PWA.
                        </p>
                    </div>
                </div>

                <!-- Unit Terhubung Info Card -->
                <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xs">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-symbols-outlined text-emerald-600 text-[20px]">apartment</span>
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Madrasah & Sekolah Terhubung</h3>
                    </div>
                    <p class="text-[11px] text-slate-500 mb-3">
                        Halaman login bersifat universal untuk seluruh unit di bawah naungan yayasan:
                    </p>
                    <div class="space-y-2">
                        @forelse($schools as $sch)
                            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 border border-slate-100 text-xs">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full {{ $sch->is_active ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                    <span class="font-bold text-slate-800">{{ $sch->name }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">{{ $sch->teachers_count }} Guru</span>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 italic">Belum ada sekolah terdaftar.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Right Column: Foundation & Portal Details -->
            <div class="lg:col-span-7 space-y-6">

                <div class="bg-white rounded-3xl p-5 sm:p-7 border border-slate-100 shadow-xs space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-sm font-black text-slate-800">Identitas Aplikasi & Yayasan</h2>
                            <p class="text-[11px] text-slate-400 mt-0.5">Teks utama yang tampil pada portal login terpadu</p>
                        </div>
                        <span class="material-symbols-outlined text-[#13793e] text-[24px]">corporate_fare</span>
                    </div>

                    <!-- Input Nama Aplikasi -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Singkatan Aplikasi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="school_name" value="{{ old('school_name', $setting->school_name ?: 'SIMPRES BU') }}" required
                               placeholder="Contoh: SIMPRES BU"
                               class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">Judul utama di halaman login (misal: <b>SIMPRES BU</b>).</p>
                    </div>

                    <!-- Input Nama Yayasan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">
                            Nama Yayasan Induk <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="foundation_name" value="{{ old('foundation_name', $setting->foundation_name ?: 'Yayasan Bahrul Ulum') }}" required
                               placeholder="Contoh: Yayasan Bahrul Ulum"
                               class="w-full px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                        <p class="text-[11px] text-slate-400 mt-1">Lembaga yayasan yang menaungi seluruh sekolah (tampil di badge atas dan hak cipta).</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nomor WhatsApp Helpdesk -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Nomor WhatsApp Bantuan / Admin
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[18px]">chat</span>
                                <input type="text" name="school_phone" value="{{ old('school_phone', $setting->school_phone ?: '089629615301') }}"
                                       placeholder="08xxxxxxxxxx"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Ditautkan ke tombol "Chat WA" bantuan login.</p>
                        </div>

                        <!-- Email Yayasan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Email Yayasan / Helpdesk
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[18px]">mail</span>
                                <input type="email" name="school_email" value="{{ old('school_email', $setting->school_email ?: 'yayasan@bahrululum.sch.id') }}"
                                       placeholder="admin@yayasan.sch.id"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Website Yayasan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Website Resmi Yayasan
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[18px]">language</span>
                                <input type="text" name="website" value="{{ old('website', $setting->website ?: 'https://bahrululum.sch.id') }}"
                                       placeholder="https://bahrululum.sch.id"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                            </div>
                        </div>

                        <!-- Alamat Kantor -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Alamat Kantor Yayasan
                            </label>
                            <div class="relative">
                                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[18px]">location_on</span>
                                <input type="text" name="school_address" value="{{ old('school_address', $setting->school_address ?: 'Besuk, Probolinggo') }}"
                                       placeholder="Besuk Probolinggo"
                                       class="w-full pl-9 pr-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="submit" 
                                class="px-6 py-3 rounded-2xl bg-[#13793e] hover:bg-[#0f6332] text-white font-bold text-xs flex items-center gap-2 shadow-lg shadow-emerald-800/20 transition-all active:scale-95 cursor-pointer">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            <span>Simpan Perubahan Branding & Logo</span>
                        </button>
                    </div>
                </div>

                <!-- Live Preview Mockup of Login Page -->
                <div class="bg-gradient-to-b from-[#f2f6fd] to-[#e8f1fc] rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-slate-600 text-[20px]">preview</span>
                            <span class="text-xs font-extrabold text-slate-800 uppercase tracking-wide">Pratinjau Halaman Login (Netral)</span>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full border border-emerald-300">
                            Tanpa Memihak MTs / MA
                        </span>
                    </div>

                    <div class="bg-white/90 backdrop-blur-xs rounded-2xl p-4 sm:p-5 border border-slate-200/60 max-w-sm mx-auto text-center shadow-xs">
                        <!-- Mini Logo Badge -->
                        <div class="w-16 h-16 rounded-full bg-white p-1.5 shadow-sm border border-slate-100 mx-auto mb-2 flex items-center justify-center">
                            <img id="mockupLogoImg" src="{{ $setting->logo_url }}" class="w-full h-full object-contain rounded-full" onerror="this.onerror=null;this.src='{{ asset('logo.jpg') }}';">
                        </div>
                        <div class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-[#13793e] font-extrabold text-[10px] uppercase mb-1">
                            {{ $setting->foundation_name ?: 'Yayasan Bahrul Ulum' }}
                        </div>
                        <h4 class="text-base font-black text-slate-900 leading-tight">
                            {{ $setting->school_name ?: 'SIMPRES BU' }}
                        </h4>
                        <p class="text-[10px] font-bold text-emerald-800 mt-0.5">
                            Sistem Informasi Manajemen Presensi Bahrul Ulum
                        </p>
                        <p class="text-[10px] text-slate-500 font-medium mt-0.5">
                            Portal Presensi & Jurnal Mengajar Guru Terpadu
                        </p>

                        <!-- Unit Pills -->
                        <div class="mt-3 p-2 rounded-xl bg-slate-50 border border-slate-200/60 text-[10px] text-slate-600 font-semibold flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-[14px] text-emerald-600">domain</span>
                            <span>MTs Syafiiyah • MA Bahrul Ulum</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </form>

</div>

<script>
    function previewUploadedLogo(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImg = document.getElementById('logoPreviewImg');
                const mockupImg = document.getElementById('mockupLogoImg');
                if (previewImg) previewImg.src = e.target.result;
                if (mockupImg) mockupImg.src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
