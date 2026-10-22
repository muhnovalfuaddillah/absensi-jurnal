@extends('layouts.admin')

@section('title', 'Data Mata Pelajaran - HadirGuru')

@section('content')
<div class="flex flex-col gap-space-md">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
        <div>
            <h1 class="font-headline-md text-headline-md font-bold text-on-surface">Data Mata Pelajaran</h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Kelola daftar mata pelajaran kurikulum dan penugasan pengajaran</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.schedules.index') }}" class="px-3.5 py-2.5 bg-surface-container text-on-surface rounded-lg font-label-md text-label-md font-semibold hover:bg-surface-container-high transition-all flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                <span>Lihat Jadwal Pelajaran</span>
            </a>
            <button type="button" onclick="openAddSubjectModal()" class="flex items-center gap-2 px-space-md py-2.5 bg-primary text-on-primary rounded-lg font-label-md text-label-md font-semibold hover:bg-primary/90 transition-all shadow-sm">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span>Tambah Mapel Baru</span>
            </button>
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-space-md">
        <div class="bg-surface-container-lowest p-space-md rounded-xl border border-surface-container shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Total Mata Pelajaran</p>
                <h3 class="text-2xl font-bold font-headline-md text-primary mt-1">{{ $totalSubjects }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">auto_stories</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-md rounded-xl border border-surface-container shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Jadwal Aktif Terdaftar</p>
                <h3 class="text-2xl font-bold font-headline-md text-secondary mt-1">{{ $totalSchedules }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-secondary/10 text-secondary flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">event_available</span>
            </div>
        </div>

        <div class="bg-surface-container-lowest p-space-md rounded-xl border border-surface-container shadow-xs flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wider">Total Sesi Jurnal</p>
                <h3 class="text-2xl font-bold font-headline-md text-emerald-600 mt-1">{{ $totalJournals }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="material-symbols-outlined text-[26px]">menu_book</span>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
        <form action="{{ route('admin.subjects.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama mapel, kode (contoh: MAT, IPA), atau deskripsi..."
                       class="w-full pl-10 pr-4 py-2 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary text-on-surface">
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-xl text-xs font-bold hover:bg-primary/90 transition-all flex items-center gap-1.5 shadow-xs">
                    <span class="material-symbols-outlined text-[16px]">filter_alt</span>
                    <span>Cari</span>
                </button>
                @if($search)
                    <a href="{{ route('admin.subjects.index') }}" class="px-3 py-2 bg-surface-container text-on-surface rounded-xl text-xs font-bold hover:bg-surface-container-high transition-all flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                        <span>Reset</span>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Data Mata Pelajaran -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-surface-container text-on-surface-variant font-label-md text-xs">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4 w-28">Kode Mapel</th>
                        <th class="py-3 px-4">Nama Mata Pelajaran</th>
                        <th class="py-3 px-4">Deskripsi / Ruang Lingkup</th>
                        <th class="py-3 px-4 text-center w-32">Jadwal Kelas</th>
                        <th class="py-3 px-4 text-center w-32">Jurnal Guru</th>
                        <th class="py-3 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-container text-xs text-on-surface">
                    @forelse($subjects as $idx => $s)
                        <tr class="hover:bg-surface-container-low/40 transition-colors">
                            <td class="py-3 px-4 text-center text-on-surface-variant font-semibold">
                                {{ $subjects->firstItem() + $idx }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-1 rounded-lg bg-primary/10 text-primary font-mono font-bold text-xs border border-primary/20">
                                    {{ $s->code }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <p class="font-bold text-on-surface text-xs">{{ $s->name }}</p>
                                <span class="text-[10px] text-on-surface-variant">ID: #{{ $s->id }}</span>
                            </td>
                            <td class="py-3 px-4 text-on-surface-variant">
                                {{ $s->description ?: '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $s->lesson_schedules_count > 0 ? 'bg-secondary/10 text-secondary' : 'bg-slate-100 text-slate-400' }}">
                                    <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                                    <span>{{ $s->lesson_schedules_count }} Sesi</span>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $s->teaching_journals_count > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-400' }}">
                                    <span class="material-symbols-outlined text-[14px]">menu_book</span>
                                    <span>{{ $s->teaching_journals_count }} Jurnal</span>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <!-- Tombol Edit -->
                                    <button type="button" onclick="openEditSubjectModal({{ json_encode($s) }})"
                                            title="Edit Mata Pelajaran"
                                            class="w-7 h-7 rounded-lg bg-surface-container-high hover:bg-primary hover:text-white text-on-surface flex items-center justify-center transition-colors">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </button>

                                    <!-- Tombol Hapus -->
                                    @if($s->lesson_schedules_count > 0 || $s->teaching_journals_count > 0)
                                        <button type="button" 
                                                onclick="Swal.fire({icon: 'warning', title: 'Tidak Dapat Dihapus', text: 'Mata pelajaran ini masih digunakan pada {{ $s->lesson_schedules_count }} jadwal dan {{ $s->teaching_journals_count }} jurnal mengajar.'})"
                                                title="Tidak dapat dihapus karena masih digunakan"
                                                class="w-7 h-7 rounded-lg bg-slate-100 text-slate-300 cursor-not-allowed flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                        </button>
                                    @else
                                        <form action="{{ route('admin.subjects.destroy', $s->id) }}" method="POST"
                                              data-confirm="Hapus mata pelajaran '{{ $s->name }}' ({{ $s->code }})?"
                                              data-confirm-title="Hapus Mata Pelajaran" data-confirm-btn="Ya, Hapus">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus Mata Pelajaran"
                                                    class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center transition-colors">
                                                <span class="material-symbols-outlined text-[16px]">delete</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-on-surface-variant">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-[40px] text-slate-300">auto_stories</span>
                                    <p class="font-bold text-xs">Belum ada data mata pelajaran.</p>
                                    <button type="button" onclick="openAddSubjectModal()" class="mt-2 text-xs text-primary font-bold hover:underline">
                                        + Tambah Mata Pelajaran Sekarang
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subjects->hasPages())
            <div class="p-space-md border-t border-surface-container">
                {{ $subjects->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL TAMBAH MATA PELAJARAN -->
<div id="addSubjectModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl w-full max-w-md shadow-xl border border-surface-container overflow-hidden animate-scale-up">
        <div class="p-space-md bg-surface-container-low border-b border-surface-container flex items-center justify-between">
            <h3 class="font-headline-sm text-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">add_circle</span>
                <span>Tambah Mata Pelajaran Baru</span>
            </h3>
            <button type="button" onclick="closeAddSubjectModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form action="{{ route('admin.subjects.store') }}" method="POST" class="p-space-md flex flex-col gap-3">
            @csrf

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Kode Mata Pelajaran <span class="text-rose-500">*</span></label>
                <input type="text" name="code" placeholder="Contoh: MAT, IPA, BIG, PAI" required maxlength="20"
                       style="text-transform: uppercase;"
                       oninput="this.value = this.value.toUpperCase()"
                       class="w-full px-3 py-2 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary text-on-surface font-mono font-bold">
                <p class="text-[10px] text-slate-400 mt-0.5">Maks. 20 karakter, huruf kapital singkatan (harus unik).</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Nama Mata Pelajaran <span class="text-rose-500">*</span></label>
                <input type="text" name="name" placeholder="Contoh: Matematika, Bahasa Inggris" required maxlength="100"
                       class="w-full px-3 py-2 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary text-on-surface font-semibold">
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Deskripsi / Ruang Lingkup (Opsional)</label>
                <textarea name="description" rows="3" placeholder="Keterangan materi, kompetensi inti, atau catatan mapel..."
                          class="w-full px-3 py-2 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary text-on-surface"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-surface-container mt-1">
                <button type="button" onclick="closeAddSubjectModal()" class="px-4 py-2 text-xs font-bold text-on-surface-variant hover:bg-surface-container rounded-xl transition-all">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-all shadow-sm flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Simpan Mata Pelajaran</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT MATA PELAJARAN -->
<div id="editSubjectModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl w-full max-w-md shadow-xl border border-surface-container overflow-hidden animate-scale-up">
        <div class="p-space-md bg-surface-container-low border-b border-surface-container flex items-center justify-between">
            <h3 class="font-headline-sm text-sm font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-[20px]">edit</span>
                <span>Edit Mata Pelajaran</span>
            </h3>
            <button type="button" onclick="closeEditSubjectModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form id="editSubjectForm" method="POST" class="p-space-md flex flex-col gap-3">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Kode Mata Pelajaran <span class="text-rose-500">*</span></label>
                <input type="text" name="code" id="editSubjectCode" required maxlength="20"
                       style="text-transform: uppercase;"
                       oninput="this.value = this.value.toUpperCase()"
                       class="w-full px-3 py-2 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary text-on-surface font-mono font-bold">
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Nama Mata Pelajaran <span class="text-rose-500">*</span></label>
                <input type="text" name="name" id="editSubjectName" required maxlength="100"
                       class="w-full px-3 py-2 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary text-on-surface font-semibold">
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface mb-1">Deskripsi / Ruang Lingkup</label>
                <textarea name="description" id="editSubjectDesc" rows="3"
                          class="w-full px-3 py-2 text-xs rounded-xl bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary text-on-surface"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-surface-container mt-1">
                <button type="button" onclick="closeEditSubjectModal()" class="px-4 py-2 text-xs font-bold text-on-surface-variant hover:bg-surface-container rounded-xl transition-all">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold text-white bg-primary hover:bg-primary/90 rounded-xl transition-all shadow-sm flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddSubjectModal() {
        document.getElementById('addSubjectModal').classList.remove('hidden');
    }

    function closeAddSubjectModal() {
        document.getElementById('addSubjectModal').classList.add('hidden');
    }

    function openEditSubjectModal(subject) {
        const form = document.getElementById('editSubjectForm');
        form.action = `{{ url('admin/mata-pelajaran') }}/${subject.id}`;
        document.getElementById('editSubjectCode').value = subject.code || '';
        document.getElementById('editSubjectName').value = subject.name || '';
        document.getElementById('editSubjectDesc').value = subject.description || '';
        document.getElementById('editSubjectModal').classList.remove('hidden');
    }

    function closeEditSubjectModal() {
        document.getElementById('editSubjectModal').classList.add('hidden');
    }
</script>
@endsection
