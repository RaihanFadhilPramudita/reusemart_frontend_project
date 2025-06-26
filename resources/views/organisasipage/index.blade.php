@extends('layouts.app')

@section('title', 'Request Donasi Saya')

@section('content')
<div class="flex">
  <x-organisasi-sidebar />

  <main class="flex-1 p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Request Donasi</h2>
      <button onclick="bukaModal()" class="bg-green-600 text-white px-4 py-2 rounded">Tambah Request</button>
    </div>

    <div class="mb-4">
      <input type="text" id="inputSearch" placeholder="Cari nama barang atau kategori..." class="border px-3 py-2 rounded w-full md:w-1/2" />
    </div>

    <div class="overflow-x-auto">
      <div class="bg-white shadow rounded min-w-[1000px] divide-y">
        <div class="px-4 py-3 font-semibold grid grid-cols-6 text-sm">
          <div>ID</div>
          <div>Nama Barang</div>
          <div>Deskripsi</div>
          <div>Status</div>
          <div>Aksi</div>
        </div>
        <div id="request-list"></div>
      </div>
    </div>
  </main>
</div>

<div id="modalForm" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 w-full max-w-xl">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-bold" id="modalTitle">Tambah Request</h2>
      <button onclick="tutupModal()" class="text-gray-500 hover:text-red-600 text-2xl">&times;</button>
    </div>
    <form id="formRequest">
      <input type="hidden" id="requestId" name="id">
      <div class="mb-3">
        <label class="block text-sm font-semibold">Nama Barang</label>
        <input type="text" id="namaBarang" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-3">
        <label class="block text-sm font-semibold">Deskripsi</label>
        <input type="text" id="deskripsi" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="text-right">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
  const baseUrl = 'http://localhost:8000/api/organisasi/request';
  
  async function loadRequests(keyword = '') {
    try {
      const token = localStorage.getItem('token');
      const url = keyword ? `${baseUrl}/search?q=${encodeURIComponent(keyword)}` : baseUrl;

      const res = await axios.get(url, {
        headers: { Authorization: `Bearer ${token}` }
      });

      const list = res.data.data || [];
      const container = document.getElementById('request-list');
      if (!list.length) {
        container.innerHTML = '<div class="p-4 text-gray-500">Tidak ada request</div>';
        return;
      }

      container.innerHTML = list.map(item => `
        <div class="px-4 py-3 grid grid-cols-6 text-sm items-center">
          <div>${item.ID_REQUEST}</div>
          <div>${item.NAMA_BARANG}</div>
          <div>${item.DESKRIPSI}</div>
          <div>${item.STATUS_REQUEST}</div>
          <div class="space-x-2">
            <button onclick='editRequest(${JSON.stringify(item)})' class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
            <button onclick='hapusRequest(${item.ID_REQUEST})' class="bg-red-600 text-white px-2 py-1 rounded">Hapus</button>
          </div>
        </div>
      `).join('');
    } catch (error) {
      console.error("Gagal muat:", error);
    }
  }

  function bukaModal() {
    document.getElementById('modalForm').classList.remove('hidden');
    document.getElementById('modalForm').classList.add('flex');
    document.getElementById('formRequest').reset();
    document.getElementById('requestId').value = '';
    document.getElementById('modalTitle').innerText = "Tambah Request";
  }

  function tutupModal() {
    document.getElementById('modalForm').classList.add('hidden');
    document.getElementById('modalForm').classList.remove('flex');
  }

  function editRequest(data) {
    bukaModal();
    document.getElementById('modalTitle').innerText = "Edit Request";
    document.getElementById('requestId').value = data.ID_REQUEST;
    document.getElementById('namaBarang').value = data.NAMA_BARANG;
    document.getElementById('deskripsi').value = data.DESKRIPSI;
  }

  async function hapusRequest(id) {
    if (!confirm("Yakin ingin menghapus request ini?")) return;
    try {
      await axios.delete(`${baseUrl}/${id}`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      alert("Berhasil dihapus!");
      loadRequests();
    } catch (error) {
      console.error("Gagal hapus:", error);
    }
  }

  document.getElementById('formRequest').addEventListener('submit', async function (e) {
    e.preventDefault();
    const id = document.getElementById('requestId').value;
    const data = {
      nama_barang: document.getElementById('namaBarang').value,
      deskripsi: document.getElementById('deskripsi').value,
    };

    try {
      if (id) {
        await axios.put(`${baseUrl}/${id}`, data, {
          headers: { Authorization: `Bearer ${token}` }
        });
      } else {
        await axios.post(baseUrl, data, {
          headers: { Authorization: `Bearer ${token}` }
        });
      }
      alert("Berhasil disimpan!");
      tutupModal();
      loadRequests();
    } catch (error) {
      alert("Gagal simpan data.");
      console.error("Gagal:", error);
    }
  });

  document.getElementById('inputSearch').addEventListener('input', () => {
    const keyword = document.getElementById('inputSearch').value.trim();
    loadRequests(keyword);
  });


  loadRequests();
</script>
@endsection
