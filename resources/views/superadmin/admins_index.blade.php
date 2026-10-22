@extends('layouts.admin')

@section('title', 'Manajemen Admin Sekolah - Superadmin Multi-Sekolah')

@section('content')
<div class="space-y-6">

    <!-- Top Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-3xl border border-slate-100 shadow-xs">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-[#13793e] text-[11px] font-black border border-emerald-200 mb-1.5">
                <span class="material-symbols-outlined text-[14px]">manage_accounts</span>
                <span>School Administrator Accounts</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                Kelola Administrator Sekolah
            </h1>
            <p class="text-xs text-slate-400 mt-0.5">
                Atur akun administrator untuk tiap-tiap sekolah yang bertugas mengelola guru, siswa, jadwal, dan presensi.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="openAddAdminModal()" 
                    class="px-4 py-2.5 rounded-2xl bg-[#13793e] hover:bg-[#0f6332] text-white font-bold text-xs flex items-center gap-2 shadow-sm transition-all active:scale-95">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>Tambah Admin Sekolah Baru</span>
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form action="{{ route('superadmin.admins.index') }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto flex-1">
            <div class="relative flex-1 sm:max-w-xs">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 text-[18px]">search</span>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari nama admin, username, email..." 
                       class="w-full pl-9 pr-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold focus:outline-none focus:border-[#13793e] focus:bg-white transition-all">
            </div>

            <select name="school_id" onchange="this.form.submit()" 
                    class="px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700 focus:outline-none focus:border-[#13793e]">
                <option value="">Semua Sekolah</option>
                @foreach($schools as $sch)
                    <option value="{{ $sch->id }}" {{ ($schoolFilter == $sch->id) ? 'selected' : '' }}>
                        {{ $sch->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                Filter
            </button>
            @if($search || $schoolFilter)
                <a href="{{ route('superadmin.admins.index') }}" class="text-xs text-rose-600 hover:underline font-bold self-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel Daftar Admin Sekolah -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 font-bold uppercase tracking-wider text-[11px] border-b border-slate-100">
                        <th class="py-3.5 px-4 sm:px-6">Nama Administrator</th>
                        <th class="py-3.5 px-4">Sekolah yang Dikelola</th>
                        <th class="py-3.5 px-4">Username & Email Login</th>
                        <th class="py-3.5 px-4 text-center">Wewenang / Role</th>
                        <th class="py-3.5 px-4 text-center">Terdaftar Sejak</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($admins as $admin)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Nama Admin -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#13793e] flex items-center justify-center font-black text-sm shrink-0">
                                        {{ strtoupper(substr($admin->name, 0, 2)) }}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-extrabold text-slate-900 text-sm">{{ $admin->name }}</span>
                                        <span class="text-[11px] text-slate-400">ID User: #{{ $admin->id }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Sekolah yang Dikelola -->
                            <td class="py-4 px-4">
                                @if($admin->school)
                                    <div class="flex items-center gap-2">
                                        <img src="{{ $admin->school->logo_url }}" alt="Logo" class="w-6 h-6 rounded-lg object-contain bg-slate-100 p-0.5 shrink-0">
                                        <div class="flex flex-col">
                                            <span class="font-extrabold text-slate-800">{{ $admin->school->name }}</span>
                                            <span class="text-[10px] text-slate-400">{{ $admin->school->foundation_name ?: '-' }}</span>
                                        </div>
                                    </div>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Belum Ditentukan
                                    </span>
                                @endif
                            </td>

                            <!-- Username & Email -->
                            <td class="py-4 px-4">
                                <div class="flex flex-col">
                                    <span class="font-mono font-bold text-slate-800 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] text-slate-400">account_circle</span>
                                        {{ $admin->username }}
                                    </span>
                                    <span class="text-[11px] text-slate-400">{{ $admin->email }}</span>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="py-4 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200">
                                    Admin Sekolah
                                </span>
                            </td>

                            <!-- Tanggal Daftar -->
                            <td class="py-4 px-4 text-center text-slate-500 font-medium">
                                {{ $admin->created_at ? $admin->created_at->format('d M Y') : '-' }}
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 sm:px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Edit Admin -->
                                    <button type="button" 
                                            onclick='openEditAdminModal(@json($admin))'
                                            class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all shadow-xs"
                                            title="Edit Akun Admin">
                                        <span class="material-symbols-outlined text-[18px]">edit</span>
                                    </button>

                                    <!-- Hapus Admin -->
                                    <form action="{{ route('superadmin.admins.destroy', $admin->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ $admin->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white flex items-center justify-center transition-all shadow-xs"
                                                title="Hapus Akun Admin">
                                            <span class="material-symbols-outlined text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">person_off</span>
                                <p class="text-sm font-semibold">Belum ada akun admin sekolah yang terdaftar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admins->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $admins->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ================= MODAL TAMBAH ADMIN SEKOLAH ================= -->
<div id="addAdminModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-lg flex flex-col shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-sm">
                    <span class="material-symbols-outlined text-[20px]">person_add</span>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Tambah Admin Sekolah</h3>
                    <p class="text-[11px] text-slate-400">Buat akun pengelola untuk sekolah tertentu</p>
                </div>
            </div>
            <button type="button" onclick="closeAddAdminModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Form Body -->
        <form action="{{ route('superadmin.admins.store') }}" method="POST" class="p-5 sm:p-6 space-y-3.5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Sekolah Penugasan <span class="text-rose-500">*</span></label>
                <select name="school_id" required 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#13793e]">
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach($schools as $sch)
                        <option value="{{ $sch->id }}">{{ $sch->name }} ({{ $sch->foundation_name ?: 'Mandiri' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Admin <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Ahmad Baihaqi, S.Kom" 
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#13793e]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Username Login <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" required placeholder="Contoh: admin_mts" 
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-[#13793e]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="admin@sekolah.sch.id" 
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-[#13793e]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi / Password <span class="text-rose-500">*</span></label>
                <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" 
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-[#13793e]">
            </div>

            <!-- Modal Actions -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAddAdminModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#13793e] hover:bg-[#0f6332] text-white font-black text-xs shadow-sm transition-all active:scale-95">
                    Buat Akun Admin
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL EDIT ADMIN SEKOLAH ================= -->
<div id="editAdminModal" class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-lg flex flex-col shadow-2xl border border-slate-100 animate-in fade-in zoom-in-95 duration-150">
        <!-- Modal Header -->
        <div class="p-5 sm:p-6 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-[#eaf8ef] text-[#13793e] flex items-center justify-center text-sm">
                    <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Edit Akun Admin</h3>
                    <p class="text-[11px] text-slate-400" id="editAdminSubtitle">Perbarui data atau reset password</p>
                </div>
            </div>
            <button type="button" onclick="closeEditAdminModal()" class="w-8 h-8 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Modal Form Body -->
        <form id="editAdminForm" action="" method="POST" class="p-5 sm:p-6 space-y-3.5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Sekolah Penugasan <span class="text-rose-500">*</span></label>
                <select id="edit_admin_school_id" name="school_id" required 
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-[#13793e]">
                    @foreach($schools as $sch)
                        <option value="{{ $sch->id }}">{{ $sch->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap Admin <span class="text-rose-500">*</span></label>
                <input type="text" id="edit_admin_name" name="name" required 
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:outline-none focus:border-[#13793e]">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Username Login <span class="text-rose-500">*</span></label>
                    <input type="text" id="edit_admin_username" name="username" required 
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-mono font-bold text-slate-800 focus:outline-none focus:border-[#13793e]">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Email <span class="text-rose-500">*</span></label>
                    <input type="email" id="edit_admin_email" name="email" required 
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-[#13793e]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Ganti Password (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" minlength="6" placeholder="Masukkan password baru jika ingin mengubah" 
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-[#13793e]">
            </div>

            <!-- Modal Actions -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeEditAdminModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
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
    function openAddAdminModal() {
        document.getElementById('addAdminModal').classList.remove('hidden');
    }
    function closeAddAdminModal() {
        document.getElementById('addAdminModal').classList.add('hidden');
    }

    function openEditAdminModal(admin) {
        document.getElementById('editAdminForm').action = '/superadmin/admin-sekolah/' + admin.id;
        document.getElementById('editAdminSubtitle').textContent = 'Admin: ' + admin.name;
        document.getElementById('edit_admin_school_id').value = admin.school_id || '';
        document.getElementById('edit_admin_name').value = admin.name || '';
        document.getElementById('edit_admin_username').value = admin.username || '';
        document.getElementById('edit_admin_email').value = admin.email || '';

        document.getElementById('editAdminModal').classList.remove('hidden');
    }
    function closeEditAdminModal() {
        document.getElementById('editAdminModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
