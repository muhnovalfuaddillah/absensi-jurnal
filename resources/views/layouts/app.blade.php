<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" name="viewport"/>
    <meta content="mobile_tab" name="shell-type"/>
    <title>@yield('title', 'HadirGuru - Presensi Guru Modern')</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpg') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#004ac6">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="HadirGuru">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- FontAwesome for fallback icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Tailwind CSS CDN & Design System Tokens -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
            extend: {
                colors: {
                    "background": "#f8f9ff",
                    "surface-container-lowest": "#ffffff",
                    "surface-dim": "#cbdbf5",
                    "surface-container-highest": "#d3e4fe",
                    "error-container": "#ffdad6",
                    "on-secondary-fixed-variant": "#173bab",
                    "surface-container-low": "#eff4ff",
                    "inverse-surface": "#213145",
                    "secondary-fixed-dim": "#b8c4ff",
                    "primary": "#004ac6",
                    "surface-container-high": "#dce9ff",
                    "on-primary": "#ffffff",
                    "on-tertiary-fixed-variant": "#004c69",
                    "inverse-primary": "#b4c5ff",
                    "on-background": "#0b1c30",
                    "primary-fixed": "#dbe1ff",
                    "inverse-on-surface": "#eaf1ff",
                    "surface": "#f8f9ff",
                    "on-error-container": "#93000a",
                    "on-tertiary-container": "#e1f2ff",
                    "secondary": "#3755c3",
                    "outline": "#737686",
                    "on-surface": "#0b1c30",
                    "primary-container": "#2563eb",
                    "on-secondary": "#ffffff",
                    "tertiary-fixed": "#c4e7ff",
                    "on-primary-container": "#eeefff",
                    "outline-variant": "#c3c6d7",
                    "on-error": "#ffffff",
                    "tertiary-fixed-dim": "#7bd0ff",
                    "tertiary-container": "#00759f",
                    "on-primary-fixed": "#00174b",
                    "secondary-container": "#708cfd",
                    "on-secondary-container": "#00217a",
                    "on-secondary-fixed": "#001453",
                    "secondary-fixed": "#dde1ff",
                    "error": "#ba1a1a",
                    "surface-tint": "#0053db",
                    "tertiary": "#005b7c",
                    "surface-variant": "#d3e4fe",
                    "primary-fixed-dim": "#b4c5ff",
                    "surface-container": "#e5eeff",
                    "on-tertiary-fixed": "#001e2c",
                    "on-surface-variant": "#434655",
                    "surface-bright": "#f8f9ff",
                    "on-tertiary": "#ffffff",
                    "on-primary-fixed-variant": "#003ea8"
                },
                borderRadius: {
                    "DEFAULT": "0.25rem",
                    "lg": "0.5rem",
                    "xl": "0.75rem",
                    "2xl": "1rem",
                    "3xl": "1.5rem",
                    "full": "9999px"
                },
                spacing: {
                    "gutter": "1rem",
                    "space-xl": "2rem",
                    "margin": "1rem",
                    "space-xs": "0.25rem",
                    "gutter-tablet": "1.5rem",
                    "space-sm": "0.5rem",
                    "space-lg": "1.5rem",
                    "margin-tablet": "2rem",
                    "space-md": "1rem"
                },
                fontFamily: {
                    "body-lg": ["Inter", "sans-serif"],
                    "headline-lg-mobile": ["Plus Jakarta Sans", "sans-serif"],
                    "label-lg": ["Inter", "sans-serif"],
                    "headline-sm": ["Plus Jakarta Sans", "sans-serif"],
                    "body-md": ["Inter", "sans-serif"],
                    "body-sm": ["Inter", "sans-serif"],
                    "headline-xl": ["Plus Jakarta Sans", "sans-serif"],
                    "label-sm": ["Inter", "sans-serif"],
                    "headline-md": ["Plus Jakarta Sans", "sans-serif"],
                    "label-md": ["Inter", "sans-serif"],
                    "headline-lg": ["Plus Jakarta Sans", "sans-serif"]
                },
                fontSize: {
                    "body-lg": ["1rem", { lineHeight: "1.5rem", fontWeight: "400" }],
                    "headline-lg-mobile": ["1.5rem", { lineHeight: "2rem", fontWeight: "700" }],
                    "label-lg": ["0.875rem", { lineHeight: "1.25rem", fontWeight: "600" }],
                    "headline-sm": ["1.125rem", { lineHeight: "1.5rem", fontWeight: "600" }],
                    "body-md": ["0.875rem", { lineHeight: "1.375rem", fontWeight: "400" }],
                    "body-sm": ["0.75rem", { lineHeight: "1.125rem", fontWeight: "400" }],
                    "headline-xl": ["2.25rem", { lineHeight: "2.75rem", fontWeight: "700" }],
                    "label-sm": ["0.6875rem", { lineHeight: "0.875rem", fontWeight: "600" }],
                    "headline-md": ["1.25rem", { lineHeight: "1.75rem", fontWeight: "600" }],
                    "label-md": ["0.75rem", { lineHeight: "1rem", fontWeight: "600" }],
                    "headline-lg": ["1.75rem", { lineHeight: "2.25rem", fontWeight: "700" }]
                }
            }
        }
    }
    </script>
    <style>
        @layer base {
            html, body {
                width: 100%;
                margin: 0;
                padding: 0;
                min-height: 100vh;
            }
            body {
                overscroll-behavior-y: none;
                -webkit-tap-highlight-color: transparent;
            }
            .pb-safe {
                padding-bottom: env(safe-area-inset-bottom, 0px);
            }
            .pt-safe {
                padding-top: env(safe-area-inset-top, 0px);
            }
            main > :first-child {
                margin-top: 0 !important;
            }
            main > :last-child {
                margin-bottom: 0 !important;
            }
        }
        ::-webkit-scrollbar {
            display: none;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
        }
        .material-symbols-outlined.fill {
            font-variation-settings: 'FILL' 1, 'wght' 600, 'GRAD' 0, 'opsz' 24;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-surface text-on-surface font-body-md text-body-md flex flex-col min-h-screen antialiased">
    @php
        $currentTeacher = auth()->check() ? auth()->user()->teacher : null;
        $appSetting = \App\Models\AttendanceSetting::getSettings();
    @endphp

    <!-- Fixed Header -->
    <header class="fixed top-0 inset-x-0 z-50 bg-surface/90 backdrop-blur-xl shadow-[0_1px_8px_rgba(0,0,0,0.04)] pt-safe">
        <div class="h-16 px-margin flex items-center justify-between gap-space-sm max-w-2xl mx-auto">
            <!-- Left: School Foundation & Page Title -->
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-white p-0.5 border border-slate-200 shadow-sm flex items-center justify-center shrink-0 overflow-hidden">
                    <img src="{{ $appSetting->logo_url }}" alt="Logo {{ $appSetting->school_name }}" class="w-full h-full object-contain rounded-full" onerror="this.onerror=null;this.src='{{ asset('logo.jpg') }}';">
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#13793e] leading-tight truncate">
                        {{ $appSetting->foundation_name ?: 'YAYASAN BAHRUL ULUM' }} <span class="text-amber-800">• 1446 H</span>
                    </span>
                    <h1 class="text-sm font-extrabold text-slate-900 tracking-tight leading-tight truncate">
                        @yield('header_title', 'Beranda Dashboard')
                    </h1>
                    <span class="text-[11px] text-slate-500 font-medium truncate leading-tight">
                        {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }} • {{ $appSetting->school_name ?? 'Madrasah' }}
                    </span>
                </div>
            </div>

            <!-- Right: Notifications & Profile Trigger -->
            <div class="flex items-center gap-2 flex-shrink-0">
                <!-- Notifications Button -->
                <button aria-label="Notifikasi" onclick="showNotificationInfo()" class="w-9 h-9 flex items-center justify-center rounded-full text-slate-600 hover:text-[#13793e] hover:bg-emerald-50 transition-colors relative" type="button">
                    <i class="fa-regular fa-bell text-base"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#13793e] ring-2 ring-white"></span>
                </button>

                <!-- Profile Avatar Circle -->
                <a href="{{ route('guru.profile') }}" class="w-9 h-9 rounded-full bg-[#13793e] text-white flex items-center justify-center ring-2 ring-emerald-600/20 hover:ring-[#13793e] transition-all overflow-hidden shadow-xs">
                    @if($currentTeacher && $currentTeacher->photo_url)
                        <img alt="{{ $currentTeacher->name }}" class="w-full h-full object-cover" src="{{ $currentTeacher->photo_url }}"/>
                    @else
                        <i class="fa-solid fa-user text-xs"></i>
                    @endif
                </a>

                <!-- Quick Logout Button -->
                <form action="{{ route('logout') }}" method="POST" class="inline" data-confirm="Apakah Anda yakin ingin keluar dari akun?" data-confirm-title="Konfirmasi Keluar" data-confirm-btn="Ya, Keluar">
                    @csrf
                    <button type="submit" title="Keluar" class="w-8 h-8 flex items-center justify-center rounded-full text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors">
                        <i class="fa-solid fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col relative w-full pt-16 pb-20 bg-slate-50/50">
        <div class="w-full max-w-2xl mx-auto flex flex-col px-4 pt-3 pb-6 gap-y-3.5">
            
            <!-- Flash Notification Alerts -->
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-3.5 rounded-2xl shadow-xs flex items-center justify-between gap-3 animate-fade-in">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="material-symbols-outlined text-emerald-600 text-[20px] flex-shrink-0">check_circle</span>
                        <span class="text-xs truncate font-medium">{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>
            @endif

            @if(session('warning'))
                <div class="bg-amber-50 border border-amber-200 text-amber-800 p-3.5 rounded-2xl shadow-xs flex items-center justify-between gap-3 animate-fade-in">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="material-symbols-outlined text-amber-600 text-[20px] flex-shrink-0">warning</span>
                        <span class="text-xs truncate font-medium">{{ session('warning') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-amber-500 hover:text-amber-700">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-2xl shadow-xs flex items-center justify-between gap-3 animate-fade-in">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="material-symbols-outlined text-rose-600 text-[20px] flex-shrink-0">error</span>
                        <span class="text-xs truncate font-medium">{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-50 border border-blue-200 text-blue-800 p-3.5 rounded-2xl shadow-xs flex items-center justify-between gap-3 animate-fade-in">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="material-symbols-outlined text-blue-600 text-[20px] flex-shrink-0">info</span>
                        <span class="text-xs truncate font-medium">{{ session('info') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700">
                        <span class="material-symbols-outlined text-[16px]">close</span>
                    </button>
                </div>
            @endif

            @if (isset($errors) && $errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-800 p-3.5 rounded-2xl shadow-xs">
                    <div class="flex items-center gap-1.5 font-bold text-xs text-rose-900 mb-1">
                        <span class="material-symbols-outlined text-[18px]">error</span>
                        <span>Perhatian:</span>
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- View Specific Body -->
            @yield('content')

        </div>
    </main>

    <!-- Fixed Bottom Tab Bar (4 Tabs: Beranda, Riwayat, Izin/Cuti, Profil) -->
    <nav class="fixed bottom-0 inset-x-0 z-50 pb-safe bg-white/95 backdrop-blur-xl shadow-[0_-2px_15px_rgba(0,0,0,0.06)] border-t border-slate-100">
        <div class="h-16 px-2 flex items-center justify-around max-w-2xl mx-auto">
            <!-- Tab 1: Beranda -->
            <a href="{{ route('guru.dashboard') }}" 
               class="flex flex-col items-center justify-center flex-1 h-12 transition-colors {{ request()->routeIs('guru.dashboard') ? 'text-[#13793e] font-bold' : 'text-slate-400 hover:text-slate-600' }}">
                <i class="fa-solid fa-table-cells-large text-[19px]"></i>
                <span class="text-[10px] mt-1 tracking-tight">Beranda</span>
            </a>

            <!-- Tab 2: Riwayat -->
            <a href="{{ route('guru.riwayat') }}" 
               class="flex flex-col items-center justify-center flex-1 h-12 transition-colors {{ request()->routeIs('guru.riwayat') ? 'text-[#13793e] font-bold' : 'text-slate-400 hover:text-slate-600' }}">
                <i class="fa-regular fa-calendar-check text-[19px]"></i>
                <span class="text-[10px] mt-1 tracking-tight">Riwayat</span>
            </a>

            <!-- Tab 3: Izin/Cuti -->
            <a href="{{ route('guru.izin') }}" 
               class="flex flex-col items-center justify-center flex-1 h-12 transition-colors {{ request()->routeIs('guru.izin*') ? 'text-[#13793e] font-bold' : 'text-slate-400 hover:text-slate-600' }}">
                <i class="fa-regular fa-calendar-plus text-[19px]"></i>
                <span class="text-[10px] mt-1 tracking-tight">Izin/Cuti</span>
            </a>

            <!-- Tab 4: Profil -->
            <a href="{{ route('guru.profile') }}" 
               class="flex flex-col items-center justify-center flex-1 h-12 transition-colors {{ request()->routeIs('guru.profile') ? 'text-[#13793e] font-bold' : 'text-slate-400 hover:text-slate-600' }}">
                <i class="fa-regular fa-user text-[19px]"></i>
                <span class="text-[10px] mt-1 tracking-tight">Profil</span>
            </a>
        </div>
    </nav>

    <script>
        // Web Audio Chime Synthesizer (Lonceng Pengingat Sekolah Halus)
        function playReminderChime() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const now = ctx.currentTime;
                
                // Nada 1: Sol (G5 - 784 Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(783.99, now);
                gain1.gain.setValueAtTime(0.25, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.6);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.6);

                // Nada 2: Do tinggi (C6 - 1046 Hz)
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(1046.50, now + 0.15);
                gain2.gain.setValueAtTime(0.3, now + 0.15);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.9);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.15);
                osc2.stop(now + 0.9);
            } catch(e) {}
        }

        // Minta Izin Notifikasi Browser HP
        function requestNotificationPermission() {
            if (!("Notification" in window)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Notifikasi Tidak Didukung',
                    text: 'Peramban Anda belum mendukung notifikasi sistem pop-up.'
                });
                return;
            }

            Notification.requestPermission().then(function (permission) {
                if (permission === "granted") {
                    playReminderChime();
                    new Notification("🔔 Notifikasi Pengingat Aktif!", {
                        body: "Anda akan menerima pengingat otomatis saat jam presensi masuk dan pulang tiba.",
                        icon: "{{ $appSetting->logo_url ?? asset('logo.jpg') }}"
                    });
                    Swal.fire({
                        icon: 'success',
                        title: 'Notifikasi Berhasil Diaktifkan!',
                        text: 'Ponsel Anda akan membunyikan lonceng dan menampilkan notifikasi saat jam presensi dibuka.',
                        confirmButtonColor: '#004ac6'
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Izin Belum Diberikan',
                        text: 'Izin notifikasi diblokir atau diabaikan di pengaturan peramban.'
                    });
                }
            });
        }

        function showNotificationInfo() {
            const hasNotif = "Notification" in window && Notification.permission === "granted";
            
            Swal.fire({
                title: 'Pengingat Presensi Sekolah',
                html: `
                    <div class="text-left text-sm space-y-3">
                        <div class="p-3 bg-surface-container-low rounded-xl border border-surface-container space-y-1.5">
                            <p class="flex items-center justify-between">
                                <span class="text-on-surface-variant font-medium">Jam Masuk:</span>
                                <strong class="text-primary font-mono">{{ substr($appSetting->time_in, 0, 5) }} - {{ substr($appSetting->time_in_late_threshold, 0, 5) }} WIB</strong>
                            </p>
                            <p class="flex items-center justify-between">
                                <span class="text-on-surface-variant font-medium">Jam Pulang:</span>
                                <strong class="text-amber-800 font-mono">{{ substr($appSetting->time_out, 0, 5) }} WIB</strong>
                            </p>
                            <p class="flex items-center justify-between">
                                <span class="text-on-surface-variant font-medium">Batas Radius GPS:</span>
                                <strong class="text-emerald-700 font-mono">{{ $appSetting->radius_meters ?? 150 }} meter</strong>
                            </p>
                        </div>

                        <div class="p-2.5 bg-blue-50 border border-blue-200 rounded-xl text-blue-900 text-xs">
                            <p class="font-bold mb-1">💡 Pengingat di HP Anda</p>
                            <p>Aktifkan notifikasi peramban agar Anda mendapatkan suara lonceng dan pesan pop-up pengingat ketika jam masuk atau jam pulang tiba.</p>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonColor: '#004ac6',
                cancelButtonColor: '#64748b',
                confirmButtonText: hasNotif ? '🔊 Tes Bunyi Lonceng' : '🔔 Aktifkan Pengingat di HP',
                cancelButtonText: 'Tutup',
                customClass: {
                    popup: 'rounded-2xl shadow-xl'
                }
            }).then((res) => {
                if (res.isConfirmed) {
                    if (hasNotif) {
                        playReminderChime();
                        Swal.fire({
                            title: 'Lonceng Berbunyi!',
                            text: 'Suara pengingat presensi berfungsi dengan baik.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    } else {
                        requestNotificationPermission();
                    }
                }
            });
        }

        // Global SweetAlert2 Form Confirmation Handler
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.hasAttribute('data-confirm')) {
                e.preventDefault();
                const msg = form.getAttribute('data-confirm');
                const title = form.getAttribute('data-confirm-title') || 'Konfirmasi';
                const icon = form.getAttribute('data-confirm-icon') || 'question';
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
            title: 'Perhatian!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#004ac6',
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
            confirmButtonColor: '#004ac6',
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
