@extends('layouts.admin')

@section('title', 'Informasi Sekolah & Maklumat Yayasan - HadirGuru')

@section('content')
<div class="flex flex-col gap-space-md">

    <!-- Top Action / Title Bar -->
    <div class="bg-surface-container-lowest p-space-md rounded-2xl shadow-sm border border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-2xl font-bold shrink-0">
                <span class="material-symbols-outlined text-[28px]">campaign</span>
            </div>
            <div>
                <h1 class="font-headline-md text-headline-md font-bold text-on-surface">Informasi Sekolah & Maklumat Yayasan</h1>
                <p class="font-body-sm text-body-sm text-on-surface-variant">
                    Kelola pengumuman resmi dan maklumat yayasan yang langsung tampil di dashboard guru secara real-time
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold shrink-0">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 animate-pulse"></span>
                <span>{{ $totalActive }} Informasi Aktif di Guru</span>
            </div>
            <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#13793e] hover:bg-[#0f6031] text-white text-xs font-bold shadow-md shadow-emerald-900/10 transition-all">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Terbitkan Informasi</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
        <span class="material-symbols-outlined text-emerald-600 text-[20px]">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(isset($errors) && $errors->any())
    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium flex flex-col gap-1">
        <div class="flex items-center gap-1.5 font-bold">
            <span class="material-symbols-outlined text-rose-600 text-[18px]">error</span>
            <span>Terdapat kesalahan input:</span>
        </div>
        <ul class="list-disc list-inside ml-5">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Daftar Informasi & Maklumat -->
    <div class="bg-surface-container-lowest p-space-md sm:p-space-lg rounded-2xl shadow-sm border border-surface-container flex flex-col gap-4">
        <div class="flex items-center justify-between pb-3 border-b border-surface-container-low">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-amber-600 text-[22px]">format_list_bulleted</span>
                <h2 class="font-headline-sm text-sm font-bold text-on-surface">Daftar Maklumat Terbit (Total: {{ $totalAll }})</h2>
            </div>
            <span class="text-xs text-on-surface-variant font-medium">Informasi aktif otomatis menggantikan kartu Maklumat Yayasan guru</span>
        </div>

        @if($announcements->isEmpty())
        <div class="py-12 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 rounded-3xl bg-amber-50 text-amber-600 flex items-center justify-center mb-3">
                <span class="material-symbols-outlined text-3xl">campaign</span>
            </div>
            <h3 class="font-bold text-sm text-slate-800 mb-1">Belum Ada Informasi Sekolah / Maklumat</h3>
            <p class="text-xs text-slate-500 max-w-sm mb-4">
                Klik tombol "Terbitkan Informasi" untuk membuat maklumat yayasan atau surat edaran pertama yang akan tampil di aplikasi guru.
            </p>
            <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#13793e] hover:bg-[#0f6031] text-white text-xs font-bold transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span>
                <span>Buat Pengumuman Baru</span>
            </button>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($announcements as $item)
            @php
                $color = match(strtoupper($item->category)) {
                    'PENTING' => ['badge' => 'bg-rose-600 text-white', 'card' => 'border-rose-100 hover:border-rose-300'],
                    'MAKLUMAT' => ['badge' => 'bg-amber-600 text-white', 'card' => 'border-amber-100 hover:border-amber-300'],
                    'SURAT EDARAN' => ['badge' => 'bg-[#13793e] text-white', 'card' => 'border-emerald-100 hover:border-emerald-300'],
                    'AGENDA' => ['badge' => 'bg-blue-600 text-white', 'card' => 'border-blue-100 hover:border-blue-300'],
                    default => ['badge' => 'bg-slate-700 text-white', 'card' => 'border-slate-200 hover:border-slate-300'],
                };
            @endphp
            <div class="rounded-2xl border {{ $color['card'] }} bg-surface-container-low/50 hover:bg-surface-container-low p-4 sm:p-5 flex flex-col justify-between gap-3 transition-all relative">
                <!-- Status Badge Header -->
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-md {{ $color['badge'] }} text-[10px] font-black uppercase tracking-wider">
                            {{ $item->category }}
                        </span>
                        @if($item->is_active)
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            <span>Tampil di Guru</span>
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-200 px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                            <span>Nonaktif</span>
                        </span>
                        @endif
                    </div>
                    <span class="text-[11px] font-semibold text-slate-400">
                        {{ $item->date ? $item->date->translatedFormat('d F Y') : '-' }}
                    </span>
                </div>

                <!-- Konten Maklumat -->
                <div>
                    <h3 class="font-headline-sm text-sm font-bold text-on-surface mb-1 leading-snug">
                        {{ $item->title }}
                    </h3>
                    <p class="text-xs text-on-surface-variant line-clamp-3 leading-relaxed">
                        {{ $item->content }}
                    </p>
                </div>

                <!-- Meta & Action Links -->
                <div class="pt-3 border-t border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 text-xs">
                    <div class="flex flex-col gap-1">
                        @if($item->letter_number)
                        <span class="text-[11px] font-medium text-slate-500 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px] text-slate-400">description</span>
                            <span>{{ $item->letter_number }}</span>
                        </span>
                        @endif

                        @if($item->attachment_path)
                        <a href="{{ asset('storage/' . $item->attachment_path) }}" target="_blank" class="text-[11px] font-bold text-[#13793e] hover:underline flex items-center gap-1">
                            <span class="material-symbols-outlined text-[15px]">attachment</span>
                            <span>Lihat Berkas Lampiran</span>
                        </a>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-1.5 self-end sm:self-auto">
                        <!-- Toggle Status -->
                        <form action="{{ route('admin.announcements.toggle', $item->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" title="{{ $item->is_active ? 'Sembunyikan dari Guru' : 'Tampilkan di Guru' }}" 
                                    class="p-1.5 rounded-lg border {{ $item->is_active ? 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100' : 'bg-slate-100 border-slate-200 text-slate-600 hover:bg-slate-200' }} transition-colors">
                                <span class="material-symbols-outlined text-[18px]">{{ $item->is_active ? 'visibility_off' : 'visibility' }}</span>
                            </button>
                        </form>

                        <!-- Edit Button -->
                        <button type="button" onclick="openEditModal({{ json_encode($item) }})" title="Edit Maklumat"
                                class="p-1.5 rounded-lg border bg-blue-50 border-blue-200 text-blue-700 hover:bg-blue-100 transition-colors">
                            <span class="material-symbols-outlined text-[18px]">edit</span>
                        </button>

                        <!-- Hapus Button -->
                        <form action="{{ route('admin.announcements.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus maklumat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" title="Hapus Maklumat"
                                    class="p-1.5 rounded-lg border bg-rose-50 border-rose-200 text-rose-700 hover:bg-rose-100 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="pt-4 border-t border-surface-container-low">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>

