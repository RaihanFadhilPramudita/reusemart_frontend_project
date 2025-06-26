@extends('layouts.app')

@section('title', 'Data Jabatan')

@section('content')
<div class="flex">
  <x-admin-sidebar />

  <main class="flex-1 p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Data Jabatan</h2>
      <a href="{{ route('jabatan.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">Tambah Jabatan</a>
    </div>

    <form method="GET" action="{{ route('jabatan.index') }}">
      <input type="text" name="search" placeholder="Cari jabatan..." class="border px-3 py-2 rounded mb-4 w-full" value="{{ request('search') }}" />
    </form>

    <div class="overflow-x-auto">
      <div class="bg-white shadow rounded min-w-[500px] divide-y">
        <div class="px-4 py-3 font-semibold grid grid-cols-3 text-sm">
          <div>ID Jabatan</div>
          <div>Nama Jabatan</div>
          <div>Aksi</div>
        </div>

        <div id="jabatan-list" class="divide-y">
          <!-- Data dimuat lewat JavaScript -->
        </div>
      </div>
    </div>
  </main>
</div>
@endsection

@section('scripts')
<script>
  const baseUrl = '/jabatan'; 
  const searchUrl = '/jabatan/search'; 

  async function loadJabatanFromIndex() {
    await loadJabatan(baseUrl);
  }

  async function loadJabatanFromSearch(keyword) {
    const url = `${searchUrl}?q=${encodeURIComponent(keyword)}`;
    await loadJabatan(url);
  }

  async function loadJabatan(url) {
    try {
      const res = await axios.get(url);
      const list =
        Array.isArray(res.data) ? res.data :
        Array.isArray(res.data.data) ? res.data.data :
        Array.isArray(res.data.data?.data) ? res.data.data.data :
        [];

      const container = document.getElementById('jabatan-list');

      if (!list.length) {
        container.innerHTML = '<div class="p-4 text-gray-500">Tidak ada data jabatan</div>';
        return;
      }

      container.innerHTML = list.map(item => `
        <div class="px-4 py-3 grid grid-cols-3 gap-2 items-start text-sm">
          <div class="break-words whitespace-normal">${item.ID_JABATAN}</div>
          <div class="break-words whitespace-normal">${item.NAMA_JABATAN}</div>
          <div class="space-x-2">
            <a href="/admin/jabatan/${item.ID_JABATAN}/edit" class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</a>
            <button onclick="hapusJabatan(${item.ID_JABATAN})" class="bg-red-600 text-white px-3 py-1 rounded">Hapus</button>
          </div>
        </div>
      `).join('');
    } catch (err) {
      console.error("Gagal memuat data jabatan", err);
    }
  }

  async function hapusJabatan(id) {
    if (!confirm('Yakin ingin menghapus jabatan ini?')) return;
    try {
      await axios.delete(`/jabatan/${id}`);
      loadJabatanFromIndex();
    } catch (err) {
      alert("Gagal menghapus jabatan");
    }
  }

  document.querySelector('form').addEventListener('submit', e => {
    e.preventDefault();
    const keyword = document.querySelector('input[name="search"]').value.trim();
    if (keyword.length === 0) {
      loadJabatanFromIndex();
    } else {
      loadJabatanFromSearch(keyword);
    }
  });

  loadJabatanFromIndex();
</script>
@endsection
