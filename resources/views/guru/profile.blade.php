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
                    <span>Akun & Kepegawaian Mandiri</span>
                </div>
                <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white leading-tight">
                    Profil Guru Saya
                </h1>
                <p class="text-xs text-emerald-100/90 font-medium mt-1 max-w-md leading-relaxed">
                    Kelola data identitas, foto profil, kepegawaian, nomor kontak, serta kata sandi Anda secara mandiri.
                </p>
            </div>

            <a href="{{ route('guru.dashboard') }}" 
               class="w-10 h-10 rounded-2xl bg-white/15 hover:bg-white/25 backdrop-blur-md text-white flex items-center justify-center transition-all shadow-xs shrink-0 border border-white/20 active:scale-95" 
               title="Kembali ke Beranda">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
        </div>
    </div>

    <!-- Error Validation Alert -->
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
            <div class="flex items-center gap-2 font-black text-rose-900 mb-1">
                <i class="fa-solid fa-triangle-exclamation text-sm text-rose-600"></i>
                <span>Mohon periksa kembali formulir profil:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px] font-medium text-rose-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- MAIN FORM PENGISIAN PROFIL MANDIRI GURU -->
    <form action="{{ route('guru.profile.update') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">
        @csrf
        @method('PUT')

        <!-- 1. KARTU FOTO PROFIL -->
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

            <div class="flex flex-col items-center gap-4">
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
                        Klik avatar di atas atau tombol di bawah untuk mengganti foto dari HP / komputer.
                    </p>
                </div>

                <!-- Action Button for Photo -->
                <button type="button" onclick="document.getElementById('avatarFileInput').click()"
                        class="py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center gap-1.5 transition-all text-xs shadow-2xs">
                    <i class="fa-solid fa-cloud-arrow-up text-xs text-[#13793e]"></i>
                    <span>Pilih Foto dari Galeri</span>
                </button>
            </div>
        </div>

        <!-- 2. DATA IDENTITAS POKOK -->
        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-xs border border-slate-100 flex flex-col gap-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Identitas Diri</h3>
                    <p class="text-[11px] text-slate-400">Nama lengkap resmi dan data nomor kepegawaian Anda</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Nama Lengkap -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Nama Lengkap & Gelar <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required
                           placeholder="Contoh: Dra. Hj. Siti Aminah, M.Pd"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>

                <!-- Jenis Kelamin -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Jenis Kelamin <span class="text-rose-500">*</span>
                    </label>
                    <select name="gender" required
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                        <option value="L" {{ old('gender', $teacher->gender) === 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                        <option value="P" {{ old('gender', $teacher->gender) === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                    </select>
                </div>

                <!-- NIP -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        NIP (Nomor Induk Pegawai)
                    </label>
                    <input type="text" name="nip" value="{{ old('nip', $teacher->nip) }}"
                           placeholder="Kosongkan jika belum memiliki NIP"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>

                <!-- NUPTK -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        NUPTK
                    </label>
                    <input type="text" name="nuptk" value="{{ old('nuptk', $teacher->nuptk) }}"
                           placeholder="Nomor Unik Pendidik dan Tenaga Kependidikan"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>

                <!-- Email Pribadi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Alamat Email
                    </label>
                    <input type="email" name="email" value="{{ old('email', $teacher->email ?: $user->email) }}"
                           placeholder="Contoh: guru@sekolah.sch.id"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>
            </div>
        </div>

        <!-- 3. KELAHIRAN & DOMISILI -->
        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-xs border border-slate-100 flex flex-col gap-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Kelahiran & Tempat Tinggal</h3>
                    <p class="text-[11px] text-slate-400">Data tempat tanggal lahir dan alamat domisili</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Tempat Lahir -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Tempat Lahir
                    </label>
                    <input type="text" name="birth_place" value="{{ old('birth_place', $teacher->birth_place) }}"
                           placeholder="Contoh: Bandung, Surabaya"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Tanggal Lahir
                    </label>
                    <input type="date" name="birth_date" value="{{ old('birth_date', $teacher->birth_date?->format('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-medium font-mono text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>

                <!-- Alamat Domisili -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Alamat Domisili Lengkap
                    </label>
                    <textarea name="address" rows="2" placeholder="Nama jalan, RT/RW, kelurahan, kecamatan, kabupaten/kota..."
                              class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-medium text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all leading-relaxed">{{ old('address', $teacher->address) }}</textarea>
                </div>
            </div>
        </div>

        <!-- 4. AKADEMIK & KEPEGAWAIAN -->
        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-xs border border-slate-100 flex flex-col gap-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Akademik & Kepegawaian</h3>
                    <p class="text-[11px] text-slate-400">Mata pelajaran ampu, status kerja, dan kualifikasi pendidikan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Mata Pelajaran Utama -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Mata Pelajaran yang Diampu
                    </label>
                    <input type="text" name="subject" value="{{ old('subject', $teacher->subject) }}"
                           placeholder="Contoh: Matematika, Bahasa Indonesia"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>

                <!-- Status Kepegawaian -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Status Kepegawaian
                    </label>
                    <select name="employment_status"
                            class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                        @php
                            $currentStatus = old('employment_status', $teacher->employment_status);
                        @endphp
                        <option value="Guru Tetap Yayasan" {{ $currentStatus == 'Guru Tetap Yayasan' ? 'selected' : '' }}>Guru Tetap Yayasan (GTY)</option>
                        <option value="Guru Tidak Tetap" {{ $currentStatus == 'Guru Tidak Tetap' ? 'selected' : '' }}>Guru Tidak Tetap (GTT)</option>
                        <option value="PNS" {{ $currentStatus == 'PNS' ? 'selected' : '' }}>PNS (Pegawai Negeri Sipil)</option>
                        <option value="PPPK" {{ $currentStatus == 'PPPK' ? 'selected' : '' }}>PPPK</option>
                        <option value="Honorer" {{ $currentStatus == 'Honorer' ? 'selected' : '' }}>Honorer</option>
                        <option value="Tendik" {{ $currentStatus == 'Tendik' ? 'selected' : '' }}>Tenaga Kependidikan (Tendik)</option>
                        @if($currentStatus && !in_array($currentStatus, ['Guru Tetap Yayasan', 'Guru Tidak Tetap', 'PNS', 'PPPK', 'Honorer', 'Tendik']))
                            <option value="{{ $currentStatus }}" selected>{{ $currentStatus }}</option>
                        @endif
                    </select>
                </div>

                <!-- Pendidikan Terakhir -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Pendidikan Terakhir
                    </label>
                    <input type="text" name="education" value="{{ old('education', $teacher->education) }}"
                           placeholder="Contoh: S1 Pendidikan Matematika, S2"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>

                <!-- Tugas Tambahan / Jabatan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5">
                        Tugas Tambahan / Jabatan Sekolah
                    </label>
                    <input type="text" name="class_duty" value="{{ old('class_duty', $teacher->class_duty) }}"
                           placeholder="Contoh: Wali Kelas 7A, Pembina OSIS"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>

                <!-- Keterangan Mengajar di Yayasan & Nama Yayasan (Dapodik) -->
                <div class="sm:col-span-2 p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-100 flex flex-col gap-2.5">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="teaches_in_foundation" id="guru_teaches_in_foundation" value="1"
                                   {{ old('teaches_in_foundation', $teacher->teaches_in_foundation ?? true) ? 'checked' : '' }}
                                   onchange="document.getElementById('guru_foundation_name_wrapper').style.display = this.checked ? 'block' : 'none'"
                                   class="w-4 h-4 text-[#13793e] rounded border-slate-300 focus:ring-[#13793e]">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-building-columns text-[#13793e]"></i>
                                <span>Mengajar di Bawah Naungan Yayasan</span>
                            </span>
                        </label>
                        <span class="text-[10px] font-extrabold text-[#13793e] bg-emerald-100/70 px-2 py-0.5 rounded-full">Data Dapodik</span>
                    </div>

                    <div id="guru_foundation_name_wrapper" style="display: {{ old('teaches_in_foundation', $teacher->teaches_in_foundation ?? true) ? 'block' : 'none' }}">
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Nama Yayasan
                        </label>
                        <input type="text" name="foundation_name" value="{{ old('foundation_name', $teacher->foundation_name ?: ($teacher->school->foundation_name ?? 'Yayasan Bahrul Ulum')) }}"
                               placeholder="Contoh: Yayasan Bahrul Ulum"
                               class="w-full px-3.5 py-2.5 rounded-2xl bg-white border border-emerald-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#13793e] transition-all">
                        <span class="block text-[10px] text-slate-500 mt-1 font-medium">Keterangan nama yayasan tempat bertugas sesuai data dapodik / kepegawaian yayasan.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. KONTAK WHATSAPP & KEAMANAN AKUN -->
        <div class="bg-white p-5 sm:p-6 rounded-3xl shadow-xs border border-slate-100 flex flex-col gap-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-xs">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Kontak & Keamanan Akun</h3>
                    <p class="text-[11px] text-slate-400">Nomor WhatsApp untuk notifikasi dan ganti kata sandi</p>
                </div>
            </div>

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
                <span class="block text-[10px] text-slate-400 mt-1 font-medium">Digunakan untuk notifikasi presensi dan koordinasi madrasah/sekolah.</span>
            </div>

            <!-- Ganti Password Collapsible -->
            <div class="pt-3 border-t border-slate-100 flex flex-col gap-3">
                <span class="text-xs text-[#13793e] font-extrabold flex items-center gap-1.5">
                    <i class="fa-solid fa-key text-xs"></i>
                    <span>Ubah Kata Sandi (Kosongkan jika tidak ingin mengganti)</span>
                </span>
                
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Password Saat Ini
                    </label>
                    <input type="password" name="current_password" placeholder="Masukkan password saat ini untuk konfirmasi"
                           class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">
                            Password Baru
                        </label>
                        <input type="password" name="new_password" placeholder="Minimal 6 karakter"
                               class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">
                            Ulangi Password Baru
                        </label>
                        <input type="password" name="new_password_confirmation" placeholder="Ketik ulang password baru"
                               class="w-full px-3.5 py-2.5 rounded-2xl bg-slate-50 border border-slate-200/80 text-xs text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-3 border-t border-slate-100">
                <button type="submit" 
                        class="w-full bg-[#13793e] hover:bg-[#0f6332] text-white py-3.5 px-4 rounded-2xl shadow-lg shadow-emerald-900/15 font-black text-xs sm:text-sm active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-sm"></i>
                    <span>Simpan Seluruh Data Profil Guru</span>
                </button>
            </div>
        </div>

    </form>

    <!-- Opsi Hapus Foto Profil jika ada foto -->
    @if($teacher->photo)
        <div class="py-2 flex justify-center">
            <form action="{{ route('guru.profile.update') }}" method="POST" 
                  data-confirm="Apakah Anda yakin ingin menghapus foto profil dan mengembalikan ke inisial nama?" 
                  data-confirm-title="Hapus Foto Profil" 
                  data-confirm-btn="Ya, Hapus Foto"
                  data-confirm-color="#dc2626">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $teacher->name }}">
                <input type="hidden" name="gender" value="{{ $teacher->gender }}">
                <input type="hidden" name="remove_photo" value="1">
                <button type="submit" class="text-xs text-rose-600 hover:text-rose-700 hover:underline flex items-center gap-1.5 font-bold">
                    <i class="fa-regular fa-trash-can text-xs"></i>
                    <span>Hapus Foto Profil Ini</span>
                </button>
            </form>
        </div>
    @endif

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

            previewImg.src = e.target.result;
            previewImg.classList.remove('hidden');
            fallbackInitials.classList.add('hidden');

            fileNameText.innerHTML = `<span class="text-emerald-700 font-bold">✅ Foto baru terpilih: ${file.name}</span><br><span class="text-[11px] text-slate-500">Klik tombol "Simpan Seluruh Data Profil Guru" di bawah untuk menyimpan perubahan.</span>`;
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
