@extends('layouts.admin')

@section('title', 'Rekapitulasi Presensi Bulanan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">

    <!-- Tab Navigasi Antara Laporan Guru & Siswa -->
    <div class="no-print flex items-center gap-2 border-b border-surface-container pb-2 mb-6">
        <a href="{{ route('admin.attendance.recap') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold bg-[#13793e] text-white shadow-sm shadow-emerald-900/15 transition-all">
            <span class="material-symbols-outlined text-[18px]">badge</span>
            <span>Laporan Absensi Guru</span>
        </a>
        <a href="{{ route('admin.students.attendance_recap') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-on-surface-variant hover:text-on-surface hover:bg-surface-container-high transition-all">
            <span class="material-symbols-outlined text-[18px]">school</span>
            <span>Laporan Absensi Siswa</span>
        </a>
    </div>

    <!-- Top Action & Filter Bar (Hidden on Print) -->
    <div class="no-print flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-[#13793e]"></i>
                <span>Rekapitulasi Presensi Guru</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Laporan rekap kehadiran, keterlambatan, dan izin guru per periode bulan.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Filter Bulan -->
            <form method="GET" action="{{ route('admin.attendance.recap') }}" class="flex items-center gap-1.5">
                <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()" 
                       class="px-3 py-2 rounded-xl border border-slate-300 text-xs font-bold text-slate-700 bg-white shadow-xs focus:ring-2 focus:ring-[#13793e] focus:border-[#13793e]">
                <button type="submit" class="py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-xs transition-colors" title="Terapkan Filter Bulan">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </form>

            <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

            <!-- Download PDF Button -->
            <button type="button" onclick="downloadPdfReport()" 
                    class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-500/20 transition-all flex items-center gap-1.5 active:scale-95" 
                    title="Unduh Berkas PDF">
                <i class="fa-solid fa-file-pdf text-[14px]"></i>
                <span>Download PDF</span>
            </button>

            <!-- Download Excel Button -->
            <a href="{{ route('admin.attendance.recap.excel', ['month' => $month]) }}" 
               class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-500/20 transition-all flex items-center gap-1.5 active:scale-95" 
               title="Unduh Berkas Excel (.xls)">
                <i class="fa-solid fa-file-excel text-[14px]"></i>
                <span>Download Excel</span>
            </a>

            <!-- Download CSV Button -->
            <a href="{{ route('admin.attendance.recap.csv', ['month' => $month]) }}" 
               class="px-3 py-2 rounded-xl bg-slate-700 hover:bg-slate-800 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-1.5 active:scale-95" 
               title="Unduh Berkas CSV">
                <i class="fa-solid fa-file-csv text-[14px]"></i>
                <span class="hidden sm:inline">CSV</span>
            </a>

            <!-- Cetak Fisik / Print Browser -->
            <button type="button" onclick="window.print()" 
                    class="px-3 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-xs transition-all flex items-center gap-1.5" 
                    title="Cetak via Printer">
                <i class="fa-solid fa-print"></i>
                <span class="hidden sm:inline">Cetak</span>
            </button>
        </div>
    </div>

    <!-- Official Report Paper Container (Rendered into PDF / Print) -->
    <div id="reportPrintContainer" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8 mb-6">
        
        <!-- School Formal Letterhead (KOP SURAT) -->
        <div class="flex items-center justify-between pb-6 border-b-2 border-slate-900 mb-6 gap-4">
            <img src="{{ $setting->logo_url ?? asset('logo.jpg') }}" alt="Logo Sekolah" class="w-20 h-20 object-contain shrink-0 rounded-lg">
            <div class="text-center flex-1">
                @if($setting->foundation_name)
                    <h3 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-slate-600">{{ $setting->foundation_name }}</h3>
                @endif
                <h2 class="text-xl sm:text-2xl font-black uppercase tracking-wider text-slate-900">{{ $setting->school_name }}</h2>
                <p class="text-xs sm:text-sm text-slate-600 font-medium mt-0.5">{{ $setting->school_address ?: 'Laporan Rekapitulasi Presensi & Keterlambatan Dewan Guru' }}</p>
                @if($setting->school_phone || $setting->school_email)
                    <p class="text-[11px] text-slate-500 font-normal">
                        @if($setting->school_phone) Telp: {{ $setting->school_phone }} @endif
                        @if($setting->school_phone && $setting->school_email) &bull; @endif
                        @if($setting->school_email) Email: {{ $setting->school_email }} @endif
                    </p>
                @endif
                <p class="text-xs text-slate-500 font-semibold mt-1">
                    Periode: <span class="text-[#13793e] font-bold">{{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F Y') }}</span>
                    &bull; Jam Masuk: {{ substr($setting->time_in, 0, 5) }} &bull; Batas Toleransi: {{ substr($setting->time_in_late_threshold, 0, 5) }} &bull; Jam Pulang: {{ substr($setting->time_out, 0, 5) }}
                </p>
            </div>
            <div class="w-20 hidden sm:block"></div>
        </div>

        <!-- Table Rekapitulasi Data Guru -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider font-bold border-y border-slate-200">
                    <tr>
                        <th class="py-3 px-3 text-center w-10">No</th>
                        <th class="py-3 px-3">Nama Lengkap Guru</th>
                        <th class="py-3 px-3">NIP / Jabatan</th>
                        <th class="py-3 px-3 text-center text-emerald-700 bg-emerald-50/50">Tepat Waktu</th>
                        <th class="py-3 px-3 text-center text-amber-700 bg-amber-50/50">Terlambat</th>
                        <th class="py-3 px-3 text-center text-amber-800">Total Telat</th>
                        <th class="py-3 px-3 text-center text-purple-700">Izin</th>
                        <th class="py-3 px-3 text-center text-rose-700">Sakit</th>
                        <th class="py-3 px-3 text-center text-slate-700">Alpa</th>
                        <th class="py-3 px-3 text-center font-black bg-emerald-50 text-emerald-900">Total Hadir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700 font-medium">
                    @php
                        $totalTepat = 0;
                        $totalTerlambat = 0;
                        $totalMenitTelat = 0;
                        $totalIzin = 0;
                        $totalSakit = 0;
                        $totalAlpa = 0;
                        $totalKehadiranAll = 0;
                    @endphp
                    @forelse($recapData as $index => $item)
                        @php
                            $totalTepat += $item->hadir;
                            $totalTerlambat += $item->terlambat;
                            $totalMenitTelat += $item->total_late_minutes;
                            $totalIzin += $item->izin;
                            $totalSakit += $item->sakit;
                            $totalAlpa += $item->alpa;
                            $totalKehadiranAll += $item->total_kehadiran;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3 px-3 text-center font-bold text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3 px-3 font-bold text-slate-900 text-xs sm:text-sm">
                                {{ $item->teacher->name }}
                            </td>
                            <td class="py-3 px-3 text-slate-500">
                                <div>{{ $item->teacher->nip ?: '-' }}</div>
                                <div class="text-[11px] text-emerald-700 font-semibold">{{ $item->teacher->subject ?: 'Guru' }}</div>
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-emerald-600 bg-emerald-50/30">
                                {{ $item->hadir }} hari
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-amber-600 bg-amber-50/30">
                                {{ $item->terlambat }} hari
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-amber-800 font-mono">
                                @if($item->total_late_minutes > 0)
                                    {{ $item->total_late_minutes }} mnt
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center font-semibold text-purple-600">
                                {{ $item->izin }}
                            </td>
                            <td class="py-3 px-3 text-center font-semibold text-rose-600">
                                {{ $item->sakit }}
                            </td>
                            <td class="py-3 px-3 text-center font-semibold text-slate-500">
                                {{ $item->alpa }}
                            </td>
                            <td class="py-3 px-3 text-center font-black bg-emerald-50/70 text-emerald-800 text-sm">
                                {{ $item->total_kehadiran }} hari
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">Belum ada data guru terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($recapData) > 0)
                <tfoot class="bg-slate-100/80 font-bold border-t-2 border-slate-300 text-slate-900">
                    <tr>
                        <td colspan="3" class="py-3 px-3 text-right font-black uppercase tracking-wider text-slate-700">Total Keseluruhan:</td>
                        <td class="py-3 px-3 text-center text-emerald-700 font-black bg-emerald-100/50">{{ $totalTepat }} hari</td>
                        <td class="py-3 px-3 text-center text-amber-700 font-black bg-amber-100/50">{{ $totalTerlambat }} hari</td>
                        <td class="py-3 px-3 text-center text-amber-900 font-mono">{{ $totalMenitTelat }} mnt</td>
                        <td class="py-3 px-3 text-center text-purple-800">{{ $totalIzin }}</td>
                        <td class="py-3 px-3 text-center text-rose-800">{{ $totalSakit }}</td>
                        <td class="py-3 px-3 text-center text-slate-600">{{ $totalAlpa }}</td>
                        <td class="py-3 px-3 text-center text-emerald-900 font-black bg-emerald-100/80 text-sm">{{ $totalKehadiranAll }} hari</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

        <!-- Tanda Tangan Cetak Formal (Khas Dokumen Sekolah Resmi) -->
        <div class="mt-12 pt-6 grid grid-cols-2 text-center text-xs text-slate-800">
            <div>
                <p class="text-slate-600">Mengetahui,</p>
                <p class="font-bold text-sm text-slate-900">Kepala Sekolah</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">{{ $setting->headmaster_name ? '( ' . $setting->headmaster_name . ' )' : '( .................................................... )' }}</p>
                <p class="text-[11px] text-slate-500">NIP. {{ $setting->headmaster_nip ?: '....................................................' }}</p>
            </div>

            <div>
                <p class="text-slate-600">{{ date('d F Y') }}</p>
                <p class="font-bold text-sm text-slate-900">Petugas Presensi / Guru Piket</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">( .................................................... )</p>
                <p class="text-[11px] text-slate-500">NIP. ....................................................</p>
            </div>
        </div>

    </div>

</div>

<!-- Library html2pdf.js via CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    function downloadPdfReport() {
        const reportElement = document.getElementById('reportPrintContainer');
        const monthLabel = "{{ \Carbon\Carbon::parse($month . '-01')->translatedFormat('F_Y') }}";
        const filename = `Rekap_Presensi_Guru_${monthLabel}.pdf`;

        Swal.fire({
            title: 'Membuat Berkas PDF...',
            html: '<div class="text-xs text-slate-500">Mohon tunggu sebentar, dokumen PDF sedang di-generate...</div>',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Konfigurasi pembuatan PDF
        const opt = {
            margin:       [8, 8, 8, 8],
            filename:     filename,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, logging: false },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };

        html2pdf().set(opt).from(reportElement).save().then(() => {
            Swal.fire({
                icon: 'success',
                title: 'PDF Berhasil Diunduh!',
                text: `Berkas ${filename} telah tersimpan di komputer/HP Anda.`,
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        }).catch((err) => {
            console.error('PDF error:', err);
            Swal.fire({
                icon: 'info',
                title: 'Buka Jendela Cetak',
                text: 'Silakan simpan via opsi "Simpan sebagai PDF" pada jendela cetak browser.',
                confirmButtonColor: '#004ac6',
                confirmButtonText: 'Buka Cetak'
            }).then(() => {
                window.print();
            });
        });
    }
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        background: white !important;
        color: black !important;
    }
    #reportPrintContainer {
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>
@endsection
