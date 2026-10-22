<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Presensi Guru - {{ $monthLabel }}</title>
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
            background-color: #e2e8f0;
            color: #0f172a;
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
            background-color: #dbeafe;
            font-weight: bold;
        }
        .bg-hadir {
            background-color: #dcfce7;
        }
        .bg-telat {
            background-color: #fef3c7;
        }
        .text-format {
            mso-number-format: "\@";
        }
        .number-format {
            mso-number-format: "#,##0";
        }
    </style>
</head>
<body>

    <!-- KOP SURAT FORMAL -->
    <table>
        <tr>
            <td colspan="13" class="kop-title no-border">
                {{ strtoupper($setting->foundation_name ?: 'YAYASAN BARUL ULUM') }}
            </td>
        </tr>
        <tr>
            <td colspan="13" class="kop-title no-border" style="font-size: 18pt; color: #1e3a8a;">
                {{ strtoupper($setting->school_name ?: 'YAYASAN BARUL ULUM') }}
            </td>
        </tr>
        <tr>
            <td colspan="13" class="kop-sub no-border">
                LAPORAN REKAPITULASI PRESENSI & KETERLAMBATAN DEWAN GURU
            </td>
        </tr>
        <tr>
            <td colspan="13" class="kop-info no-border">
                {{ $setting->school_address ?: 'Alamat Sekolah / Yayasan' }}
                @if($setting->school_phone) | Telp: {{ $setting->school_phone }} @endif
                @if($setting->school_email) | Email: {{ $setting->school_email }} @endif
            </td>
        </tr>
        <tr>
            <td colspan="13" class="kop-info no-border" style="font-weight: bold; color: #1e40af; padding-bottom: 15px;">
                Periode: {{ $monthLabel }} (Tanggal {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }})
            </td>
        </tr>
        <tr><td colspan="13" class="no-border" style="height: 10px;"></td></tr>
    </table>

    <!-- TABEL DATA REKAPITULASI -->
    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 40px;">No</th>
                <th rowspan="2" style="width: 220px;">Nama Lengkap Guru</th>
                <th rowspan="2" style="width: 140px;">NIP</th>
                <th rowspan="2" style="width: 140px;">NUPTK</th>
                <th rowspan="2" style="width: 150px;">Mata Pelajaran</th>
                <th rowspan="2" style="width: 140px;">Status Kepegawaian</th>
                <th colspan="7">Rekapitulasi Kehadiran (Hari)</th>
            </tr>
            <tr>
                <th style="width: 80px; background-color: #bbf7d0;">Tepat Waktu</th>
                <th style="width: 80px; background-color: #fde68a;">Terlambat</th>
                <th style="width: 90px; background-color: #fed7aa;">Total Telat (Menit)</th>
                <th style="width: 60px;">Izin</th>
                <th style="width: 60px;">Sakit</th>
                <th style="width: 60px;">Alpa</th>
                <th style="width: 90px; background-color: #bfdbfe;">Total Hadir</th>
            </tr>
        </thead>
        <tbody>
            @php
                $sumHadir = 0;
                $sumTelat = 0;
                $sumTelatMenit = 0;
                $sumIzin = 0;
                $sumSakit = 0;
                $sumAlpa = 0;
                $sumTotalHadir = 0;
            @endphp
            @forelse($recapData as $index => $item)
                @php
                    $sumHadir += $item->hadir;
                    $sumTelat += $item->terlambat;
                    $sumTelatMenit += $item->total_late_minutes;
                    $sumIzin += $item->izin;
                    $sumSakit += $item->sakit;
                    $sumAlpa += $item->alpa;
                    $sumTotalHadir += $item->total_kehadiran;
                @endphp
                <tr>
                    <td class="text-center number-format">{{ $index + 1 }}</td>
                    <td class="text-left" style="font-weight: bold;">{{ $item->teacher->name }}</td>
                    <td class="text-center text-format">{{ $item->teacher->nip ?: '-' }}</td>
                    <td class="text-center text-format">{{ $item->teacher->nuptk ?: '-' }}</td>
                    <td class="text-left">{{ $item->teacher->subject ?: '-' }}</td>
                    <td class="text-left">{{ $item->teacher->employment_status ?: 'Guru Tetap' }}</td>
                    <td class="text-center bg-hadir number-format">{{ $item->hadir }}</td>
                    <td class="text-center bg-telat number-format">{{ $item->terlambat }}</td>
                    <td class="text-center number-format">{{ $item->total_late_minutes }}</td>
                    <td class="text-center number-format">{{ $item->izin }}</td>
                    <td class="text-center number-format">{{ $item->sakit }}</td>
                    <td class="text-center number-format">{{ $item->alpa }}</td>
                    <td class="text-center bg-total number-format" style="font-weight: bold;">{{ $item->total_kehadiran }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center">Tidak ada data guru yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="bg-total">
                <td colspan="6" class="text-right" style="font-weight: bold; padding-right: 15px;">TOTAL KESELURUHAN:</td>
                <td class="text-center number-format">{{ $sumHadir }}</td>
                <td class="text-center number-format">{{ $sumTelat }}</td>
                <td class="text-center number-format">{{ $sumTelatMenit }}</td>
                <td class="text-center number-format">{{ $sumIzin }}</td>
                <td class="text-center number-format">{{ $sumSakit }}</td>
                <td class="text-center number-format">{{ $sumAlpa }}</td>
                <td class="text-center number-format" style="font-size: 12pt;">{{ $sumTotalHadir }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- LEMBAR PENGESAHAN TANDA TANGAN -->
    <table style="margin-top: 30px;">
        <tr>
            <td colspan="4" class="no-border text-center" style="width: 35%;">
                Mengetahui,<br>
                <strong>Kepala Sekolah</strong>
                <br><br><br><br><br>
                <u><strong>{{ $setting->headmaster_name ?: '( .................................................... )' }}</strong></u><br>
                NIP. {{ $setting->headmaster_nip ?: '....................................................' }}
            </td>
            <td colspan="5" class="no-border"></td>
            <td colspan="4" class="no-border text-center" style="width: 35%;">
                {{ date('d F Y') }}<br>
                <strong>Petugas Presensi / Guru Piket</strong>
                <br><br><br><br><br>
                <u><strong>( .................................................... )</strong></u><br>
                NIP. ....................................................
            </td>
        </tr>
    </table>

</body>
</html>
