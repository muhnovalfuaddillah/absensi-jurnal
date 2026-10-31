<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Kelas {{ $class->name }} - {{ $setting->school_name ?? 'SIMPRES BU' }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpg') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; margin: 0; padding: 0; }
            .print-page { box-shadow: none !important; border: 2px solid #0f172a !important; margin: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-4 sm:p-8 font-['Inter'] antialiased">

    <!-- Print Navigation Bar -->
    <div class="no-print w-full max-w-xl mb-4 flex items-center justify-between">
        <a href="{{ route('admin.classes.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-slate-700 hover:text-[#13793e] rounded-xl font-semibold text-xs shadow-sm border border-slate-200 transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Kembali ke Data Kelas</span>
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#13793e] hover:bg-[#0f6031] text-white rounded-xl font-bold text-xs shadow-md shadow-emerald-900/10 transition-all">
            <span class="material-symbols-outlined text-[18px]">print</span>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <!-- Official Placard / Poster -->
    <div class="print-page w-full max-w-xl bg-white rounded-3xl shadow-xl border-4 border-[#13793e] p-8 sm:p-10 flex flex-col items-center text-center relative overflow-hidden">
        
        <!-- Watermark Background Decor -->
        <div class="absolute -right-12 -bottom-12 opacity-5 pointer-events-none">
            <span class="material-symbols-outlined text-[240px]">qr_code_2</span>
        </div>

        <!-- School Header -->
        <div class="flex items-center justify-center gap-4 pb-6 border-b-2 border-slate-200 w-full">
            <img src="{{ $setting->logo_url ?? asset('logo.jpg') }}" alt="Logo" class="w-16 h-16 object-contain rounded-xl border border-slate-200 p-1">
            <div class="text-left">
                <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">
                    {{ $setting->foundation_name ?: 'YAYASAN PENDIDIKAN BARUL ULUM' }}
                </p>
                <h1 class="font-['Plus_Jakarta_Sans'] font-extrabold text-lg text-slate-900 leading-tight">
                    {{ $setting->school_name ?? 'SMA / SMK Negeri 1 Teladan' }}
                </h1>
                <p class="text-xs text-slate-600 mt-0.5">SIMPRES BU (Sistem Informasi Manajemen Presensi Bahrul Ulum)</p>
            </div>
        </div>

        <!-- Banner Title -->
        <div class="mt-6 mb-4">
            <span class="px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-900 font-extrabold text-xs uppercase tracking-widest inline-block border border-emerald-200 font-mono">
                TITIK CHECK-IN PEMBELAJARAN KELAS
            </span>
            <h2 class="font-['Plus_Jakarta_Sans'] font-black text-4xl text-slate-900 mt-2 tracking-tight">
                {{ $class->name }}
            </h2>
            <p class="text-xs font-semibold text-slate-600 mt-1">
                Tingkat {{ $class->grade_level }} • Tahun Ajaran {{ $class->academic_year }}
            </p>
        </div>

        <!-- QR Code Canvas / Image -->
        <div class="my-4 p-4 bg-white rounded-2xl border-2 border-slate-900 shadow-md inline-block">
            <img src="{{ $class->qr_code_image_url }}" alt="QR Code {{ $class->name }}" class="w-64 h-64 object-contain">
        </div>

        <!-- QR Token Reference -->
        <div class="bg-slate-50 px-4 py-1.5 rounded-lg border border-slate-200 font-mono text-xs font-bold text-slate-700">
            Token: {{ $class->qr_code_token }}
        </div>

        <!-- Instructions Box -->
        <div class="mt-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 text-xs w-full text-left flex items-start gap-3">
            <span class="material-symbols-outlined text-amber-700 text-[24px] shrink-0 mt-0.5">info</span>
            <div>
                <p class="font-bold text-amber-900 mb-0.5">Petunjuk Bapak/Ibu Guru:</p>
                <p class="leading-relaxed">
                    Sebelum memulai kegiatan tatap muka di kelas ini, buka aplikasi <b>SIMPRES BU</b> di ponsel Anda, lalu pindai (scan) QR Code di atas. Form jurnal dan presensi siswa akan otomatis terbuka setelah QR terverifikasi.
                </p>
            </div>
        </div>

        <!-- Footer Card -->
        <div class="mt-6 pt-4 border-t border-slate-200 w-full flex items-center justify-between text-xs text-slate-500 font-medium">
            <span>Wali Kelas: <b>{{ $class->homeroomTeacher->name ?? 'Belum Ditentukan' }}</b></span>
            <span>Dicetak: {{ now()->translatedFormat('d F Y') }}</span>
        </div>

    </div>

</body>
</html>
