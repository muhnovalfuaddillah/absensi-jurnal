@extends('layouts.app')

@section('title', 'Pindai QR Code Ruang Kelas - E-Presensi')

@section('content')
<div class="flex flex-col gap-4">

    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('guru.jurnal.index') }}" 
               class="w-9 h-9 rounded-full bg-white border border-slate-200/80 text-slate-700 flex items-center justify-center hover:bg-slate-50 transition-colors shadow-2xs">
                <i class="fa-solid fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h1 class="text-lg font-extrabold text-slate-900 tracking-tight leading-tight">Pindai QR Ruang Kelas</h1>
                <p class="text-xs text-slate-500 font-medium">Scan barcode di dinding kelas untuk membuka jurnal</p>
            </div>
        </div>
    </div>

    @if($targetSchedule)
        <!-- Target Info -->
        <div class="p-3.5 rounded-3xl bg-emerald-50/70 border border-emerald-200 flex items-center gap-3 text-xs text-emerald-950 font-bold shadow-2xs">
            <div class="w-8 h-8 rounded-xl bg-[#13793e] text-white flex items-center justify-center text-xs shrink-0">
                <i class="fa-regular fa-calendar-check"></i>
            </div>
            <div>
                <p class="font-extrabold text-emerald-950">Target: {{ $targetSchedule->subject->name }} ({{ $targetSchedule->schoolClass->name }})</p>
                <p class="text-[11px] text-emerald-700 font-medium">{{ $targetSchedule->day }}, Jam {{ substr($targetSchedule->start_time, 0, 5) }} - {{ substr($targetSchedule->end_time, 0, 5) }} WIB</p>
            </div>
        </div>
    @endif

    <!-- Scanner Box Container -->
    <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-xs flex flex-col items-center gap-4">
        
        <!-- Live Camera Viewfinder -->
        <div class="relative w-full max-w-sm aspect-square bg-slate-950 rounded-3xl overflow-hidden shadow-lg flex flex-col items-center justify-center border-4 border-slate-800">
            
            <div id="qr-reader" class="w-full h-full"></div>

            <!-- Overlay Petunjuk Viewfinder -->
            <div id="scannerOverlay" class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center p-6">
                <!-- Target Box Borders with Emerald Glow -->
                <div class="w-56 h-56 border-2 border-emerald-400 rounded-3xl relative animate-pulse shadow-[0_0_25px_rgba(19,121,62,0.4)]">
                    <div class="absolute -top-1 -left-1 w-6 h-6 border-t-4 border-l-4 border-emerald-400 rounded-tl-xl"></div>
                    <div class="absolute -top-1 -right-1 w-6 h-6 border-t-4 border-r-4 border-emerald-400 rounded-tr-xl"></div>
                    <div class="absolute -bottom-1 -left-1 w-6 h-6 border-b-4 border-l-4 border-emerald-400 rounded-bl-xl"></div>
                    <div class="absolute -bottom-1 -right-1 w-6 h-6 border-b-4 border-r-4 border-emerald-400 rounded-br-xl"></div>
                </div>
                <p class="mt-4 text-white text-[11px] font-bold bg-black/70 px-3.5 py-1.5 rounded-full backdrop-blur-md text-center shadow-md">
                    Arahkan kamera ke QR Code di dinding kelas
                </p>
            </div>

            <!-- Loading State -->
            <div id="scannerLoading" class="absolute inset-0 bg-slate-950 flex flex-col items-center justify-center gap-2.5 text-white">
                <i class="fa-solid fa-circle-notch animate-spin text-3xl text-[#13793e]"></i>
                <span class="text-xs font-semibold text-slate-300">Menghubungkan ke kamera ponsel...</span>
            </div>
        </div>

        <!-- Camera Controls -->
        <div class="w-full max-w-sm flex items-center justify-between gap-2.5">
            <button type="button" onclick="switchCamera()" id="btnSwitchCam" 
                    class="flex-1 py-2.5 px-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center gap-2 transition-colors shadow-2xs">
                <i class="fa-solid fa-camera-rotate text-xs"></i>
                <span>Ganti Kamera</span>
            </button>
            <button type="button" onclick="restartScanner()" 
                    class="py-2.5 px-4 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold flex items-center justify-center gap-2 transition-colors shadow-2xs">
                <i class="fa-solid fa-arrows-rotate text-xs"></i>
                <span>Muat Ulang</span>
            </button>
        </div>

        <!-- Info Card -->
        <div class="w-full max-w-sm p-3.5 bg-emerald-50 rounded-2xl border border-emerald-200/80 text-emerald-950 text-xs flex items-start gap-3">
            <i class="fa-solid fa-circle-info text-[#13793e] text-base shrink-0 mt-0.5"></i>
            <div>
                <p class="font-extrabold text-emerald-950 mb-0.5">Validasi Fisik Ruang Kelas:</p>
                <p class="leading-relaxed text-[11px] text-emerald-800 font-medium">
                    Pindai QR Code kelas secara langsung untuk memastikan kegiatan pembelajaran tatap muka terlaksana di dalam ruang yang tepat.
                </p>
            </div>
        </div>

        <!-- Alternatif Masukkan Token Kelas Manual -->
        <div class="w-full max-w-sm pt-3 border-t border-slate-100 flex flex-col gap-2.5">
            <button type="button" onclick="toggleManualInput()" class="text-xs text-[#13793e] font-extrabold hover:underline flex items-center justify-between py-1">
                <span class="flex items-center gap-2">
                    <i class="fa-solid fa-key text-xs"></i>
                    <span>Atau Masukkan Token Ruang Kelas Manual:</span>
                </span>
                <i class="fa-solid fa-chevron-down text-xs transition-transform" id="manualToggleIcon"></i>
            </button>
            <div id="manualInputContainer" class="hidden flex flex-col gap-2 pt-1">
                <form onsubmit="event.preventDefault(); submitManualToken();" class="flex gap-2">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-keyboard absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="manualClassToken" 
                               placeholder="Ketik token kelas (misal: CLS-...)" 
                               autocomplete="off"
                               autocapitalize="characters"
                               class="w-full pl-9 pr-3 py-2.5 text-xs uppercase font-mono rounded-2xl bg-slate-50 border border-slate-200 text-slate-800 focus:outline-none focus:border-[#13793e] focus:bg-white font-bold tracking-wider placeholder:tracking-normal placeholder:font-sans placeholder:font-normal transition-all">
                    </div>
                    <button type="submit" class="px-4 py-2.5 bg-[#13793e] hover:bg-[#0f6332] text-white rounded-2xl text-xs font-black shrink-0 shadow-md shadow-emerald-800/15 flex items-center gap-1.5 active:scale-95 transition-all">
                        <span>Verifikasi</span>
                        <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </button>
                </form>
                <p class="text-[10px] text-slate-400 font-medium">
                    Gunakan ini jika kamera perangkat Anda sedang terkendala. Token tertera di bagian bawah lembar QR Code fisik.
                </p>
            </div>
        </div>

    </div>

