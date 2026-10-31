<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPRES BU - Sistem Informasi Manajemen Presensi Bahrul Ulum</title>
    
    <link rel="icon" type="image/png" href="{{ asset('icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon-192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}?v={{ time() }}">
    <meta name="theme-color" content="#13793e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="SIMPRES BU">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
        @keyframes bounce-up-right {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(4px, -6px); }
        }
        .animate-bounce-corner {
            animation: bounce-up-right 1.2s infinite ease-in-out;
        }
    </style>
</head>
<body class="min-h-full bg-gradient-to-b from-[#f2f6fd] via-[#f7f9ff] to-[#edf3fa] text-slate-800 flex flex-col justify-between py-6 px-4 sm:px-6 relative">

    <!-- Floating Pointer / Chrome Menu Guide Banner (Tampil saat pengguna diarahkan ke menu Chrome) -->
    <div id="chromePointerGuide" class="fixed top-2 right-2 z-50 max-w-xs bg-slate-900 text-white rounded-2xl p-3.5 shadow-2xl border border-emerald-500/50 hidden">
        <div class="flex items-start gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0 font-bold text-sm">
                ⋮
            </div>
            <div class="text-xs">
                <div class="flex items-center justify-between">
                    <span class="font-extrabold text-emerald-400">Ketuk Menu Titik 3 di Atas ↗</span>
                    <button type="button" onclick="hideChromeGuide()" class="text-slate-400 hover:text-white ml-2 text-sm">✕</button>
                </div>
                <p class="text-slate-200 mt-1">
                    Lalu pilih menu <b class="text-white bg-white/20 px-1 py-0.5 rounded">"Instal Aplikasi"</b> atau <b class="text-white bg-white/20 px-1 py-0.5 rounded">"Tambahkan ke Layar Utama"</b>
                </p>
            </div>
            <div class="text-emerald-400 text-lg animate-bounce-corner shrink-0">
                <i class="fa-solid fa-arrow-trend-up"></i>
            </div>
        </div>
    </div>

    @php
        $foundation = $setting->foundation_name ?? 'Yayasan Bahrul Ulum';
        $appName = $setting->school_name ?? 'SIMPRES BU';

        $waNumber = preg_replace('/[^0-9]/', '', $setting->school_phone ?? '089629615301');
        if (str_starts_with($waNumber, '0')) {
            $waNumber = '62' . substr($waNumber, 1);
        }
    @endphp

    <div class="w-full max-w-sm sm:max-w-md mx-auto my-auto">

        <!-- Top Header: Logo & School Title -->
        <div class="text-center mb-5">
            <!-- Circular Logo Badge -->
            <div class="relative inline-flex items-center justify-center mb-3.5">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-white p-2 shadow-[0_10px_25px_-5px_rgba(0,0,0,0.08)] border border-slate-100 flex items-center justify-center">
                    <img src="{{ $setting->logo_url }}" alt="Logo {{ $foundation }}" class="w-full h-full object-contain rounded-full" onerror="this.onerror=null;this.src='{{ asset('logo.jpg') }}';">
                </div>
                <!-- Status Green Indicator Dot -->
                <span class="w-4 h-4 rounded-full bg-[#13793e] border-2 border-white absolute bottom-1 right-1 shadow-sm"></span>
            </div>

            <!-- Main Heading -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-[#13793e] font-extrabold text-[11px] tracking-wider mb-2 uppercase">
                <i class="fa-solid fa-building-columns text-[10px]"></i>
                {{ $foundation }}
            </div>
            <h1 class="text-2xl sm:text-[28px] font-black text-slate-900 tracking-tight leading-tight">
                {{ $appName }}
            </h1>
            <p class="text-xs sm:text-[13px] font-bold text-emerald-800 mt-1">
                Sistem Informasi Manajemen Presensi Bahrul Ulum
            </p>
            <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5 leading-snug">
                Portal Presensi & Jurnal Mengajar Guru Terpadu
            </p>
        </div>

        <!-- Flash Messages -->
        @if(session('error'))
            <div class="mb-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2.5 shadow-sm">
                <i class="fa-solid fa-circle-exclamation text-rose-500 text-base shrink-0"></i>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5 shadow-sm">
                <i class="fa-solid fa-circle-check text-emerald-600 text-base shrink-0"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- White Card Container -->
        <div class="bg-white rounded-3xl p-5 sm:p-7 shadow-[0_12px_35px_-8px_rgba(20,40,90,0.06)] border border-slate-100">

            <!-- Multi-School Units Badge Banner -->
            <div class="bg-gradient-to-r from-emerald-50 via-[#ecfbf2] to-teal-50 border border-emerald-200/80 rounded-2xl p-3 flex items-center justify-between mb-4 shadow-2xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-emerald-600/10 text-[#13793e] flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-school text-xs"></i>
                    </div>
                    <div>
                        <div class="text-[11px] font-extrabold text-[#13793e] uppercase tracking-wide">Layanan Presensi Terpadu</div>
                        <div class="text-[11px] text-slate-600 font-medium">
                            @if(isset($schools) && $schools->count() > 0)
                                {{ $schools->pluck('name')->implode(' • ') }}
                            @else
                                MTs Syafiiyah &bull; MA Bahrul Ulum
                            @endif
                        </div>
                    </div>
                </div>
                <span class="px-2 py-0.5 rounded-md bg-white text-[#13793e] font-extrabold text-[10px] tracking-wider border border-emerald-200 shadow-2xs flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    AKTIF
                </span>
            </div>

            <!-- Login Form -->
            <form action="{{ route('login.submit') }}" method="POST" class="space-y-3.5">
                @csrf

                <!-- Input Email / NIP / Username -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-1.5">
                        Email, Username, atau NIP Guru
                    </label>
                    <div class="bg-[#f0f4fc] rounded-2xl px-3.5 py-3 flex items-center gap-3 border border-slate-200/40 focus-within:border-emerald-600 focus-within:bg-white focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all">
                        <i class="fa-regular fa-user text-slate-400 text-sm shrink-0"></i>
                        <input type="text" name="login" id="loginInput" value="{{ old('login') }}" required autofocus
                               placeholder="Contoh: admin@sekolah.sch.id / NIP Guru"
                               class="w-full bg-transparent text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 outline-none">
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-bold text-slate-800">
                            Password
                        </label>
                        <button type="button" onclick="openForgotModal()" class="text-xs font-semibold text-[#13793e] hover:underline">
                            Lupa Password?
                        </button>
                    </div>
                    <div class="bg-[#f0f4fc] rounded-2xl px-3.5 py-3 flex items-center gap-3 border border-slate-200/40 focus-within:border-emerald-600 focus-within:bg-white focus-within:ring-2 focus-within:ring-emerald-500/20 transition-all">
                        <i class="fa-solid fa-lock text-slate-400 text-sm shrink-0"></i>
                        <input type="password" name="password" id="passwordInput" required
                               placeholder="Masukkan password Anda"
                               class="w-full bg-transparent text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 outline-none">
                        <button type="button" onclick="togglePasswordVisibility()" class="text-slate-400 hover:text-slate-600 text-sm shrink-0">
                            <i id="eyeIcon" class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Hak Akses Notice -->
                <div class="flex items-center justify-between text-xs pt-0.5">
                    <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-600">
                        <input type="checkbox" name="remember" checked class="w-4 h-4 rounded border-slate-300 text-[#13793e] focus:ring-[#13793e]">
                        <span>Ingat Saya</span>
                    </label>
                    <span class="text-slate-400 text-[11px]">
                        Hak akses otomatis terdeteksi
                    </span>
                </div>

                <!-- Main Submit Button -->
                <button type="submit" 
                        class="w-full py-3.5 px-4 rounded-2xl bg-[#13793e] hover:bg-[#0f6332] text-white font-bold text-xs sm:text-sm shadow-lg shadow-emerald-800/15 transition-all active:scale-[0.98] flex items-center justify-center gap-2 mt-2">
                    <span>Masuk ke Sistem Presensi</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Divider -->
            <div class="relative flex items-center justify-center my-4">
                <div class="border-t border-slate-200 w-full"></div>
                <span class="bg-white px-3 text-[11px] text-slate-400 font-medium shrink-0">Aplikasi Mobile</span>
                <div class="border-t border-slate-200 w-full"></div>
            </div>

            <!-- Download / Install Aplikasi PWA (Android) -->
            <button type="button" id="btnInstallPwa" onclick="handlePwaInstall()"
                    class="w-full py-2.5 px-3.5 rounded-2xl bg-[#ecfdf5] hover:bg-[#d1fae5] border border-emerald-300 text-emerald-950 text-xs font-bold transition-all flex items-center justify-between group cursor-pointer shadow-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[#13793e] text-white flex items-center justify-center shadow-xs group-hover:scale-105 transition-transform shrink-0">
                        <i class="fa-brands fa-android text-lg"></i>
                    </div>
                    <div class="text-left">
                        <div class="flex items-center gap-1.5">
                            <span class="text-slate-900 font-extrabold text-[12px]" id="pwaBtnTitle">Pasang Aplikasi Android</span>
                            <span class="px-1.5 py-0.2 bg-emerald-600 text-white text-[9px] rounded font-bold">PWA</span>
                        </div>
                        <p class="text-[10px] text-emerald-700 font-medium" id="pwaBtnSubtitle">Instal langsung ke layar utama HP</p>
                    </div>
                </div>
                <div class="text-[#13793e] text-xs font-bold flex items-center gap-1 group-hover:translate-x-0.5 transition-transform bg-white/70 px-2 py-1 rounded-lg border border-emerald-200">
                    <span id="pwaBtnAction">Pasang</span>
                    <i class="fa-solid fa-download text-[11px]"></i>
                </div>
            </button>

        </div>

        <!-- Help / Support Box -->
        <div class="bg-[#eaf1ff] border border-blue-200/60 rounded-2xl p-3 sm:p-3.5 flex items-center justify-between mt-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-[#d5e4ff] text-[#13793e] flex items-center justify-center text-base sm:text-lg shrink-0">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs text-slate-800 leading-tight">Kendala Login / Presensi?</h4>
                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Bantuan Tim IT {{ $foundation }}</p>
                </div>
            </div>
            <a href="https://wa.me/{{ $waNumber }}?text=Halo%20Admin,%20saya%20mengalami%20kendala%20saat%20login%20ke%20aplikasi%20{{ urlencode($appName) }}." 
               target="_blank"
               class="px-3 py-1.5 sm:py-2 rounded-xl bg-[#13793e] hover:bg-[#0f6332] text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all shrink-0">
                <span>Chat WA</span>
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
            </a>
        </div>

        <!-- Footer Copyright -->
        <div class="text-center mt-5 text-[11px] text-slate-400 font-medium leading-relaxed">
            <p>© {{ date('Y') }} {{ $foundation }} - {{ $appName }}. Seluruh Hak Cipta Dilindungi.</p>
            <p class="text-slate-400/80">Infrastruktur Kepegawaian & Presensi Terpadu Berbasis Digital.</p>
        </div>

    </div>

    <!-- Modal Panduan Install PWA Android -->
    <div id="pwaModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs hidden">
        <div class="bg-white rounded-3xl max-w-sm w-full p-5 sm:p-6 text-slate-800 shadow-2xl relative border border-slate-100">
            <button type="button" onclick="closePwaModal()" class="absolute right-4 top-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <!-- Header Modal -->
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-[#e8f7ee] border border-[#c4ebd1] flex items-center justify-center text-[#13793e] text-2xl shrink-0">
                    <i class="fa-brands fa-android"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-base text-slate-900 leading-tight">Pasang Aplikasi di HP</h3>
                    <p class="text-xs text-slate-500">Android PWA (Tanpa Play Store)</p>
                </div>
            </div>

            <!-- Petunjuk Langkah Praktis -->
            <div class="space-y-2 text-xs text-slate-600 mb-4">
                <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-5 h-5 rounded-full bg-[#13793e] text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">1</div>
                    <div>
                        <span class="font-bold text-slate-800">Ketuk Menu Titik Tiga (⋮)</span>
                        <p class="text-[11px] text-slate-500">Terletak di pojok kanan atas browser Google Chrome Anda.</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-5 h-5 rounded-full bg-[#13793e] text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">2</div>
                    <div>
                        <span class="font-bold text-slate-800">Pilih "Instal Aplikasi"</span>
                        <p class="text-[11px] text-slate-500">Atau pilih <b class="text-slate-700">"Tambahkan ke Layar Utama"</b>.</p>
                    </div>
                </div>

                <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-5 h-5 rounded-full bg-[#13793e] text-white flex items-center justify-center font-bold text-[10px] shrink-0 mt-0.5">3</div>
                    <div>
                        <span class="font-bold text-slate-800">Tekan "Instal / Tambah"</span>
                        <p class="text-[11px] text-slate-500">Ikon aplikasi akan langsung muncul di beranda HP Anda!</p>
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="space-y-2">
                <button type="button" id="btnModalAction" onclick="handleModalAction()" 
                        class="w-full py-3 px-4 rounded-xl bg-[#13793e] hover:bg-[#0f6332] font-bold text-xs text-white shadow-md shadow-emerald-800/20 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-pointer"></i>
                    <span id="txtModalAction">Tunjukkan Titik 3 di Chrome</span>
                </button>
                <button type="button" onclick="closePwaModal()" 
                        class="w-full py-2 px-3 text-xs font-semibold text-slate-400 hover:text-slate-600 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Lupa Sandi -->
    <div id="forgotModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-slate-800 shadow-2xl relative border border-slate-100 text-center">
            <button type="button" onclick="closeForgotModal()" class="absolute right-4 top-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 hover:text-slate-800 transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>

            <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center text-2xl mx-auto mb-3">
                <i class="fa-solid fa-key"></i>
            </div>
            <h3 class="font-extrabold text-base text-slate-900 mb-1">Reset Kata Sandi</h3>
            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                Untuk keamanan akun, permohonan reset password dilayani langsung oleh Tim Layanan IT {{ $foundation }}.
            </p>

            <a href="https://wa.me/{{ $waNumber }}?text=Halo%20Admin,%20saya%20lupa%20kata%20sandi%20akun%20presensi%20{{ urlencode($appName) }}%20({{ urlencode($foundation) }}).%20Mohon%20bantuan%20reset." 
               target="_blank"
               class="w-full py-3 px-4 rounded-xl bg-[#13793e] hover:bg-[#0f6332] text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md shadow-emerald-800/20">
                <i class="fa-brands fa-whatsapp text-sm"></i>
                <span>Hubungi Admin via WhatsApp</span>
            </a>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePasswordVisibility() {
            const pwd = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Forgot Password Modal
        function openForgotModal() {
            document.getElementById('forgotModal').classList.remove('hidden');
        }
        function closeForgotModal() {
            document.getElementById('forgotModal').classList.add('hidden');
        }

        // ===================================
        // PWA INSTALLATION HANDLER (ANDROID)
        // ===================================
        let deferredPrompt = null;

        // Cek jika aplikasi sudah dibuka dalam mode standalone (sudah terinstal)
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        if (isStandalone) {
            const btnTitle = document.getElementById('pwaBtnTitle');
            const btnSub = document.getElementById('pwaBtnSubtitle');
            const btnAction = document.getElementById('pwaBtnAction');
            if (btnTitle) btnTitle.textContent = 'Aplikasi Terpasang';
            if (btnSub) btnSub.textContent = 'Anda sedang membuka versi aplikasi PWA';
            if (btnAction) btnAction.innerHTML = '<i class="fa-solid fa-check text-emerald-600"></i>';
        }

        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            console.log('PWA: Prompt instalasi siap dipicu.');

            // Perbarui tombol modal jika prompt native siap
            const txtAction = document.getElementById('txtModalAction');
            if (txtAction) {
                txtAction.textContent = 'Instal Sekarang (Otomatis)';
            }
        });

        window.addEventListener('appinstalled', () => {
            console.log('PWA: Aplikasi sukses terpasang di perangkat.');
            deferredPrompt = null;
            closePwaModal();
            hideChromeGuide();
            alert('Aplikasi E-Presensi berhasil dipasang di layar utama HP Anda!');
        });

        function handlePwaInstall() {
            if (isStandalone) {
                alert('Aplikasi sudah terpasang dan sedang berjalan di layar utama Anda.');
                return;
            }

            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('PWA: Pengguna menyetujui instalasi.');
                    }
                    deferredPrompt = null;
                });
            } else {
                openPwaModal();
            }
        }

        function handleModalAction() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('PWA: Pengguna menyetujui instalasi.');
                    }
                    deferredPrompt = null;
                });
                closePwaModal();
            } else {
                // Tampilkan banner pemandu menuju menu titik 3 Chrome
                closePwaModal();
                showChromeGuide();
            }
        }

        function showChromeGuide() {
            const guide = document.getElementById('chromePointerGuide');
            if (guide) {
                guide.classList.remove('hidden');
                // Auto hide setelah 12 detik jika tidak ditutup
                setTimeout(() => {
                    hideChromeGuide();
                }, 12000);
            }
        }

        function hideChromeGuide() {
            const guide = document.getElementById('chromePointerGuide');
            if (guide) {
                guide.classList.add('hidden');
            }
        }

        function openPwaModal() {
            document.getElementById('pwaModal').classList.remove('hidden');
        }

        function closePwaModal() {
            document.getElementById('pwaModal').classList.add('hidden');
        }

        // Daftarkan Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js?v={{ time() }}')
                    .then((reg) => {
                        console.log('PWA: Service Worker terdaftar.', reg.scope);
                        reg.update();
                    })
                    .catch((err) => console.log('PWA: Service Worker gagal.', err));
            });
        }
    </script>
</body>
</html>
