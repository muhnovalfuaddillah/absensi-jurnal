@extends('errors.layout')

@section('title', 'Halaman Tidak Ditemukan (404)')
@section('code', '404')

@section('icon')
<div class="relative w-24 h-24 rounded-3xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 shadow-inner">
    <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-amber-500 text-white flex items-center justify-center text-xs">
        <i class="fa-solid fa-question"></i>
    </div>
    <i class="fa-solid fa-compass text-4xl"></i>
</div>
@endsection

@section('heading', 'Halaman Tidak Ditemukan')

@section('message')
Tautan atau halaman yang Anda tuju tidak tersedia, telah dipindahkan, atau alamat URL yang Anda masukkan keliru. Silakan kembali ke beranda aplikasi presensi.
@endsection
