<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="web_dashboard" name="shell-type"/>
    <title>@yield('title', 'Monitoring Presensi Guru Real-Time') - HadirGuru</title>

    <link rel="icon" type="image/jpeg" href="{{ $schoolSetting->logo_url ?? asset('logo.jpg') }}"/>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#13793e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="HadirGuru">
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
        <div class="flex flex-col">
            <!-- App Brand Header -->
            <div class="h-16 px-space-lg flex items-center justify-between bg-surface-container-low/70 border-b border-surface-container">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-space-sm">
                    <img src="{{ $schoolSetting->logo_url ?? asset('logo.jpg') }}" alt="Logo" class="w-10 h-10 rounded-xl object-contain bg-surface-container-lowest p-1 shadow-sm border border-emerald-200/80 shrink-0">
                    <div class="flex flex-col">
                        <span class="font-headline-sm text-headline-sm text-primary font-black tracking-tight leading-tight">HadirGuru</span>
                        <span class="font-label-sm text-label-sm text-on-surface-variant font-semibold leading-tight truncate max-w-[160px]">
                            {{ $schoolSetting->foundation_name ?: $schoolSetting->school_name }}
                        </span>
                    </div>
                </a>
                <button type="button" onclick="toggleAdminSidebar()" class="lg:hidden text-on-surface-variant hover:text-on-surface">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Menu Utama -->
            <div class="px-space-md py-space-sm">
                <p class="px-space-sm py-space-xs font-label-sm text-label-sm uppercase tracking-wider text-outline">Menu Utama</p>
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2.5 transition-colors rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">grid_view</span>
                        <span class="font-label-lg text-label-lg">Monitoring Hari Ini</span>
                    </a>

                    <a href="{{ route('admin.teachers.index') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.teachers.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">badge</span>
                        <span class="font-label-lg text-label-lg">Data Guru & Tendik</span>
                    </a>

                    <a href="{{ route('admin.attendance.index') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.attendance.index') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">fact_check</span>
                        <span class="font-label-lg text-label-lg">Presensi & Absen Manual</span>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">schedule</span>
                        <span class="font-label-lg text-label-lg">Pengaturan Jam Presensi</span>
                    </a>

                    <a href="{{ route('admin.profile') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.profile') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
                        <span class="font-label-lg text-label-lg">Profil Admin & Tim IT</span>
                    </a>

                    <a href="{{ route('admin.announcements.index') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.announcements.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">campaign</span>
                        <span class="font-label-lg text-label-lg">Informasi Sekolah</span>
                    </a>
                </nav>

                <!-- Kategori Laporan & Rekapitulasi -->
                <p class="px-space-sm py-space-xs mt-3 font-label-sm text-label-sm uppercase tracking-wider text-outline">Laporan & Rekapitulasi</p>
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('admin.attendance.recap') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.attendance.recap*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">assignment_ind</span>
                        <span class="font-label-lg text-label-lg">Laporan Absensi Guru</span>
                    </a>

                    <a href="{{ route('admin.students.attendance_recap') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2.5 rounded-lg transition-colors {{ request()->routeIs('admin.students.attendance_recap*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">school</span>
                        <span class="font-label-lg text-label-lg">Laporan Absensi Siswa</span>
                    </a>
                </nav>

                <p class="px-space-sm py-space-xs mt-3 font-label-sm text-label-sm uppercase tracking-wider text-outline">Akademik & Pembelajaran</p>
                <nav class="flex flex-col gap-1">
                    <a href="{{ route('admin.schedules.index') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2 rounded-lg transition-colors {{ request()->routeIs('admin.schedules.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">calendar_month</span>
                        <span class="font-label-lg text-label-lg">Jadwal Pelajaran</span>
                    </a>

                    <a href="{{ route('admin.subjects.index') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2 rounded-lg transition-colors {{ request()->routeIs('admin.subjects.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">auto_stories</span>
                        <span class="font-label-lg text-label-lg">Mata Pelajaran</span>
                    </a>

                    <a href="{{ route('admin.classes.index') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2 rounded-lg transition-colors {{ request()->routeIs('admin.classes.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">meeting_room</span>
                        <span class="font-label-lg text-label-lg">Data Kelas</span>
                    </a>

                    <a href="{{ route('admin.students.index') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2 rounded-lg transition-colors {{ request()->routeIs('admin.students.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">groups</span>
                        <span class="font-label-lg text-label-lg">Data Siswa</span>
                    </a>

                    <a href="{{ route('admin.journals.index') }}" 
                       class="flex items-center gap-space-sm px-space-md py-2 rounded-lg transition-colors {{ request()->routeIs('admin.journals.*') ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm' : 'text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}">
                        <span class="material-symbols-outlined text-[20px]">menu_book</span>
                        <span class="font-label-lg text-label-lg">Jurnal Mengajar Guru</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Sidebar Bottom Footer -->
        <div class="p-space-md bg-surface-container-lowest">
            <div class="p-space-md rounded-xl bg-surface-container-low flex items-center justify-between mb-2">
                <div class="flex items-center gap-space-sm">
                    <div class="w-2.5 h-2.5 rounded-full bg-tertiary-container animate-pulse"></div>
                    <div class="flex flex-col">
                        <span class="font-label-md text-label-md text-on-surface">Status GPS Cloud</span>
                        <span class="font-body-sm text-body-sm text-on-surface-variant">Geofence Aktif</span>
                    </div>
                </div>
                <span class="material-symbols-outlined text-tertiary text-[20px]">check_circle</span>
            </div>

            <!-- Logout Link -->
            <form action="{{ route('logout') }}" method="POST" data-confirm="Apakah Anda yakin ingin keluar dari Akun Administrator?" data-confirm-title="Konfirmasi Keluar" data-confirm-btn="Ya, Keluar">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 rounded-lg text-error hover:bg-error-container/40 font-label-sm text-label-sm transition-colors">
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

    @stack('scripts')
</body>
</html>
