@extends('layouts.app')

@section('title', 'Laporan Komisi Bulanan')

@section('content')
  <div class="flex">
    <x-owner-sidebar />

    <main class="flex-1 p-6">
      @php
        $tahunAwal = date('Y', 0); // = "1970"

        $bulanList = [
          1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
          5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
          9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
      @endphp

      <div class="flex items-center gap-4 mb-4">
        <h2 class="text-xl font-bold">Laporan Komisi Bulanan</h2>

        <select id="bulanSelect" onchange="fetchLaporan()" class="border px-3 py-1 rounded text-sm">
          <option value="">Semua Bulan</option>
          @foreach ($bulanList as $num => $nama)
            <option value="{{ $num }}">{{ $nama }}</option>
          @endforeach
        </select>

        <select id="tahunSelect" onchange="fetchLaporan()" class="border px-3 py-1 rounded text-sm">
          <option value="">tahun
        </option>
          @for ($y = now()->year; $y >= $tahunAwal; $y--)
            <option value="{{ $y }}">{{ $y }}</option>
          @endfor
        </select>


        <button onclick="unduhLaporanPDF()" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded shadow">
          🖨 Cetak PDF
        </button>
      </div>


      <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
          <thead class="bg-gray-100 text-left font-semibold">
            <tr>
              <th class="px-4 py-3">ID Barang</th>
              <th class="px-4 py-3">Nama Barang</th>
              <th class="px-4 py-3">Harga Jual</th>
              <th class="px-4 py-3">Tanggal Masuk</th>
              <th class="px-4 py-3">Tanggal Laku</th>
              <th class="px-4 py-3">Komisi Hunter</th>
              <th class="px-4 py-3">Komisi ReUse Mart</th>
              <th class="px-4 py-3">Bonus Penitip</th>
            </tr>
          </thead>
          <tbody id="laporanBody" class="divide-y divide-gray-100"></tbody>
        </table>
      </div>
    </main>
  </div>
@endsection

@section('scripts')
<script>
  const headers = {
    Authorization: `Bearer ${localStorage.getItem('token')}`
  };

  async function fetchLaporan() {
    const bulan = document.getElementById('bulanSelect').value;
    const tahun = document.getElementById('tahunSelect').value;

    const params = new URLSearchParams();
    if (bulan) params.append('bulan', bulan);
    if (tahun) params.append('tahun', tahun);

    try {
      const res = await axios.get(`http://localhost:8000/api/owner/komisi?${params.toString()}`, { headers });
      const rows = res.data.data.map(komisi => {
        const barang = komisi.barang || {};
        return `
          <tr>
            <td class="px-4 py-2">${barang.ID_BARANG || '-'}</td>
            <td class="px-4 py-2">${barang.NAMA_BARANG || '-'}</td>
            <td class="px-4 py-2">Rp ${formatRupiah(barang.HARGA)}</td>
            <td class="px-4 py-2">${formatTanggal(barang.TANGGAL_MASUK)}</td>
            <td class="px-4 py-2">${formatTanggal(barang.TANGGAL_JUAL)}</td>
            <td class="px-4 py-2">Rp ${formatRupiah(komisi.JUMLAH_KOMISI_HUNTER)}</td>
            <td class="px-4 py-2">Rp ${formatRupiah(komisi.JUMLAH_KOMISI_REUSE_MART)}</td>
            <td class="px-4 py-2">Rp ${formatRupiah(komisi.BONUS_PENITIP)}</td>
          </tr>
        `;
      });

      document.getElementById('laporanBody').innerHTML = rows.join('');
    } catch (err) {
      console.error('Gagal memuat laporan:', err);
      alert('Gagal memuat laporan donasi.');
    }
  }



  function formatRupiah(value) {
    return Number(value || 0).toLocaleString('id-ID');
  }

  function formatTanggal(tgl) {
    return tgl ? new Date(tgl).toLocaleDateString('id-ID') : '-';
  }

  async function unduhLaporanPDF() {
    const bulan = document.getElementById('bulanSelect').value;
    const tahun = document.getElementById('tahunSelect').value;

    const params = new URLSearchParams();
    if (bulan) params.append('bulan', bulan);
    if (tahun) params.append('tahun', tahun);

    try {
      const res = await axios.get(`http://localhost:8000/api/owner/laporan/komisi-bulanan?${params.toString()}`, {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Accept': 'application/pdf'
        },
        responseType: 'blob'
      });

      const blob = new Blob([res.data], { type: 'application/pdf' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `laporan_komisi_bulanan.pdf`;
      link.click();
      URL.revokeObjectURL(url);
    } catch (error) {
      console.error('Gagal unduh laporan:', error);
      alert('Gagal mengunduh laporan PDF.');
    }
  }



  document.addEventListener('DOMContentLoaded', fetchLaporan);
</script>
@endsection
