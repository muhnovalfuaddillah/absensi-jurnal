@extends('layouts.admin')

@section('title', 'Manajemen Jadwal Pelajaran - HadirGuru')

@section('content')
<div class="flex flex-col gap-space-md">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-space-sm bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
        <div>
            <h1 class="font-headline-md text-headline-md font-bold text-on-surface">Jadwal Pelajaran Sekolah</h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant">Kelola jadwal tatap muka kelas, mata pelajaran, dan guru pengampu</p>
        </div>
        <button type="button" onclick="openAddScheduleModal()" class="flex items-center gap-2 px-space-md py-2.5 bg-primary text-on-primary rounded-lg font-label-md text-label-md font-semibold hover:bg-primary/90 transition-all shadow-sm">
            <span class="material-symbols-outlined text-[20px]">add</span>
            <span>Tambah Jadwal Baru</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-surface-container-lowest p-space-md rounded-xl shadow-sm border border-surface-container">
        <form method="GET" action="{{ route('admin.schedules.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">Hari:</label>
                <select name="day" class="w-full text-xs rounded-lg bg-surface-container-low border border-surface-container-high py-2 px-3 text-on-surface focus:outline-none focus:border-primary">
                    <option value="">Semua Hari</option>
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $d)
                        <option value="{{ $d }}" {{ $day == $d ? 'selected' : '' }}>{{ $d }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">Kelas:</label>
                <select name="class_id" class="w-full text-xs rounded-lg bg-surface-container-low border border-surface-container-high py-2 px-3 text-on-surface focus:outline-none focus:border-primary">
                    <option value="">Semua Kelas</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-on-surface-variant mb-1">Guru Pengampu:</label>
                <select name="teacher_id" class="w-full text-xs rounded-lg bg-surface-container-low border border-surface-container-high py-2 px-3 text-on-surface focus:outline-none focus:border-primary">
                    <option value="">Semua Guru</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}" {{ $teacherId == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-primary text-on-primary rounded-lg text-xs font-semibold hover:bg-primary/90">
                    Filter
                </button>
                <a href="{{ route('admin.schedules.index') }}" class="py-2 px-3 bg-surface-container text-on-surface-variant rounded-lg text-xs font-semibold hover:bg-surface-container-high">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Schedules -->
    <div class="bg-surface-container-lowest rounded-xl shadow-sm border border-surface-container overflow-hidden">
        <div class="p-space-md border-b border-surface-container flex items-center justify-between">
            <span class="font-bold text-xs text-on-surface">Total: {{ $schedules->total() }} Jadwal Terdaftar</span>
            <span class="text-xs text-on-surface-variant">Tahun Ajaran 2025/2026</span>
        </div>

        @if($schedules->isEmpty())
            <div class="py-12 text-center text-xs text-slate-400 flex flex-col items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[36px] text-slate-300">event_busy</span>
                <span>Belum ada jadwal pelajaran yang sesuai filter.</span>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-surface-container bg-surface-container-low text-on-surface-variant uppercase text-[10px] tracking-wider">
                            <th class="py-3 px-space-md">Hari & Jam</th>
                            <th class="py-3 px-space-md">Kelas</th>
                            <th class="py-3 px-space-md">Mata Pelajaran</th>
                            <th class="py-3 px-space-md">Guru Pengampu</th>
                            <th class="py-3 px-space-md">Ruangan</th>
                            <th class="py-3 px-space-md text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-container-low">
                        @foreach($schedules as $s)
                            <tr class="hover:bg-surface-container-low/50 transition-colors">
                                <td class="py-3 px-space-md">
                                    <span class="px-2 py-0.5 rounded font-bold text-[11px] bg-primary/10 text-primary mr-1.5">{{ $s->day }}</span>
                                    <span class="font-mono text-slate-600 font-semibold">{{ $s->formatted_time }} WIB</span>
                                </td>
                                <td class="py-3 px-space-md font-bold text-on-surface">
                                    {{ $s->schoolClass->name ?? '-' }}
                                </td>
                                <td class="py-3 px-space-md">
                                    <p class="font-bold text-primary">{{ $s->subject->name ?? '-' }}</p>
                                    <p class="text-[10px] text-slate-400">Kode: {{ $s->subject->code ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-space-md">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-surface-container flex items-center justify-center font-bold text-[10px] text-primary">
                                            {{ $s->teacher->initials ?? 'GU' }}
                                        </div>
                                        <span class="font-semibold text-on-surface">{{ $s->teacher->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-space-md text-slate-600">
                                    {{ $s->room ?: '-' }}
                                </td>
                                <td class="py-3 px-space-md text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button type="button" onclick="openEditScheduleModal({{ json_encode($s) }})"
                                                class="w-7 h-7 rounded-lg bg-surface-container-high hover:bg-primary hover:text-white flex items-center justify-center transition-colors">
                                            <span class="material-symbols-outlined text-[16px]">edit</span>
                                        </button>
                                        <form action="{{ route('admin.schedules.destroy', $s->id) }}" method="POST"
                                              data-confirm="Apakah Anda yakin ingin menghapus jadwal ini?"
                                              data-confirm-title="Hapus Jadwal" data-confirm-btn="Ya, Hapus">
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
                {{ $schedules->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL TAMBAH JADWAL -->
<div id="addScheduleModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl w-full max-w-lg shadow-xl border border-surface-container overflow-hidden animate-scale-up">
        <div class="p-space-md bg-surface-container-low border-b border-surface-container flex items-center justify-between">
            <h3 class="font-headline-sm text-sm font-bold text-on-surface">Tambah Jadwal Pelajaran</h3>
            <button type="button" onclick="closeAddScheduleModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form action="{{ route('admin.schedules.store') }}" method="POST" class="p-space-md flex flex-col gap-3">
            @csrf

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Hari <span class="text-rose-500">*</span></label>
                    <select name="day" required class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Kelas <span class="text-rose-500">*</span></label>
                    <select name="class_id" required class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Mata Pelajaran <span class="text-rose-500">*</span></label>
                    <select name="subject_id" required class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                        @foreach($subjects as $sb)
                            <option value="{{ $sb->id }}">{{ $sb->name }} ({{ $sb->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Guru Pengampu <span class="text-rose-500">*</span></label>
                    <select name="teacher_id" required class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Jam Mulai <span class="text-rose-500">*</span></label>
                    <input type="time" name="start_time" value="07:30" required class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Jam Selesai <span class="text-rose-500">*</span></label>
                    <input type="time" name="end_time" value="09:00" required class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Ruangan</label>
                    <input type="text" name="room" placeholder="Contoh: R. 7A / Lab" class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-surface-container">
                <button type="button" onclick="closeAddScheduleModal()" class="px-4 py-2 bg-surface-container text-on-surface-variant rounded-lg text-xs font-semibold hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg text-xs font-semibold hover:bg-primary/90">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT JADWAL -->
<div id="editScheduleModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-surface-container-lowest rounded-2xl w-full max-w-lg shadow-xl border border-surface-container overflow-hidden animate-scale-up">
        <div class="p-space-md bg-surface-container-low border-b border-surface-container flex items-center justify-between">
            <h3 class="font-headline-sm text-sm font-bold text-on-surface">Edit Jadwal Pelajaran</h3>
            <button type="button" onclick="closeEditScheduleModal()" class="text-on-surface-variant hover:text-on-surface">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form id="editScheduleForm" method="POST" class="p-space-md flex flex-col gap-3">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Hari</label>
                    <select name="day" id="editDay" required class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                        @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Kelas</label>
                    <select name="class_id" id="editClassId" required class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Mata Pelajaran</label>
                    <select name="subject_id" id="editSubjectId" required class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                        @foreach($subjects as $sb)
                            <option value="{{ $sb->id }}">{{ $sb->name }} ({{ $sb->code }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Guru Pengampu</label>
                    <select name="teacher_id" id="editTeacherId" required class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Jam Mulai</label>
                    <input type="time" name="start_time" id="editStartTime" required class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Jam Selesai</label>
                    <input type="time" name="end_time" id="editEndTime" required class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface mb-1">Ruangan</label>
                    <input type="text" name="room" id="editRoom" class="w-full py-2 px-3 text-xs rounded-lg bg-surface-container-low border border-surface-container-high focus:outline-none focus:border-primary">
                </div>
            </div>

            <div class="pt-2 flex items-center justify-end gap-2 border-t border-surface-container">
                <button type="button" onclick="closeEditScheduleModal()" class="px-4 py-2 bg-surface-container text-on-surface-variant rounded-lg text-xs font-semibold hover:bg-surface-container-high">Batal</button>
                <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg text-xs font-semibold hover:bg-primary/90">Update Jadwal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddScheduleModal() {
        document.getElementById('addScheduleModal').classList.remove('hidden');
    }
    function closeAddScheduleModal() {
        document.getElementById('addScheduleModal').classList.add('hidden');
    }
    function openEditScheduleModal(s) {
        const form = document.getElementById('editScheduleForm');
        form.action = `{{ url('admin/jadwal-pelajaran') }}/${s.id}`;
        document.getElementById('editDay').value = s.day;
        document.getElementById('editClassId').value = s.class_id;
        document.getElementById('editSubjectId').value = s.subject_id;
        document.getElementById('editTeacherId').value = s.teacher_id;
        document.getElementById('editStartTime').value = s.start_time.substring(0, 5);
        document.getElementById('editEndTime').value = s.end_time.substring(0, 5);
        document.getElementById('editRoom').value = s.room || '';
        document.getElementById('editScheduleModal').classList.remove('hidden');
    }
    function closeEditScheduleModal() {
        document.getElementById('editScheduleModal').classList.add('hidden');
    }
</script>
@endsection
