@extends('layouts.app')

@section('title', 'Data Penitip')

@section('content')
<div class="flex">
  <x-admin-sidebar />

  <main class="flex-1 p-6">
    <div class="flex justify-between items-center mb-4">
      <button id="btnHitungTopSeller" class="bg-green-600 text-white px-4 py-2 rounded mb-4">
        🔥 Hitung Top Seller
      </button>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-100 text-left font-semibold" id="penitipHeader">
          <tr>
            <th class="px-4 py-3">Nama Penitip</th>
            <th class="px-4 py-3">Badge</th>
          </tr>
        </thead>
        <tbody id="penitipBody" class="divide-y divide-gray-100"></tbody>
      </table>
    </div>
  </main>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const headers = {
      Authorization: `Bearer ${localStorage.getItem('token')}`
    };

    let isTopSellerView = false;

    const btn = document.getElementById('btnHitungTopSeller');
    if (btn) {
      btn.addEventListener('click', async () => {
        try {
          await axios.post('http://localhost:8000/api/admin/hitung-top-seller', {}, { headers });
          alert('✅ Berhasil hitung Top Seller!');
          isTopSellerView = true;
          await loadPenjualanPenitip();
        } catch (err) {
          console.error(err);
          alert('❌ Gagal menghitung Top Seller');
        }
      });
    }

    loadPenitipBiasa();

    async function loadPenitipBiasa() {
      try {
        const res = await axios.get('http://localhost:8000/api/admin/penitip', { headers });
        const list = res.data.data;
        updateHeader(false);
        renderTable(list);
      } catch (err) {
        console.error('Gagal load penitip:', err);
      }
    }

    async function loadPenjualanPenitip() {
      try {
        const res = await axios.get('http://localhost:8000/api/admin/penitip/penjualan', { headers });
        const list = res.data.data;
        updateHeader(true);
        renderTable(list.sort((a, b) => b.total_penjualan - a.total_penjualan));
      } catch (err) {
        console.error('Gagal load total penjualan:', err);
      }
    }

    function updateHeader(showTotal) {
      const header = document.getElementById('penitipHeader');
      header.innerHTML = `
        <tr>
          <th class="px-4 py-3">Nama Penitip</th>
          <th class="px-4 py-3">Badge</th>
          ${showTotal ? '<th class="px-4 py-3">Total Penjualan</th>' : ''}
        </tr>
      `;
    }

    function renderTable(data) {
      const container = document.getElementById('penitipBody');
      container.innerHTML = data.map(item => `
        <tr class="${item.BADGE?.includes('Top Seller') ? 'bg-yellow-100 font-bold' : ''}">
          <td class="px-4 py-2">${item.NAMA_PENITIP}</td>
          <td class="px-4 py-2">${item.BADGE || '-'}</td>
          ${isTopSellerView ? `<td class="px-4 py-2">Rp ${formatRupiah(item.total_penjualan || 0)}</td>` : ''}
        </tr>
      `).join('');
    }

    function formatRupiah(value) {
      return Number(value || 0).toLocaleString('id-ID');
    }
  });
</script>

