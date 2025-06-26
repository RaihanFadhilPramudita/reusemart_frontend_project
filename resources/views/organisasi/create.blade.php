@extends('layouts.app')

@section('content')
<div class="flex">
  <x-admin-sidebar />

  <main class="flex-1 p-6">
    <h2 class="text-xl font-bold mb-4">Tambah Organisasi</h2>

    <form method="POST" action="{{ route('organisasi.store') }}" class="space-y-4">
      @csrf
      <div>
        <label class="block text-sm font-medium">Nama Organisasi</label>
        <input type="text" name="nama" class="w-full px-3 py-2 border rounded" required>
      </div>

      <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Simpan</button>
    </form>
  </main>
</div>
@endsection
