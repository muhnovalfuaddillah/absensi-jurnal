@extends('errors.layout')

@section('title', 'Akses Ditolak (403)')
@section('code', '403')

@section('icon')
<div class="relative w-24 h-24 rounded-3xl bg-rose-50 border border-rose-100 flex items-center justify-center text-rose-600 shadow-inner">
    <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs">
        <i class="fa-solid fa-lock"></i>
    </div>
    <i class="fa-solid fa-shield-halved text-4xl"></i>
</div>
@endsection

@section('heading', 'Akses Halaman Dibatasi')

@section('message')
Anda tidak memiliki izin atau hak akses untuk membuka halaman ini. Silakan pastikan Anda masuk menggunakan akun dengan peran yang sesuai atau hubungi administrator sekolah.
@endsection
