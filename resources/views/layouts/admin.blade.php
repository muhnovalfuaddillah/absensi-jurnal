<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="web_dashboard" name="shell-type"/>
    <title>@yield('title', 'Monitoring Presensi Guru Real-Time') - SIMPRES BU</title>

    <link rel="icon" type="image/jpeg" href="{{ $schoolSetting->logo_url ?? asset('logo.jpg') }}"/>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#13793e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIMPRES BU">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        @layer base {
            html, body { margin: 0; padding: 0; }
            body { overscroll-behavior: none; }
            main > :first-child { margin-top: 0 !important; }
            main > :last-child { margin-bottom: 0 !important; }
        }
        ::-webkit-scrollbar { display: none; }
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
            display: block;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(19, 121, 62, 0.2);
            border-radius: 9999px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(19, 121, 62, 0.4);
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        @media print {
            aside, header, .no-print { display: none !important; }
            .pl-72 { padding-left: 0 !important; }
            main { padding-top: 0 !important; }
        }
    </style>

    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "tertiary-container": "#059669",
                        "surface-container-high": "#d4ebd9",
                        "inverse-primary": "#a7f3d0",
                        "surface-container": "#e2f0e6",
                        "on-primary-fixed": "#052e16",
                        "outline-variant": "#c2d6c7",
                        "on-tertiary-fixed-variant": "#064e3b",
                        "background": "#f4f9f5",
                        "inverse-on-surface": "#ecfdf5",
                        "surface-container-highest": "#c5e4cd",
                        "on-surface": "#0b2416",
                        "surface-tint": "#13793e",
                        "secondary-fixed": "#d1fae5",
                        "on-background": "#0b2416",
                        "secondary-fixed-dim": "#a7f3d0",
                        "surface-dim": "#cde7d3",
                        "tertiary": "#047857",
                        "primary-fixed-dim": "#a7f3d0",
                        "primary": "#13793e",
                        "on-tertiary-fixed": "#022c22",
                        "on-tertiary": "#ffffff",
                        "tertiary-fixed-dim": "#6ee7b7",
                        "on-tertiary-container": "#ffffff",
                        "on-secondary-fixed": "#022c22",
                        "secondary": "#0f766e",
                        "on-primary": "#ffffff",
                        "on-primary-fixed-variant": "#14532d",
                        "secondary-container": "#10b981",
                        "surface-variant": "#d4ebd9",
                        "outline": "#6d8574",
                        "on-secondary-fixed-variant": "#134e4a",
                        "on-surface-variant": "#30533c",
                        "tertiary-fixed": "#a7f3d0",
                        "error-container": "#fee2e2",
                        "on-secondary": "#ffffff",
                        "on-error": "#ffffff",
                        "primary-fixed": "#dcfce7",
                        "on-error-container": "#991b1b",
                        "surface-bright": "#ffffff",
                        "on-secondary-container": "#ffffff",
                        "primary-container": "#13793e",
                        "surface-container-low": "#edf6ef",
                        "surface-container-lowest": "#ffffff",
                        "on-primary-container": "#ffffff",
                        "inverse-surface": "#132a1e",
                        "error": "#dc2626",
                        "surface": "#f4f9f5"
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    spacing: {
                        "space-xs": "0.25rem",
                        "space-lg": "1.5rem",
                        "space-md": "1rem",
                        "margin": "1rem",
                        "gutter-tablet": "1.5rem",
                        "gutter": "1rem",
                        "space-xl": "2rem",
                        "margin-tablet": "2rem",
                        "space-sm": "0.5rem"
                    },
                    fontFamily: {
                        "body-sm": ["Inter"],
                        "headline-lg-mobile": ["Plus Jakarta Sans"],
                        "headline-md": ["Plus Jakarta Sans"],
                        "body-lg": ["Inter"],
                        "label-md": ["Inter"],
                        "label-sm": ["Inter"],
                        "headline-xl": ["Plus Jakarta Sans"],
                        "headline-sm": ["Plus Jakarta Sans"],
                        "body-md": ["Inter"],
                        "headline-lg": ["Plus Jakarta Sans"],
                        "label-lg": ["Inter"]
                    },
                    fontSize: {
                        "body-sm": ["0.75rem", { "lineHeight": "1.125rem", "fontWeight": "400" }],
                        "headline-lg-mobile": ["1.5rem", { "lineHeight": "2rem", "fontWeight": "700" }],
                        "headline-md": ["1.25rem", { "lineHeight": "1.75rem", "fontWeight": "600" }],
                        "body-lg": ["1rem", { "lineHeight": "1.5rem", "fontWeight": "400" }],
                        "label-md": ["0.75rem", { "lineHeight": "1rem", "fontWeight": "600" }],
                        "label-sm": ["0.6875rem", { "lineHeight": "0.875rem", "fontWeight": "600" }],
                        "headline-xl": ["2.25rem", { "lineHeight": "2.75rem", "fontWeight": "700" }],
                        "headline-sm": ["1.125rem", { "lineHeight": "1.5rem", "fontWeight": "600" }],
                        "body-md": ["0.875rem", { "lineHeight": "1.375rem", "fontWeight": "400" }],
                        "headline-lg": ["1.75rem", { "lineHeight": "2.25rem", "fontWeight": "700" }],
                        "label-lg": ["0.875rem", { "lineHeight": "1.25rem", "fontWeight": "600" }]
                    }
                }
            }
        };
    </script>
    @stack('styles')
