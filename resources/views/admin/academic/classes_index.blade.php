@extends('layouts.admin')

@section('title', 'Data Kelas - SIMPRES BU')

@section('content')
<div class="flex flex-col gap-space-md">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
        <div>
            <h1 class="font-headline-md text-headline-md font-bold text-on-surface">Data Kelas & Rombongan Belajar</h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Kelola daftar kelas, tingkat, dan penugasan wali kelas</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.students.index') }}" class="px-3.5 py-2.5 bg-surface-container text-on-surface rounded-lg font-label-md text-label-md font-semibold hover:bg-surface-container-high transition-all">
                Lihat Daftar Siswa
            </a>
            <button type="button" onclick="openAddClassModal()" class="flex items-center gap-2 px-space-md py-2.5 bg-primary text-on-primary rounded-lg font-label-md text-label-md font-semibold hover:bg-primary/90 transition-all shadow-sm">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span>Tambah Kelas Baru</span>
            </button>
        </div>
    </div>

    <!-- Grid / Cards Kelas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-space-md">
        @foreach($classes as $c)
            <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container p-space-md flex flex-col justify-between gap-4 hover:shadow-md transition-shadow">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-primary/10 text-primary">
                            Tingkat {{ $c->grade_level }}
                        </span>
                        <span class="text-xs text-on-surface-variant font-mono">{{ $c->academic_year }}</span>
                    </div>

                    <div>
                        <h2 class="font-headline-sm text-base font-bold text-on-surface">{{ $c->name }}</h2>
                        <p class="text-xs text-on-surface-variant mt-1 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">person</span>
                            <span>Wali: <b>{{ $c->homeroomTeacher->name ?? 'Belum Ditentukan' }}</b></span>
                        </p>
                    </div>

                    <div class="flex items-center gap-4 mt-2 pt-2 border-t border-surface-container-low text-xs text-slate-600">
                        <span class="inline-flex items-center gap-1 font-semibold">
                            <span class="material-symbols-outlined text-[16px] text-primary">groups</span>
                            <span>{{ $c->students_count }} Siswa Terdaftar</span>
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-surface-container">
                    <a href="{{ route('admin.students.index', ['class_id' => $c->id]) }}" class="text-xs font-semibold text-primary hover:underline flex items-center gap-1">
                        <span>Kelola Siswa</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>

                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('admin.classes.print_qr', $c->id) }}" target="_blank" title="Cetak Poster QR Code Kelas"
                           class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 hover:bg-emerald-100 flex items-center gap-1 text-[11px] font-bold border border-emerald-200 transition-colors">
                            <span class="material-symbols-outlined text-[16px]">qr_code_2</span>
                            <span>QR Kelas</span>
                        </a>

                        <button type="button" onclick="openEditClassModal({{ json_encode($c) }})"
                                class="w-7 h-7 rounded-lg bg-surface-container-high hover:bg-primary hover:text-white flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-[16px]">edit</span>
                        </button>
                        <form action="{{ route('admin.classes.destroy', $c->id) }}" method="POST"
                              data-confirm="Hapus kelas {{ $c->name }} beserta seluruh data siswa dan jadwalnya?"
                              data-confirm-title="Hapus Kelas" data-confirm-btn="Ya, Hapus">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition-colors">
                                <span class="material-symbols-outlined text-[16px]">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<!-- MODAL TAMBAH KELAS -->
<div id="addClassModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl w-full max-w-md shadow-xl border border-surface-container overflow-hidden animate-scale-up">
        <div class="p-space-md bg-surface-container-low border-b border-surface-container flex items-center justify-between">
            <h3 class="font-headline-sm text-sm font-bold text-on-surface">Tambah Kelas Baru</h3>
            <button type="button" onclick="closeAddClassModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form action="{{ route('admin.classes.store') }}" method="POST" class="p-space-md flex flex-col gap-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Nama Kelas <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: VII-C / VIII-A / X-IPA-1"
                       class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Tingkat / Jenjang <span class="text-rose-500">*</span></label>
                    <input type="text" name="grade_level" required placeholder="Contoh: 7 / 8 / 9 / X"
                           class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Tahun Ajaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="academic_year" value="2025/2026" required
                           class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Wali Kelas (Opsional)</label>
                <select name="homeroom_teacher_id" class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                    <option value="">-- Pilih Wali Kelas --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-surface-container">
                <button type="button" onclick="closeAddClassModal()" class="px-4 py-2 bg-surface-container text-on-surface-variant rounded-lg text-xs font-semibold hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg text-xs font-semibold hover:bg-primary/90">Simpan Kelas</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT KELAS -->
<div id="editClassModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl w-full max-w-md shadow-xl border border-surface-container overflow-hidden animate-scale-up">
        <div class="p-space-md bg-surface-container-low border-b border-surface-container flex items-center justify-between">
            <h3 class="font-headline-sm text-sm font-bold text-on-surface">Edit Data Kelas</h3>
            <button type="button" onclick="closeEditClassModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form id="editClassForm" method="POST" class="p-space-md flex flex-col gap-3">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Nama Kelas</label>
                <input type="text" name="name" id="editClassName" required
                       class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Tingkat</label>
                    <input type="text" name="grade_level" id="editGradeLevel" required
                           class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Tahun Ajaran</label>
                    <input type="text" name="academic_year" id="editAcademicYear" required
                           class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Wali Kelas</label>
                <select name="homeroom_teacher_id" id="editHomeroomId" class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                    <option value="">-- Pilih Wali Kelas --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-surface-container">
                <button type="button" onclick="closeEditClassModal()" class="px-4 py-2 bg-surface-container text-on-surface-variant rounded-lg text-xs font-semibold hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg text-xs font-semibold hover:bg-primary/90">Update Kelas</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddClassModal() {
        document.getElementById('addClassModal').classList.remove('hidden');
    }
    function closeAddClassModal() {
        document.getElementById('addClassModal').classList.add('hidden');
    }
    function openEditClassModal(c) {
        const form = document.getElementById('editClassForm');
        form.action = `{{ url('admin/kelas') }}/${c.id}`;
        document.getElementById('editClassName').value = c.name;
        document.getElementById('editGradeLevel').value = c.grade_level;
        document.getElementById('editAcademicYear').value = c.academic_year;
        document.getElementById('editHomeroomId').value = c.homeroom_teacher_id || '';
        document.getElementById('editClassModal').classList.remove('hidden');
    }
    function closeEditClassModal() {
        document.getElementById('editClassModal').classList.add('hidden');
    }
</script>
@endsection
