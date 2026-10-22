@extends('errors.layout')

@section('title', 'Sesi Halaman Berakhir (419)')
@section('code', '419')

@section('icon')
<div class="relative w-24 h-24 rounded-3xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 shadow-inner">
    <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center text-xs">
        <i class="fa-solid fa-clock-rotate-left"></i>
    </div>
    <i class="fa-solid fa-hourglass-half text-4xl"></i>
</div>
@endsection

@section('heading', 'Sesi Halaman Telah Berakhir')

@section('message')
Demi keamanan akun dan integritas data presensi, sesi halaman Anda telah kedaluwarsa karena tidak ada aktivitas dalam waktu lama. Silakan muat ulang halaman atau login kembali.
@endsection