</head>
<body class="bg-background font-body-md text-on-surface antialiased">

    @php
        $schoolSetting = \App\Models\AttendanceSetting::getSettings();
    @endphp

    <!-- Mobile Drawer Overlay -->
    <div id="sidebarOverlay" onclick="toggleAdminSidebar()" class="fixed inset-0 bg-slate-900/40 z-40 hidden lg:hidden"></div>

    <!-- Aside Sidebar -->
    <aside id="adminSidebar" class="fixed left-0 top-0 h-full w-72 bg-surface-container-lowest shadow-[0_4px_24px_rgba(19,121,62,0.08)] border-r border-surface-container z-50 flex flex-col justify-between -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <!-- App Brand Header -->
        <div class="h-16 px-space-lg flex items-center justify-between bg-surface-container-low/70 border-b border-surface-container shrink-0">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-space-sm">
                <img src="{{ $schoolSetting->logo_url ?? asset('logo.jpg') }}" alt="Logo" class="w-10 h-10 rounded-xl object-contain bg-surface-container-lowest p-1 shadow-sm border border-emerald-200/80 shrink-0">
                <div class="flex flex-col">
                    <span class="font-headline-sm text-headline-sm text-primary font-black tracking-tight leading-tight">SIMPRES BU</span>
                    <span class="font-label-sm text-[11px] text-on-surface-variant font-medium leading-tight truncate max-w-[170px]" title="Sistem Informasi Manajemen Presensi Bahrul Ulum">
                        Presensi Bahrul Ulum
                    </span>
                </div>
            </a>
            <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden text-on-surface-variant hover:text-on-surface p-1 rounded-lg hover:bg-surface-container">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        @php
            $isStudentMasterActive = request()->routeIs('admin.students.*') && !request()->routeIs('admin.students.attendance_recap*');
            $isStudentRecapActive = request()->routeIs('admin.students.attendance_recap*');
            $isTeacherRecapActive = request()->routeIs('admin.attendance.recap*');
            $isAttendanceManualActive = request()->routeIs('admin.attendance.index') || request()->routeIs('admin.attendance.manual');
        @endphp

        <!-- Scrollable Navigation Area -->
        <div class="flex-1 overflow-y-auto px-space-md py-4 space-y-5 sidebar-scroll">
            
            <!-- KATEGORI KHUSUS: SUPER ADMINISTRATOR PUSAT -->
            @if(is_superadmin())
            <div class="p-3 rounded-2xl bg-gradient-to-br from-amber-500/10 via-emerald-500/10 to-transparent border border-amber-400/30">
                <div class="px-1 pb-2 flex items-center justify-between">
                    <span class="text-[11px] font-black uppercase tracking-wider text-amber-700 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-amber-600">verified</span>
                        Super Administrator
                    </span>
                    <span class="text-[10px] font-black text-amber-800 bg-amber-100 px-1.5 py-0.5 rounded-full border border-amber-300">Pusat</span>
                </div>
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('superadmin.dashboard') }}" 
                       class="group flex items-center justify-between px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('superadmin.dashboard') ? 'bg-amber-500 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-amber-100/60 font-semibold' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">space_dashboard</span>
                            <span class="text-xs">Dashboard Global</span>
                        </div>
                    </a>
                    <a href="{{ route('superadmin.schools.index') }}" 
                       class="group flex items-center justify-between px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('superadmin.schools.*') ? 'bg-amber-500 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-amber-100/60 font-semibold' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">apartment</span>
                            <span class="text-xs">Kelola Semua Sekolah</span>
                        </div>
                    </a>
                    <a href="{{ route('superadmin.admins.index') }}" 
                       class="group flex items-center justify-between px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('superadmin.admins.*') ? 'bg-amber-500 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-amber-100/60 font-semibold' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">manage_accounts</span>
                            <span class="text-xs">Kelola Admin Sekolah</span>
                        </div>
                    </a>
                    <a href="{{ route('superadmin.settings.index') }}" 
                       class="group flex items-center justify-between px-2.5 py-1.5 rounded-xl transition-all {{ request()->routeIs('superadmin.settings.*') ? 'bg-amber-500 text-white font-bold shadow-xs' : 'text-slate-700 hover:bg-amber-100/60 font-semibold' }}">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">palette</span>
                            <span class="text-xs">Logo Login & Yayasan</span>
                        </div>
                    </a>
                </nav>
            </div>
            @endif

            <!-- KATEGORI 1: MENU UTAMA -->
            <div>
                <div class="px-space-sm pb-1.5 flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Utama & Real-Time
                    </span>
                    <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/60">Live</span>
                </div>
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.dashboard') ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">grid_view</span>
                            <span class="text-[13.5px]">Monitoring Hari Ini</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.kiosk') }}" target="_blank"
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.kiosk') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}"
                       title="Buka Mode Layar Kiosk / Meja Piket">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.kiosk') ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">desktop_windows</span>
                            <span class="text-[13.5px]">Kiosk Presensi (Piket)</span>
                        </div>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ request()->routeIs('admin.kiosk') ? 'bg-white/20 text-white' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/50' }}">Buka</span>
                    </a>
                </nav>
            </div>

            <!-- KATEGORI 2: DATA MASTER (DATA POKOK) -->
            <div class="pt-2 border-t border-slate-100">
                <div class="px-space-sm pb-1.5 flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-800 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px] text-emerald-600">dataset</span>
                        Data Master
                    </span>
                    <span class="text-[10px] font-semibold text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200/60">4 Data Pokok</span>
                </div>
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('admin.teachers.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.teachers.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.teachers.*') ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">badge</span>
                            <span class="text-[13.5px]">Data Guru & Tendik</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.students.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ $isStudentMasterActive ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ $isStudentMasterActive ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">groups</span>
                            <span class="text-[13.5px]">Data Siswa</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.classes.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.classes.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.classes.*') ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">meeting_room</span>
                            <span class="text-[13.5px]">Data Kelas & QR</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.subjects.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.subjects.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.subjects.*') ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">auto_stories</span>
                            <span class="text-[13.5px]">Mata Pelajaran</span>
                        </div>
                    </a>
                </nav>
            </div>

            <!-- KATEGORI 3: AKADEMIK & PEMBELAJARAN -->
            <div class="pt-2 border-t border-slate-100">
                <div class="px-space-sm pb-1.5 flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-blue-800 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px] text-blue-600">school</span>
                        Akademik & Jadwal
                    </span>
                    <span class="text-[10px] font-semibold text-blue-800 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200/60">KBM</span>
                </div>
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('admin.schedules.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.schedules.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.schedules.*') ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">calendar_month</span>
                            <span class="text-[13.5px]">Jadwal Pelajaran</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.journals.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.journals.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.journals.*') ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">menu_book</span>
                            <span class="text-[13.5px]">Jurnal Mengajar Guru</span>
                        </div>
                    </a>
                </nav>
            </div>

            <!-- KATEGORI 4: PRESENSI & ABSENSI -->
            <div class="pt-2 border-t border-slate-100">
                <div class="px-space-sm pb-1.5 flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-teal-800 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px] text-teal-600">fact_check</span>
                        Presensi & Harian
                    </span>
                    <span class="text-[10px] font-semibold text-teal-800 bg-teal-50 px-1.5 py-0.5 rounded border border-teal-200/60">Harian</span>
                </div>
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('admin.attendance.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ $isAttendanceManualActive ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ $isAttendanceManualActive ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">how_to_reg</span>
                            <span class="text-[13.5px]">Presensi & Absen Manual</span>
                        </div>
                    </a>
                </nav>
            </div>

            <!-- KATEGORI 5: LAPORAN & REKAPITULASI -->
            <div class="pt-2 border-t border-slate-100">
                <div class="px-space-sm pb-1.5 flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-amber-800 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px] text-amber-600">analytics</span>
                        Laporan & Rekapitulasi
                    </span>
                    <span class="text-[10px] font-semibold text-amber-800 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200/60">Export</span>
                </div>
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('admin.attendance.recap') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ $isTeacherRecapActive ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ $isTeacherRecapActive ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">assignment_ind</span>
                            <span class="text-[13.5px]">Laporan Absensi Guru</span>
                        </div>
                        <span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded {{ $isTeacherRecapActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-amber-100 group-hover:text-amber-800' }}">Guru</span>
                    </a>

                    <a href="{{ route('admin.students.attendance_recap') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ $isStudentRecapActive ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ $isStudentRecapActive ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">school</span>
                            <span class="text-[13.5px]">Laporan Absensi Siswa</span>
                        </div>
                        <span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded {{ $isStudentRecapActive ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600 group-hover:bg-amber-100 group-hover:text-amber-800' }}">Siswa</span>
                    </a>
                </nav>
            </div>

            <!-- KATEGORI 6: PENGATURAN & LAINNYA -->
            <div class="pt-2 border-t border-slate-100">
                <div class="px-space-sm pb-1.5 flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[15px] text-slate-500">settings</span>
                        Pengaturan & Informasi
                    </span>
                </div>
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('admin.settings.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.settings.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.settings.*') ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">schedule</span>
                            <span class="text-[13.5px]">Pengaturan Jam & Radius</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.announcements.index') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.announcements.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.announcements.*') ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">campaign</span>
                            <span class="text-[13.5px]">Informasi Sekolah</span>
                        </div>
                    </a>

                    <a href="{{ route('admin.profile') }}" 
                       class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all {{ request()->routeIs('admin.profile') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-[20px] {{ request()->routeIs('admin.profile') ? 'text-on-primary-container' : 'text-slate-400 group-hover:text-primary' }}">manage_accounts</span>
                            <span class="text-[13.5px]">Profil Admin & Tim IT</span>
                        </div>
                    </a>
                </nav>
            </div>

        </div>

        <!-- Sidebar Bottom Footer -->
        <div class="p-space-md bg-surface-container-lowest border-t border-surface-container/60 shrink-0">
            <div class="p-space-sm rounded-xl bg-surface-container-low flex items-center justify-between mb-2">
                <div class="flex items-center gap-space-sm">
                    <div class="w-2.5 h-2.5 rounded-full bg-tertiary-container animate-pulse shrink-0"></div>
                    <div class="flex flex-col">
                        <span class="font-label-md text-label-md text-on-surface leading-tight">GPS Cloud School</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant leading-tight">Geofence Aktif</span>
                    </div>
                </div>
                <span class="material-symbols-outlined text-tertiary text-[20px]">check_circle</span>
            </div>

            <!-- Logout Link -->
            <form action="{{ route('logout') }}" method="POST" data-confirm="Apakah Anda yakin ingin keluar dari Akun Administrator?" data-confirm-title="Konfirmasi Keluar" data-confirm-btn="Ya, Keluar">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 rounded-xl text-error hover:bg-error-container/40 font-label-sm text-label-sm font-semibold transition-colors">
                    <span class="material-symbols-outlined text-[18px]">logout</span>
                    <span>Keluar Sistem</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Container -->
    <div class="pl-0 lg:pl-72">
        <!-- Header -->
        <header class="fixed top-0 left-0 lg:left-72 right-0 h-16 bg-surface-container-lowest/95 backdrop-blur-xl shadow-[0_2px_15px_rgba(19,121,62,0.06)] border-b border-surface-container z-40 flex items-center justify-between px-space-lg">
            <div class="flex items-center gap-space-md">
                <!-- Hamburger for mobile -->
                <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden p-2 rounded-lg text-on-surface-variant hover:bg-surface-container">
                    <span class="material-symbols-outlined">menu</span>
                </button>

                <div class="flex items-center gap-space-xs bg-surface-container-low px-space-md py-1.5 rounded-full text-on-surface-variant">
                    <span class="material-symbols-outlined text-primary text-[18px]">event</span>
                    <span id="liveHeaderDate" class="font-label-md text-label-md text-on-surface">
                        {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                    <span class="text-outline-variant">•</span>
                    <span id="liveHeaderClock" class="font-label-md text-label-md text-primary font-semibold">
                        {{ now()->format('H.i') }} WIB
                    </span>
                </div>

                <div class="hidden lg:flex items-center gap-space-xs px-2.5 py-1 rounded-full bg-surface-container text-on-secondary-container text-label-sm font-label-sm">
                    <span class="w-2 h-2 rounded-full {{ $schoolSetting->is_holiday_today ? 'bg-amber-500' : 'bg-secondary' }}"></span>
                    <span>
                        @if($schoolSetting->is_holiday_today)
                            Hari Ini Libur ({{ $schoolSetting->today_schedule?->note ?? 'Libur Sekolah' }})
                        @else
                            Jam Masuk: {{ substr($schoolSetting->time_in, 0, 5) }} - {{ substr($schoolSetting->time_in_late_threshold, 0, 5) }} WIB • Pulang: {{ substr($schoolSetting->time_out, 0, 5) }} WIB
                        @endif
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-space-md">
                <!-- Multi-School Context / Switcher Badge -->
                @php
                    $currentSchool = current_school();
                    $allSchools = all_schools();
                @endphp
                @if(is_superadmin())
                    <div class="relative" id="schoolSwitcherDropdown">
                        <button type="button" onclick="toggleSchoolDropdown()" 
                                class="flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-amber-50 hover:bg-amber-100/80 border border-amber-300 text-amber-900 transition-all text-xs font-bold shadow-xs active:scale-95">
                            <span class="material-symbols-outlined text-[17px] text-amber-700">corporate_fare</span>
                            <span class="max-w-[120px] sm:max-w-[170px] truncate">{{ $currentSchool->name }}</span>
                            <span class="material-symbols-outlined text-[16px] text-amber-700">arrow_drop_down</span>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="schoolDropdownMenu" class="absolute right-0 top-full mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 hidden">
                            <div class="px-3.5 py-1.5 border-b border-slate-100 flex items-center justify-between">
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400">Pilih Konteks Sekolah</span>
                                <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-1.5 py-0.5 rounded">Superadmin</span>
                            </div>
                            <div class="max-h-56 overflow-y-auto p-1.5 space-y-1">
                                @foreach($allSchools as $sch)
                                    <form action="{{ route('superadmin.switch_school') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="school_id" value="{{ $sch->id }}">
                                        <button type="submit" 
                                                class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-left text-xs font-bold transition-colors {{ $currentSchool->id == $sch->id ? 'bg-emerald-50 text-[#13793e]' : 'text-slate-700 hover:bg-slate-50' }}">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <img src="{{ $sch->logo_url }}" alt="Logo" class="w-5 h-5 rounded-md object-contain bg-white p-0.5 border border-slate-200 shrink-0">
                                                <span class="truncate">{{ $sch->name }}</span>
                                            </div>
                                            @if($currentSchool->id == $sch->id)
                                                <span class="material-symbols-outlined text-[16px] text-[#13793e]">check</span>
                                            @endif
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                            <div class="p-1.5 border-t border-slate-100">
                                <a href="{{ route('superadmin.dashboard') }}" 
                                   class="flex items-center justify-center gap-1.5 w-full py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition-colors">
                                    <span class="material-symbols-outlined text-[15px]">space_dashboard</span>
                                    <span>Dashboard Pusat Superadmin</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-[#13793e] text-xs font-bold">
                        <img src="{{ $currentSchool->logo_url }}" alt="Logo" class="w-5 h-5 rounded-md object-contain bg-white p-0.5 border border-emerald-200">
                        <span class="max-w-[150px] truncate">{{ $currentSchool->name }}</span>
                    </div>
                @endif

                <a href="{{ route('admin.attendance.index') }}" class="relative p-2 rounded-full hover:bg-surface-container-high text-on-surface-variant hover:text-on-surface transition-colors" type="button" title="Notifikasi Kehadiran">
                    <span class="material-symbols-outlined text-[22px]">notifications</span>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full"></span>
                </a>

                <a href="{{ route('admin.profile') }}" class="flex items-center gap-space-sm pl-space-sm border-l border-surface-container hover:opacity-80 transition-opacity" title="Buka Profil Administrator & Kontak Tim IT">
                    <div class="flex flex-col text-right hidden sm:flex">
                        <span class="font-label-lg text-label-lg text-on-surface leading-tight">{{ auth()->user()?->name ?? 'Administrator' }}</span>
                        <span class="font-label-sm text-label-sm text-primary font-bold leading-tight">Admin & Tim IT</span>
                    </div>
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-on-primary font-bold shadow-xs">
                        <span class="material-symbols-outlined text-on-primary text-[18px]">manage_accounts</span>
                    </div>
                </a>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="relative pt-16 bg-background w-full min-h-screen px-space-lg py-space-md">
            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-surface-container-high border border-primary/20 text-on-surface flex items-start gap-3 shadow-sm">
                    <span class="material-symbols-outlined text-primary text-[20px]">check_circle</span>
                    <div class="flex-1 font-label-md text-label-md">{{ session('success') }}</div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-on-surface-variant hover:text-on-surface">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-error-container text-on-error-container flex items-start gap-3 shadow-sm">
                    <span class="material-symbols-outlined text-error text-[20px]">error</span>
                    <div class="flex-1 font-label-md text-label-md">{{ session('error') }}</div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-on-error-container hover:opacity-80">
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Live Clock Script -->
    <script>
        function updateAdminClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            const clockEl = document.getElementById('liveHeaderClock');
            if (clockEl) {
                clockEl.textContent = `${hours}.${minutes}.${seconds} WIB`;
            }
        }
        setInterval(updateAdminClock, 1000);
        updateAdminClock();

        function toggleAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Global SweetAlert2 Form Confirmation Handler
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.hasAttribute('data-confirm')) {
                e.preventDefault();
                const msg = form.getAttribute('data-confirm');
                const title = form.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
                const icon = form.getAttribute('data-confirm-icon') || 'warning';
                const confirmBtn = form.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';
                const confirmColor = form.getAttribute('data-confirm-color') || '#004ac6';

                Swal.fire({
                    title: title,
                    html: `<div class="text-sm text-slate-600">${msg.replace(/\n/g, '<br>')}</div>`,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: confirmColor,
                    cancelButtonColor: '#737686',
                    confirmButtonText: confirmBtn,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl shadow-xl',
                        confirmButton: 'rounded-xl font-bold px-4 py-2 text-sm',
                        cancelButton: 'rounded-xl font-medium px-4 py-2 text-sm'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: 'Memproses...',
                            text: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        form.removeAttribute('data-confirm');
                        form.submit();
                    }
                });
            }
        });
    </script>

    <!-- SweetAlert2 Flash Alerts -->
    @if(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            timer: 3500,
            timerProgressBar: true,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            customClass: {
                popup: 'rounded-xl shadow-lg border border-emerald-100'
            }
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#13793e',
            confirmButtonText: 'Tutup',
            customClass: {
                popup: 'rounded-2xl shadow-xl'
            }
        });
    </script>
    @endif

    @if(session('warning'))
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: '{{ session('warning') }}',
            confirmButtonColor: '#13793e',
            confirmButtonText: 'Mengerti',
            customClass: {
                popup: 'rounded-2xl shadow-xl'
            }
        });
    </script>
    @endif

    <script>
        function toggleSchoolDropdown() {
            const menu = document.getElementById('schoolDropdownMenu');
            if (menu) menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.getElementById('schoolSwitcherDropdown');
            const menu = document.getElementById('schoolDropdownMenu');
            if (dropdown && menu && !dropdown.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
