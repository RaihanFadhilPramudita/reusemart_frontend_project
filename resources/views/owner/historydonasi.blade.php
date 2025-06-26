@extends('layouts.app')

@section('title', 'History Donasi')

@section('content')
<div class="flex bg-gray-100 min-h-screen overflow-hidden">
  <x-owner-sidebar />

  <main class="flex-1 min-w-0 p-6 overflow-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">History Donasi ke Organisasi</h2>

    <div class="mb-4">
      <input type="text" id="inputSearch" placeholder="Cari nama organisasi..." class="border px-3 py-2 rounded w-full md:w-1/2" />
    </div>

    <div id="history-donasi" class="bg-white shadow rounded-lg overflow-x-auto">
      <div class="p-4 text-center text-gray-500">Masukkan nama organisasi untuk menampilkan data.</div>
    </div>
  </main>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const token = localStorage.getItem('token');

    async function loadHistory(keyword = '') {
      const container = document.getElementById('history-donasi');
      if (!keyword) {
        container.innerHTML = '<div class="p-4 text-gray-500">Masukkan nama organisasi untuk menampilkan data.</div>';
        return;
      }

      try {
        const res = await axios.get('http://localhost:8000/api/owner/donasi/history-search', {
          headers: { Authorization: `Bearer ${token}` },
          params: { q: keyword }
        });

        const list = res.data.data || [];

        if (!list.length) {
          container.innerHTML = '<div class="p-4 text-gray-500">Tidak ada data donasi untuk organisasi tersebut.</div>';
          return;
        }

        container.innerHTML = `
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-600 text-white">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold">Nama Organisasi</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Nama Barang</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Jenis Barang</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Nama Penerima</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Tanggal Donasi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              ${list.map(item => `
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 text-sm text-gray-800">${item.request_donasi?.organisasi?.NAMA_ORGANISASI || '-'}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">${item.barang?.NAMA_BARANG || '-'}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">${item.JENIS_BARANG || '-'}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">${item.NAMA_PENERIMA || '-'}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">${item.TANGGAL_DONASI || '-'}</td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `;
      } catch (error) {
        container.innerHTML = `<div class="p-4 text-red-600">Gagal memuat data: ${error.message}</div>`;
      }
    }

    document.getElementById('inputSearch').addEventListener('input', (e) => {
      const keyword = e.target.value.trim();
      loadHistory(keyword);
    });
  });
</script>
@endsection
