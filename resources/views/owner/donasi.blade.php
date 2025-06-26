@extends('layouts.app')

@section('title', 'Kelola Donasi')

@section('content')
<div class="flex">
  <x-owner-sidebar />

  <main class="flex-1 p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Daftar Donasi</h2>
      <button onclick="bukaModal()" class="bg-green-600 text-white px-4 py-2 rounded">Tambah Donasi</button>
    </div>
    <div class="mb-4 flex justify-end">
        <button onclick="cetakLaporanDonasiBarang()" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded shadow">
          🖨 Cetak Laporan Donasi Barang
        </button>
      </div>

    <div class="overflow-x-auto bg-white shadow rounded">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-100 text-left text-sm font-semibold">
          <tr>
            <th class="px-4 py-3">ID</th>
            <th class="px-4 py-3">Barang</th>
            <th class="px-4 py-3">Organisasi</th>
            <th class="px-4 py-3">Penerima</th>
            <th class="px-4 py-3">Tanggal</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody id="donasiList" class="text-sm divide-y divide-gray-200"></tbody>
      </table>
    </div>
  </main>
</div>

<div id="modalDonasi" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
  <div class="bg-white p-6 rounded-lg w-full max-w-2xl space-y-4">
    <h3 class="text-lg font-bold" id="modalTitle">Tambah Donasi</h3>
    <form id="formDonasi" class="grid grid-cols-2 gap-4">
    <input type="hidden" id="editIdDonasi">
        <div id="fieldBarang">
            <select id="idBarang" class="border px-3 py-2 rounded" required>
                <option value="">-- Pilih Barang --</option>
            </select>
        </div>
    <select id="idRequest" class="border px-3 py-2 rounded" required>
        <option value="">-- Pilih Request Donasi --</option>
    </select>
    <div id="fieldStatus" class="col-span-2">
      <select id="statusDonasi" class="border px-3 py-2 rounded w-full">
        <option value="">-- Pilih Status Barang --</option>
        <option value="pending">Pending</option>
        <option value="Didonasikan">Didonasikan</option>
        <option value="Selesai">Selesai</option>
      </select>
    </div>
    <input type="text" id="namaPenerima" placeholder="Nama Penerima" class="border px-3 py-2 rounded" required>
    <input type="date" id="tanggalDonasi" class="border px-3 py-2 rounded">
      <div class="col-span-2 flex justify-end space-x-2">
        <button type="button" onclick="tutupModal()" class="bg-gray-400 text-white px-4 py-2 rounded">Batal</button>
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  const headers = { Authorization: `Bearer ${token}` };
  const baseApi = 'http://localhost:8000/api/owner/donasi';

  async function fetchDonasi() {
    try {
      const res = await axios.get(baseApi, { headers });
      const list = document.getElementById('donasiList');

      const donasiDidonasikan = res.data.data.filter(d => d.barang?.STATUS_BARANG === 'Didonasikan');

      list.innerHTML = res.data.data.map(d => `
        <tr>
          <td class="px-4 py-2">${d.ID_DONASI}</td>
          <td class="px-4 py-2">${d.barang?.NAMA_BARANG || '-'}</td>
          <td class="px-4 py-2">${d.request_donasi?.organisasi?.NAMA_ORGANISASI || '-'}</td>
          <td class="px-4 py-2">${d.NAMA_PENERIMA}</td>
          <td class="px-4 py-2">${d.TANGGAL_DONASI ?? '-'}</td>
          <td class="px-4 py-2">
            <span class="inline-block px-2 py-1 text-xs font-medium rounded-full ${
              d.barang?.STATUS_BARANG === 'pending' ? 'bg-yellow-100 text-yellow-800' :
              d.barang?.STATUS_BARANG === 'Didonasikan' ? 'bg-green-100 text-blue-800' :
              'bg-gray-100 text-gray-800'
            }">
              ${d.barang?.STATUS_BARANG || '-'}
            </span>
          </td>
          <td class="px-4 py-2 space-x-2">
            <button onclick="editDonasi(${d.ID_DONASI})" class="bg-yellow-400 text-white px-3 py-1 rounded">Edit</button>
          </td>
        </tr>
      `).join('');
    } catch (err) {
      console.error("Gagal ambil data donasi:", err);
    }
  }

    function bukaModal(mode = 'tambah') {
        const selectBarang = document.getElementById('idBarang');     
        const fieldBarang = document.getElementById('fieldBarang'); 
        const namaPenerima = document.getElementById('namaPenerima'); 

        document.getElementById('modalDonasi').classList.remove('hidden');
        document.getElementById('formDonasi').reset();
        document.getElementById('editIdDonasi').value = '';
        document.getElementById('modalTitle').innerText = mode === 'edit' ? 'Edit Donasi' : 'Tambah Donasi';

        const fieldStatus = document.getElementById('fieldStatus');

        if (mode === 'edit') {
          fieldBarang.style.display = 'none';
          fieldStatus.style.display = 'block';
          namaPenerima.removeAttribute('required');
          selectBarang.removeAttribute('required');
        } else {
          fieldBarang.style.display = 'block';
          fieldStatus.style.display = 'none';
          selectBarang.setAttribute('required', true);
          namaPenerima.setAttribute('required', true);
          fetchBarangLayakDonasi();
        }


        fetchRequestDonasi();
    }


  function tutupModal() {
    document.getElementById('modalDonasi').classList.add('hidden');
  }

    async function editDonasi(id) {
    try {
        const res = await axios.get(`${baseApi}/${id}`, { headers });
        const d = res.data.data;

        bukaModal('edit');
        document.getElementById('modalTitle').innerText = 'Edit Donasi';

        await fetchBarangLayakDonasi();
        await fetchRequestDonasi();

        document.getElementById('editIdDonasi').value = d.ID_DONASI;
        document.getElementById('idBarang').value = String(d.ID_BARANG ?? '');
        document.getElementById('idRequest').value = String(d.ID_REQUEST ?? '');
        document.getElementById('namaPenerima').value = d.NAMA_PENERIMA ?? '';
        document.getElementById('tanggalDonasi').value = d.TANGGAL_DONASI?.split('T')[0] || '';
        document.getElementById('statusDonasi').value = d.barang?.STATUS_BARANG || '';
    } catch (err) {
        console.error("Gagal ambil data donasi:", err);
    }
    }



  async function fetchBarangLayakDonasi() {
    try {
        const res = await axios.get('http://localhost:8000/api/owner/barang/layak-donasi', { headers });
        const select = document.getElementById('idBarang');
        select.innerHTML = '<option value="">-- Pilih Barang --</option>';
        res.data.data.forEach(barang => {
        select.innerHTML += `<option value="${barang.ID_BARANG}">${barang.NAMA_BARANG}</option>`;
        });
    } catch (err) {
        console.error('Gagal ambil data barang:', err);
    }
}

    async function fetchRequestDonasi() {
    try {
        const res = await axios.get('http://localhost:8000/api/owner/request_donasi', { headers });
        const select = document.getElementById('idRequest');
        select.innerHTML = '<option value="">-- Pilih Request Donasi --</option>';

        res.data.data.forEach(request => {
        const orgName = request.organisasi?.NAMA_ORGANISASI || '-';
        select.innerHTML += `<option value="${request.ID_REQUEST}" data-org="${orgName}">Request #${request.ID_REQUEST} - ${orgName}</option>`;
        });

    } catch (err) {
        console.error('Gagal ambil data request donasi:', err);
    }
    }

    document.getElementById('formDonasi').addEventListener('submit', async function (e) {
      e.preventDefault();

      const idDonasi = document.getElementById('editIdDonasi').value;
      const idBarang = document.getElementById('idBarang').value;
      const idRequest = document.getElementById('idRequest').value;
      const namaPenerima = document.getElementById('namaPenerima').value;
      const tanggalDonasi = document.getElementById('tanggalDonasi').value;
      const statusDonasi = document.getElementById('statusDonasi').value;

      try {
        const payload = {
          id_barang: idBarang,
          id_request: idRequest,
          nama_penerima: namaPenerima,
          tanggal_donasi: tanggalDonasi,
          ...(idDonasi && { status_barang: statusDonasi }) // hanya saat edit
        };


        const url = idDonasi
          ? `${baseApi}/${idDonasi}`
          : baseApi;

        const method = idDonasi ? 'put' : 'post';

        await axios[method](url, payload, { headers });

        alert('Donasi berhasil disimpan!');
        tutupModal();
        fetchDonasi();
      } catch (err) {
        console.error('Gagal simpan donasi:', err.response?.data || err.message);
        alert(err.response?.data?.message || 'Terjadi kesalahan saat menyimpan donasi.');
      }
    });

    async function cetakLaporanDonasiBarang() {
      const token = localStorage.getItem('token');
      try {
        const response = await axios.get('http://localhost:8000/api/owner/laporan/donasi-barang', {
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
        link.setAttribute('download', 'laporan-donasi-barang.pdf');
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

        alert('Gagal mengunduh laporan donasi barang. Cek console untuk detail.');
      }
    }

  fetchDonasi();
</script>
@endsection
