@extends('errors.layout')

@section('title', 'Terjadi Kendala pada Aplikasi (500)')
@section('code', '500')

@section('icon')
<div class="relative w-24 h-24 rounded-3xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 shadow-inner">
    <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs animate-ping"></div>
    <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs">
        <i class="fa-solid fa-exclamation"></i>
    </div>
    <i class="fa-solid fa-server text-4xl"></i>
</div>
@endsection

@section('heading', 'Terjadi Kendala Teknis pada Sistem')

@section('message')
Mohon maaf, sistem presensi sedang mengalami hambatan teknis sementara. Data Anda tetap aman. Silakan klik <b>Muat Ulang Halaman</b> atau hubungi <b>Tim IT</b> jika kendala terus berlanjut.
@endsection