</div>

<!-- ==================================================== -->
<!-- MODAL: TERBITKAN INFORMASI BARU -->
<!-- ==================================================== -->
<div id="createAnnouncementModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-surface-container flex flex-col gap-4 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3 border-b border-surface-container">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">campaign</span>
                </div>
                <div>
                    <h3 class="font-headline-sm text-sm font-bold text-on-surface">Terbitkan Informasi / Maklumat</h3>
                    <p class="text-[11px] text-on-surface-variant">Akan langsung tampil di kartu Maklumat Yayasan pada aplikasi Guru</p>
                </div>
            </div>
            <button type="button" onclick="closeCreateModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined text-[22px]">close</span>
            </button>
        </div>

        <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3.5">
            @csrf

            <!-- Kategori & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">
                        Kategori Maklumat <span class="text-error">*</span>
                    </label>
                    <select name="category" required class="w-full px-3 py-2 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
                        <option value="MAKLUMAT">MAKLUMAT</option>
                        <option value="PENTING">PENTING</option>
                        <option value="SURAT EDARAN">SURAT EDARAN</option>
                        <option value="AGENDA">AGENDA SEKOLAH</option>
                        <option value="PENGUMUMAN">PENGUMUMAN UMUM</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">
                        Tanggal Terbit <span class="text-error">*</span>
                    </label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                           class="w-full px-3 py-2 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
                </div>
            </div>

            <!-- Judul Informasi -->
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">
                    Judul Informasi / Maklumat <span class="text-error">*</span>
                </label>
                <input type="text" name="title" placeholder="Contoh: Peringatan Hari Santri & Pembagian Jadwal Mengajar" required
                       class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
            </div>

            <!-- Isi Maklumat -->
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">
                    Isi Informasi / Maklumat <span class="text-error">*</span>
                </label>
                <textarea name="content" rows="4" placeholder="Tuliskan isi edaran atau maklumat secara lengkap untuk dibaca oleh seluruh guru..." required
                          class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low border border-surface-container-high text-xs text-on-surface focus:outline-none focus:border-primary leading-relaxed"></textarea>
            </div>

            <!-- No Surat Edaran & Tautan Eksternal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">
                        Nomor Surat Edaran (Opsional)
                    </label>
                    <input type="text" name="letter_number" placeholder="Contoh: No. 421/YBU/{{ date('Y') }}"
                           class="w-full px-3 py-2 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-medium text-on-surface focus:outline-none focus:border-primary">
                </div>

                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">
                        Tautan Eksternal (Opsional)
                    </label>
                    <input type="url" name="external_link" placeholder="https://..."
                           class="w-full px-3 py-2 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-medium text-on-surface focus:outline-none focus:border-primary">
                </div>
            </div>

            <!-- Lampiran Berkas PDF / Gambar -->
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">
                    Lampiran Berkas (PDF / Foto Edaran, Maks 5MB)
                </label>
                <input type="file" name="attachment" accept=".pdf,image/png,image/jpeg,image/jpg"
                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-surface-container-high file:text-on-surface hover:file:bg-surface-container">
            </div>

            <!-- Checkbox Langsung Aktifkan -->
            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="createIsActive" value="1" checked class="w-4 h-4 rounded text-[#13793e] focus:ring-[#13793e]">
                <label for="createIsActive" class="text-xs font-bold text-on-surface cursor-pointer">
                    Langsung tampilkan di Dashboard Aplikasi Guru
                </label>
            </div>

            <!-- Modal Actions -->
            <div class="flex items-center justify-end gap-2 pt-4 border-t border-surface-container mt-2">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-container text-on-surface text-xs font-bold transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#13793e] hover:bg-[#0f6031] text-white text-xs font-bold shadow-md shadow-emerald-900/10 transition-all">
                    Terbitkan Sekarang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================================================== -->
