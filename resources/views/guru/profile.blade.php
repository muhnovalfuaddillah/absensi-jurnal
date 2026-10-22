@extends('layouts.app')

@section('title', 'Profil Guru - E-Presensi')

@section('content')
<div class="flex flex-col gap-4">

    <!-- Top Hero Banner -->
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#13793e] via-[#0f6332] to-[#0a4d26] p-5 sm:p-6 text-white shadow-lg shadow-emerald-900/15">
        <div class="absolute -right-8 -top-8 w-44 h-44 rounded-full bg-white/5 pointer-events-none blur-2xl"></div>
        <div class="absolute -left-10 -bottom-10 w-44 h-44 rounded-full bg-emerald-400/10 pointer-events-none blur-2xl"></div>

        <div class="relative z-10 flex items-center justify-between gap-3">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-[11px] font-bold text-emerald-100 border border-white/10 mb-2">
                    <i class="fa-solid fa-id-card-clip text-[10px]"></i>
                    <span>Akun & Kepegawaian</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white leading-tight">
                    Profil Saya
                </h1>
                <p class="text-xs text-emerald-100/90 font-medium mt-1 max-w-md leading-relaxed">
                    Pengaturan identitas, foto profil, nomor kontak, serta keamanan akun Anda.
                </p>
            </div>

            <a href="{{ route('guru.dashboard') }}" 
               class="w-10 h-10 rounded-2xl bg-white/15 hover:bg-white/25 backdrop-blur-md text-white flex items-center justify-center transition-all shadow-xs shrink-0 border border-white/20 active:scale-95" 
               title="Kembali ke Beranda">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
        </div>
    </div>

    <!-- Kartu Foto Profil -->
    <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-xs border border-slate-100 flex flex-col gap-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-camera"></i>
                </div>
                <h2 class="text-sm font-extrabold text-slate-900">Foto Profil Guru</h2>
            </div>
            @if($teacher->photo_url)
                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-[#dcfce7] text-[#15803d] border border-emerald-200 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check text-[9px]"></i>
                    <span>Foto Terpasang</span>
                </span>
            @else
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                    Belum Ada Foto
                </span>
            @endif
        </div>

        <!-- Form Khusus Upload Foto Profil -->
        <form id="profilePhotoForm" action="{{ route('guru.profile.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col items-center gap-4">
            @csrf
            @method('PUT')

            <!-- Avatar Preview Area -->
            <div class="relative group cursor-pointer" onclick="document.getElementById('avatarFileInput').click()">
                <div class="w-28 h-28 rounded-3xl overflow-hidden ring-4 ring-[#13793e]/20 shadow-md bg-slate-100 flex items-center justify-center transition-transform group-hover:scale-105">
                    <img id="avatarImagePreview" 
                         src="{{ $teacher->photo_url ?: '' }}" 
                         alt="{{ $teacher->name }}" 
                         class="w-full h-full object-cover {{ $teacher->photo_url ? '' : 'hidden' }}" />
                    
                    <div id="avatarFallbackInitials" class="w-full h-full bg-emerald-50 text-[#13793e] font-black flex items-center justify-center text-3xl {{ $teacher->photo_url ? 'hidden' : '' }}">
                        {{ $teacher->initials ?? strtoupper(substr($teacher->name, 0, 2)) }}
                    </div>
                </div>

                <!-- Camera Badge Icon -->
                <div class="absolute -bottom-1 -right-1 w-9 h-9 rounded-2xl bg-[#13793e] text-white shadow-md flex items-center justify-center ring-2 ring-white transition-transform group-hover:scale-110" title="Klik untuk pilih foto">
                    <i class="fa-solid fa-camera text-xs"></i>
                </div>
            </div>

            <!-- Hidden File Input -->
            <input type="file" id="avatarFileInput" name="photo" accept="image/jpeg,image/png,image/webp" class="hidden" onchange="previewSelectedPhoto(this)">

            <div class="text-center space-y-1">
                <h3 class="text-sm font-black text-slate-900">{{ $teacher->name }}</h3>
                <p id="selectedPhotoFileName" class="text-xs text-slate-400">
                    Klik foto di atas atau tombol di bawah untuk memilih foto dari galeri HP / komputer.
                </p>
            </div>

            <!-- Action Buttons for Photo -->
            <div class="flex items-center gap-2.5 w-full max-w-xs mt-1">
                <button type="button" onclick="document.getElementById('avatarFileInput').click()"
                        class="flex-1 py-2.5 px-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center gap-1.5 transition-all text-xs shadow-2xs">
                    <i class="fa-solid fa-cloud-arrow-up text-xs text-[#13793e]"></i>
                    <span>Pilih Foto Baru</span>
                </button>

                <!-- Save Photo Button -->
                <button type="submit" id="btnSaveNewPhoto"
                        class="hidden flex-1 py-2.5 px-3 rounded-2xl bg-[#13793e] hover:bg-[#0f6332] text-white font-black text-xs shadow-md flex items-center justify-center gap-1.5 transition-all animate-bounce">
                    <i class="fa-solid fa-floppy-disk text-xs"></i>
                    <span>Simpan Foto</span>
                </button>
            </div>

            <p class="text-[11px] text-slate-400 text-center max-w-sm font-medium">
                Format: JPG, PNG, atau WEBP (Maksimal 5 MB). Foto ini digunakan sebagai foto profil resmi dan referensi verifikasi presensi.
            </p>
        </form>

        <!-- Opsi Hapus Foto Profil jika sudah ada foto -->
        @if($teacher->photo)
            <div class="pt-3 border-t border-slate-100 flex justify-center">
                <form action="{{ route('guru.profile.update') }}" method="POST" 
                      data-confirm="Apakah Anda yakin ingin menghapus foto profil dan mengembalikan ke inisial nama?" 
                      data-confirm-title="Hapus Foto Profil" 
                      data-confirm-btn="Ya, Hapus Foto"
                      data-confirm-color="#dc2626">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="remove_photo" value="1">
                    <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 hover:underline flex items-center gap-1.5 font-bold">
                        <i class="fa-regular fa-trash-can text-xs"></i>
                        <span>Hapus Foto Profil Ini</span>
                    </button>
                </form>
            </div>
        @endif
    </div>

    <!-- Data Kepegawaian Card -->
    <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-xs border border-slate-100 flex flex-col gap-3.5">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                <i class="fa-solid fa-id-badge"></i>
            </div>
            <h3 class="text-sm font-extrabold text-slate-900">Data Kepegawaian</h3>
        </div>

        <div class="grid grid-cols-2 gap-2.5 text-xs">
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                <span class="block text-[10px] text-slate-400 font-semibold">NIP</span>
                <strong class="font-bold text-slate-800 truncate block mt-0.5 font-mono">{{ $teacher->nip ?: '-' }}</strong>
            </div>
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                <span class="block text-[10px] text-slate-400 font-semibold">NUPTK</span>
                <strong class="font-bold text-slate-800 truncate block mt-0.5 font-mono">{{ $teacher->nuptk ?: '-' }}</strong>
            </div>
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                <span class="block text-[10px] text-slate-400 font-semibold">Status Kepegawaian</span>
                <strong class="font-bold text-[#13793e] truncate block mt-0.5">{{ $teacher->employment_status ?: 'Guru Tetap Yayasan' }}</strong>
            </div>
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                <span class="block text-[10px] text-slate-400 font-semibold">Mata Pelajaran Utama</span>
                <strong class="font-bold text-slate-800 truncate block mt-0.5">{{ $teacher->subject ?: 'Tenaga Pengajar' }}</strong>
            </div>
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                <span class="block text-[10px] text-slate-400 font-semibold">Pendidikan Terakhir</span>
                <strong class="font-bold text-slate-800 truncate block mt-0.5">{{ $teacher->education ?: 'S1 Sarjana' }}</strong>
            </div>
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100">
                <span class="block text-[10px] text-slate-400 font-semibold">Tugas / Jabatan</span>
                <strong class="font-bold text-slate-800 truncate block mt-0.5">{{ $teacher->class_duty ?: ($teacher->tugas_tambahan ?: 'Guru') }}</strong>
            </div>
            <div class="bg-slate-50 p-3 rounded-2xl border border-slate-100 col-span-2">
                <span class="block text-[10px] text-slate-400 font-semibold">Email / Username Login</span>
                <strong class="font-mono font-bold text-slate-800 truncate block mt-0.5">{{ $user->email }}</strong>
            </div>
        </div>
    </div>

    <!-- Form Kontak & Keamanan Password -->
    <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-xs border border-slate-100 flex flex-col gap-4">
        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
            <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h3 class="text-sm font-extrabold text-slate-900">Perbarui Kontak & Keamanan Akun</h3>
        </div>

        <form action="{{ route('guru.profile.update') }}" method="POST" class="flex flex-col gap-3.5">
            @csrf
            @method('PUT')

            <!-- WhatsApp -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                    Nomor WhatsApp Aktif
                </label>
                <div class="relative">
                    <i class="fa-brands fa-whatsapp absolute left-3.5 top-1/2 -translate-y-1/2 text-emerald-600 text-sm"></i>
                    <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" placeholder="Contoh: 081234567890"
                           class="w-full pl-9 pr-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-bold font-mono text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>
                <span class="block text-[10px] text-slate-400 mt-1 font-medium">Digunakan untuk pengingat presensi dan informasi penting madrasah.</span>
            </div>

            <div class="pt-3 border-t border-slate-100 flex flex-col gap-3">
                <span class="text-xs text-[#13793e] font-extrabold flex items-center gap-1.5">
                    <i class="fa-solid fa-key text-xs"></i>
                    <span>Ganti Kata Sandi (Opsional)</span>
                </span>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Password Saat Ini
                    </label>
                    <input type="password" name="current_password" placeholder="Masukkan jika ingin mengganti"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Password Baru
                    </label>
                    <input type="password" name="new_password" placeholder="Minimal 6 karakter"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Konfirmasi Password Baru
                    </label>
                    <input type="password" name="new_password_confirmation" placeholder="Ulangi password baru"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>
            </div>

            <button type="submit" 
                    class="w-full mt-2 bg-[#13793e] hover:bg-[#0f6332] text-white py-3.5 px-4 rounded-2xl shadow-md shadow-emerald-900/15 font-black text-xs sm:text-sm active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-floppy-disk text-xs"></i>
                <span>Simpan Perubahan Kontak & Password</span>
            </button>
        </form>
    </div>

</div>

<script>
    function previewSelectedPhoto(input) {
        if (!input.files || !input.files[0]) return;

        const file = input.files[0];

        // Validasi ukuran maksimal 5 MB
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran Foto Terlalu Besar',
                text: 'Maksimal ukuran foto adalah 5 MB. Silakan pilih foto dengan resolusi lebih kecil.',
                confirmButtonColor: '#13793e'
            });
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('avatarImagePreview');
            const fallbackInitials = document.getElementById('avatarFallbackInitials');
            const fileNameText = document.getElementById('selectedPhotoFileName');
            const btnSave = document.getElementById('btnSaveNewPhoto');

            previewImg.src = e.target.result;
            previewImg.classList.remove('hidden');
            fallbackInitials.classList.add('hidden');

            fileNameText.innerHTML = `<span class="text-emerald-700 font-semibold">✅ Foto terpilih: ${file.name}</span><br><span class="text-[11px] text-slate-500">Klik 'Simpan Foto' untuk menyimpan ke profil.</span>`;
            btnSave.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
