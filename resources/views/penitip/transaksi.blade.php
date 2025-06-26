@extends('layouts.app')

@section('title', 'Riwayat Transaksi Penitip')

@section('content')
<div class="min-h-screen bg-gray-100 py-10">
  <div class="max-w-6xl mx-auto px-4">
    <div class="bg-white rounded shadow">
      <div class="flex justify-between items-center bg-green-600 text-white p-4 rounded-t">
        <h1 class="text-lg font-semibold">Riwayat Transaksi</h1>
        <a href="/penitip/profile" class="text-green-600 bg-white px-3 py-1 rounded hover:bg-gray-100">Kembali</a>
      </div>

      <div class="p-4 space-y-4">
        <!-- Tabs -->
        <div class="flex space-x-4 border-b">
          <button id="tab-barang" class="tab-active">Barang</button>
          <button id="tab-penjualan">Penjualan</button>
          <button id="tab-komisi">Komisi</button>
        </div>

        <!-- Content: Barang -->
        <div id="content-barang">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-stat-card title="Total Barang" id="total-barang" />
            <x-stat-card title="Terjual" id="total-terjual" color="green" />
            <x-stat-card title="Pendapatan" id="total-pendapatan" color="green" prefix="Rp" />
          </div>

          <x-table :headers="['Kode', 'Nama', 'Kategori', 'Masuk', 'Harga', 'Status', 'Jual']" id="barang-list" colspan="7" />
        </div>

        <!-- Content: Penjualan -->
        <div id="content-penjualan" class="hidden">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-stat-card title="Total Penjualan" id="total-penjualan" />
            <x-stat-card title="Bulan Ini" id="penjualan-bulan-ini" color="green" />
            <x-stat-card title="Total Nilai" id="total-nilai-penjualan" color="green" prefix="Rp" />
          </div>

          <x-table :headers="['Nota', 'Tanggal', 'Barang', 'Pembeli', 'Harga', 'Komisi', 'Pendapatan']" id="penjualan-list" colspan="7" />
        </div>

        <!-- Content: Komisi -->
        <div id="content-komisi" class="hidden">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-stat-card title="Total Komisi" id="total-komisi" color="red" prefix="Rp" />
            <x-stat-card title="Bonus" id="total-bonus" color="green" prefix="Rp" />
            <x-stat-card title="Bulan Ini" id="komisi-bulan-ini" color="red" prefix="Rp" />
          </div>

          <x-table :headers="['ID', 'Tanggal', 'Barang', 'Harga', 'Komisi ReUse', 'Komisi Hunter', 'Bonus']" id="komisi-list" colspan="7" />
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
// Tambahkan JS modularisasi atau versi pendek dari script di file asli.
// Contoh:
// - handleTabs()
// - fetchAndRenderBarang()
// - fetchAndRenderPenjualan()
// - fetchAndRenderKomisi()
</script>
@endpush
