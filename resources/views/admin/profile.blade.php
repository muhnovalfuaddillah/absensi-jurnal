@extends('layouts.admin')

@section('title', 'Profil Administrator & Kontak Tim IT - HadirGuru')

@section('content')
<div class="flex flex-col gap-space-md">

    <!-- Top Action / Title Bar -->
    <div class="bg-surface-container-lowest p-space-md rounded-2xl shadow-sm border border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-2xl font-bold shrink-0">
                <span class="material-symbols-outlined text-[28px]">manage_accounts</span>
            </div>
            <div>
                <h1 class="font-headline-md text-headline-md font-bold text-on-surface">Profil Administrator & Kontak Tim IT</h1>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Kelola data identitas admin, kata sandi, serta nomor WhatsApp Tim IT aplikasi
                </p>
            </div>
        </div>

        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold shrink-0">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse"></span>
            <span>Tersinkronisasi ke Tombol "Bantuan IT" Guru</span>
        </div>
    </div>

    <!-- Main Grid: Form Profil & Kartu Informasi -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-space-md">
        
        <!-- Kolom Kiri / Utama: Form Edit Profil & Nomor WhatsApp -->
        <div class="lg:col-span-2 bg-surface-container-lowest p-space-lg rounded-2xl shadow-sm border border-surface-container flex flex-col gap-4">
            <div class="flex items-center justify-between pb-3 border-b border-surface-container-low">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">person_pin</span>
                    <h2 class="font-headline-sm text-sm font-bold text-on-surface">Data Diri Administrator & Tim IT</h2>
                </div>
                <span class="text-[11px] text-on-surface-variant font-medium">Role: <b>Administrator / Tim IT</b></span>
            </div>

            <form action="{{ route('admin.profile.update') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <!-- Nama Administrator -->
                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1">
                            Nama Lengkap Administrator <span class="text-error">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-bold text-on-surface focus:outline-none focus:border-primary transition-all">
                    </div>

                    <!-- Email Login -->
                    <div>
                        <label class="block text-xs font-bold text-on-surface mb-1">
                            Email Login Administrator <span class="text-error">*</span>
                        </label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-semibold text-on-surface focus:outline-none focus:border-primary transition-all">
                    </div>
                </div>

                <!-- Username Login (Opsional) -->
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">
                        Username Login (Opsional)
                    </label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" placeholder="Contoh: admin_syafiiyah"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-mono font-semibold text-on-surface focus:outline-none focus:border-primary transition-all">
                    <p class="text-[10px] text-on-surface-variant mt-1">Dapat digunakan sebagai alternatif email saat masuk ke sistem.</p>
                </div>

                <!-- HIGHLIGHT UTAMA: NOMOR WHATSAPP TIM IT APLIKASI -->
                <div class="p-4 bg-emerald-50/70 border-2 border-emerald-300 rounded-2xl flex flex-col gap-2.5 shadow-2xs">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">support_agent</span>
                            </span>
                            <div>
                                <label class="block text-xs font-extrabold text-emerald-950">
                                    Nomor WhatsApp Tim IT Aplikasi (Helpdesk Guru) <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[10px] text-emerald-700 font-medium">Tujuan kontak otomatis tombol "Bantuan IT" di aplikasi ponsel guru</span>
                            </div>
                        </div>

                        @if($cleanPhone)
                            <a href="https://wa.me/{{ $waFormatted }}?text=Halo%20Tim%20IT%20Aplikasi,%20uji%20coba%20sambungan%20pesan%20bantuan." 
                               target="_blank" 
                               class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold shadow-2xs transition-all flex items-center gap-1 active:scale-95" 
                               title="Cek apakah nomor ini langsung membuka WhatsApp">
                                <span>Test Chat WA</span>
                                <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                            </a>
                        @endif
                    </div>

                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-emerald-700 text-[18px]">call</span>
                        <input type="text" name="phone" value="{{ old('phone', $setting->school_phone ?? '089629615301') }}" required placeholder="Contoh: 089629615301"
                               class="w-full pl-9 pr-3.5 py-2.5 rounded-xl bg-white border border-emerald-400 text-xs font-mono font-black text-emerald-950 focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
                    </div>

                    <div class="p-2.5 rounded-xl bg-white/80 border border-emerald-200 text-[11px] text-emerald-900 leading-relaxed font-medium">
                        💡 <b>Integrasi Otomatis:</b> Nomor yang Anda simpan di sini akan otomatis menjadi nomor tujuan saat seluruh guru menekan tombol <b>"Bantuan IT"</b> di dashboard HP mereka, tanpa perlu konfigurasi tambahan.
                    </div>
                </div>

                <!-- Bagian Ganti Kata Sandi (Opsional) -->
                <div class="pt-3 border-t border-surface-container-low flex flex-col gap-3">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-primary text-[18px]">lock_reset</span>
                        <h3 class="text-xs font-bold text-on-surface">Ubah Kata Sandi Administrator (Opsional)</h3>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-on-surface-variant mb-1">
                            Kata Sandi Saat Ini
                        </label>
                        <input type="password" name="current_password" placeholder="Masukkan jika ingin mengganti kata sandi"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low border border-surface-container-high text-xs text-on-surface focus:outline-none focus:border-primary transition-all">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-on-surface-variant mb-1">
                                Kata Sandi Baru
                            </label>
                            <input type="password" name="new_password" placeholder="Minimal 6 karakter"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low border border-surface-container-high text-xs text-on-surface focus:outline-none focus:border-primary transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-on-surface-variant mb-1">
                                Konfirmasi Kata Sandi Baru
                            </label>
                            <input type="password" name="new_password_confirmation" placeholder="Ulangi kata sandi baru"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low border border-surface-container-high text-xs text-on-surface focus:outline-none focus:border-primary transition-all">
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit -->
                <div class="pt-2">
                    <button type="submit" 
                            class="w-full py-3 px-4 rounded-xl bg-primary hover:bg-primary/95 text-on-primary font-bold text-xs sm:text-sm shadow-md active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[20px]">save</span>
                        <span>Simpan Perubahan Profil & Kontak Tim IT</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Kolom Kanan: Ringkasan Lembaga & Quick Info -->
        <div class="flex flex-col gap-space-md">
            
            <!-- Kartu Status Akun -->
            <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-surface-container flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center text-on-primary font-black text-lg shadow-sm">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="font-bold text-sm text-on-surface truncate">{{ $user->name }}</h3>
                        <p class="text-xs text-on-surface-variant truncate">{{ $user->email }}</p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-primary/10 text-primary">
                            Hak Akses Penuh (Administrator)
                        </span>
                    </div>
                </div>

                <div class="pt-3 border-t border-surface-container-low flex flex-col gap-2 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-on-surface-variant font-medium">Lembaga Sekolah:</span>
                        <strong class="text-on-surface">{{ $setting->school_name ?? 'MTS SYAFIIYAH' }}</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-on-surface-variant font-medium">Yayasan Pengelola:</span>
                        <strong class="text-on-surface">{{ $setting->foundation_name ?? 'Yayasan Barul Ulum' }}</strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-on-surface-variant font-medium">NPSN:</span>
                        <strong class="font-mono text-on-surface">{{ $setting->npsn ?: '-' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Kartu Pintasan Pengaturan Lembaga -->
            <div class="bg-surface-container-lowest p-5 rounded-2xl shadow-sm border border-surface-container flex flex-col gap-3">
                <div class="flex items-center gap-2 text-xs font-bold text-on-surface">
                    <span class="material-symbols-outlined text-primary text-[18px]">settings</span>
                    <span>Pengaturan Jam & Radius Presensi</span>
                </div>
                <p class="text-[11px] text-on-surface-variant leading-relaxed">
                    Pengaturan jam masuk, jam pulang, radius geofence GPS, dan logo madrasah dapat dikelola melalui halaman Pengaturan Sekolah.
                </p>
                <a href="{{ route('admin.settings.index') }}" 
                   class="py-2.5 px-3 rounded-xl bg-surface-container-high hover:bg-surface-container text-primary font-bold text-xs text-center transition-colors">
                    Buka Pengaturan Sekolah
                </a>
            </div>

        </div>

    </div>

</div>
@endsection
