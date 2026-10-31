<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Terjadi Kendala Sistem') - SIMPRES BU</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome 6 & Material Symbols -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#13793e',
                            800: '#0f6031',
                            900: '#14532d',
                        },
                        navy: {
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#004ac6',
                            900: '#0f172a',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    },
                    animation: {
                        'float': 'float 4s ease-in-out infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-10px)' },
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: radial-gradient(circle at 50% 0%, #f1f8f3 0%, #f8fafc 60%, #eef2f6 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 20px 45px -15px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(15, 23, 42, 0.03);
        }
    </style>
</head>
<body class="flex flex-col min-h-screen text-slate-800 antialiased selection:bg-[#13793e] selection:text-white">

    @php
        // Ambil pengaturan dengan fail-safe jika database tidak tersambung
        try {
            $setting = \App\Models\AttendanceSetting::getSettings();
            $schoolName = $setting->school_name ?: 'Madrasah';
            $foundation = $setting->foundation_name ?: 'Yayasan Bahrul Ulum';
            $logoUrl = $setting->logo_url ?: asset('logo.jpg');
            $rawPhone = $setting->school_phone ?: '081234567890';
        } catch (\Throwable $e) {
            $schoolName = 'Madrasah';
            $foundation = 'Yayasan Bahrul Ulum';
            $logoUrl = asset('logo.jpg');
            $rawPhone = '081234567890';
        }

        $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
        if (str_starts_with($cleanPhone, '0')) {
            $waNumber = '62' . substr($cleanPhone, 1);
        } elseif (str_starts_with($cleanPhone, '62')) {
            $waNumber = $cleanPhone;
        } else {
            $waNumber = '62' . $cleanPhone;
        }

        $statusCode = View::yieldContent('code', '500');
        $statusTitle = View::yieldContent('title', 'Terjadi Kendala Sistem');
        $waMessage = "Halo Tim IT Madrasah, saya mengalami kendala pada aplikasi SIMPRES BU dengan kode error: " . $statusCode . " (" . $statusTitle . "). Mohon bantuannya.";
    @endphp

    <!-- Decorative Top Bar -->
    <div class="h-1.5 w-full bg-gradient-to-r from-[#13793e] via-emerald-500 to-[#004ac6]"></div>

    <!-- Main Container -->
    <div class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-lg">
            
            <!-- School Brand Pill -->
            <div class="flex items-center justify-center gap-2 mb-4 animate-float">
                <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-2xl bg-white/80 border border-slate-200/80 shadow-xs backdrop-blur-md">
                    <img src="{{ $logoUrl }}" alt="Logo" class="w-6 h-6 rounded-lg object-contain" onerror="this.src='{{ asset('logo.jpg') }}'">
                    <span class="font-extrabold text-xs text-slate-800 tracking-tight">{{ $schoolName }}</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span class="text-[11px] text-slate-500 font-medium truncate max-w-[140px]">{{ $foundation }}</span>
                </div>
            </div>

            <!-- Error Main Glass Card -->
            <div class="glass-card rounded-[2.5rem] p-6 sm:p-8 flex flex-col items-center text-center relative overflow-hidden">
                
                <!-- Background Geometric Glow -->
                <div class="absolute -top-24 -right-24 w-48 h-48 rounded-full bg-emerald-500/10 blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-48 h-48 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

                <!-- Error Icon Illustration -->
                <div class="relative mb-5">
                    @yield('icon')
                </div>

                <!-- Error Code Badge -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 border border-slate-200/80 text-slate-600 font-extrabold text-xs tracking-wider mb-2">
                    <span class="material-symbols-outlined text-[15px] text-amber-600">warning</span>
                    <span>KODE STATUS: @yield('code', '500')</span>
                </div>

                <!-- Error Heading & Description -->
                <h1 class="font-black text-xl sm:text-2xl text-slate-900 leading-snug mb-2">
                    @yield('heading', 'Terjadi Kendala pada Aplikasi')
                </h1>
                
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-md mb-6">
                    @yield('message', 'Mohon maaf atas ketidaknyamanan ini. Sistem sedang mengalami kendala sementara atau halaman yang Anda tuju sedang tidak tersedia.')
                </p>

                <!-- Detailed Technical Note (If in debug mode or available) -->
                @if(config('app.debug') && !empty($exception) && $exception->getMessage())
                <div class="w-full mb-6 text-left">
                    <details class="rounded-2xl bg-rose-50/70 border border-rose-200 p-3 text-[11px] text-rose-800">
                        <summary class="font-bold cursor-pointer flex items-center justify-between text-rose-700">
                            <span>Rincian Teknis Masalah (Mode Debug)</span>
                            <i class="fa-solid fa-chevron-down text-[10px]"></i>
                        </summary>
                        <div class="mt-2 font-mono text-[10.5px] p-2 rounded-xl bg-white border border-rose-200 overflow-x-auto text-rose-900 leading-normal">
                            {{ $exception->getMessage() }}
                        </div>
                    </details>
                </div>
                @endif

                <!-- Primary Action Buttons -->
                <div class="w-full flex flex-col sm:flex-row items-center gap-2.5 mb-5">
                    <button type="button" onclick="window.location.reload()" 
                            class="w-full sm:flex-1 py-3 px-4 rounded-2xl bg-[#13793e] hover:bg-[#0f6031] text-white font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 shadow-lg shadow-emerald-900/15 transition-all transform active:scale-95">
                        <i class="fa-solid fa-arrow-rotate-right text-xs"></i>
                        <span>Muat Ulang Halaman</span>
                    </button>

                    <a href="{{ url('/') }}" 
                       class="w-full sm:flex-1 py-3 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs sm:text-sm flex items-center justify-center gap-2 border border-slate-200/80 transition-all transform active:scale-95">
                        <i class="fa-solid fa-house text-xs"></i>
                        <span>Kembali ke Beranda</span>
                    </a>
                </div>

                <!-- IT Support Box (Direct WhatsApp Connect) -->
                <div class="w-full rounded-2xl bg-surface-container-low bg-slate-50 border border-slate-200/80 p-3.5 flex items-center justify-between gap-3 text-left">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#13793e] flex items-center justify-center text-lg shrink-0">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-extrabold text-xs text-slate-800 leading-tight truncate">Bantuan Tim IT Aplikasi</h4>
                            <p class="text-[11px] text-slate-500 font-medium leading-tight mt-0.5 truncate">Laporkan kendala langsung via WhatsApp</p>
                        </div>
                    </div>

                    <a href="https://wa.me/{{ $waNumber }}?text={{ urlencode($waMessage) }}" 
                       target="_blank" 
                       class="px-3.5 py-2 rounded-xl bg-[#13793e] hover:bg-[#0f6031] text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition-all shrink-0">
                        <span>Hubungi IT</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>

            </div>

            <!-- Page Footer Note -->
            <div class="text-center mt-5 text-[11px] text-slate-400 font-medium">
                <p>&copy; {{ date('Y') }} {{ $schoolName }} &bull; SIMPRES BU (Sistem Informasi Manajemen Presensi Bahrul Ulum)</p>
            </div>

        </div>
    </div>

</body>
</html>
