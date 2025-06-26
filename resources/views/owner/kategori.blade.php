@extends('layouts.app')

@section('title', 'Laporan Penjualan per Kategori')

@section('content')
<div class="flex">
  <x-owner-sidebar />

  <main class="flex-1 p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Laporan Penjualan per Kategori</h2>
      <button onclick="unduhLaporanPDF()" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded shadow">
        🖨 Cetak PDF
      </button>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-100 text-left font-semibold">
          <tr>
            <th class="px-4 py-3">Kategori</th>
            <th class="px-4 py-3">Jumlah Item Terjual</th>
            <th class="px-4 py-3">Jumlah Item Gagal Terjual</th>
          </tr>
        </thead>
        <tbody id="kategoriBody" class="divide-y divide-gray-100"></tbody>
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

  async function fetchLaporanKategori() {
    try {
      const res = await axios.get('http://localhost:8000/api/owner/kategori', { headers });
      const rows = res.data.data.map(k => `
        <tr>
          <td class="px-4 py-2">${k.kategori}</td>
          <td class="px-4 py-2 text-center">${k.terjual}</td>
          <td class="px-4 py-2 text-center">${k.gagal}</td>
        </tr>
      `);
      document.getElementById('kategoriBody').innerHTML = rows.join('');
    } catch (err) {
      console.error('Gagal memuat laporan:', err);
      alert('Gagal memuat laporan kategori.');
    }
  }

  async function unduhLaporanPDF() {
    try {
      const res = await axios.get('http://localhost:8000/api/owner/laporan/kategori', {
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
      link.download = 'laporan_penjualan_kategori.pdf';
      link.click();
      URL.revokeObjectURL(url);
    } catch (error) {
      if (
        error.response &&
        error.response.data instanceof Blob &&
        error.response.headers['content-type']?.includes('application/json')
      ) {
        const text = await error.response.data.text();
        const json = JSON.parse(text);
        console.error('📛 ERROR DETAIL:', json);
        alert(`Gagal export: ${json.message || 'Terjadi error saat generate PDF.'}`);
      } else {
        console.error('Gagal unduh laporan:', error);
        alert('Gagal mengunduh laporan PDF.');
      }
    }
  }

  fetchLaporanKategori();
</script>
@endsection
