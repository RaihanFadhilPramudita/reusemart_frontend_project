@extends('layouts.app')

@section('title', 'Beranda Penitip')

@section('content')
<div class="flex min-h-screen bg-gray-100">
  <x-penitip-sidebar />

  <main class="flex-1 p-6">
    <h2 class="text-2xl font-bold mb-4">Selamat Datang, <span id="penitipNama">Penitip</span></h2>
    <p class="text-gray-700">Silakan pilih menu di sebelah kiri untuk mengelola data penitip Anda.</p>
  </main>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const token = localStorage.getItem('token');
  const user = JSON.parse(localStorage.getItem('user'));

  if (!token || !user) {
    alert('Belum login. Silakan login terlebih dahulu.');
    window.location.href = '/login';
    return;
  }

  const namaEl = document.getElementById('penitipNama');
  if (namaEl && user.nama_penitip) {
    namaEl.textContent = user.nama_penitip;
  }
});
</script>
@endsection
