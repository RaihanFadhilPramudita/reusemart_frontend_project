@extends('layouts.app')

@section('title', 'Bantuan')

@section('content')
<div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-3xl mx-auto bg-white p-6 shadow-md rounded">
        <h2 class="text-xl font-bold mb-4">Bantuan Umum</h2>
        <p class="text-gray-700 leading-relaxed">
            Jika Anda mengalami kendala saat menggunakan platform kami, silakan hubungi customer service melalui halaman <a href="/hubungi-kami" class="text-green-600 hover:underline">Hubungi Kami</a> atau baca FAQ di halaman utama. Beberapa solusi umum:
        </p>
        <ul class="mt-4 list-disc list-inside text-sm text-gray-600">
            <li>Tidak bisa login: pastikan email dan password benar.</li>
            <li>Barang tidak muncul: coba reload atau periksa koneksi internet.</li>
            <li>Butuh bantuan lebih lanjut? Tim kami siap membantu setiap hari kerja.</li>
        </ul>
    </div>
</div>
@endsection