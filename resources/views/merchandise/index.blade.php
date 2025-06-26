@extends('layouts.app')

@section('title', 'Data Merchandise')

@section('content')
<div class="flex">
  <x-admin-sidebar />

  <main class="flex-1 p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Data Merchandise</h2>
      <a href="{{ route('merchandise.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">Tambah Merchandise</a>
    </div>

    <form method="GET" action="{{ route('merchandise.index') }}">
      <input type="text" name="search" placeholder="Cari merchandise..." class="border px-3 py-2 rounded mb-4 w-full" value="{{ request('search') }}" />
    </form>

    <div class="overflow-x-auto">
      <div class="bg-white shadow rounded min-w-[700px] divide-y">
        <div class="px-4 py-3 font-semibold grid grid-cols-6 text-sm">
          <div>ID</div>
          <div>Nama</div>
          <div>Deskripsi</div>
          <div>Poin</div>
          <div>Stok</div>
          <div>Aksi</div>
        </div>

        <div id="merchandise-list" class="divide-y"></div>
      </div>
    </div>
  </main>
</div>
@endsection

@section('scripts')
<script>
  async function loadMerchandise() {
    try {
      const res = await axios.get('/merchandise');
      const list = res.data.data || res.data;
      const container = document.getElementById('merchandise-list');

      if (!list.length) {
        container.innerHTML = '<div class="p-4 text-gray-500">Tidak ada data merchandise</div>';
        return;
      }

      container.innerHTML = list.map(item => `
        <div class="px-4 py-3 grid grid-cols-6 gap-2 items-start text-sm">
          <div class="break-words whitespace-normal">${item.ID_MERCHANDISE}</div>
          <div class="break-words whitespace-normal">${item.NAMA_MERCHANDISE}</div>
          <div class="break-words whitespace-normal">${item.DESKRIPSI}</div>
          <div class="break-words whitespace-normal">${item.POIN_REQUIRED}</div>
          <div class="break-words whitespace-normal">${item.STOK}</div>
          <div class="space-x-2">
            <a href="/admin/merchandise/${item.ID_MERCHANDISE}/edit" class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</a>
            <button onclick="hapusMerchandise(${item.ID_MERCHANDISE})" class="bg-red-600 text-white px-3 py-1 rounded">Hapus</button>
          </div>
        </div>
      `).join('');
    } catch (err) {
      console.error("Gagal memuat data merchandise", err);
    }
  }

  async function hapusMerchandise(id) {
    if (!confirm('Yakin ingin menghapus merchandise ini?')) return;
    try {
      await axios.delete(`/merchandise/${id}`);
      loadMerchandise();
    } catch (err) {
      alert("Gagal menghapus merchandise");
    }
  }

  loadMerchandise();
</script>
@endsection
