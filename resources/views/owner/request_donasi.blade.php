@extends('layouts.app')

@section('title', 'Request Donasi')

@section('content')
<div class="flex bg-gray-100 min-h-screen overflow-hidden">
  <x-owner-sidebar />

  <main class="flex-1 min-w-0 p-6 overflow-auto">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Daftar Request Donasi</h2>

    <div class="mb-4">
      <input type="text" id="inputSearch" placeholder="Cari nama barang atau kategori..." class="border px-3 py-2 rounded w-full md:w-1/2" />
      <button onclick="cetakLaporanRequestDonasi()" class="bg-green-700 text-white px-4 py-2 rounded shadow">
        🖨 Cetak Laporan Request Donasi
      </button>
    </div>

    <div id="request-donasi" class="bg-white shadow rounded-lg overflow-x-auto">
      <div class="p-4 text-center text-gray-500">Memuat data...</div>
    </div>
  </main>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const token = localStorage.getItem('token');
    const baseUrl = 'http://localhost:8000/api/owner/request_donasi';

    async function loadRequests(keyword = '') {
      const container = document.getElementById('request-donasi');
      try {
        const res = await axios.get(baseUrl, {
          headers: { Authorization: `Bearer ${token}` },
          params: { q: keyword }
        });

        const list = res.data.data || [];

        if (!list.length) {
          container.innerHTML = '<div class="p-4 text-gray-500">Tidak ada request donasi.</div>';
          return;
        }

        container.innerHTML = `
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-600 text-white">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold">Nama Organisasi</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">ID Permohonan</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Nama Barang</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Deskripsi</th>
                <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              ${list.map(item => `
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 text-sm text-gray-800">${item.organisasi?.NAMA_ORGANISASI || '-'}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">${item.ID_REQUEST}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">${item.NAMA_BARANG}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">${item.DESKRIPSI}</td>
                  <td class="px-6 py-4 text-sm">
                    <span class="inline-block px-2 py-1 text-xs font-medium rounded-full ${
                      item.STATUS_REQUEST === 'Menunggu' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'
                    }">${item.STATUS_REQUEST}</span>
                  </td>
                </tr>
              `).join('')}
            </tbody>
          </table>
        `;
      } catch (error) {
        console.error("Gagal load request donasi:", error);

        let message = 'Terjadi kesalahan saat memuat data.';
        if (error.response) {
          if (error.response.status === 404) {
            message = 'Endpoint tidak ditemukan (404). Periksa URL backend.';
          } else if (error.response.status === 401) {
            message = 'Tidak terautentikasi. Silakan login ulang.';
          } else {
            message = `Error ${error.response.status}: ${error.response.statusText}`;
          }
        }

        container.innerHTML = `<div class="p-4 text-red-600">${message}</div>`;
      }
    }

    document.getElementById('inputSearch').addEventListener('input', (e) => {
      const keyword = e.target.value.trim();
      loadRequests(keyword);
    });

    loadRequests();
  });

  async function cetakLaporanRequestDonasi() {
      const token = localStorage.getItem('token');

      try {
        const response = await axios.get('http://localhost:8000/api/owner/laporan/request-donasi', {
          responseType: 'blob',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/pdf'
          }
        });

        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = URL.createObjectURL(blob);

        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'laporan-request-donasi.pdf');
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(url);

      } catch (error) {
        if (
          error.response &&
          error.response.data instanceof Blob &&
          error.response.headers['content-type']?.includes('application/json')
        ) {
          const text = await error.response.data.text();
          console.error('Error isi respons JSON:', text);
        } else {
          console.error('Gagal unduh:', error);
        }

        alert('Gagal mengunduh laporan request donasi. Cek console untuk detail.');
      }
    }

</script>
@endsection