<!-- MODAL: EDIT INFORMASI / MAKLUMAT -->
<!-- ==================================================== -->
<div id="editAnnouncementModal" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-surface-container-lowest rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-surface-container flex flex-col gap-4 max-h-[90vh] overflow-y-auto">
        
        <div class="flex items-center justify-between pb-3 border-b border-surface-container">
            <div class="flex items-center gap-2">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                </div>
                <div>
                    <h3 class="font-headline-sm text-sm font-bold text-on-surface">Ubah Informasi / Maklumat</h3>
                    <p class="text-[11px] text-on-surface-variant">Perbarui rincian maklumat yang sedang tayang</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined text-[22px]">close</span>
            </button>
        </div>

        <form id="editAnnouncementForm" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3.5">
            @csrf
            @method('PUT')

            <!-- Kategori & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">
                        Kategori Maklumat <span class="text-error">*</span>
                    </label>
                    <select name="category" id="editCategory" required class="w-full px-3 py-2 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
                        <option value="MAKLUMAT">MAKLUMAT</option>
                        <option value="PENTING">PENTING</option>
                        <option value="SURAT EDARAN">SURAT EDARAN</option>
                        <option value="AGENDA">AGENDA SEKOLAH</option>
                        <option value="PENGUMUMAN">PENGUMUMAN UMUM</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">
                        Tanggal Terbit <span class="text-error">*</span>
                    </label>
                    <input type="date" name="date" id="editDate" required
                           class="w-full px-3 py-2 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
                </div>
            </div>

            <!-- Judul Informasi -->
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">
                    Judul Informasi / Maklumat <span class="text-error">*</span>
                </label>
                <input type="text" name="title" id="editTitle" required
                       class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-bold text-on-surface focus:outline-none focus:border-primary">
            </div>

            <!-- Isi Maklumat -->
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">
                    Isi Informasi / Maklumat <span class="text-error">*</span>
                </label>
                <textarea name="content" id="editContent" rows="4" required
                          class="w-full px-3.5 py-2.5 rounded-xl bg-surface-container-low border border-surface-container-high text-xs text-on-surface focus:outline-none focus:border-primary leading-relaxed"></textarea>
            </div>

            <!-- No Surat Edaran & Tautan Eksternal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">
                        Nomor Surat Edaran (Opsional)
                    </label>
                    <input type="text" name="letter_number" id="editLetterNumber"
                           class="w-full px-3 py-2 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-medium text-on-surface focus:outline-none focus:border-primary">
                </div>

                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">
                        Tautan Eksternal (Opsional)
                    </label>
                    <input type="url" name="external_link" id="editExternalLink"
                           class="w-full px-3 py-2 rounded-xl bg-surface-container-low border border-surface-container-high text-xs font-medium text-on-surface focus:outline-none focus:border-primary">
                </div>
            </div>

            <!-- Lampiran Berkas PDF / Gambar -->
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">
                    Ganti Lampiran Berkas (PDF / Foto Edaran, Maks 5MB)
                </label>
                <input type="file" name="attachment" accept=".pdf,image/png,image/jpeg,image/jpg"
                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-surface-container-high file:text-on-surface hover:file:bg-surface-container">
                <span id="currentAttachmentNotice" class="text-[11px] text-slate-400 mt-1 block"></span>
            </div>

            <!-- Checkbox Aktifkan -->
            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" id="editIsActive" value="1" class="w-4 h-4 rounded text-[#13793e] focus:ring-[#13793e]">
                <label for="editIsActive" class="text-xs font-bold text-on-surface cursor-pointer">
                    Tampilkan di Dashboard Aplikasi Guru
                </label>
            </div>

            <!-- Modal Actions -->
            <div class="flex items-center justify-end gap-2 pt-4 border-t border-surface-container mt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl bg-surface-container-high hover:bg-surface-container text-on-surface text-xs font-bold transition-all">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-primary hover:bg-[#0f6031] text-white text-xs font-bold shadow-md shadow-emerald-900/10 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('createAnnouncementModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createAnnouncementModal').classList.add('hidden');
    }

    function openEditModal(item) {
        const form = document.getElementById('editAnnouncementForm');
        form.action = `/admin/informasi-sekolah/${item.id}`;

        document.getElementById('editTitle').value = item.title || '';
        document.getElementById('editCategory').value = item.category || 'MAKLUMAT';
        document.getElementById('editContent').value = item.content || '';
        document.getElementById('editDate').value = item.date ? item.date.substring(0, 10) : '';
        document.getElementById('editLetterNumber').value = item.letter_number || '';
        document.getElementById('editExternalLink').value = item.external_link || '';
        document.getElementById('editIsActive').checked = Boolean(item.is_active);

        const notice = document.getElementById('currentAttachmentNotice');
        if (item.attachment_path) {
            notice.textContent = `Berkas saat ini: ${item.attachment_path} (Unggah file baru jika ingin mengganti)`;
        } else {
            notice.textContent = 'Belum ada lampiran berkas.';
        }

        document.getElementById('editAnnouncementModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editAnnouncementModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
