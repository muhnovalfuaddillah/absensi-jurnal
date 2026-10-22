@extends('layouts.admin')

@section('title', 'Manajemen Sekolah - Superadmin Multi-Sekolah')

@section('content')
<div class="space-y-6">

    <!-- Top Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-3xl border border-slate-100 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-[#13793e] text-[11px] font-black border border-emerald-200 mb-1.5">
                <span class="material-symbols-outlined text-[14px]">apartment</span>
                <span>Multi-School Management</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                Daftar Sekolah Terdaftar
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">
                Kelola seluruh institusi/sekolah, informasi kontak, koordinat geofence, serta jam operasional masing-masing.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="openAddSchoolModal()" 
                    class="px-4 py-2.5 rounded-2xl bg-[#13793e] hover:bg-[#0f6332] text-white font-bold text-xs flex items-center gap-2 shadow-sm transition-all active:scale-95">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>Tambah Sekolah Baru</span>
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('superadmin.schools.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto flex-1">
            <div class="relative flex-1 sm:max-w-xs">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama sekolah, NPSN, yayasan..." 
                       class="w-full pl-9 pr-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
            </div>

            <select name="status" onchange="this.form.submit()" 
                    class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 focus:outline-none focus:border-[#13793e]">
                <option value="">Semua Status</option>
                <option value="1" {{ ($status === '1') ? 'selected' : '' }}>Aktif</option>
                <option value="0" {{ ($status === '0') ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                Filter
            </button>
            @if($search || $status !== null && $status !== '')
                <a href="{{ route('superadmin.schools.index') }}" class="text-xs text-rose-600 hover:underline font-bold self-center">
                    Reset
                </a>
            @endif
        </form>

        <div class="text-xs text-slate-400 font-semibold self-center shrink-0">
            Total: <span class="font-bold text-slate-800">{{ $totalSchools }}</span> Sekolah ({{ $activeSchools }} Aktif)
        </div>
    </div>

    <!-- Tabel Daftar Sekolah -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider text-[11px] border-b border-slate-100">
                        <th class="py-3.5 px-4 sm:px-6">Nama Sekolah</th>
                        <th class="py-3.5 px-4">NPSN & Yayasan</th>
                        <th class="py-3.5 px-4">Kepala Sekolah</th>
                        <th class="py-3.5 px-4">Kontak & Alamat</th>
                        <th class="py-3.5 px-4 text-center">Statistik</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($schools as $school)
                        @php
                            $isCurrentActive = current_school_id() == $school->id;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors {{ $isCurrentActive ? 'bg-emerald-50/30' : '' }}">
                            <!-- Nama & Logo Sekolah -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $school->logo_url }}" alt="{{ $school->name }}" 
                                         class="w-11 h-11 rounded-2xl object-contain bg-white border border-slate-200 p-1 shadow-xs shrink-0">
                                    <div class="flex flex-col min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="font-extrabold text-slate-900 text-sm">{{ $school->name }}</span>
                                            @if($isCurrentActive)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    Aktif
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-slate-400 truncate max-w-[220px]">
                                            Slug: {{ $school->slug }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- NPSN & Yayasan -->
                            <td class="py-4 px-4">
                                <div class="flex flex-col">
                                    <span class="font-mono font-bold text-slate-800">{{ $school->npsn ?: '-' }}</span>
                                    <span class="text-[11px] text-slate-400">{{ $school->foundation_name ?: '-' }}</span>
                                </div>
                            </td>

                            <!-- Kepala Sekolah -->
                            <td class="py-4 px-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-800">{{ $school->headmaster_name ?: '-' }}</span>
                                    <span class="text-[11px] text-slate-400 font-mono">NIP: {{ $school->headmaster_nip ?: '-' }}</span>
                                </div>
                            </td>

                            <!-- Kontak & Alamat -->
                            <td class="py-4 px-4">
                                <div class="flex flex-col max-w-[220px]">
                                    <span class="font-semibold text-slate-800">{{ $school->phone ?: '-' }}</span>
                                    <span class="text-[11px] text-slate-400 truncate">{{ $school->address ?: '-' }}</span>
                                </div>
                            </td>

                            <!-- Statistik -->
                            <td class="py-4 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5 font-bold">
                                    <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 text-[11px]" title="Jumlah Guru">
                                        {{ $school->teachers_count }} Guru
                                    </span>
                                    <span class="px-2 py-0.5 rounded-lg bg-violet-50 text-violet-700 text-[11px]" title="Jumlah Siswa">
                                        {{ $school->students_count }} Siswa
                                    </span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4 text-center">
                                <form action="{{ route('superadmin.schools.toggle', $school->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-black cursor-pointer transition-all {{ $school->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' : 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200' }}"
                                            title="Klik untuk ubah status">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $school->is_active ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        <span>{{ $school->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 sm:px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Switch ke sekolah ini -->
                                    <form action="{{ route('superadmin.switch_school') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="school_id" value="{{ $school->id }}">
                                        <button type="submit" 
                                                class="w-8 h-8 rounded-xl bg-emerald-50 hover:bg-[#13793e] text-[#13793e] hover:text-white flex items-center justify-center transition-all shadow-xs"
                                                title="Beralih ke konteks sekolah ini">
                                            <span class="material-symbols-outlined text-[18px]">sync_alt</span>
                                        </button>
                                    </form>

                                    <!-- Edit -->
                                    <button type="button" 
                                            onclick='openEditSchoolModal(@json($school))'
                                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all shadow-xs"
                                            title="Edit Data Sekolah">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>

                                    <!-- Hapus (jika > 1 sekolah) -->
                                    @if($schools->total() > 1)
                                        <form action="{{ route('superadmin.schools.destroy', $school->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus sekolah {{ $school->name }}? PERINGATAN: Seluruh data guru, siswa, kelas, dan absensi di sekolah ini akan ikut terhapus!')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center transition-all shadow-xs"
                                                    title="Hapus Sekolah">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">search_off</span>
                                <p class="text-sm font-semibold">Tidak ada data sekolah yang sesuai dengan pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($schools->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $schools->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ================= MODAL TAMBAH SEKOLAH BARU ================= -->
<div id="addSchoolModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-sm">
                    <span class="material-symbols-outlined text-[20px]">add_business</span>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Tambah Sekolah Baru</h3>
                    <p class="text-[11px] text-slate-400">Daftarkan institusi/sekolah baru ke dalam sistem aplikasi</p>
                </div>
            </div>
            <button type="button" onclick="closeAddSchoolModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Form Body -->
        <form action="{{ route('superadmin.schools.store') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-4">
            @csrf

            <!-- Section 1: Profil Sekolah -->
            <div>
                <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#13793e]"></span>
                    Profil & Identitas Lembaga
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Resmi Sekolah <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: SMA Barul Ulum, SMP Negeri 1" 
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">NPSN</label>
                        <input type="text" name="npsn" placeholder="Nomor Pokok Sekolah Nasional" 
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-[#13793e]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Yayasan / Naungan</label>
                        <input type="text" name="foundation_name" placeholder="Contoh: Yayasan Barul Ulum" 
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#13793e]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kepala Sekolah</label>
                        <input type="text" name="headmaster_name" placeholder="Contoh: Drs. H. Ahmad Fauzi, M.Pd" 
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#13793e]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">NIP Kepala Sekolah</label>
                        <input type="text" name="headmaster_nip" placeholder="NIP Kepala Sekolah" 
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono text-slate-800 focus:outline-none focus:border-[#13793e]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" placeholder="Contoh: 081234567890" 
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#13793e]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Sekolah</label>
                        <input type="email" name="email" placeholder="admin@sekolah.sch.id" 
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-[#13793e]">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Lengkap</label>
                        <textarea name="address" rows="2" placeholder="Nama jalan, kelurahan, kecamatan, kota..." 
                                  class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-[#13793e]"></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Logo Sekolah (Opsional)</label>
                        <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" 
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-[#13793e] hover:file:bg-emerald-100 cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- Section 2: Buat Akun Admin Sekolah Langsung (Opsional) -->
            <div class="pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="text-xs font-black uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Buat Akun Administrator Sekolah (Opsional)
                    </h4>
                    <span class="text-[10px] font-semibold text-slate-400">Bisa diisi nanti</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-amber-50/50 p-3.5 rounded-2xl border border-amber-200/60">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Admin</label>
                        <input type="text" name="admin_name" placeholder="Contoh: Admin Sekolah" 
                               class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#13793e]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Username Login Admin</label>
                        <input type="text" name="admin_username" placeholder="Contoh: admin_sekolah1" 
                               class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-[#13793e]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Admin</label>
                        <input type="email" name="admin_email" placeholder="admin@sekolah.sch.id" 
                               class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-[#13793e]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Password Admin (Min. 6 Karakter)</label>
                        <input type="password" name="admin_password" placeholder="Minimal 6 karakter" 
                               class="w-full px-3 py-2 rounded-xl bg-white border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-[#13793e]">
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeAddSchoolModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#13793e] hover:bg-[#0f6332] text-white font-black text-xs shadow-sm transition-all active:scale-95">
                    Simpan Sekolah Baru
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT DATA SEKOLAH ================= -->
<div id="editSchoolModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-xl max-h-[90vh] flex flex-col shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-sm">
                    <span class="material-symbols-outlined text-[20px]">edit</span>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Edit Data Sekolah</h3>
                    <p class="text-[11px] text-slate-400" id="editSchoolTitleSubtitle">Perbarui identitas dan status sekolah</p>
                </div>
            </div>
            <button type="button" onclick="closeEditSchoolModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Form Body -->
        <form id="editSchoolForm" action="" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-3.5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Resmi Sekolah <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_name" name="name" required 
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#13793e]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">NPSN</label>
                    <input type="text" id="edit_npsn" name="npsn" 
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-[#13793e]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Yayasan</label>
                    <input type="text" id="edit_foundation_name" name="foundation_name" 
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#13793e]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kepala Sekolah</label>
                    <input type="text" id="edit_headmaster_name" name="headmaster_name" 
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#13793e]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">NIP Kepala Sekolah</label>
                    <input type="text" id="edit_headmaster_nip" name="headmaster_nip" 
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono text-slate-800 focus:outline-none focus:border-[#13793e]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Telepon</label>
                    <input type="text" id="edit_phone" name="phone" 
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-[#13793e]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email Sekolah</label>
                    <input type="email" id="edit_email" name="email" 
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-[#13793e]">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Lengkap</label>
                    <textarea id="edit_address" name="address" rows="2" 
                              class="w-full px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-[#13793e]"></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Perbarui Logo Sekolah (Opsional)</label>
                    <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" 
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-[#13793e] hover:file:bg-emerald-100 cursor-pointer">
                </div>
                <div class="sm:col-span-2 flex items-center gap-2 pt-2">
                    <input type="checkbox" id="edit_is_active" name="is_active" value="1" class="w-4 h-4 text-[#13793e] rounded focus:ring-0">
                    <label for="edit_is_active" class="text-xs font-bold text-slate-800 cursor-pointer">
                        Sekolah Berstatus Aktif (Dapat diakses oleh admin & guru)
                    </label>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeEditSchoolModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#13793e] hover:bg-[#0f6332] text-white font-black text-xs shadow-sm transition-all active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openAddSchoolModal() {
        document.getElementById('addSchoolModal').classList.remove('hidden');
    }
    function closeAddSchoolModal() {
        document.getElementById('addSchoolModal').classList.add('hidden');
    }

    function openEditSchoolModal(school) {
        document.getElementById('editSchoolForm').action = '/superadmin/sekolah/' + school.id;
        document.getElementById('editSchoolTitleSubtitle').textContent = 'ID Sekolah: #' + school.id;
        document.getElementById('edit_name').value = school.name || '';
        document.getElementById('edit_npsn').value = school.npsn || '';
        document.getElementById('edit_foundation_name').value = school.foundation_name || '';
        document.getElementById('edit_headmaster_name').value = school.headmaster_name || '';
        document.getElementById('edit_headmaster_nip').value = school.headmaster_nip || '';
        document.getElementById('edit_phone').value = school.phone || '';
        document.getElementById('edit_email').value = school.email || '';
        document.getElementById('edit_address').value = school.address || '';
        document.getElementById('edit_is_active').checked = school.is_active ? true : false;

        document.getElementById('editSchoolModal').classList.remove('hidden');
    }
    function closeEditSchoolModal() {
        document.getElementById('editSchoolModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
