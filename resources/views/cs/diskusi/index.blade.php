@extends('layouts.app')

@section('title', 'Diskusi Produk CS')

@section('content')
<div class="flex">
  <x-cs-sidebar />


  <main class="flex-1 p-6">
    <h2 class="text-2xl font-bold mb-4">Diskusi Produk</h2>

    <div id="produk-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <!-- Produk dimuat lewat JS -->
    </div>
  </main>
</div>
@endsection

@section('scripts')
<script>
  const container = document.getElementById('produk-list');

  async function loadBarang() {
    try {
      const res = await axios.get('http://localhost:8000/api/barang');
      const data = res.data.data;

      container.innerHTML = data.map(item => `
        <div class="bg-white shadow rounded overflow-hidden">
          <img src="http://localhost:8000/storage/${item.FOTO_BARANG}" class="w-full h-48 object-cover">
          <div class="p-4">
            <h3 class="text-lg font-semibold">${item.NAMA_BARANG}</h3>
            <a href="/cs/diskusi/${item.ID_BARANG}" class="text-green-600 font-semibold">Lihat Diskusi</a>
          </div>
        </div>
      `).join('');
    } catch (error) {
      console.error(error);
      container.innerHTML = '<p class="text-red-500">Gagal memuat produk.</p>';
    }
  }

  loadBarang();
</script>
@endsection
