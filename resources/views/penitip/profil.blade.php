@extends('layouts.app')

@section('title', 'Profil Penitip')

@section('content')
<div class="flex min-h-screen bg-gray-100">
  <x-penitip-sidebar />

  <main class="flex-1 p-6">
    <h2 class="text-2xl font-bold mb-4">Profil Saya</h2>
    <div class="bg-white shadow rounded p-6 space-y-4 max-w-lg">
      <div>
        <strong>Nama:</strong>
        <p id="namaPenitip">Memuat...</p>
      </div>
      <div>
        <strong>Email:</strong>
        <p id="emailPenitip">Memuat...</p>
      </div>
      <div>
        <strong>No Telepon:</strong>
        <p id="teleponPenitip">Memuat...</p>
      </div>
      <div>
        <strong>No KTP:</strong>
        <p id="ktpPenitip">Memuat...</p>
      </div>
      <div>
        <strong>Tanggal Lahir:</strong>
        <p id="lahirPenitip">Memuat...</p>
      </div>
      <div>
        <strong>Saldo:</strong>
        <p id="saldoPenitip">Memuat...</p>
      </div>
      <div>
        <strong>Poin Sosial:</strong>
        <p id="poinPenitip">Memuat...</p>
      </div>
    </div>
    <div class="mt-8">
    <h2 class="text-lg font-bold mb-4">Riwayat Penjualan</h2>
    <div id="penitip-transaksi" class="space-y-4 text-sm text-gray-700">
      <p>Memuat data...</p>
  </div>
  </main>
</div>
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

  document.getElementById('namaPenitip').textContent = user.NAMA_PENITIP ?? '-';
  document.getElementById('emailPenitip').textContent = user.EMAIL ?? '-';
  document.getElementById('teleponPenitip').textContent = user.NO_TELEPON ?? '-';
  document.getElementById('ktpPenitip').textContent = user.NO_KTP ?? '-';
  document.getElementById('lahirPenitip').textContent = user.TANGGAL_LAHIR ?? '-';
  document.getElementById('saldoPenitip').textContent = user.SALDO != null ? 'Rp' + parseInt(user.SALDO).toLocaleString('id-ID') : 'Rp0';
  document.getElementById('poinPenitip').textContent = user.POIN_SOSIAL ?? '0';
});
</script>
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

  document.getElementById('namaPenitip').textContent = user.NAMA_PENITIP ?? '-';
  document.getElementById('emailPenitip').textContent = user.EMAIL ?? '-';
  document.getElementById('teleponPenitip').textContent = user.NO_TELEPON ?? '-';
  document.getElementById('ktpPenitip').textContent = user.NO_KTP ?? '-';
  document.getElementById('lahirPenitip').textContent = user.TANGGAL_LAHIR ?? '-';
  document.getElementById('saldoPenitip').textContent = user.SALDO != null ? 'Rp' + parseInt(user.SALDO).toLocaleString('id-ID') : 'Rp0';
  document.getElementById('poinPenitip').textContent = user.POIN_SOSIAL ?? '0';

  loadTransaksiPenitip(); // penting!
});

async function loadTransaksiPenitip() {
  const container = document.getElementById('penitip-transaksi');
  const token = localStorage.getItem('token');

  try {
    const res = await axios.get('http://localhost:8000/api/penitip/transaksi', {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    const data = res.data.data;
    console.log('Transaksi:', data); // Debug

    if (!data.length) {
      container.innerHTML = '<p class="text-gray-500">Belum ada transaksi.</p>';
      return;
    }

    container.innerHTML = data.map(item => `
      <div class="border rounded p-4 bg-white shadow">
        <div class="flex justify-between mb-2">
          <div>
            <h4 class="font-semibold">No Nota: ${item.NO_NOTA}</h4>
            <p class="text-gray-500 text-sm">${new Date(item.WAKTU_PESAN).toLocaleDateString('id-ID')}</p>
          </div>
          <div class="text-green-600 font-semibold">Rp ${parseInt(item.TOTAL_AKHIR).toLocaleString('id-ID')}</div>
        </div>
        ${(item.detailTransaksi || []).map(d => `
          <div class="flex justify-between text-sm">
            <span>${d.barang?.NAMA_BARANG || '-'}</span>
            <span>x${d.JUMLAH}</span>
          </div>
        `).join('')}
      </div>
    `).join('');
  } catch (err) {
    console.error('Gagal load transaksi:', err);
    container.innerHTML = '<p class="text-red-500">Gagal memuat transaksi.</p>';
  }
}
</script>
@endsection