</div>

<!-- HTML5-QRCode Library -->
<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
    let html5QrCode = null;
    let currentFacingMode = "environment"; // default kamera belakang
    let isProcessing = false;

    document.addEventListener('DOMContentLoaded', function() {
        startScanner();
    });

    function startScanner() {
        const loadingEl = document.getElementById('scannerLoading');
        loadingEl.classList.remove('hidden');

        if (html5QrCode) {
            html5QrCode.stop().catch(() => {}).then(() => initCamera());
        } else {
            html5QrCode = new Html5Qrcode("qr-reader");
            initCamera();
        }
    }

    function initCamera() {
        const loadingEl = document.getElementById('scannerLoading');
        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0
        };

        html5QrCode.start(
            { facingMode: currentFacingMode },
            config,
            onScanSuccess,
            onScanError
        ).then(() => {
            loadingEl.classList.add('hidden');
        }).catch(err => {
            console.warn("Gagal membuka kamera facingMode, fallback ke video: true", err);
            html5QrCode.start(
                { video: true },
                config,
                onScanSuccess,
                onScanError
            ).then(() => {
                loadingEl.classList.add('hidden');
            }).catch(e => {
                loadingEl.innerHTML = `
                    <div class="flex flex-col items-center gap-2 p-4 text-center">
                        <i class="fa-solid fa-video-slash text-3xl text-rose-500"></i>
                        <p class="text-xs text-rose-300 font-semibold">Izin kamera tidak diberikan atau perangkat tidak mendukung.</p>
                    </div>
                `;
            });
        });
    }

    function switchCamera() {
        currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
        startScanner();
    }

    function restartScanner() {
        isProcessing = false;
        startScanner();
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (isProcessing) return;
        isProcessing = true;

        playSuccessBeep();

        Swal.fire({
            title: 'Memverifikasi QR Kelas...',
            text: 'Memeriksa keabsahan kode ruang kelas',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        if (html5QrCode) {
            html5QrCode.stop().catch(() => {});
        }

        fetch('{{ route("guru.api.verify_class_qr") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                qr_token: decodedText,
                schedule_id: '{{ $targetSchedule?->id }}'
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Verifikasi Berhasil!',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = data.redirect_url;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'QR Tidak Valid!',
                    text: data.message || 'Bukan QR Code kelas yang terdaftar.',
                    confirmButtonText: 'Scan Ulang',
                    confirmButtonColor: '#13793e'
                }).then(() => {
                    restartScanner();
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Koneksi Gagal',
                text: 'Terjadi kesalahan saat memeriksa QR Code kelas.',
                confirmButtonText: 'Coba Lagi',
                confirmButtonColor: '#13793e'
            }).then(() => {
                restartScanner();
            });
        });
    }

    function onScanError(errorMessage) {
        // Ignored
    }

    function playSuccessBeep() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(880, ctx.currentTime);
            gain.gain.setValueAtTime(0.2, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.3);
        } catch(e) {}
    }

    function toggleManualInput() {
        const el = document.getElementById('manualInputContainer');
        const icon = document.getElementById('manualToggleIcon');
        if (el.classList.contains('hidden')) {
            el.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            el.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    }

    function submitManualToken() {
        const input = document.getElementById('manualClassToken');
        const token = input ? input.value.trim() : '';
        if (!token) {
            Swal.fire({
                icon: 'warning',
                title: 'Token Belum Diisi',
                text: 'Silakan ketik token kelas yang tertera pada lembar QR Code!',
                confirmButtonColor: '#13793e'
            });
            return;
        }
        onScanSuccess(token, null);
    }
</script>
@endsection
