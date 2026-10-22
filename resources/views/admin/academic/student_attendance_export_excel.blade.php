<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi Siswa - {{ $selectedClass->name ?? 'Kelas' }} - {{ $monthLabel }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11pt;
            color: #000;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #333333;
            padding: 6px 8px;
            vertical-align: middle;
        }
        th {
            background-color: #d1fae5;
            color: #064e3b;
            font-weight: bold;
            text-align: center;
        }
        .kop-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            border: none;
        }
        .kop-sub {
            font-size: 12pt;
            text-align: center;
            border: none;
        }
        .kop-info {
            font-size: 9pt;
            color: #555;
            text-align: center;
            border: none;
        }
        .no-border {
            border: none !important;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .text-left {
            text-align: left;
        }
        .bg-total {
            background-color: #ecfdf5;
            font-weight: bold;
        }
        .bg-hadir {
            background-color: #f0fdf4;
            color: #15803d;
            font-weight: bold;
        }
        .bg-sakit {
            background-color: #fffbeb;
            color: #b45309;
            font-weight: bold;
        }
        .bg-izin {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: bold;
        }
        .bg-alpa {
            background-color: #fef2f2;
            color: #b91c1c;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <!-- KOP RESMI LAPORAN -->
    <table>
        <tr>
            <td colspan="9" class="kop-sub">{{ strtoupper($setting->foundation_name ?: 'YAYASAN PENDIDIKAN') }}</td>
        </tr>
        <tr>
            <td colspan="9" class="kop-title">{{ strtoupper($setting->school_name ?: 'MADRASAH') }}</td>
        </tr>
        <tr>
            <td colspan="9" class="kop-info">
                {{ $setting->school_address ?: 'Alamat Sekolah / Madrasah' }}
                @if($setting->school_phone) | Telp: {{ $setting->school_phone }} @endif
                @if($setting->school_email) | Email: {{ $setting->school_email }} @endif
            </td>
        </tr>
        <tr>
            <td colspan="9" class="no-border" style="height: 10px;"></td>
        </tr>
        <tr>
            <td colspan="9" class="no-border" style="font-weight: bold; font-size: 13pt; text-align: center; border-bottom: 2px solid #000;">
                LAPORAN REKAPITULASI PRESENSI KEHADIRAN SISWA
            </td>
        </tr>
        <tr>
            <td colspan="9" class="no-border" style="height: 8px;"></td>
        </tr>
        <tr>
            <td colspan="4" class="no-border" style="font-weight: bold;">
                Kelas: {{ $selectedClass->name ?? '-' }} (Tingkat {{ $selectedClass->grade_level ?? '-' }})
            </td>
            <td colspan="5" class="no-border text-right" style="font-weight: bold;">
                Periode Bulan: {{ $monthLabel }}
            </td>
        </tr>
        <tr>
            <td colspan="4" class="no-border">
                Wali Kelas: {{ $selectedClass->homeroomTeacher->name ?? 'Belum Ditentukan' }}
            </td>
            <td colspan="5" class="no-border text-right">
                Tahun Ajaran: {{ $selectedClass->academic_year ?? date('Y') . '/' . (date('Y') + 1) }}
            </td>
        </tr>
        <tr>
            <td colspan="4" class="no-border" style="font-weight: bold;">
                Kepala Madrasah / Sekolah: {{ $setting->headmaster_name ?: 'Muh Noval Fuaddillah' }}
            </td>
            <td colspan="5" class="no-border text-right">
                NIP: {{ $setting->headmaster_nip ?: '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="9" class="no-border" style="height: 10px;"></td>
        </tr>
    </table>

    <!-- TABEL REKAPITULASI SISWA -->
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 100px;">NIS</th>
                <th style="width: 220px;">Nama Lengkap Siswa</th>
                <th style="width: 50px;">L/P</th>
                <th style="width: 75px;">Hadir (H)</th>
                <th style="width: 75px;">Sakit (S)</th>
                <th style="width: 75px;">Izin (I)</th>
                <th style="width: 75px;">Alpa (A)</th>
                <th style="width: 110px;">% Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totHadir = 0;
                $totSakit = 0;
                $totIzin = 0;
                $totAlpa = 0;
            @endphp
            @forelse($students as $idx => $s)
                @php
                    $st = $stats[$s->id] ?? ['hadir' => 0, 'sakit' => 0, 'izin' => 0, 'alpa' => 0, 'total' => 0, 'persentase' => 100];
                    $totHadir += $st['hadir'];
                    $totSakit += $st['sakit'];
                    $totIzin += $st['izin'];
                    $totAlpa += $st['alpa'];
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center" style="mso-number-format:'\@';">{{ $s->nis ?? '-' }}</td>
                    <td class="text-left" style="font-weight: 500;">{{ $s->name }}</td>
                    <td class="text-center">{{ $s->gender }}</td>
                    <td class="text-center bg-hadir">{{ $st['hadir'] }}</td>
                    <td class="text-center bg-sakit">{{ $st['sakit'] }}</td>
                    <td class="text-center bg-izin">{{ $st['izin'] }}</td>
                    <td class="text-center bg-alpa">{{ $st['alpa'] }}</td>
                    <td class="text-center bg-total">{{ $st['persentase'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="color: #64748b; padding: 20px;">
                        Belum ada data siswa terdaftar pada kelas ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($students->isNotEmpty())
        <tfoot>
            <tr style="background-color: #d1fae5; font-weight: bold;">
                <td colspan="4" class="text-right" style="font-weight: bold; text-transform: uppercase;">
                    Total Akumulasi Seluruh Siswa:
                </td>
                <td class="text-center bg-hadir">{{ $totHadir }}</td>
                <td class="text-center bg-sakit">{{ $totSakit }}</td>
                <td class="text-center bg-izin">{{ $totIzin }}</td>
                <td class="text-center bg-alpa">{{ $totAlpa }}</td>
                <td class="text-center bg-total">
                    @php
                        $grandTotal = $totHadir + $totSakit + $totIzin + $totAlpa;
                        $avgPct = $grandTotal > 0 ? round(($totHadir / $grandTotal) * 100, 1) : 100;
                    @endphp
                    Rata-rata: {{ $avgPct }}%
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    <!-- TANDA TANGAN PENGESAHAN -->
    <table>
        <tr>
            <td colspan="9" class="no-border" style="height: 30px;"></td>
        </tr>
        <tr>
            <td colspan="5" class="no-border"></td>
            <td colspan="4" class="no-border text-center" style="font-size: 10pt;">
                {{ $setting->school_city ?: 'Ditetapkan' }}, {{ now()->translatedFormat('d F Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="4" class="no-border text-center" style="font-size: 10pt;">
                Mengetahui,<br>
                <b>Kepala {{ $setting->school_name }}</b>
                <br><br><br><br>
                <u><b>{{ $setting->headmaster_name ?: 'Muh Noval Fuaddillah' }}</b></u><br>
                NIP. {{ $setting->headmaster_nip ?: '-' }}
            </td>
            <td colspan="1" class="no-border"></td>
            <td colspan="4" class="no-border text-center" style="font-size: 10pt;">
                Wali Kelas {{ $selectedClass->name ?? '' }},<br>
                <br><br><br><br>
                <u><b>{{ $selectedClass->homeroomTeacher->name ?? '..........................................' }}</b></u><br>
                NIP. {{ $selectedClass->homeroomTeacher->nip ?? '...................................' }}
            </td>
        </tr>
    </table>

</body>
</html>
