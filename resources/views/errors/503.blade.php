@extends('errors.layout')

@section('title', 'Pemeliharaan Sistem Sedang Berlangsung (503)')
@section('code', '503')

@section('icon')
<div class="relative w-24 h-24 rounded-3xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-[#13793e] shadow-inner">
    <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center text-xs animate-spin">
        <i class="fa-solid fa-gear"></i>
    </div>
    <i class="fa-solid fa-screwdriver-wrench text-4xl"></i>
</div>
@endsection

@section('heading', 'Sistem Sedang Dalam Pemeliharaan')

@section('message')
Aplikasi HadirGuru sedang melakukan pembaruan berkala dan optimalisasi server untuk meningkatkan kenyamanan presensi Anda. Sistem akan segera aktif kembali beberapa saat lagi.
@endsection
