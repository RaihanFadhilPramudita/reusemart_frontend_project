@extends('layouts.app')

@section('content')
<div class="flex">
  <aside class="w-64 bg-white shadow h-full min-h-screen">
    <nav class="p-4 space-y-2">
      <a href="{{ route('organisasi.index') }}" class="block px-4 py-2 rounded-md font-medium bg-green-700 text-white">Organisasi</a>
      <a href="{{ route('pegawai.index') }}" class="block px-4 py-2 rounded-md font-medium hover:bg-gray-100 text-gray-700">Pegawai</a>
      <a href="{{ route('jabatan.index') }}" class="block px-4 py-2 rounded-md font-medium hover:bg-gray-100 text-gray-700">Jabatan</a>
      <a href="{{ route('merchandise.index') }}" class="block px-4 py-2 rounded-md font-medium hover:bg-gray-100 text-gray-700">Merchandise</a>
    </nav>
  </aside>

  <main class="flex-1 p-6">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-bold">Data Organisasi</h2>
      <a href="{{ route('organisasi.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">Tambah Organisasi</a>
    </div>

    <form method="GET" action="{{ route('organisasi.index') }}">
      <input type="text" name="search" placeholder="Cari organisasi..." class="border px-3 py-2 rounded mb-4 w-full" value="{{ request('search') }}" />
    </form>

    <div class="bg-white shadow rounded divide-y">
      <div class="px-4 py-3 font-semibold grid grid-cols-3">
        <div>ID</div>
        <div>Nama</div>
        <div>Aksi</div>
      </div>

      @foreach ($organisasis as $organisasi)
        <div class="px-4 py-3 grid grid-cols-3 items-center">
          <div>{{ $organisasi->id }}</div>
          <div>{{ $organisasi->nama }}</div>
          <div class="space-x-2">
            <a href="{{ route('organisasi.edit', $organisasi->id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded">Edit</a>
            <form action="{{ route('organisasi.destroy', $organisasi->id) }}" method="POST" class="inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded">Hapus</button>
            </form>
          </div>
        </div>
      @endforeach
    </div>
  </main>
</div>
@endsection
