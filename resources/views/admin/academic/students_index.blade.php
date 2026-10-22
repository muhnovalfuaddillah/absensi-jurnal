@extends('layouts.admin')

@section('title', 'Data Siswa & Kenaikan Kelas - HadirGuru')

@section('content')
<div class="flex flex-col gap-space-md">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
        <div>
            <h1 class="font-headline-md text-headline-md font-bold text-on-surface">Data Siswa & Kenaikan Kelas</h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Kelola kontak orang tua (WhatsApp), rombongan belajar, dan kenaikan kelas massal</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openPromoteModal()" class="flex items-center gap-2 px-3.5 py-2.5 bg-secondary text-white rounded-lg font-label-md text-label-md font-semibold hover:bg-secondary/90 transition-all shadow-sm">
                <span class="material-symbols-outlined text-[20px]">upgrade</span>
                <span>Kenaikan / Pindah Kelas</span>
            </button>
            <button type="button" onclick="openAddStudentModal()" class="flex items-center gap-2 px-space-md py-2.5 bg-primary text-on-primary rounded-lg font-label-md text-label-md font-semibold hover:bg-primary/90 transition-all shadow-sm">
                <span class="material-symbols-outlined text-[20px]">person_add</span>
                <span>Tambah Siswa</span>
            </button>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
        <form method="GET" action="{{ route('admin.students.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="relative sm:col-span-2">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-[18px] text-slate-400">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIS, atau nama orang tua..."
                       class="w-full pl-9 pr-3 py-2 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary text-on-surface">
            </div>

            <div>
                <select name="class_id" class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary text-on-surface">
                    <option value="">Semua Rombel / Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->academic_year }})</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-primary text-on-primary rounded-lg text-xs font-semibold hover:bg-primary/90">
                    Filter
                </button>
                <a href="{{ route('admin.students.index') }}" class="py-2 px-3 bg-surface-container text-on-surface-variant rounded-lg text-xs font-semibold hover:bg-surface-container-high">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Form Kenaikan Kelas Massal Menggunakan Checkbox Tabel -->
    <form id="bulkActionForm" action="{{ route('admin.students.promote') }}" method="POST">
        @csrf

        <!-- Table Students -->
        <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
            <div class="p-space-md border-b border-surface-container flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-xs text-on-surface">Total: {{ $students->total() }} Siswa</span>
                    <span id="selectedCounterBadge" class="hidden px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary text-white">
                        0 Siswa Dipilih
                    </span>
                </div>

                <!-- Tombol Cepat saat ada siswa yang dicentang -->
                <div id="bulkActionToolbar" class="hidden flex items-center gap-2">
                    <button type="button" onclick="openPromoteModalWithSelected()" class="px-3 py-1.5 bg-secondary text-white rounded-lg text-xs font-bold flex items-center gap-1.5 shadow-xs">
                        <span class="material-symbols-outlined text-[16px]">upgrade</span>
                        <span>Proses Kenaikan Kelas</span>
                    </button>
                    <button type="button" onclick="graduateSelected()" class="px-3 py-1.5 bg-slate-700 text-white rounded-lg text-xs font-bold flex items-center gap-1.5 shadow-xs">
                        <span class="material-symbols-outlined text-[16px]">school</span>
                        <span>Luluskan Siswa</span>
                    </button>
                </div>
            </div>

            @if($students->isEmpty())
                <div class="py-12 text-center text-xs text-slate-400 flex flex-col items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[36px] text-slate-300">group_off</span>
                    <span>Belum ada data siswa yang cocok dengan kriteria pencarian.</span>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-surface-container bg-surface-container-low text-on-surface-variant uppercase text-[10px] tracking-wider">
                                <th class="py-3 px-3 w-10 text-center">
                                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this.checked)" class="rounded text-primary focus:ring-0">
                                </th>
                                <th class="py-3 px-space-md">Nama Lengkap Siswa</th>
                                <th class="py-3 px-space-md">Kelas / Rombel</th>
                                <th class="py-3 px-space-md">NIS / NISN</th>
                                <th class="py-3 px-space-md">Kontak Orang Tua (WhatsApp)</th>
                                <th class="py-3 px-space-md text-center">Status</th>
                                <th class="py-3 px-space-md text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container-low">
                            @foreach($students as $idx => $s)
                                <tr class="hover:bg-surface-container-low/50 transition-colors">
                                    <td class="py-3 px-3 text-center">
                                        <input type="checkbox" name="student_ids[]" value="{{ $s->id }}" onchange="updateSelectedCount()" class="student-checkbox rounded text-primary focus:ring-0">
                                    </td>
                                    <td class="py-3 px-space-md">
                                        <div class="flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 {{ $s->gender == 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                                {{ $s->gender }}
                                            </span>
                                            <div>
                                                <p class="font-bold text-on-surface">{{ $s->name }}</p>
                                                <span class="text-[10px] text-slate-400">ID: #{{ $s->id }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-space-md">
                                        <span class="px-2.5 py-1 rounded-md font-bold text-[11px] bg-primary/10 text-primary border border-primary/20">
                                            {{ $s->schoolClass->name ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-space-md font-mono text-slate-600">
                                        <p>{{ $s->nis ?: '-' }}</p>
                                        <span class="text-[10px] text-slate-400">NISN: {{ $s->nisn ?: '-' }}</span>
                                    </td>
                                    <td class="py-3 px-space-md">
                                        @if($s->parent_phone)
                                            <div class="flex items-center gap-2">
                                                <div>
                                                    <p class="font-semibold text-on-surface text-xs">{{ $s->parent_name ?: 'Orang Tua / Wali' }}</p>
                                                    <span class="text-[10px] font-mono text-slate-500">{{ $s->parent_phone }}</span>
                                                </div>
                                                <a href="https://wa.me/{{ $s->formatted_parent_phone }}" target="_blank"
                                                   title="Chat WhatsApp Orang Tua"
                                                   class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-colors">
                                                    <span class="material-symbols-outlined text-[16px]">chat</span>
                                                </a>
                                            </div>
                                        @else
                                            <span class="text-[11px] text-slate-400 italic">Belum ada nomor WA</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-space-md text-center">
                                        @php
                                            $st = $s->status ?? ($s->is_active ? 'aktif' : 'lulus');
                                            $stColors = [
                                                'aktif' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'lulus' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'mutasi' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'keluar' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase border {{ $stColors[$st] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ $st }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-space-md text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" onclick="openEditStudentModal({{ json_encode($s) }})"
                                                    title="Edit Siswa & Kontak Ortu"
                                                    class="w-7 h-7 rounded-lg bg-surface-container-high hover:bg-primary hover:text-white flex items-center justify-center transition-colors">
                                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                            </button>
                                            <form action="{{ route('admin.students.destroy', $s->id) }}" method="POST"
                                                  data-confirm="Hapus data siswa {{ $s->name }}?"
                                                  data-confirm-title="Hapus Siswa" data-confirm-btn="Ya, Hapus">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition-colors">
                                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="p-space-md border-t border-surface-container">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </form>

</div>

<!-- MODAL KENAIKAN / PINDAH KELAS MASSAL -->
<div id="promoteModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl w-full max-w-lg shadow-xl border border-surface-container overflow-hidden animate-scale-up">
        <div class="p-space-md bg-secondary text-white flex items-center justify-between">
            <h3 class="font-headline-sm text-sm font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">upgrade</span>
                <span>Kenaikan Kelas & Promosi Siswa</span>
            </h3>
            <button type="button" onclick="closePromoteModal()" class="text-white/80 hover:text-white">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form action="{{ route('admin.students.promote') }}" method="POST" class="p-space-md flex flex-col gap-4">
            @csrf

            <!-- Hidden input container for selected IDs -->
            <div id="promoteStudentIdsContainer"></div>

            <div class="p-3 bg-secondary/10 rounded-xl border border-secondary/20 text-xs text-secondary-dark flex items-start gap-2">
                <span class="material-symbols-outlined text-[18px] shrink-0 text-secondary">info</span>
                <span>Pilih kelas tujuan kenaikan kelas. Siswa terpilih akan langsung dipindahkan ke rombel kelas yang baru dan statusnya diaktifkan.</span>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Aksi Pembaharuan <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-surface-container-high bg-surface-container-low cursor-pointer hover:bg-surface-container has-[:checked]:border-secondary has-[:checked]:bg-secondary/10">
                        <input type="radio" name="action" value="promote" checked onchange="togglePromoteAction(this.value)" class="text-secondary focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-on-surface">Naik / Pindah Kelas</p>
                            <p class="text-[10px] text-slate-500">Pindah ke rombel baru</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-surface-container-high bg-surface-container-low cursor-pointer hover:bg-surface-container has-[:checked]:border-secondary has-[:checked]:bg-secondary/10">
                        <input type="radio" name="action" value="graduate" onchange="togglePromoteAction(this.value)" class="text-secondary focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-on-surface">Luluskan Siswa</p>
                            <p class="text-[10px] text-slate-500">Set sebagai Alumni</p>
                        </div>
                    </label>
                </div>
            </div>

            <div id="targetClassGroup">
                <label class="block text-xs font-bold text-on-surface mb-1">Pilih Kelas / Rombel Tujuan <span class="text-rose-500">*</span></label>
                <select name="target_class_id" id="targetClassSelect" required
                        class="w-full py-2.5 px-3 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-secondary font-semibold text-on-surface">
                    <option value="">-- Pilih Kelas Tujuan --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">Tingkat {{ $c->grade_level }} - {{ $c->name }} ({{ $c->academic_year }})</option>
                    @endforeach
                </select>
            </div>

            <div class="p-3 bg-surface-container-low rounded-xl border border-surface-container">
                <p class="text-xs font-bold text-on-surface mb-1">Status Pilihan Siswa:</p>
                <p id="promoteSelectedSummary" class="text-xs text-slate-600">0 siswa telah dipilih dari tabel.</p>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-surface-container">
                <button type="button" onclick="closePromoteModal()" class="px-4 py-2 bg-surface-container text-on-surface-variant rounded-xl text-xs font-semibold hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-4 py-2 bg-secondary text-white rounded-xl text-xs font-bold hover:bg-secondary/90 shadow-sm flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">check</span>
                    <span>Proses Sekarang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL TAMBAH SISWA -->
<div id="addStudentModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl w-full max-w-md shadow-xl border border-surface-container overflow-hidden animate-scale-up">
        <div class="p-space-md bg-surface-container-low border-b border-surface-container flex items-center justify-between">
            <h3 class="font-headline-sm text-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">person_add</span>
                <span>Tambah Siswa Baru</span>
            </h3>
            <button type="button" onclick="closeAddStudentModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form action="{{ route('admin.students.store') }}" method="POST" class="p-space-md flex flex-col gap-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Kelas / Rombel <span class="text-rose-500">*</span></label>
                <select name="class_id" required class="w-full py-2 px-3 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->academic_year }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Nama Lengkap Siswa <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Muhammad Rayhan"
                       class="w-full py-2 px-3 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">NIS</label>
                    <input type="text" name="nis" placeholder="Nomor Induk Siswa"
                           class="w-full py-2 px-3 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">NISN</label>
                    <input type="text" name="nisn" placeholder="Nomor Induk Nasional"
                           class="w-full py-2 px-3 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Jenis Kelamin <span class="text-rose-500">*</span></label>
                <div class="flex items-center gap-4 mt-1">
                    <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold">
                        <input type="radio" name="gender" value="L" checked class="text-primary focus:ring-0">
                        <span>Laki-laki</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold">
                        <input type="radio" name="gender" value="P" class="text-primary focus:ring-0">
                        <span>Perempuan</span>
                    </label>
                </div>
            </div>

            <!-- Kontak Orang Tua / Wali -->
            <div class="p-3 bg-surface-container-low rounded-xl border border-surface-container flex flex-col gap-2.5">
                <p class="text-xs font-bold text-on-surface flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-primary text-[16px]">contact_phone</span>
                    <span>Kontak Orang Tua / Wali (Untuk Notifikasi WA)</span>
                </p>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Nama Orang Tua / Wali</label>
                    <input type="text" name="parent_name" placeholder="Contoh: Bpk. Joko / Ibu Siti"
                           class="w-full py-1.5 px-3 text-xs rounded-lg bg-surface-container-lowest border border-surface-container-high focus:outline-none focus:border-primary">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Nomor WhatsApp Orang Tua</label>
                    <input type="text" name="parent_phone" placeholder="Contoh: 08123456789"
                           class="w-full py-1.5 px-3 text-xs rounded-lg bg-surface-container-lowest border border-surface-container-high focus:outline-none focus:border-primary font-mono">
                    <p class="text-[10px] text-slate-400 mt-0.5">Digunakan untuk kirim otomatis notifikasi jika siswa Alpa / Tidak Masuk.</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Status Siswa</label>
                <select name="status" class="w-full py-2 px-3 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                    <option value="aktif" selected>Aktif</option>
                    <option value="lulus">Lulus / Alumni</option>
                    <option value="mutasi">Mutasi / Pindah</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-surface-container">
                <button type="button" onclick="closeAddStudentModal()" class="px-4 py-2 bg-surface-container text-on-surface-variant rounded-xl text-xs font-semibold hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-xl text-xs font-semibold hover:bg-primary/90 shadow-sm">Simpan Siswa</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT SISWA -->
<div id="editStudentModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl w-full max-w-md shadow-xl border border-surface-container overflow-hidden animate-scale-up">
        <div class="p-space-md bg-surface-container-low border-b border-surface-container flex items-center justify-between">
            <h3 class="font-headline-sm text-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">edit</span>
                <span>Edit Data Siswa</span>
            </h3>
            <button type="button" onclick="closeEditStudentModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form id="editStudentForm" method="POST" class="p-space-md flex flex-col gap-3">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Kelas / Rombel</label>
                <select name="class_id" id="editStudentClassId" required class="w-full py-2 px-3 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->academic_year }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Nama Lengkap Siswa</label>
                <input type="text" name="name" id="editStudentName" required
                       class="w-full py-2 px-3 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">NIS</label>
                    <input type="text" name="nis" id="editStudentNis"
                           class="w-full py-2 px-3 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">NISN</label>
                    <input type="text" name="nisn" id="editStudentNisn"
                           class="w-full py-2 px-3 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Jenis Kelamin</label>
                <div class="flex items-center gap-4 mt-1">
                    <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold">
                        <input type="radio" name="gender" id="editGenderL" value="L" class="text-primary focus:ring-0">
                        <span>Laki-laki</span>
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer text-xs font-semibold">
                        <input type="radio" name="gender" id="editGenderP" value="P" class="text-primary focus:ring-0">
                        <span>Perempuan</span>
                    </label>
                </div>
            </div>

            <!-- Kontak Orang Tua / Wali -->
            <div class="p-3 bg-surface-container-low rounded-xl border border-surface-container flex flex-col gap-2.5">
                <p class="text-xs font-bold text-on-surface flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-primary text-[16px]">contact_phone</span>
                    <span>Kontak Orang Tua / Wali (Untuk Notifikasi WA)</span>
                </p>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Nama Orang Tua / Wali</label>
                    <input type="text" name="parent_name" id="editStudentParentName" placeholder="Contoh: Bpk. Joko / Ibu Siti"
                           class="w-full py-1.5 px-3 text-xs rounded-lg bg-surface-container-lowest border border-surface-container-high focus:outline-none focus:border-primary">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Nomor WhatsApp Orang Tua</label>
                    <input type="text" name="parent_phone" id="editStudentParentPhone" placeholder="Contoh: 08123456789"
                           class="w-full py-1.5 px-3 text-xs rounded-lg bg-surface-container-lowest border border-surface-container-high focus:outline-none focus:border-primary font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Status Siswa</label>
                <select name="status" id="editStudentStatus" class="w-full py-2 px-3 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                    <option value="aktif">Aktif</option>
                    <option value="lulus">Lulus / Alumni</option>
                    <option value="mutasi">Mutasi / Pindah</option>
                    <option value="keluar">Keluar</option>
                </select>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-surface-container">
                <button type="button" onclick="closeEditStudentModal()" class="px-4 py-2 bg-surface-container text-on-surface-variant rounded-xl text-xs font-semibold hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-xl text-xs font-semibold hover:bg-primary/90 shadow-sm">Update Siswa</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleSelectAll(isChecked) {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => cb.checked = isChecked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selected = document.querySelectorAll('.student-checkbox:checked');
        const count = selected.length;
        const badge = document.getElementById('selectedCounterBadge');
        const toolbar = document.getElementById('bulkActionToolbar');

        if (count > 0) {
            badge.innerText = `${count} Siswa Dipilih`;
            badge.classList.remove('hidden');
            toolbar.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
            toolbar.classList.add('hidden');
        }
    }

    function getSelectedStudentIds() {
        const selected = document.querySelectorAll('.student-checkbox:checked');
        return Array.from(selected).map(cb => cb.value);
    }

    function openPromoteModal() {
        const ids = getSelectedStudentIds();
        setupPromoteForm(ids);
        document.getElementById('promoteModal').classList.remove('hidden');
    }

    function openPromoteModalWithSelected() {
        const ids = getSelectedStudentIds();
        if (ids.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Ada Siswa Dipilih',
                text: 'Centang kotak pada baris siswa yang ingin dinaikkan kelas terlebih dahulu.',
            });
            return;
        }
        setupPromoteForm(ids);
        document.getElementById('promoteModal').classList.remove('hidden');
    }

    function setupPromoteForm(ids) {
        const container = document.getElementById('promoteStudentIdsContainer');
        const summary = document.getElementById('promoteSelectedSummary');
        container.innerHTML = '';

        if (ids.length === 0) {
            summary.innerHTML = `<span class="text-amber-600 font-semibold">Peringatan: Belum ada siswa yang dicentang. Silakan centang siswa di tabel terlebih dahulu.</span>`;
        } else {
            summary.innerHTML = `<span class="text-emerald-700 font-bold">Sebanyak ${ids.length} siswa</span> telah dipilih untuk diproses.`;
            ids.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'student_ids[]';
                input.value = id;
                container.appendChild(input);
            });
        }
    }

    function togglePromoteAction(val) {
        const grp = document.getElementById('targetClassGroup');
        const select = document.getElementById('targetClassSelect');
        if (val === 'graduate') {
            grp.classList.add('hidden');
            select.required = false;
        } else {
            grp.classList.remove('hidden');
            select.required = true;
        }
    }

    function graduateSelected() {
        const ids = getSelectedStudentIds();
        if (ids.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Belum Ada Siswa Dipilih',
                text: 'Centang kotak pada baris siswa yang ingin diluluskan terlebih dahulu.',
            });
            return;
        }

        Swal.fire({
            title: 'Konfirmasi Kelulusan Siswa',
            text: `Apakah Anda yakin ingin memperbarui status ${ids.length} siswa terpilih menjadi LULUS / Alumni?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#1e40af',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Luluskan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                setupPromoteForm(ids);
                const form = document.querySelector('#promoteModal form');
                const radioGraduate = form.querySelector('input[name="action"][value="graduate"]');
                if (radioGraduate) radioGraduate.checked = true;
                togglePromoteAction('graduate');
                form.submit();
            }
        });
    }

    function closePromoteModal() {
        document.getElementById('promoteModal').classList.add('hidden');
    }

    function openAddStudentModal() {
        document.getElementById('addStudentModal').classList.remove('hidden');
    }
    function closeAddStudentModal() {
        document.getElementById('addStudentModal').classList.add('hidden');
    }

    function openEditStudentModal(s) {
        const form = document.getElementById('editStudentForm');
        form.action = `{{ url('admin/siswa') }}/${s.id}`;
        document.getElementById('editStudentClassId').value = s.class_id;
        document.getElementById('editStudentName').value = s.name;
        document.getElementById('editStudentNis').value = s.nis || '';
        document.getElementById('editStudentNisn').value = s.nisn || '';
        document.getElementById('editStudentParentName').value = s.parent_name || '';
        document.getElementById('editStudentParentPhone').value = s.parent_phone || '';
        document.getElementById('editStudentStatus').value = s.status || (s.is_active ? 'aktif' : 'lulus');

        if (s.gender === 'L') {
            document.getElementById('editGenderL').checked = true;
        } else {
            document.getElementById('editGenderP').checked = true;
        }
        document.getElementById('editStudentModal').classList.remove('hidden');
    }
    function closeEditStudentModal() {
        document.getElementById('editStudentModal').classList.add('hidden');
    }
</script>
@endsection
